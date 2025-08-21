@extends('emails.layouts.base')

@section('content')
    <div style="margin-bottom:32px;">
        <h1 style="color:#1e3a8a;font-size:24px;font-weight:700;margin:0 0 12px;">MystiDraw Neuigkeiten</h1>
        <p style="font-size:15px;color:#334155;margin:0;">Hallo {{ $user->name }},</p>
    </div>

    <div style="font-size:15px;line-height:1.55;color:#334155;margin-bottom:32px;">
        {!! nl2br(e($body)) !!}
    </div>

    <div style="background:#f1f5f9;padding:18px;border-radius:10px;margin-bottom:32px;font-size:13px;color:#475569;">
        Du erhältst diese E-Mail, weil du unseren Newsletter zu neuen Raffles & Gewinnspielen abonniert hast.
    </div>

    <div style="text-align:center;margin-top:24px;font-size:12px;color:#64748b;">
        <a href="{{ $unsubscribe_url }}" style="color:#1e3a8a;">Newsletter abbestellen</a>
    </div>
@endsection
