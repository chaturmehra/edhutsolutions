@extends('layouts.user')

@section('title', $quiz->title)

@push('scripts')
    <script src="{{ asset('assets/js/quiz.js') }}?v={{ @filemtime(public_path('assets/js/quiz.js')) }}" defer></script>
@endpush

@section('content')
    @php
        $total = $questions->count();
        $passMark = rtrim(rtrim(number_format((float) $quiz->pass_percentage, 1), '0'), '.');
    @endphp

    <header class="learn-top quiz-run-top">
        <div>
            <p class="insight-kicker">{{ $quiz->course ? $quiz->course->shortLabel().' '.mb_strtolower($quiz->kindLabel()) : $quiz->kindLabel() }}</p>
            <h1>{{ $quiz->title }}</h1>
            <p>{{ $total }} {{ \Illuminate\Support\Str::plural('question', $total) }} · pass mark {{ $passMark }}%{{ $quiz->time_limit_minutes ? ' · '.$quiz->time_limit_minutes.' minutes' : '' }}</p>
        </div>
        <div class="quiz-timer" data-quiz-timer @if ($remaining !== null) data-remaining="{{ $remaining }}" @else data-elapsed="{{ $elapsed }}" @endif role="timer" aria-live="off">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 9v4l2.5 2M9.5 2.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>
                <strong data-quiz-clock>{{ $remaining !== null ? sprintf('%02d:%02d', intdiv($remaining, 60), $remaining % 60) : sprintf('%02d:%02d', intdiv($elapsed, 60), $elapsed % 60) }}</strong>
                <small>{{ $remaining !== null ? 'Time left' : 'Time spent' }}</small>
            </span>
        </div>
    </header>

    @if ($total === 0)
        <section class="learn-card">
            <p class="muted">This quiz has no questions yet.</p>
        </section>
    @else
        <section class="quiz-run">
            <div class="quiz-progress">
                <div class="quiz-progress-head">
                    <strong>Question <span data-quiz-current>1</span> of {{ $total }}</strong>
                    <span><span data-quiz-answered>0</span> of {{ $total }} answered</span>
                </div>
                <div class="quiz-bar" aria-hidden="true"><span data-quiz-bar></span></div>
                <nav class="quiz-dots" aria-label="Questions">
                    @foreach ($questions as $question)
                        <button type="button" data-quiz-go="{{ $loop->index }}" aria-label="Question {{ $loop->iteration }}">{{ $loop->iteration }}</button>
                    @endforeach
                </nav>
            </div>

            <form method="post" action="{{ route('quizzes.submit', $quiz) }}" data-quiz-form data-quiz-key="edhut-quiz-{{ $attempt->id }}" data-quiz-heartbeat="{{ route('quizzes.heartbeat', $quiz) }}">
                @csrf
                <input type="hidden" name="timed_out" value="0" data-quiz-timeout>
                @foreach ($questions as $question)
                    <fieldset class="quiz-question quiz-step" data-quiz-step>
                        <legend class="quiz-step-label">Question {{ $loop->iteration }}</legend>
                        @if ($question->scenario)
                            <p class="quiz-scenario">{{ $question->scenario }}</p>
                        @endif
                        <h2>{{ $question->prompt }}</h2>
                        <p class="quiz-hint">{{ \App\Support\QuestionTypes::instruction($question->type) }} · {{ rtrim(rtrim(number_format((float) $question->marks, 2), '0'), '.') }} {{ (float) $question->marks === 1.0 ? 'mark' : 'marks' }}</p>
                        @if (\App\Support\QuestionTypes::isChoice($question->type))
                            <div class="quiz-options">
                                @foreach ($question->options as $option)
                                    <label class="quiz-option" @if (! $quiz->isMock() && $option->is_correct) data-quiz-correct @endif>
                                        <input type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}" name="answers[{{ $question->id }}][]" value="{{ $option->id }}">
                                        <span class="quiz-option-key" aria-hidden="true">{{ chr(65 + $loop->index) }}</span>
                                        <span>{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif (\App\Support\QuestionTypes::isEssay($question->type))
                            <textarea class="textarea quiz-write" name="answers[{{ $question->id }}][text]" rows="8" aria-label="Your answer"></textarea>
                        @else
                            <input class="input quiz-write" type="text" name="answers[{{ $question->id }}][text]" aria-label="Your answer">
                        @endif

                        @unless ($quiz->isMock())
                            @php($correct = $question->options->where('is_correct', true))
                            <div class="quiz-reveal">
                                <button class="quiz-reveal-btn" type="button" data-quiz-reveal aria-expanded="false" aria-controls="answer-{{ $question->id }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>
                                    <span data-quiz-reveal-label>Show answer</span>
                                </button>
                                <div class="quiz-answer" id="answer-{{ $question->id }}" hidden>
                                    @if (\App\Support\QuestionTypes::isEssay($question->type))
                                        <strong>Answer</strong>
                                        <p>This question is reviewed by a trainer, so it has no single correct answer.</p>
                                    @elseif (\App\Support\QuestionTypes::showsAcceptedAnswers($question->type))
                                        <strong>{{ $correct->count() === 1 ? 'Correct answer' : 'Accepted answers' }}</strong>
                                        <p>{{ $correct->pluck('option_text')->implode(' · ') }}</p>
                                    @else
                                        <strong>{{ $correct->count() === 1 ? 'Correct answer' : 'Correct answers' }}</strong>
                                        <ul>
                                            @foreach ($question->options as $option)
                                                @if ($option->is_correct)
                                                    <li><span class="quiz-option-key" aria-hidden="true">{{ chr(65 + $loop->index) }}</span> {{ $option->option_text }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if ($question->explanation)
                                        <strong>Explanation</strong>
                                        <p>{{ $question->explanation }}</p>
                                    @endif
                                </div>
                            </div>
                        @endunless
                    </fieldset>
                @endforeach

                <div class="quiz-nav">
                    <button class="btn btn-line" type="button" data-quiz-prev>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Previous
                    </button>
                    <button class="btn btn-primary" type="button" data-quiz-next>
                        Next
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button class="btn btn-primary" type="submit" data-quiz-submit>Submit answers</button>
                </div>
            </form>
        </section>

        <div class="quiz-timeup" data-quiz-timeup hidden>
            <div>
                <strong>Time is up</strong>
                <p>Submitting your answers…</p>
            </div>
        </div>
    @endif
@endsection
