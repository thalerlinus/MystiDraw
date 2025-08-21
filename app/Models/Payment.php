<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','user_id','provider','provider_txn_id','invoice_number','amount','currency','status','paid_at','email_sent_at','raw_response',
        'credit_note_number','refund_email_sent_at'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'email_sent_at' => 'datetime',
            'refund_email_sent_at' => 'datetime',
            'raw_response' => 'array'
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
