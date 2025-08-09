<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id','user_id','order_id','serial','price_paid','status','drawn_at'
    ];

    protected function casts(): array
    {
        return [
            'price_paid' => 'decimal:2',
            'drawn_at' => 'datetime'
        ];
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function outcome()
    {
        return $this->hasOne(TicketOutcome::class);
    }
}
