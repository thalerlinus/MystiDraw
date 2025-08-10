<?php

namespace App\Services;

class CdnService
{
    public static function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }
        
        // Wenn bereits absolute URL, direkt zurückgeben
        if (preg_match('/^https?:\/\//i', $imagePath)) {
            return $imagePath;
        }
        
        // BunnyCDN Pull Zone aus Config holen
        $pullZone = config('filesystems.disks.bunnycdn.pull_zone');
        
        if ($pullZone) {
            // Pull Zone normalisieren (entferne Protokoll und trailing slash)
            $normalizedPullZone = preg_replace('/^https?:\/\//i', '', $pullZone);
            $normalizedPullZone = rtrim($normalizedPullZone, '/');
            
            // Pfad normalisieren (entferne leading slash)
            $sanitizedPath = ltrim($imagePath, '/');
            
            return "https://{$normalizedPullZone}/{$sanitizedPath}";
        }
        
        // Fallback zu lokalem Storage
        return "/storage/{$imagePath}";
    }

    public static function deriveThumbPath($path)
    {
        if (!$path) {
            return $path;
        }

        $lastDot = strrpos($path, '.');
        if ($lastDot === false) {
            return $path . '_thumb';
        }

        return substr($path, 0, $lastDot) . '_thumb' . substr($path, $lastDot);
    }

    public static function getThumbUrl($basePath)
    {
        return self::getImageUrl(self::deriveThumbPath($basePath));
    }

    public static function getImagePair($basePath)
    {
        $mainUrl = self::getImageUrl($basePath);
        $thumbPath = self::deriveThumbPath($basePath);
        $thumbUrl = self::getImageUrl($thumbPath);
        
        return [
            'url' => $mainUrl,
            'thumbUrl' => $thumbUrl,
            'thumbPath' => $thumbPath
        ];
    }

    /**
     * Get best available image URL for a product
     */
    public static function getProductImageUrl($product)
    {
        if (!$product) {
            return null;
        }

        // Zuerst versuche thumbnail_path vom Product
        if (!empty($product->thumbnail_path)) {
            return self::getImageUrl($product->thumbnail_path);
        }

        // Als Fallback erste ProductImage verwenden
        if ($product->images && $product->images->isNotEmpty()) {
            $firstImage = $product->images->first();
            return self::getImageUrl($firstImage->path);
        }

        return null;
    }
}
