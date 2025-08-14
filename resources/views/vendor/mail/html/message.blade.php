@extends('emails.layouts.base')

@section('content')
    {!! $slot !!}
    
    {{-- Subcopy --}}
    @isset($subcopy)
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
        <div style="font-size: 14px; color: #64748b;">
            {!! $subcopy !!}
        </div>
    </div>
    @endisset
@endsection
