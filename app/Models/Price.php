<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'field_id',
    'time_period',
    'start_time',
    'end_time',
    'price_per_hour',
    'description',
    'is_active'
])]
class Price extends Model
{
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
