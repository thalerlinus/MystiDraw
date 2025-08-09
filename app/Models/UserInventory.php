<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInventory extends Model
{
    use HasFactory;

    protected $table = 'user_inventory';

    protected $fillable = [
        'user_id','product_id','quantity','last_ticket_id'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer'
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

    public function lastTicket()
    {
        return $this->belongsTo(Ticket::class, 'last_ticket_id');
    }
}
