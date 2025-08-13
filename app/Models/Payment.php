<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','provider','provider_txn_id','invoice_number','amount','currency','status','paid_at','email_sent_at','raw_response'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'email_sent_at' => 'datetime',
            'raw_response' => 'array'
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
