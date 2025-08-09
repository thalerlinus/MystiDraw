<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id','raffle_item_id','product_id','tier','decided_by','rng_seed','rng_roll','status','assigned_at','fulfilled_at'
    ];

    protected function casts(): array
    {
        return [
            'rng_roll' => 'decimal:6',
            'assigned_at' => 'datetime',
            'fulfilled_at' => 'datetime'
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function raffleItem()
    {
        return $this->belongsTo(RaffleItem::class, 'raffle_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
