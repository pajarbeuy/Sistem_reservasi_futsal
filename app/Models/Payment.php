<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'booking_id',
    'amount',
    'payment_method',
    'payment_status',
    'transaction_id'
])]
class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * The migrations use Indonesian table name `transaksi`.
     */
    protected $table = 'transaksi';

    protected $fillable = [
        'booking_id',
        'user_id',
        'kode_transaksi',
        'jumlah',
        'metode',
        'bukti_pembayaran',
        'status',
        'tanggal_bayar',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Compatibility accessors for frontend naming
    public function getAmountAttribute()
    {
        return $this->attributes['jumlah'] ?? null;
    }

    public function getPaymentMethodAttribute()
    {
        return $this->attributes['metode'] ?? null;
    }

    public function getReferenceIdAttribute()
    {
        return $this->attributes['kode_transaksi'] ?? null;
    }

    public function getStatusAttribute()
    {
        // map Indonesian statuses to frontend-friendly values
        $status = $this->attributes['status'] ?? null;
        return match ($status) {
            'sukses' => 'completed',
            'pending' => 'pending',
            'gagal' => 'failed',
            'refund' => 'refund',
            default => $status,
        };
    }
}
