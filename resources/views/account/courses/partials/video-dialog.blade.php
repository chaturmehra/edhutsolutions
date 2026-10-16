<dialog class="video-dialog" data-video-dialog aria-labelledby="video-dialog-title">
    <div class="video-dialog-head">
        <h2 id="video-dialog-title" data-video-heading>Video</h2>
        <button class="video-dialog-close" type="button" data-video-close aria-label="Close video">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="video-stage">
        <div class="protected-video video-stage-file" data-protect data-video-file hidden>
            <video controls controlslist="nodownload noremoteplayback noplaybackrate" disablepictureinpicture disableremoteplayback playsinline preload="none"></video>
            <span class="media-mark" aria-hidden="true" data-video-mark>{{ $site->get('site_name') }} · {{ $site->get('email') }}</span>
        </div>
        <iframe class="video-stage-frame" data-video-frame title="{{ $frameTitle ?? 'Course video' }}" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" hidden></iframe>
    </div>
</dialog>

@push('scripts')
    <script src="{{ asset('assets/js/media-protect.js') }}?v={{ @filemtime(public_path('assets/js/media-protect.js')) }}"></script>
    <script src="{{ asset('assets/js/learn-video.js') }}?v={{ @filemtime(public_path('assets/js/learn-video.js')) }}" defer></script>
@endpush
