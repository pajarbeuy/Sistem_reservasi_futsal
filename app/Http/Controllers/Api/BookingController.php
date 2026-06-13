<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->hasRole('admin')) {
                $bookings = Booking::with(['user', 'field', 'payment'])->latest()->get();
            } else {
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
                'message' => 'Error fetching bookings'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'field_id' => 'required|exists:fields,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'phone_number' => 'nullable|string|max:30',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $field = Field::find($request->field_id);
            if (!$field) {
                return response()->json([
                    'error' => true,
                    'message' => 'Field not found'
                ], 404);
            }

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);

            // Validate times are valid
            if ($startTime->gte($endTime)) {
                return response()->json([
                    'error' => true,
                    'message' => 'End time must be after start time'
                ], 400);
            }

            $user = $request->user();

            // Check availability
            $overlapping = Booking::where('field_id', $field->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                })
                ->first();

            if ($overlapping) {
                if ($overlapping->user_id === $user->id && $overlapping->status === 'pending') {
                    return response()->json([
                        'error' => false,
                        'message' => 'Booking pending sudah ada. Silakan lanjutkan pembayaran.',
                        'data' => $overlapping->load(['field', 'user'])
                    ]);
                }

                return response()->json([
                    'error' => true,
                    'message' => 'Lapangan sudah dibooking untuk slot waktu ini.'
                ], 409);
            }

            // Calculate price
            $durationInHours = $startTime->diffInMinutes($endTime) / 60;
            $totalPrice = $durationInHours * $field->price_per_hour;

            $booking = Booking::create([
                'user_id' => $user->id,
                'booking_code' => 'BK-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'field_id' => $field->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_price' => $totalPrice,
                'phone_number' => $request->input('phone_number'),
                'notes' => $request->input('notes'),
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Booking created successfully. Please proceed to payment.',
                'data' => $booking->load(['field', 'user'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error creating booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $booking = Booking::with(['user', 'field', 'payment'])->findOrFail($id);
            $user = $request->user();
            
            // Authorization check
            if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'error' => false,
                'data' => $booking
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Booking not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Booking show error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error fetching booking'
            ], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $user = $request->user();

            // Authorization check
            if (!$user->hasRole('admin') && $booking->user_id !== $user->id) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($booking->status === 'completed' || $booking->status === 'cancelled') {
                return response()->json([
                    'error' => true,
                    'message' => 'Booking cannot be cancelled in its current state.'
                ], 400);
            }

            $booking->update([
                'status' => 'cancelled',
                'payment_status' => $booking->payment_status === 'paid' ? $booking->payment_status : 'failed',
                'cancelled_at' => now(),
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Booking cancelled successfully.',
                'data' => $booking
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Booking not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Booking cancel error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error cancelling booking'
            ], 500);
        }
    }
}
