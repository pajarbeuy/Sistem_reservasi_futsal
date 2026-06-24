<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'field_id',
        'time_period',
        'start_time',
        'end_time',
        'price_per_hour',
        'description',
        'is_active'
    ];
    use HasFactory;

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
