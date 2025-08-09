<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','status','starts_at','ends_at','base_ticket_price','currency','public_stats'
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'base_ticket_price' => 'decimal:2',
            'public_stats' => 'boolean'
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'raffle_category');
    }

    public function pricingTiers()
    {
        return $this->hasMany(RafflePricingTier::class);
    }

    public function items()
    {
        return $this->hasMany(RaffleItem::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
