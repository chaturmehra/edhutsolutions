@extends('layouts.user')

@section('title', 'Quizzes')

@section('content')
    @include('account.partials.top', ['heading' => 'Quizzes'])

    <section class="learn-card">
        <header class="card-head">
            <h2>Quizzes</h2>
            <div class="chip-row">
                <a class="{{ $filter === 'all' ? 'is-on' : '' }}" href="{{ route('account.quizzes.index') }}">All ({{ $counts['all'] }})</a>
                <a class="{{ $filter === 'available' ? 'is-on' : '' }}" href="{{ route('account.quizzes.index', ['status' => 'available']) }}">Available ({{ $counts['available'] }})</a>
                <a class="{{ $filter === 'progress' ? 'is-on' : '' }}" href="{{ route('account.quizzes.index', ['status' => 'progress']) }}">In progress ({{ $counts['progress'] }})</a>
                <a class="{{ $filter === 'done' ? 'is-on' : '' }}" href="{{ route('account.quizzes.index', ['status' => 'done']) }}">Completed ({{ $counts['done'] }})</a>
            </div>
        </header>

        <div class="quiz-list">
            @forelse ($cards as $card)
                @php $quiz = $card['quiz']; @endphp
                <article class="quiz-card">
                    @if ($quiz->course)
                        <span class="course-pill">{{ $quiz->course->title }}</span>
                    @endif
                    <h3>{{ $quiz->title }}</h3>
                    <p class="meta-row">
                        <span>{{ $quiz->questions_count }} {{ \Illuminate\Support\Str::plural('question', $quiz->questions_count) }}</span>
                        <span>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' min' : 'No time limit' }}</span>
                        <span>Pass {{ rtrim(rtrim(number_format((float) $quiz->pass_percentage, 1), '0'), '.') }}%</span>
                    </p>
                    @if ($card['latest'])
                        <p class="score-line {{ $card['latest']->passed ? 'is-pass' : 'is-fail' }}">
                            {{ rtrim(rtrim(number_format((float) $card['latest']->percentage, 1), '0'), '.') }}%
                            <small>{{ $card['latest']->passed ? 'Passed' : 'Not passed' }}</small>
                        </p>
                    @else
                        <p class="muted">Not submitted yet.</p>
                    @endif
                    <div class="quiz-actions">
                        @if ($card['state'] === 'progress')
                            <a class="btn btn-primary" href="{{ route('quizzes.attempt', $quiz) }}">Continue quiz</a>
                        @else
                            <form method="post" action="{{ route('quizzes.start', $quiz) }}">
                                @csrf
                                <button class="btn btn-primary" type="submit">{{ $card['latest'] ? 'Retake quiz' : 'Start quiz' }}</button>
                            </form>
                        @endif
                        @if ($card['latest'])
                            <a class="btn btn-line" href="{{ route('quizzes.results', [$quiz, $card['latest']]) }}">Review quiz</a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="muted">Quizzes for an approved course show up here. A course that is still waiting for approval stays locked.</p>
            @endforelse
        </div>
    </section>
@endsection
