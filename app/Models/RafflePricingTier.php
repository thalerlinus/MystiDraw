<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RafflePricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id','min_qty','unit_price'
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2'
        ];
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
