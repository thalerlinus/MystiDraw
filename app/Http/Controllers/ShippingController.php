<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\TicketOutcome;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\OrderAddress;
use App\Models\UserItem;
use App\Services\CdnService;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'selected_ticket_ids' => 'required|array|min:1',
            'selected_ticket_ids.*' => 'required|integer'
        ]);

        $user = Auth::user();
        $ticketIds = $request->selected_ticket_ids;

        // Get the selected items with their product information
        $selectedItems = TicketOutcome::whereIn('ticket_id', $ticketIds)
            ->whereHas('ticket', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('tier', '!=', 'none')
            ->where('status', 'assigned') // Only items that can be shipped
            ->with([
                'ticket',
                'raffleItem.product.images',
                'raffleItem.raffle'
            ])
            ->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keine gültigen Items für den Versand gefunden.');
        }

        // Get user's addresses
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();

        // Transform selected items similar to inventory controller
        $transformedItems = $selectedItems->map(function ($outcome) {
            $product = $outcome->raffleItem->product;
            
            return [
                'id' => $outcome->id,
                'ticket_serial' => $outcome->ticket->serial,
                'ticket_id' => $outcome->ticket->id,
                'raffle_name' => $outcome->raffleItem->raffle->name,
                'raffle_id' => $outcome->raffleItem->raffle->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image_url' => CdnService::getProductImageUrl($product),
                    'value' => $product->price,
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'path' => $image->path,
                            'alt_text' => $image->alt_text,
                        ];
                    }),
                ],
                'tier' => $outcome->tier,
                'is_last_one' => $outcome->is_last_one,
                'won_at' => $outcome->created_at,
                'status' => $outcome->status,
            ];
        });

        // Group items similar to inventory controller
        $createPrizeGroups = function($items) {
            $grouped = [];
            
            foreach ($items as $item) {
                $productKey = $item['product']['name'] . '|' . $item['tier'] . '|' . ($item['is_last_one'] ? '1' : '0');
                
                if (!isset($grouped[$productKey])) {
                    $grouped[$productKey] = [
                        'product' => $item['product'],
                        'tier' => $item['tier'],
                        'count' => 1,
                        'is_last_one' => $item['is_last_one'] || false,
                        'status' => $item['status'],
                        'tickets' => [[
                            'serial' => $item['ticket_serial'],
                            'ticket_id' => $item['ticket_id'],
                            'is_last_one' => $item['is_last_one']
                        ]]
                    ];
                } else {
                    $grouped[$productKey]['count']++;
                    $grouped[$productKey]['tickets'][] = [
                        'serial' => $item['ticket_serial'],
                        'ticket_id' => $item['ticket_id'],
                        'is_last_one' => $item['is_last_one']
                    ];
                }
            }
            
            return array_values($grouped);
        };

        $groupedSelectedItems = $createPrizeGroups($transformedItems);

        return Inertia::render('Shipping/Create', [
            'selectedItems' => $groupedSelectedItems,
            'addresses' => $addresses,
            'shippingCost' => 7.00,
            'bunny' => [
                'pull_zone' => config('filesystems.disks.bunnycdn.pull_zone'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'selected_ticket_ids' => 'required|array|min:1',
            'selected_ticket_ids.*' => 'required|integer',
            'address_id' => 'sometimes|integer|exists:addresses,id',
            // New address fields
            'first_name' => 'required_without:address_id|string|max:255',
            'last_name' => 'required_without:address_id|string|max:255',
            'company' => 'sometimes|nullable|string|max:255',
            'street' => 'required_without:address_id|string|max:255',
            'house_number' => 'sometimes|nullable|string|max:20',
            'address2' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'required_without:address_id|string|max:20',
            'city' => 'required_without:address_id|string|max:255',
            'country' => 'required_without:address_id|string|max:255',
            'country_code' => 'sometimes|string|size:2',
            'phone' => 'sometimes|nullable|string|max:50',
            'save_address' => 'sometimes|boolean'
        ]);

        $user = Auth::user();
        $ticketIds = $request->selected_ticket_ids;

        return DB::transaction(function () use ($request, $user, $ticketIds) {
            // Get the selected items
            $selectedOutcomes = TicketOutcome::whereIn('ticket_id', $ticketIds)
                ->whereHas('ticket', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('tier', '!=', 'none')
                ->where('status', 'assigned')
                ->get();

            if ($selectedOutcomes->isEmpty()) {
                throw new \Exception('Keine gültigen Items für den Versand gefunden.');
            }

            // Handle address
            if ($request->has('address_id') && $request->address_id) {
                // Use existing address
                $address = $user->addresses()->findOrFail($request->address_id);
                $addressData = $address->toArray();
                unset($addressData['id'], $addressData['user_id'], $addressData['label'], $addressData['is_default'], $addressData['created_at'], $addressData['updated_at']);
            } else {
                // Use new address data
                $addressData = $request->only([
                    'first_name', 'last_name', 'company', 'street', 'house_number',
                    'address2', 'postal_code', 'city', 'country', 'country_code', 'phone'
                ]);
                // Normalize country_code if user entered full country name
                if (!isset($addressData['country_code']) || strlen($addressData['country_code']) !== 2) {
                    $map = [
                        'Deutschland' => 'DE', 'Österreich' => 'AT', 'Austria' => 'AT', 'Schweiz' => 'CH', 'Switzerland' => 'CH',
                        'Germany' => 'DE'
                    ];
                    $countryName = $addressData['country'] ?? ($addressData['country_code'] ?? null);
                    if ($countryName && isset($map[$countryName])) {
                        $addressData['country_code'] = $map[$countryName];
                    } elseif ($countryName && strlen($countryName) === 2) {
                        $addressData['country_code'] = strtoupper($countryName);
                    } else {
                        $addressData['country_code'] = 'DE';
                    }
                } else {
                    $addressData['country_code'] = strtoupper($addressData['country_code']);
                }

                // Save address to user's address book if requested
                if ($request->save_address) {
                    $user->addresses()->create(array_merge($addressData, [
                        'label' => 'Versandadresse vom ' . now()->format('d.m.Y'),
                        'is_default' => false
                    ]));
                }
            }

            // Create order address
            $orderAddress = OrderAddress::create(array_merge($addressData, [
                'order_id' => null, // We don't have an order for this
                'type' => 'shipping'
            ]));

            // Create shipment
            $shipment = Shipment::create([
                'user_id' => $user->id,
                'order_id' => null,
                'order_address_id' => $orderAddress->id,
                'status' => 'draft',
                'cost' => 7.00,
                'currency' => 'EUR',
            ]);

            // Create user items and shipment items
            foreach ($selectedOutcomes as $outcome) {
                // Create or update user_items entry
                $userItem = UserItem::create([
                    'user_id' => $user->id,
                    'product_id' => $outcome->raffleItem->product_id,
                    'ticket_outcome_id' => $outcome->id,
                    'status' => 'reserved_for_shipping',
                    'owned_at' => $outcome->created_at,
                ]);

                // Add to shipment
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'user_item_id' => $userItem->id,
                ]);

                // Update outcome status
                $outcome->update(['status' => 'reserved_for_shipping']);
            }

            return redirect()->route('inventory.index')->with('success', 
                'Versand wurde erfolgreich erstellt! Deine Items werden bald versendet.'
            );
        });
    }

    public function show(Shipment $shipment)
    {
        // Ensure user can only view their own shipments
        if ($shipment->user_id !== Auth::id()) {
            abort(403);
        }

        $shipment->load(['address', 'items.userItem.product']);

        return Inertia::render('Shipping/Show', [
            'shipment' => $shipment
        ]);
    }
}
