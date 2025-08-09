<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','product_id','ticket_outcome_id','status','owned_at','shipped_at'
    ];

    protected function casts(): array
    {
        return [
            'owned_at' => 'datetime',
            'shipped_at' => 'datetime'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ticketOutcome()
    {
        return $this->belongsTo(TicketOutcome::class);
    }
}
