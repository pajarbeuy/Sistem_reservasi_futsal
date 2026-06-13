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
     * Returns hourly slots (06:00-07:00, 07:00-08:00, etc.)
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;

        // Get all hourly prices for this field
        $prices = Price::where('field_id', $fieldId)
            ->where('is_active', true)
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

        // Check each hourly slot
        foreach ($prices as $price) {
            $isAvailable = true;
            
            // Check if this slot overlaps with any booking
            foreach ($bookings as $booking) {
                $bookingStart = $booking->start_time;
                $bookingEnd = $booking->end_time;
                
                // Create datetime objects for comparison
                $slotStart = Carbon::createFromFormat('H:i:s', $price->start_time)->setDateFrom($date);
                $slotEnd = Carbon::createFromFormat('H:i:s', $price->end_time)->setDateFrom($date);
                
                // Check if times overlap
                if ($slotStart < $bookingEnd && $slotEnd > $bookingStart) {
                    $isAvailable = false;
                    break;
                }
            }

            $availableSlots[] = [
                'start_time' => Carbon::createFromFormat('H:i:s', $price->start_time)->format('H:i'),
                'end_time' => Carbon::createFromFormat('H:i:s', $price->end_time)->format('H:i'),
                'time_period' => $price->time_period,
                'price_per_hour' => $price->price_per_hour,
                'status' => $isAvailable ? 'tersedia' : 'terbooking',
                'available' => $isAvailable,
            ];
        }

        return response()->json([
            'success' => true,
            'field_id' => $fieldId,
            'date' => $date,
            'slots' => $availableSlots
        ]);
    }

    /**
     * Get schedule summary for a date (hourly breakdown)
     */
    public function getDaySchedule(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $fieldId = $request->field_id;
        $date = $request->date;

        // Get all hourly prices for this field
        $prices = Price::where('field_id', $fieldId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($prices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pricing information available'
            ], 404);
        }

        // Get booked slots for this date
        $bookings = Booking::where('field_id', $fieldId)
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_time', $date)
            ->get();

        $schedule = [];

        // Check each hourly slot
        foreach ($prices as $price) {
            $isBooked = false;
            
            // Check if this slot is booked
            foreach ($bookings as $booking) {
                $slotStart = Carbon::createFromFormat('H:i:s', $price->start_time)->setDateFrom($date);
                $slotEnd = Carbon::createFromFormat('H:i:s', $price->end_time)->setDateFrom($date);
                
                // Check if times overlap
                if ($slotStart < $booking->end_time && $slotEnd > $booking->start_time) {
                    $isBooked = true;
                    break;
                }
            }

            $schedule[] = [
                'time_period' => $price->time_period,
                'start_time' => Carbon::createFromFormat('H:i:s', $price->start_time)->format('H:i'),
                'end_time' => Carbon::createFromFormat('H:i:s', $price->end_time)->format('H:i'),
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
}
