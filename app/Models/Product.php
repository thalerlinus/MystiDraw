<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku','name','description','base_cost','active','default_tier'
    ,'thumbnail_path'
    ];

    protected function casts(): array
    {
        return [
            'base_cost' => 'decimal:2',
            'active' => 'boolean'
        ];
    }

    public function images()
    {
    return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function raffleItems()
    {
        return $this->hasMany(RaffleItem::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            // Bilder explizit löschen, damit deren Model-Events (Datei-Removal) feuern
            $images = $product->images()->get();
            foreach ($images as $img) { $img->delete(); }
        });
    }
}
