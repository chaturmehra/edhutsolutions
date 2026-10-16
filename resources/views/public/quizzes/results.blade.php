@extends('layouts.user')

@section('title', 'Quiz result')

@push('head')
    <link href="{{ asset('assets/css/quiz-review.css') }}?v={{ @filemtime(public_path('assets/css/quiz-review.css')) }}" rel="stylesheet">
@endpush

@section('content')
    @include('account.partials.top', ['heading' => 'Quiz result'])

    <section class="learn-card result-hero">
        <p>{{ $quiz->title }}</p>
        <strong class="{{ $attempt->passed ? 'is-pass' : 'is-fail' }}">{{ rtrim(rtrim(number_format((float) $attempt->percentage, 1), '0'), '.') }}%</strong>
        <span class="pill {{ $attempt->passed ? 'is-ok' : 'is-bad' }}">{{ $attempt->passed ? 'Passed' : 'Not passed' }}</span>
        <p class="muted">{{ rtrim(rtrim(number_format((float) $attempt->score, 2), '0'), '.') }} / {{ rtrim(rtrim(number_format((float) $attempt->max_score, 2), '0'), '.') }} · pass mark {{ rtrim(rtrim(number_format((float) $quiz->pass_percentage, 1), '0'), '.') }}%</p>
    </section>

    @include('components.quiz.review', ['answers' => $attempt->answers])

    <div class="result-links">
        @if ($quiz->course && auth()->user()->can('viewContent', $quiz->course))
            <a class="btn btn-line" href="{{ route('learn.show', $quiz->course) }}#{{ $quiz->isMock() ? 'mock-test' : 'practice-test' }}">Back to {{ mb_strtolower($quiz->kindLabel()) }}s</a>
            <form method="post" action="{{ route('quizzes.start', $quiz) }}">
                @csrf
                <button class="btn btn-primary" type="submit">{{ $quiz->isMock() ? 'Take a new mock test' : 'Retake test' }}</button>
            </form>
        @else
            <a class="btn btn-line" href="{{ route('account.quizzes.index') }}">Back to quizzes</a>
            <a class="btn btn-primary" href="{{ route('quizzes.show', $quiz) }}">Quiz details</a>
        @endif
    </div>
@endsection
