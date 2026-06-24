<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controller untuk menangani pemesanan (booking) lapangan futsal
 */
class BookingController extends Controller
{
    /**
     * Menampilkan daftar booking.
     * Jika user adalah admin, tampilkan semua booking.
     * Jika user biasa, tampilkan hanya booking miliknya.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->hasRole('admin')) {
                // Admin dapat melihat semua riwayat
                $bookings = Booking::with(['user', 'field', 'payment'])->latest()->get();
            } else {
                // User biasa hanya melihat riwayat miliknya
                $bookings = Booking::with(['field', 'payment'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();
            }

            return response()->json([
                'error' => false,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            Log::error('Booking index error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengambil data booking'
            ], 500);
        }
    }

    /**
     * Membuat data booking baru di database.
     * Logika ini memeriksa ketersediaan jadwal, validasi rentang waktu,
     * serta menghitung total harga sesuai tarif.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi Input Data
            $validator = Validator::make($request->all(), [
                'field_id' => 'required|exists:fields,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'phone_number' => 'nullable|string|max:30',
                'notes' => 'nullable|string|max:1000',
                'total_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 2. Mengecek apakah lapangan yang di-request ada dan tersedia secara general
            $field = Field::find($request->field_id);
            if (!$field) {
                return response()->json([
                    'error' => true,
                    'message' => 'Lapangan tidak ditemukan'
                ], 404);
            }

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);

            if ($startTime->gte($endTime)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Waktu selesai harus sesudah waktu mulai'
                ], 400);
            }

            if ($startTime->isPast()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Waktu booking tidak boleh di masa lalu.'
                ], 400);
            }

            if (!$field->is_available) {
                return response()->json([
                    'error' => true,
                    'message' => 'Lapangan sedang ditutup atau tidak tersedia.'
                ], 400);
            }

            $user = $request->user();

            // 3. Mengecek Ketersediaan Jadwal (Mencegah Double Booking)
            // Cek apakah ada booking lain di jam yang sama yang statusnya:
            // a. Confirmed (sudah fix)
            // b. Pending (masih dalam jendela pembayaran 15 menit terakhir)
            $overlapping = Booking::where('field_id', $field->id)
                ->where(function ($query) {
                    $query->where('status', 'confirmed')
                          ->orWhere(function ($q) {
                              $q->where('status', 'pending')
                                ->where('created_at', '>=', now()->subMinutes(15));
                          });
                })
                ->where(function ($query) use ($startTime, $endTime) {
                    // Logika Intersection/Irisan Waktu
                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                })
                ->first();

            if ($overlapping) {
                // Jika jadwal bentrok dengan booking-an diri sendiri yang statusnya masih pending, 
                // arahkan user untuk melanjutkan pembayaran
                if ($overlapping->user_id === $user->id && $overlapping->status === 'pending') {
                    return response()->json([
                        'error' => false,
                        'message' => 'Booking pending sudah ada. Silakan lanjutkan pembayaran.',
                        'data' => $overlapping->load(['field', 'user'])
                    ]);
                }

                // Jika bentrok dengan orang lain atau sudah confirmed
                return response()->json([
                    'error' => true,
                    'message' => 'Mohon maaf, lapangan sudah dibooking pada jam tersebut.'
                ], 409);
            }

            // 4. Kalkulasi harga total berdasarkan tabel harga (berbeda-beda per jam)
            $totalPrice = $this->calculateTotalPrice($field, $startTime, $endTime);

            // 5. Simpan Booking ke Database
            $booking = Booking::create([
                'user_id' => $user->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'field_id' => $field->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_price' => $totalPrice,
                'phone_number' => $request->input('phone_number'),
                'notes' => $request->input('notes'),
                'status' => 'pending',         // Awal booking status pending
                'payment_status' => 'pending', // Awal pembayaran status pending
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Berhasil membuat pesanan. Silakan lanjutkan ke pembayaran.',
                'data' => $booking->load(['field', 'user'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung total harga booking dengan mempertimbangkan harga spesifik 
     * di rentang waktu tertentu (Misalnya: Pagi harga 100k, Malam harga 150k).
     *
     * @param Field $field Objek Lapangan
     * @param Carbon $startTime Waktu Mulai
     * @param Carbon $endTime Waktu Selesai
     * @return float Total Harga
     */
    private function calculateTotalPrice(Field $field, Carbon $startTime, Carbon $endTime): float
    {
        // Ambil harga spesifik untuk lapangan ini
        $prices = Price::where('field_id', $field->id)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // Jika kosong, ambil harga default/global
        if ($prices->isEmpty()) {
            $prices = Price::whereNull('field_id')
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
        }

        $coveredMinutes = 0;
        $total = 0.0;

        // Loop untuk setiap rentang harga untuk menghitung irisan waktunya
        foreach ($prices as $price) {
            $periodStart = Carbon::parse($startTime->toDateString() . ' ' . Carbon::parse($price->start_time)->format('H:i:s'));
            $periodEnd = Carbon::parse($startTime->toDateString() . ' ' . Carbon::parse($price->end_time)->format('H:i:s'));

            // Tentukan irisan waktu booking dengan periode harga ini
            $overlapStart = $startTime->greaterThan($periodStart) ? $startTime : $periodStart;
            $overlapEnd = $endTime->lessThan($periodEnd) ? $endTime : $periodEnd;

            // Jika ada irisan waktu, hitung harganya proporsional per menit
            if ($overlapStart->lt($overlapEnd)) {
                $minutes = $overlapStart->diffInMinutes($overlapEnd);
                $coveredMinutes += $minutes;
                $total += ((float) $price->price_per_hour) * ($minutes / 60);
            }
        }

        $durationMinutes = $startTime->diffInMinutes($endTime);

        // Jika seluruh durasi berhasil di-cover oleh tabel harga, kembalikan totalnya
        if ($coveredMinutes >= $durationMinutes && $total > 0) {
            return round($total, 2);
        }

        // Fallback: Gunakan harga dasar lapangan (price_per_hour dari field)
        return round(((float) $field->price_per_hour) * ($durationMinutes / 60), 2);
    }

