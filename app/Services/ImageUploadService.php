<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

/**
 * Zentraler Service für Bild-Resize & Upload zu BunnyCDN.
 */
class ImageUploadService
{
    /** @var ImageManager */
    protected $manager;

    public function __construct()
    {
        $driver = class_exists(ImagickDriver::class) && extension_loaded('imagick')
            ? new ImagickDriver()
            : new \Intervention\Image\Drivers\Gd\Driver();
        $this->manager = new ImageManager($driver);
    }

    /**
     * Skaliert ein Bild proportional auf maximal width/height und lädt es als JPEG hoch.
     */
    public function createAndUploadImage(string $localPath, string $bunnyPath, int $width, int $height, int $quality = 85): bool
    {
        $img = $this->readAndResize($localPath, $width, $height);
        $payload = (string) $img->toJpeg($quality);
        return Storage::disk('bunnycdn')->put($bunnyPath, $payload, 'public');
    }

    /**
     * Erstellt Hauptbild + Thumb und gibt deren Pfade zurück.
     * @return array{main:string,thumb:string}
     */
    public function createAndUploadImageWithThumb(
        string $localPath,
        string $baseBunnyPath,
        array $mainSize = [1600,1600],
        array $thumbSize = [400,400],
        int $qualityMain = 85,
        int $qualityThumb = 75
    ): array {
        $this->createAndUploadImage($localPath, $baseBunnyPath, $mainSize[0], $mainSize[1], $qualityMain);
        $thumbPath = $this->deriveThumbPath($baseBunnyPath);
        $this->createAndUploadImage($localPath, $thumbPath, $thumbSize[0], $thumbSize[1], $qualityThumb);
        return ['main' => $baseBunnyPath, 'thumb' => $thumbPath];
    }

    protected function readAndResize(string $localPath, int $width, int $height)
    {
    $img = $this->manager->read($localPath); // remove orientate() for compatibility
        return $img->scaleDown(width: $width, height: $height);
    }

    public function deriveThumbPath(string $path): string
    {
        $dot = strrpos($path, '.');
        if ($dot === false) { return $path . '_thumb'; }
        return substr($path, 0, $dot) . '_thumb' . substr($path, $dot);
    }

    /**
     * Löscht Hauptbild + Thumb (falls vorhanden) von BunnyCDN. Fehler werden stumm ignoriert.
     */
    public function deleteImageWithThumb(?string $path): void
    {
        if (!$path) { return; }
        try {
            if (Storage::disk('bunnycdn')->exists($path)) {
                Storage::disk('bunnycdn')->delete($path);
            }
        } catch (\Throwable) {}
        $thumb = $this->deriveThumbPath($path);
        try {
            if (Storage::disk('bunnycdn')->exists($thumb)) {
                Storage::disk('bunnycdn')->delete($thumb);
            }
        } catch (\Throwable) {}
    }

    /**
     * Löscht mehrere Pfade effizient.
     */
    public function deleteMany(array $paths): void
    {
        foreach ($paths as $p) { $this->deleteImageWithThumb($p); }
    }
}
