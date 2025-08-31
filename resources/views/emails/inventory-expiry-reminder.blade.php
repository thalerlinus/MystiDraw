@extends('emails.layouts.base')

@section('content')
  <div style="font-size:16px;color:#0f172a;">
    <h1 style="font-size:22px;margin:0 0 12px;">Erinnerung: Inventar-Items verfallen bald</h1>
    <p style="margin:0 0 12px;">Hallo {{ $user->name ?? 'MystiDraw Nutzer' }},</p>
    <p style="margin:0 0 12px;">
      einige deiner gewonnenen Items sind bereits seit fast 2 Monaten in deinem Inventar. 
      Gemäß unseren AGB verfallen nicht angeforderte Items nach 2 Monaten und können nicht mehr versendet werden.
    </p>
    <p style="margin:0 0 12px;">
      Sichere dir jetzt deine Gewinne und fordere den Versand an.
    </p>

    <div style="margin:20px 0;">
      <a href="{{ config('app.url') }}/inventory" style="display:inline-block;padding:12px 18px;background:linear-gradient(135deg,#1e3a8a,#1e40af);color:#fff;text-decoration:none;border-radius:10px;font-weight:700;">🧾 Inventar ansehen</a>
    </div>

    <p style="margin:0 0 8px;color:#334155;">
      Hinweis: Diese Erinnerung wurde automatisch generiert. Details findest du in unseren <a href="{{ route('agb') }}" style="color:#1e40af;">AGB</a>.
    </p>
  </div>
@endsection