    /**
     * Menampilkan detail satu booking tertentu
     *
     * @param Request $request
     * @param mixed $id Booking ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $booking = Booking::with(['user', 'field', 'payment'])->findOrFail($id);
            $user = $request->user();
            
            // Authorization: User hanya bisa melihat bookingnya sendiri (kecuali admin)
            if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Anda tidak memiliki akses (Unauthorized)'
                ], 403);
            }

            return response()->json([
                'error' => false,
                'data' => $booking
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Booking show error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengambil detail booking'
            ], 500);
        }
    }

    /**
     * Membatalkan pemesanan (booking) oleh user/admin
     *
     * @param Request $request
     * @param mixed $id Booking ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $user = $request->user();

            // Authorization: User hanya bisa membatalkan bookingnya sendiri (kecuali admin)
            if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Anda tidak memiliki akses (Unauthorized)'
                ], 403);
            }

            // Validasi state booking
            if ($booking->status === 'completed' || $booking->status === 'cancelled') {
                return response()->json([
                    'error' => true,
                    'message' => 'Booking ini sudah tidak bisa dibatalkan.'
                ], 400);
            }

            // Eksekusi pembatalan
            $booking->update([
                'status' => 'cancelled',
                // Jika sudah paid (dibayar) maka status pembayaran tetap paid (mungkin urusan refund nanti)
                // Jika pending, ubah jadi failed.
                'payment_status' => $booking->payment_status === 'paid' ? $booking->payment_status : 'failed',
                'cancelled_at' => now(),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Booking berhasil dibatalkan.',
                'data' => $booking
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Booking cancel error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat membatalkan booking'
            ], 500);
        }
    }
}
