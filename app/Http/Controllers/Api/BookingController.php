<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            $bookings = Booking::with(['user', 'field', 'payment'])->latest()->get();
        } else {
            $bookings = Booking::with(['field', 'payment'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_id' => 'required|exists:fields,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $field = Field::find($request->field_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // Check availability
        $overlapping = Booking::where('field_id', $field->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($overlapping) {
            return response()->json(['error' => 'The field is already booked for this time slot.'], 409);
        }

        // Calculate price
        $durationInHours = $startTime->diffInMinutes($endTime) / 60;
        $totalPrice = $durationInHours * $field->price_per_hour;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'field_id' => $field->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking created successfully. Please proceed to payment.',
            'booking' => $booking->load('field')
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'field', 'payment'])->findOrFail($id);
        
        // Authorization check
        if (!Auth::user()->hasRole('admin') && $booking->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        // Authorization check
        if (!Auth::user()->hasRole('admin') && $booking->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return response()->json(['error' => 'Booking cannot be cancelled in its current state.'], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking
        ]);
    }
}
