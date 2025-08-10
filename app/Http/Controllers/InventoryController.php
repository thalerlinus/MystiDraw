<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketOutcome;
use Inertia\Inertia;

class InventoryController extends Controller
{
    private function getImageUrl($imagePath)
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

    public function index()
    {
        // Get all won prizes for the authenticated user
        $wonPrizes = TicketOutcome::whereHas('ticket', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('tier', '!=', 'none')
            ->with([
                'ticket',
                'raffleItem.product.images',
                'raffleItem.raffle'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($outcome) {
                $product = $outcome->raffleItem->product;
                $image = $product->images->first();
                
                return [
                    'id' => $outcome->id,
                    'ticket_serial' => $outcome->ticket->serial,
                    'raffle_name' => $outcome->raffleItem->raffle->name,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'image_url' => $image ? $this->getImageUrl($image->path) : null,
                        'value' => $product->price,
                    ],
                    'tier' => $outcome->tier,
                    'won_at' => $outcome->created_at,
                    'status' => $outcome->status,
                ];
            });

        // Group by status for better organization
        $inventoryData = [
            'assigned' => $wonPrizes->where('status', 'assigned'),
            'shipped' => $wonPrizes->where('status', 'shipped'),
            'delivered' => $wonPrizes->where('status', 'delivered'),
        ];

        // Statistics
        $stats = [
            'total_prizes' => $wonPrizes->count(),
            'total_value' => $wonPrizes->sum(fn($prize) => $prize['product']['value']),
            'pending_shipment' => $wonPrizes->where('status', 'assigned')->count(),
            'shipped_items' => $wonPrizes->where('status', 'shipped')->count(),
        ];

        return Inertia::render('Inventory/Index', [
            'inventory' => $inventoryData,
            'stats' => $stats,
        ]);
    }
}
