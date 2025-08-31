<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRecovery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','product_id','raffle_item_id','quantity','purged_at'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'purged_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function raffleItem()
    {
        return $this->belongsTo(RaffleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
