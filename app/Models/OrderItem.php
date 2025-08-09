<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','raffle_id','quantity','unit_price','subtotal'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2'
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
