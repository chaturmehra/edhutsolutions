@extends('layouts.user')

@section('title', $pdf->title)

@section('content')
    <header class="learn-top">
        <div>
            <h1>{{ $pdf->title }}</h1>
            <p>{{ $owner->title }} <span>· view only</span></p>
        </div>
        @if ($owner instanceof \App\Models\Service)
            <a class="btn btn-browse" href="{{ route('learn.services.show', $owner) }}">Back to service</a>
        @else
            <a class="btn btn-browse" href="{{ route('learn.show', $owner) }}">Back to course</a>
        @endif
    </header>

    <section class="learn-card pdf-viewer" data-protect data-pdf-viewer
        data-src="{{ route('learn.pdf.file', $pdf) }}"
        data-worker="{{ asset('assets/vendor/pdfjs/pdf.worker.min.js') }}">
        <div class="pdf-tools">
            <span class="pdf-count" data-pdf-status>Loading…</span>
            <div class="pdf-zoom">
                <button class="btn btn-line" type="button" data-pdf-zoom="-1" aria-label="Zoom out">−</button>
                <span data-pdf-scale>100%</span>
                <button class="btn btn-line" type="button" data-pdf-zoom="1" aria-label="Zoom in">+</button>
            </div>
        </div>
        <div class="pdf-pages" data-pdf-pages data-watermark="{{ $site->get('site_name') }} · {{ $site->get('email') }}"></div>
        <p class="pdf-print-note">This document is available to view online only.</p>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/pdfjs/pdf.min.js') }}"></script>
    <script src="{{ asset('assets/js/media-protect.js') }}?v={{ @filemtime(public_path('assets/js/media-protect.js')) }}"></script>
@endpush
