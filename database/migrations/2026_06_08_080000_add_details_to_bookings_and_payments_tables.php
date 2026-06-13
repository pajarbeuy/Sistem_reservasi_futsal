<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'booking_code')) {
                $table->string('booking_code')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('bookings', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('total_price');
            }

            if (! Schema::hasColumn('bookings', 'notes')) {
                $table->text('notes')->nullable()->after('phone_number');
            }

            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('status');
            }

            if (! Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('bookings', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('bookings', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('transaction_id');
            }

            if (! Schema::hasColumn('payments', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('payments', 'callback_payload')) {
                $table->json('callback_payload')->nullable()->after('failed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['callback_payload', 'failed_at', 'paid_at'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            foreach ([
                'cancelled_at',
                'confirmed_at',
                'paid_at',
                'payment_method',
                'payment_status',
                'notes',
                'phone_number',
                'booking_code',
            ] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
