@extends('layouts.admin')

@push('head')
    <link href="{{ asset('assets/css/quiz-review.css') }}?v={{ @filemtime(public_path('assets/css/quiz-review.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $number = fn ($value) => rtrim(rtrim(number_format((float) $value, 1), '0'), '.');
        $quiz = $attempt->quiz;
        $spent = $attempt->submitted_at ? $attempt->timeSpentSeconds() : null;
        $minutes = $spent !== null ? (int) ceil($spent / 60) : null;
    @endphp
    <section class="panel-head">
        <div>
            <h1>{{ $quiz?->title ?? 'Quiz result' }}</h1>
            <p>{{ $attempt->user?->name }} · {{ $attempt->user?->email }}</p>
        </div>
        <a class="btn btn-outline" href="{{ route('admin.quiz-results.index') }}">Back</a>
    </section>

    <section class="panel form-panel">
        <h2>Summary</h2>
        <dl class="order-facts">
            <div>
                <dt>Result</dt>
                <dd>
                    @if (! $attempt->submitted_at)
                        <span class="tag is-wait">In progress</span>
                    @elseif ($attempt->passed)
                        <span class="tag">Passed</span>
                    @else
                        <span class="tag is-bad">Not passed</span>
                    @endif
                </dd>
            </div>
            <div><dt>Score</dt><dd>{{ $attempt->submitted_at ? $number($attempt->percentage).'% ('.$number($attempt->score).' / '.$number($attempt->max_score).')' : '—' }}</dd></div>
            <div><dt>Pass mark</dt><dd>{{ $quiz ? $number($quiz->pass_percentage).'%' : '—' }}</dd></div>
            <div><dt>Course</dt><dd>{{ $quiz?->course?->title ?? '—' }}</dd></div>
            <div><dt>Started</dt><dd>{{ $attempt->started_at?->format('d M Y, h:i A') ?? '—' }}</dd></div>
            <div><dt>Submitted</dt><dd>{{ $attempt->submitted_at?->format('d M Y, h:i A') ?? 'Not yet' }}</dd></div>
            <div><dt>Time taken</dt><dd>{{ $minutes !== null ? $minutes.' min'.($quiz?->time_limit_minutes ? ' of '.$quiz->time_limit_minutes : '') : '—' }}</dd></div>
            <div><dt>Questions</dt><dd>{{ $attempt->answers->count() }}</dd></div>
        </dl>
    </section>

    <section class="panel form-panel">
        <h2>Answers</h2>
        @if ($attempt->answers->isEmpty())
            <p class="meta">No answers yet. The learner has not submitted this attempt.</p>
        @else
            @include('components.quiz.review', ['answers' => $attempt->answers, 'pickedLabel' => "Learner's answer"])
        @endif
    </section>
@endsection
