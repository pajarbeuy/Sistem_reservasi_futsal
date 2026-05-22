<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Price;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Get available time slots for a specific field and date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:30',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;
        $durationMinutes = $request->duration_minutes ?? 60;

        // Get all prices to determine time slots
        $prices = Price::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($prices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pricing information available'
            ], 404);
        }

        // Get booked slots for this field and date
        $bookings = Booking::where('field_id', $fieldId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_time', $date)
            ->get();

        $availableSlots = [];

        // Generate all possible time slots
        foreach ($prices as $price) {
            $startTime = Carbon::createFromFormat('H:i:s', $price->start_time);
            $endTime = Carbon::createFromFormat('H:i:s', $price->end_time);

            $currentTime = $startTime->clone();

            while ($currentTime->addMinutes($durationMinutes)->lte($endTime)) {
                $slotStart = $currentTime->clone()->subMinutes($durationMinutes);
                $slotEnd = $currentTime->clone();

                // Check if slot is available (not overlapping with any booking)
                $isAvailable = true;
                foreach ($bookings as $booking) {
                    $bookingStart = Carbon::createFromFormat('H:i', $booking->start_time->format('H:i'));
                    $bookingEnd = Carbon::createFromFormat('H:i', $booking->end_time->format('H:i'));

                    if ($slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $availableSlots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'time_period' => $price->time_period,
                        'price_per_hour' => $price->price_per_hour,
                        'status' => 'tersedia',
                        'available' => true,
                    ];
                }

                $currentTime = $slotEnd->clone();
            }
        }

        return response()->json([
            'success' => true,
            'field_id' => $fieldId,
            'date' => $date,
            'duration_minutes' => $durationMinutes,
            'slots' => $availableSlots
        ]);
    }

    /**
     * Get schedule summary for a date
     */
    public function getDaySchedule(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;

        // Get all prices
        $prices = Price::where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // Get booked slots
        $bookings = Booking::where('field_id', $fieldId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_time', $date)
            ->get();

        $schedule = [];

        foreach ($prices as $price) {
            $bookedCount = 0;
            foreach ($bookings as $booking) {
                $bookingStart = $booking->start_time->format('H:i');
                $bookingEnd = $booking->end_time->format('H:i');

                // Check if booking overlaps with this time period
                if ($bookingStart < $price->end_time && $bookingEnd > $price->start_time) {
                    $bookedCount++;
                }
            }

            $schedule[] = [
                'time_period' => $price->time_period,
                'start_time' => $price->start_time,
                'end_time' => $price->end_time,
                'price_per_hour' => $price->price_per_hour,
                'available_count' => max(0, 8 - $bookedCount), // Assuming 8 slots per period
                'booked_count' => $bookedCount,
                'status' => $bookedCount > 0 ? 'partially_booked' : 'available'
            ];
        }

        return response()->json([
            'success' => true,
            'field_id' => $fieldId,
            'date' => $date,
            'schedule' => $schedule
        ]);
    }
}
