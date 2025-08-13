<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::query();
        if ($status = $request->get('status')) {
            $query->where('status',$status);
        }
        $shipments = $query->latest()->paginate(40)->withQueryString();
        return Inertia::render('Admin/Shipments/Index', [ 'shipments' => $shipments, 'filters' => ['status'=>$status] ]);
    }

    public function show(Shipment $shipment)
    {
    $shipment->load(['order','items.userItem.product','address']);
        return Inertia::render('Admin/Shipments/Show', [ 'shipment' => $shipment ]);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,queued,label_printed,shipped,delivered,returned',
            'carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:150',
            'tracking_url' => 'nullable|url|max:255',
        ]);

        \Log::info('Admin Shipment Update Request', ['shipment_id' => $shipment->id, 'data' => $data]);

        $shouldSendShippedMail = false;
        \DB::transaction(function () use ($shipment, $data, &$shouldSendShippedMail) {
            $shipment->loadMissing(['items.userItem.ticketOutcome']);
            $wasShipped = (bool)$shipment->shipped_at;
            $shipment->update($data + [
                'shipped_at' => ($data['status'] === 'shipped' && !$shipment->shipped_at) ? now() : $shipment->shipped_at,
                'delivered_at' => ($data['status'] === 'delivered' && !$shipment->delivered_at) ? now() : $shipment->delivered_at,
            ]);
            if ($data['status'] === 'shipped' && !$wasShipped && $shipment->shipped_at) {
                $shouldSendShippedMail = true;
            }

            if (in_array($data['status'], ['shipped','delivered'])) {
                // Markiere UserItems & Outcomes als shipped
                foreach ($shipment->items as $sItem) {
                    $ui = $sItem->userItem;
                    if ($ui && $ui->status !== 'shipped') {
                        $ui->update([
                            'status' => 'shipped',
                            'shipped_at' => $ui->shipped_at ?? now(),
                        ]);
                        if ($ui->ticketOutcome && $ui->ticketOutcome->status !== 'fulfilled') {
                            $ui->ticketOutcome->update([
                                'status' => 'fulfilled',
                                'fulfilled_at' => $ui->ticketOutcome->fulfilled_at ?? now(),
                            ]);
                        }
                    }
                }
            }
        });

        if ($shouldSendShippedMail) {
            dispatch(new \App\Jobs\SendShipmentShippedEmail($shipment->fresh(['items.userItem.ticketOutcome.raffleItem.product','address']), $shipment->user));
        }

    return redirect()->back()->with('message', 'Shipment aktualisiert.');
    }
}
