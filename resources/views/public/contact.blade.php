@extends('layouts.public')

@section('toast_errors', '1')

@push('head')
    <link href="{{ asset('assets/css/contact.css') }}?v={{ @filemtime(public_path('assets/css/contact.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="contact-hero">
        <div class="wrap">
            <p class="kicker">Contact</p>
            <h1>Speak with our team.</h1>
            <p class="lede">Whether you are exploring a certification, a cohort date, or a corporate program, write to EdHut and the team will reply.</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap contact-grid">
            <div>
                <p class="kicker">Reach us</p>
                <h2>Let's start the conversation.</h2>
                <div class="contact-reach">
                    <a href="mailto:{{ $site->get('email') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <span>{{ $site->get('email') }}</span>
                    </a>
                    <div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 4h3l1.2 3.2-1.8 1.1a12 12 0 0 0 5.3 5.3l1.1-1.8L20 13v3a2 2 0 0 1-2.2 2A16 16 0 0 1 4 6.2 2 2 0 0 1 6 4h2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        <span><span class="reach-kicker">Phone</span><a href="tel:+917042121784">{{ $site->get('phone_display') }}</a></span>
                    </div>
                    <a href="https://wa.me/{{ $site->get('whatsapp') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21a9 9 0 1 0-7.7-4.4L3 21l4.5-1.2A9 9 0 0 0 12 21Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        <span><span class="reach-kicker">WhatsApp (preferred mode)</span>{{ $site->get('phone_display') }}</span>
                    </a>
                    <p>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.6"/></svg>
                        <span>{{ $site->get('address') }}</span>
                    </p>
                    <p>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 5 6v6c0 4.2 2.8 7.4 7 9 4.2-1.6 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        <span>PMI Authorized Training Partner</span>
                    </p>
                </div>
            </div>

            <form class="message-card" method="post" action="{{ route('contact.store') }}">
                @csrf
                <input type="hidden" name="subject" value="Website enquiry">
                <h2>Send us a message</h2>
                <p class="lede">Share your question and EdHut will reply by email or phone.</p>
                <div class="form-split">
                    <label class="field"><span>Full name</span><input class="input" name="name" required value="{{ old('name') }}" @error('name') aria-invalid="true" @enderror></label>
                    <label class="field"><span>Email</span><input class="input" type="email" name="email" required value="{{ old('email') }}" @error('email') aria-invalid="true" @enderror></label>
                </div>
                <label class="field"><span>Phone</span><input class="input" type="tel" name="phone" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" title="Digits only, 7 to 15 digits" autocomplete="tel" data-digits value="{{ old('phone') }}" @error('phone') aria-invalid="true" @enderror></label>
                <label class="field"><span>Message</span><textarea class="textarea" name="message" required @error('message') aria-invalid="true" @enderror>{{ old('message', is_string(request('message')) ? request('message') : '') }}</textarea></label>
                <div class="send-row">
                    <p>{{ $site->get('iso_line') }}</p>
                    <button class="btn btn-primary" type="submit">Send message <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </div>
            </form>
        </div>
    </section>
@endsection
