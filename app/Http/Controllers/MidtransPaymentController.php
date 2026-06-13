<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransPaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Midtrans Snap Token
     */
    public function createMidtransToken(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'nullable|numeric|min:1',
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'required|string',
        ]);

        try {
            if (blank(config('midtrans.server_key')) || blank(config('midtrans.client_key'))) {
                return response()->json([
                    'error' => true,
                    'message' => 'Konfigurasi Midtrans belum lengkap. Isi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di file .env.',
                ], 422);
            }

            $booking = Booking::with('user')->findOrFail($validated['booking_id']);

            if ($booking->user_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthorized',
                ], 403);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'error' => true,
                    'message' => 'Pembayaran hanya bisa dibuat untuk booking yang masih pending.',
                ], 400);
            }

            $amount = (int) round($booking->total_price);

            // Create transaction data for Midtrans
            $transactionDetails = [
                'order_id' => 'BOOKING-' . $booking->id . '-' . time(),
                'gross_amount' => $amount,
            ];

            $customerDetails = [
                'first_name' => $validated['customer_name'] ?? $booking->user->name,
                'email' => $validated['customer_email'] ?? $booking->user->email,
                'phone' => $validated['customer_phone'],
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'error' => route('payment.error'),
                    'pending' => route('payment.pending'),
                ],
            ];

            // Create Snap token
            $snapToken = Snap::getSnapToken($payload);

            // Save payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'transaction_id' => $transactionDetails['order_id'],
            ]);

            $booking->update([
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'error' => false,
                'token' => $snapToken,
                'redirect_url' => route('payment.finish'),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans token error: ' . $e->getMessage(), [
                'booking_id' => $request->input('booking_id'),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle Midtrans callback
     */
    public function handleCallback(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'status_code' => 'required|string',
            'gross_amount' => 'required',
            'signature_key' => 'required|string',
            'transaction_status' => 'required|string',
            'fraud_status' => 'nullable|string',
        ]);

        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash(
            'sha512',
            $validated['order_id'] . $validated['status_code'] . $validated['gross_amount'] . $serverKey
        );

        if (!hash_equals($expectedSignature, $validated['signature_key'])) {
            Log::warning('Invalid Midtrans notification signature', [
                'order_id' => $validated['order_id'],
                'transaction_status' => $validated['transaction_status'],
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Invalid signature',
            ], 403);
        }

        $payment = Payment::with('booking')->where('transaction_id', $validated['order_id'])->first();

        if (!$payment) {
            Log::warning('Midtrans notification payment not found', [
                'order_id' => $validated['order_id'],
                'transaction_status' => $validated['transaction_status'],
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Payment not found',
            ], 404);
        }

        $transactionStatus = $validated['transaction_status'];
        $fraudStatus = $validated['fraud_status'] ?? null;
        $paymentStatus = $this->mapMidtransPaymentStatus($transactionStatus, $fraudStatus);
        $paidAt = $paymentStatus === 'success' ? now() : null;
        $failedAt = $paymentStatus === 'failed' ? now() : null;
        $paymentMethod = $request->input('payment_type', 'midtrans');

        DB::transaction(function () use ($payment, $paymentStatus, $paidAt, $failedAt, $paymentMethod, $request) {
            $payment->update([
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'paid_at' => $paidAt,
                'failed_at' => $failedAt,
                'callback_payload' => $request->all(),
            ]);

            if ($paymentStatus === 'success') {
                $payment->booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'paid_at' => $paidAt,
                    'confirmed_at' => now(),
                ]);
            }

            if ($paymentStatus === 'failed') {
                $payment->booking->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'payment_method' => $paymentMethod,
                    'cancelled_at' => now(),
                ]);
            }

            if ($paymentStatus === 'pending') {
                $payment->booking->update([
                    'payment_status' => 'pending',
                    'payment_method' => $paymentMethod,
                ]);
            }
        });

        Log::info('Midtrans notification processed', [
            'order_id' => $validated['order_id'],
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_status' => $paymentStatus,
            'booking_id' => $payment->booking_id,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Notification processed',
            'payment_status' => $paymentStatus,
        ]);
    }

    private function mapMidtransPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'challenge' ? 'pending' : 'success';
        }

        return match ($transactionStatus) {
            'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Payment success redirect
     */
    public function paymentFinish()
    {
        return redirect('/dashboard')->with('message', 'Pembayaran berhasil diproses!');
    }

    /**
     * Payment error redirect
     */
    public function paymentError()
    {
        return redirect('/dashboard')->with('error', 'Pembayaran gagal!');
    }

    /**
     * Payment pending redirect
     */
    public function paymentPending()
    {
        return redirect('/dashboard')->with('message', 'Pembayaran menunggu konfirmasi!');
    }
}
