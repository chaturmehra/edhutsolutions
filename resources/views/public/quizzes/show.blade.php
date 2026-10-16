@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @php($pass = rtrim(rtrim(number_format((float) $quiz->pass_percentage, 2), '0'), '.'))
    <section class="svc-hero svc-detail-hero">
        <div class="wrap svc-detail-grid">
            <div>
                <p class="svc-crumb"><a href="{{ route('quizzes.index') }}">Quizzes</a> <span aria-hidden="true">/</span> {{ $quiz->title }}</p>
                <p class="svc-pill"><span class="dot" aria-hidden="true"></span> {{ $quiz->course?->shortLabel() ?: 'Practice check' }}</p>
                <h1>{{ $quiz->title }}</h1>
                @if ($quiz->description)
                    <p>{{ $quiz->description }}</p>
                @endif
                <div class="hero-actions">
                    @if ($canTake)
                        <form method="post" action="{{ route('quizzes.start', $quiz) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">{{ $resume ? 'Continue quiz' : 'Start quiz' }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        </form>
                    @else
                        @guest
                            <a class="btn btn-primary" href="{{ route('login') }}">Sign in to start</a>
                        @endguest
                        @if ($quiz->course)
                            <a class="btn btn-ghost" href="{{ route('courses.show', $quiz->course) }}">View course</a>
                        @endif
                    @endif
                </div>
                @unless ($canTake)
                    @if ($quiz->course)
                        <p class="quiz-lock">This check opens after your enrollment in {{ $quiz->course->title }} is approved.</p>
                    @endif
                @endunless
            </div>
            <aside class="quiz-brief">
                <p class="kicker">This check</p>
                <ul class="quiz-facts">
                    <li><strong>{{ $pass }}%</strong><span>Pass mark</span></li>
                    <li><strong>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' min' : 'None' }}</strong><span>Time limit</span></li>
                    <li><strong>{{ $quiz->question_count }}</strong><span>{{ \Illuminate\Support\Str::plural('Question', $quiz->question_count) }}</span></li>
                </ul>
                @if ($quiz->course)
                    <a class="quiz-course-link" href="{{ route('courses.show', $quiz->course) }}">
                        <span>
                            <em>{{ $quiz->course->shortLabel() }}</em>
                            <strong>{{ $quiz->course->title }}</strong>
                        </span>
                        <span class="practice-go" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </a>
                @endif
            </aside>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            <div class="svc-section-head">
                <p class="kicker">Before you begin</p>
                <h2>How this check works.</h2>
            </div>
            <div class="quiz-steps">
                <article>
                    <span>01</span>
                    <h3>Enrollment first</h3>
                    <p>The check stays locked until an administrator approves your enrollment in the linked course.</p>
                </article>
                <article>
                    <span>02</span>
                    <h3>{{ $quiz->time_limit_minutes ? 'Timed sitting' : 'No clock' }}</h3>
                    <p>{{ $quiz->time_limit_minutes ? 'You have '.$quiz->time_limit_minutes.' minutes, one question at a time. When the timer reaches zero, your answers are submitted automatically.' : 'This check has no time limit. Submit when you are ready.' }}</p>
                </article>
                <article>
                    <span>03</span>
                    <h3>Score on submit</h3>
                    <p>Choice and short answers are marked when you submit. A long answer is saved and is not marked automatically.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
