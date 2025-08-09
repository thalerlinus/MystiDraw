<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id','user_item_id'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function userItem()
    {
        return $this->belongsTo(UserItem::class, 'user_item_id');
    }
}
