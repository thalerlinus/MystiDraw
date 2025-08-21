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

    // Neue Methode: nächste SKU generieren (z.B. P00001 -> P00002)
    public static function generateNextSku(): string
    {
        $lastSku = static::orderByDesc('id')->value('sku');
        if (!$lastSku) {
            return 'P00001';
        }
        if (preg_match('/^([A-Za-z]*)(\d+)$/', $lastSku, $m)) {
            $prefix = $m[1];
            $num = $m[2];
            $next = (int)$num + 1;
            return $prefix . str_pad((string)$next, strlen($num), '0', STR_PAD_LEFT);
        }
        // Fallback falls Format nicht passt
        return $lastSku . '-1';
    }
}
