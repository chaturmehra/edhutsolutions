@php $currentCourse ??= null; @endphp
<section class="section home-roadmap has-world-map {{ $roadmapTone ?? 'band-tint' }}" id="{{ $roadmapId ?? 'courses' }}">
    <div class="wrap">
        <div class="roadmap-head">
            <div>
                <p class="kicker">Certification roadmap</p>
                <h2>Choose your next credential.</h2>
                <p class="lede">PMI-approved instruction for PMP, PgMP, PfMP, and PMI-RMP, online and in person. Pick the credential that matches where you are in your career.</p>
            </div>
            <aside class="roadmap-note">
                <span class="roadmap-note-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21V11M12 11l-5-5M12 11l5-5M4 6h4V2M20 6h-4V2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="17" r="1.6" fill="currentColor"/></svg>
                </span>
                <div>
                    <strong>Your career. Your choice.</strong>
                    <p>Each PMI credential fits a different stage of experience. Pick the one that matches your role, or talk to an advisor before you apply.</p>
                    <a class="home-more" href="{{ route('courses.index') }}">All courses</a>
                </div>
            </aside>
        </div>
        <div class="roadmap-steps" data-shot-track data-shot-label="Show step">
            @foreach ($roadmapCourses as $step)
                @php $isCurrent = $currentCourse && $currentCourse->is($step); @endphp
                <a @class(['roadmap-step', 'is-current' => $isCurrent]) href="{{ route('courses.show', $step) }}" @if ($isCurrent) aria-current="page" @endif>
                    <span class="roadmap-step-top">
                        <span class="roadmap-step-no">Step {{ $loop->iteration }}</span>
                        <span class="roadmap-code">{{ $step->shortLabel() }}®</span>
                    </span>
                    <h3>{{ $step->title }}</h3>
                    <p>{{ $step->short_description }}</p>
                    <span class="roadmap-explore">
                        {{ $isCurrent ? 'You are here' : 'Explore' }}
                        @unless ($isCurrent)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endunless
                    </span>
                </a>
            @endforeach
        </div>
        <div class="shot-dots" data-shot-dots></div>
    </div>
</section>
