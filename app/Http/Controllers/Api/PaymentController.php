<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $booking = Booking::findOrFail($request->booking_id);
        $user = $request->user();

        // Authorization check
        if (!$user || (!$user->hasRole('admin') && $booking->user_id !== $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Payment can only be processed for pending bookings.'], 400);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json(['error' => 'Booking has already been paid.'], 400);
        }

        try {
            return DB::transaction(function () use ($booking, $request) {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'success', // Assuming success for this simulation
                    'transaction_id' => $request->transaction_id ?? 'TXN-' . time(),
                    'paid_at' => now(),
                ]);

                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => $request->payment_method,
                    'paid_at' => now(),
                    'confirmed_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Payment successful. Booking confirmed.',
                    'payment' => $payment,
                    'booking' => $booking->load('field')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Payment failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $payment = Payment::with('booking')->findOrFail($id);
        $user = $request->user();

        // Authorization check
        if (!$user || (!$user->hasRole('admin') && $payment->booking->user_id !== $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($payment);
    }
}
