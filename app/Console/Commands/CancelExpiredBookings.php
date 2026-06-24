<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan booking pending yang belum dibayar lebih dari 15 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredBookings = Booking::with('payment')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(15))
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
                'cancelled_at' => now(),
            ]);

            if ($booking->payment && $booking->payment->payment_status === 'pending') {
                $booking->payment->update([
                    'payment_status' => 'failed',
                    'failed_at' => now(),
                ]);
            }

            $count++;
        }

        if ($count > 0) {
            Log::info("Cancelled {$count} expired bookings.");
            $this->info("Cancelled {$count} expired bookings.");
        } else {
            $this->info('No expired bookings found.');
        }
    }
}
