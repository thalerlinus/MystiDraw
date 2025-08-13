@extends('emails.layouts.base')

@section('content')
    <div style="margin-bottom: 32px; text-align:center;">
        <h1 style="color:#1e3a8a; font-size:26px; font-weight:700; margin:0 0 8px;">📦 Deine Sendung ist unterwegs</h1>
        <p style="color:#64748b; font-size:16px; margin:0;">Hallo {{ $user->name ?? 'MystiDraw Nutzer' }}, deine Gewinne verlassen gerade unser Lager.</p>
    </div>

    <div style="background:#f1f5f9; border:1px solid #e2e8f0; padding:20px 24px; border-radius:12px; margin-bottom:28px;">
        <h2 style="margin:0 0 14px; font-size:18px; color:#1e3a8a;">Sendungsdetails</h2>
        <p style="margin:4px 0; font-size:14px;">Carrier: <strong>{{ $shipment->carrier ?? '-' }}</strong></p>
        <p style="margin:4px 0; font-size:14px;">Tracking Nummer: <strong>{{ $shipment->tracking_number ?? '-' }}</strong></p>
        @if($shipment->tracking_url)
            <p style="margin:12px 0 0;">
                <a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener" style="display:inline-block; background:#1e3a8a; color:#fff; padding:10px 18px; text-decoration:none; font-weight:600; border-radius:8px;">Tracking öffnen</a>
            </p>
        @endif
        <p style="margin:18px 0 0; font-size:12px; color:#475569;">Status seit: {{ optional($shipment->shipped_at)->format('d.m.Y H:i') ?? 'soeben' }} Uhr</p>
    </div>

    <div style="margin-bottom:28px;">
        <h2 style="margin:0 0 14px; font-size:18px; color:#1e3a8a;">Enthaltene Items ({{ $shipment->items->count() }})</h2>
        <ul style="list-style:none; padding:0; margin:0;">
            @foreach($shipment->items as $sItem)
                @php($product = $sItem->userItem->ticketOutcome->raffleItem->product ?? null)
                <li style="padding:10px 12px; border:1px solid #e2e8f0; background:#fff; border-radius:8px; margin-bottom:8px; font-size:14px;">
                    {{ $product?->name ?? ('Produkt #'.$sItem->userItem->product_id) }}
                </li>
            @endforeach
        </ul>
    </div>


    <div style="text-align:center; margin-top:10px;">
        <a href="{{ config('app.url') }}/inventory" style="display:inline-block; padding:14px 26px; background:linear-gradient(135deg,#1e3a8a,#1e40af); color:#fff; text-decoration:none; font-weight:600; border-radius:10px; font-size:15px;">🔍 Inventar ansehen</a>
    </div>
@endsection
