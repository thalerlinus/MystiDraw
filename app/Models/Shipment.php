<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','order_id','order_address_id','status','carrier','service','tracking_number','tracking_url','weight_g','cost','currency','label_path','shipped_at','delivered_at'
    ];

    protected function casts(): array
    {
        return [
            'weight_g' => 'integer',
            'cost' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function address()
    {
        return $this->belongsTo(OrderAddress::class, 'order_address_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
