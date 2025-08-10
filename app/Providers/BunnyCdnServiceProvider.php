<?php
// app/Providers/BunnyCdnServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

// Die korrekten League-Flysystem-Klassen:
use League\Flysystem\Filesystem as LeagueFilesystem;


// Der Laravel-Adapter wrapper
use Illuminate\Filesystem\FilesystemAdapter;

// Und natürlich der BunnyCDN-Adapter
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNAdapter;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNClient;
use PlatformCommunity\Flysystem\BunnyCDN\BunnyCDNRegion;

class BunnyCdnServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        
        Storage::extend('bunnycdn', function ($app, array $config) {
            // 1) Erstelle den Bunny-Client
            $client = new BunnyCDNClient(
                $config['storage_zone'],
                $config['api_key'],
                $config['region'] ?? BunnyCDNRegion::FALKENSTEIN
            );

            // 2) Adapter mit (optional) Pull-Zone
            $adapter = new BunnyCDNAdapter(
                $client,
                $config['pull_zone'] ?? null
            );

            // 3) League-Filesystem initalisieren — hier kommt ein ARRAY in LeagueConfig
            $leagueFs = new LeagueFilesystem(
                $adapter,
                $config                     // ✅ richtig: plain array
            );

            // 4) Laravel-Wrapper drumherum
            return new FilesystemAdapter(
                $leagueFs,
                $adapter,
                $config
            );
        });
    }
}
