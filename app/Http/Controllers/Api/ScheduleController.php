<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Price;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller untuk menangani logika jadwal lapangan futsal.
 */
class ScheduleController extends Controller
{
    /**
     * Mengambil daftar slot waktu yang tersedia untuk lapangan dan tanggal tertentu.
     * Slot dikembalikan dalam bentuk blok waktu (contoh: per 30 menit atau per 1 jam)
     * yang akan digunakan oleh frontend untuk membangun grid jadwal.
     *
     * @param Request $request Menerima 'field_id', 'date', dan 'duration_minutes'
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(Request $request)
    {
        // Validasi parameter input dari frontend
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:30|max:240|multiple_of:30', // Default 60 menit jika tidak dikirim
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;
        $durationMinutes = (int) $request->input('duration_minutes', 60);

        // Mengambil daftar harga aktif untuk lapangan ini (menentukan jam buka/tutup)
        $prices = $this->activePricesForField($fieldId);

        if ($prices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada informasi harga dan jadwal buka untuk lapangan ini'
            ], 404);
        }

        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        // Mengambil semua data booking di tanggal tersebut yang statusnya:
        // 1. Confirmed (Sudah dibayar)
        // 2. Pending (Sedang dalam proses pembayaran, maksimal 15 menit ke belakang)
        $bookings = Booking::where('field_id', $fieldId)
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                      ->orWhere(function ($q) {
                          $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinutes(15)); // Toleransi waktu pembayaran 15 menit
                      });
            })
            ->where(function ($query) use ($startOfDay, $endOfDay) {
                $query->where('start_time', '<', $endOfDay)
                      ->where('end_time', '>', $startOfDay);
            })
            ->get();

        $availableSlots = [];

        // Membangun array ketersediaan slot waktu
        foreach ($prices as $price) {
            $periodStart = Carbon::parse($date . ' ' . $this->timeString($price->start_time));
            $priceEnd = Carbon::parse($date . ' ' . $this->timeString($price->end_time));
            $slotStart = $periodStart->copy();

            // Memecah rentang waktu harga menjadi slot-slot kecil sesuai duration_minutes (misal 30 menit)
            while ($slotStart->copy()->addMinutes($durationMinutes)->lte($priceEnd)) {
                $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);
                
                // Cek apakah slot ini beririsan dengan data booking yang ada
                $isAvailable = !$this->overlapsBookings($slotStart, $slotEnd, $bookings);

                $availableSlots[] = [
                    'start_time' => $slotStart->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'time_period' => $price->time_period, // Misal: "Pagi", "Siang", "Malam"
                    'price_per_hour' => $price->price_per_hour,
                    'total_price' => ((float) $price->price_per_hour) * ($durationMinutes / 60),
                    'duration_minutes' => $durationMinutes,
                    'status' => $isAvailable ? 'tersedia' : 'terbooking',
                    'available' => $isAvailable,
                ];

                $slotStart->addMinutes($durationMinutes);
            }
        }

        return response()->json([
            'success' => true,
            'field_id' => $fieldId,
            'date' => $date,
            'slots' => $availableSlots
        ]);
    }

    /**
     * Mengambil ringkasan jadwal dalam satu hari secara utuh.
     * Mirip dengan getAvailableSlots namun digunakan untuk tampilan list biasa.
     */
    public function getDaySchedule(Request $request)
    {
        // Validasi input
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;

        $prices = $this->activePricesForField($fieldId);

        if ($prices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada informasi harga untuk lapangan ini'
            ], 404);
        }

        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        // Mengambil booking (confirmed / pending dalam 15 menit terakhir)
        $bookings = Booking::where('field_id', $fieldId)
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                      ->orWhere(function ($q) {
                          $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinutes(15));
                      });
            })
            ->where(function ($query) use ($startOfDay, $endOfDay) {
                $query->where('start_time', '<', $endOfDay)
                      ->where('end_time', '>', $startOfDay);
            })
            ->get();

        $schedule = [];

        // Memeriksa setiap blok harga secara langsung tanpa dipecah per durasi spesifik
        foreach ($prices as $price) {
            $slotStart = Carbon::parse($date . ' ' . $this->timeString($price->start_time));
            $slotEnd = Carbon::parse($date . ' ' . $this->timeString($price->end_time));
            
            // Cek apakah slot besar ini sudah ada yang booking
            $isBooked = $this->overlapsBookings($slotStart, $slotEnd, $bookings);

            $schedule[] = [
                'time_period' => $price->time_period,
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'price_per_hour' => $price->price_per_hour,
                'is_booked' => $isBooked,
                'status' => $isBooked ? 'terbooking' : 'tersedia'
            ];
        }

        return response()->json([
            'success' => true,
            'field_id' => $fieldId,
            'date' => $date,
            'schedule' => $schedule
        ]);
    }

    /**
     * Mendapatkan daftar harga yang aktif untuk suatu lapangan tertentu.
     * Jika tidak ada harga spesifik untuk lapangan ini, akan mengambil harga default.
     *
     * @param int|string $fieldId ID lapangan
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function activePricesForField(int|string $fieldId)
    {
        $prices = Price::where('field_id', $fieldId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($prices->isNotEmpty()) {
            return $prices;
        }

        // Fallback: Mengambil harga global (field_id is null)
        return Price::whereNull('field_id')
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Memeriksa apakah suatu slot waktu beririsan (overlap) dengan jadwal yang sudah di-booking.
     *
     * @param Carbon $slotStart Waktu mulai slot
     * @param Carbon $slotEnd Waktu selesai slot
     * @param \Illuminate\Support\Collection $bookings Daftar booking
     * @return bool True jika bentrok, False jika kosong
     */
    private function overlapsBookings(Carbon $slotStart, Carbon $slotEnd, $bookings): bool
    {
        foreach ($bookings as $booking) {
            // Logika overlap: 
            // Waktu mulai slot LEBIH KECIL dari waktu selesai booking, 
            // DAN waktu selesai slot LEBIH BESAR dari waktu mulai booking
            if ($slotStart->lt($booking->end_time) && $slotEnd->gt($booking->start_time)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format helper untuk mengubah waktu menjadi format "H:i:s".
     *
     * @param string $value Waktu
     * @return string
     */
    private function timeString($value): string
    {
        return Carbon::parse($value)->format('H:i:s');
    }
}
