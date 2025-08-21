<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->unsubscribe_token)) {
                $model->unsubscribe_token = bin2hex(random_bytes(16));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
