@php
    $voiceChip ??= null;
    $voiceTone ??= null;
    $voiceTitle ??= 'Voices from our alumni.';
    $voiceLede ??= 'Reviews from professionals who trained with EdHut for PMP, PgMP, and related PMI preparation.';
    $voiceMeta = function ($testimonial) use ($voiceChip) {
        $haystack = strtolower($testimonial->quote.' '.$testimonial->headline);
        $chip = $voiceChip ?? (str_contains($haystack, 'pgmp') ? 'PgMP' : (str_contains($haystack, 'pfmp') ? 'PfMP' : (str_contains($haystack, 'rmp') ? 'PMI-RMP' : (str_contains($haystack, 'pmp') ? 'PMP' : 'PMI'))));
        $parts = collect(preg_split('/\s+/', trim($testimonial->name)))->filter()->values();
        $initials = strtoupper(mb_substr((string) $parts->first(), 0, 1).mb_substr((string) $parts->last(), 0, 1));

        return [$chip, $initials];
    };
    $featured = $testimonials->first();
    [$featuredChip, $featuredInitials] = $featured ? $voiceMeta($featured) : ['PMI', 'EH'];
@endphp
<section @class(['home-voices', $voiceTone]) data-voice-slider>
    <div class="wrap">
        <div class="voice-head">
            <div>
                <p class="kicker">Success in their words</p>
                <h2>{{ $voiceTitle }}</h2>
                <p>{{ $voiceLede }}</p>
            </div>
            <div class="voice-stats">
                <article><strong>{{ $site->get('stat_students') }}</strong><span>Professionals trained</span></article>
                <article><strong>{{ $site->get('rating') }}</strong><span><a href="{{ config('edhut.google_reviews_url') }}" target="_blank" rel="noopener noreferrer">From {{ $site->get('review_count') }} reviews</a></span></article>
            </div>
        </div>
        @if ($featured)
            <article class="voice-feature">
                <div class="voice-person">
                    <span class="voice-chip" data-voice-chip>{{ $featuredChip }}</span>
                    <span class="voice-mark" data-voice-initials aria-hidden="true">{{ $featuredInitials }}</span>
                    <strong data-voice-name>{{ $featured->name }}</strong>
                </div>
                <div class="voice-copy">
                    <p class="stars" aria-label="Rated {{ $featured->rating }} out of 5">★★★★★ <span>Verified review</span></p>
                    <blockquote data-voice-quote>{{ $featured->quote }}</blockquote>
                    <span class="voice-tag" data-voice-tag>{{ $featured->headline }}</span>
                    <div class="voice-nav" @if ($testimonials->count() < 2) hidden @endif>
                        <div class="voice-dots" data-voice-dots></div>
                        <div class="hero-actions">
                            <button class="voice-arrow" type="button" data-voice-prev aria-label="Previous review">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button class="voice-arrow" type="button" data-voice-next aria-label="Next review">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m10 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
            <div class="voice-minis" data-voice-minis @if ($testimonials->count() < 2) hidden @endif>
                @foreach ($testimonials->slice(1, 3) as $testimonial)
                    @php [$chip, $initials] = $voiceMeta($testimonial); @endphp
                    <article class="home-card voice-mini">
                        <span class="voice-mini-top"><span class="voice-mini-mark" aria-hidden="true">{{ $initials }}</span><span class="voice-chip">{{ $chip }}</span></span>
                        <strong>{{ $testimonial->name }}</strong>
                        <p>{{ \Illuminate\Support\Str::limit($testimonial->quote, 140) }}</p>
                    </article>
                @endforeach
            </div>
            <div hidden>
                @foreach ($testimonials as $testimonial)
                    @php [$chip, $initials] = $voiceMeta($testimonial); @endphp
                    <article data-voice data-name="{{ $testimonial->name }}" data-initials="{{ $initials }}" data-chip="{{ $chip }}" data-headline="{{ $testimonial->headline }}">{{ $testimonial->quote }}</article>
                @endforeach
            </div>
        @endif
    </div>
</section>
