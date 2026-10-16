@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="svc-hero">
        <div class="wrap svc-intro">
            <p class="svc-pill"><span class="dot" aria-hidden="true"></span> Practice checks · PMI Authorized Training Partner</p>
            <h1>Check what you <span>already know.</span></h1>
            <p>These are short checks published with an EdHut course. A check opens after your enrollment in that course is approved. They are not PMI exam papers.</p>
        </div>
    </section>

    <section class="section svc-list">
        <div class="wrap">
            <div class="svc-grid">
                @forelse ($quizzes as $quiz)
                    @php($pass = rtrim(rtrim(number_format((float) $quiz->pass_percentage, 2), '0'), '.'))
                    <article class="svc-card quiz-card-public">
                        <span class="svc-icon course-mark">{{ $quiz->course?->shortLabel() ?: 'Quiz' }}</span>
                        <h2><a href="{{ route('quizzes.show', $quiz) }}">{{ $quiz->title }}</a></h2>
                        @if ($quiz->course)
                            <p class="quiz-course-name">{{ $quiz->course->title }}</p>
                        @endif
                        @if ($quiz->description)
                            <p>{{ $quiz->description }}</p>
                        @endif
                        <ul class="svc-fees">
                            <li><span>Pass mark {{ $pass }}%</span></li>
                            <li><span>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' min' : 'No time limit' }}</span></li>
                            <li><span>{{ $quiz->question_count }} {{ \Illuminate\Support\Str::plural('question', $quiz->question_count) }}</span></li>
                        </ul>
                        <div class="svc-card-foot">
                            <p>{{ $quiz->course ? 'Course check' : 'Practice check' }}</p>
                            <a class="svc-go" href="{{ route('quizzes.show', $quiz) }}" aria-label="Open {{ $quiz->title }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <article class="quiz-empty">
                        <h2>No active quizzes yet.</h2>
                        <p>Practice checks appear here when a course publishes one.</p>
                    </article>
                @endforelse
            </div>
            {{ $quizzes->links() }}
        </div>
    </section>

    <section class="section">
        <div class="wrap svc-cta">
            <div>
                <p class="kicker">Course access</p>
                <h2>Need the course before the check?</h2>
                <p>Ask about a cohort. Files, videos, and quizzes stay locked until an administrator approves the order.</p>
            </div>
            <div class="hero-actions">
                <a class="btn" href="{{ route('courses.index') }}">Browse courses</a>
                <a class="btn svc-cta-line" href="{{ route('contact') }}">Quick enquiry</a>
            </div>
        </div>
    </section>
@endsection
