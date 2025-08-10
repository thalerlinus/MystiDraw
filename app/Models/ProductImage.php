<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','path','alt','sort_order','is_primary'
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean'
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (ProductImage $image) {
            // Beim Löschen auch Datei + Thumb von Bunny entfernen
            try { app(\App\Services\ImageUploadService::class)->deleteImageWithThumb($image->path); } catch (\Throwable) {}
        });
    }
}
