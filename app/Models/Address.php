<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','label','first_name','last_name','company','street','house_number','address2','postal_code','city','state','country','country_code','phone','is_default'
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
