<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','type','first_name','last_name','company','street','house_number','address2','postal_code','city','state','country_code','phone'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
