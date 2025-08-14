@extends('emails.layouts.base')

@section('content')
    {{-- Greeting --}}
    <div style="margin-bottom: 32px;">
        @if (! empty($greeting))
        <h1 style="color: #1e3a8a; font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">
            {{ $greeting }}
        </h1>
        @else
        @if ($level === 'error')
        <h1 style="color: #dc2626; font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">
            ⚠️ Ups!
        </h1>
        @else
        <h1 style="color: #1e3a8a; font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">
            👋 Hallo!
        </h1>
        @endif
        @endif
    </div>

    {{-- Intro Lines --}}
    @if (!empty($introLines))
    <div style="margin-bottom: 32px;">
        @foreach ($introLines as $line)
        <p style="font-size: 16px; color: #64748b; margin-bottom: 16px; line-height: 1.6;">
            {{ $line }}
        </p>
        @endforeach
    </div>
    @endif

    {{-- Action Button --}}
    @isset($actionText)
    <div style="text-align: center; margin: 32px 0;">
        @php
            $buttonColor = match ($level) {
                'error' => 'background-color: #dc2626; border-color: #dc2626;',
                'success' => 'background-color: #059669; border-color: #059669;',
                default => 'background-color: #1e3a8a; border-color: #1e3a8a;',
            };
        @endphp
        <a href="{{ $actionUrl }}" 
           style="display: inline-block; padding: 14px 28px; {{ $buttonColor }} color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; border: 2px solid; transition: all 0.2s;">
            {{ $actionText }}
        </a>
    </div>
    @endisset

    {{-- Outro Lines --}}
    @if (!empty($outroLines))
    <div style="margin-bottom: 32px;">
        @foreach ($outroLines as $line)
        <p style="font-size: 16px; color: #64748b; margin-bottom: 16px; line-height: 1.6;">
            {{ $line }}
        </p>
        @endforeach
    </div>
    @endif

    {{-- Salutation --}}
    @if (! empty($salutation))
    <div style="margin-top: 32px;">
        <p style="font-size: 16px; color: #64748b;">
            {{ $salutation }}
        </p>
    </div>
    @else
    <div style="margin-top: 32px;">
        <p style="font-size: 16px; color: #64748b;">
            Beste Grüße,<br>
            Das MystiDraw Team
        </p>
    </div>
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
        <div style="font-size: 14px; color: #64748b;">
            <p style="margin-bottom: 8px;">
                Falls Sie Probleme beim Klicken des "{{ $actionText }}" Buttons haben, 
                kopieren Sie die untenstehende URL und fügen Sie diese in Ihren Webbrowser ein:
            </p>
            <p style="word-break: break-all; color: #94a3b8;">
                <a href="{{ $actionUrl }}" style="color: #1e3a8a;">{{ $displayableActionUrl }}</a>
            </p>
        </div>
    </div>
    @endisset
@endsection
