<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id','product_id','tier','quantity_total','quantity_awarded','weight','is_last_one'
    ];

    protected function casts(): array
    {
        return [
            'quantity_total' => 'integer',
            'quantity_awarded' => 'integer',
            'weight' => 'integer',
            'is_last_one' => 'boolean'
        ];
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outcomes()
    {
        return $this->hasMany(TicketOutcome::class, 'raffle_item_id');
    }
}
