@use('App\Support\QuestionTypes')
@php
    $number = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
@endphp
<ol class="qr-list">
    @foreach ($answers as $answer)
        @php
            $question = $answer->question;
            $type = $question?->type ?? 'mcq';
            $essay = QuestionTypes::isEssay($type);
            $selected = collect($answer->selected_option_ids ?? [])->map(fn ($id) => (int) $id);
            $state = $essay ? 'is-open' : ($answer->is_correct ? 'is-correct' : 'is-wrong');
            $marks = (float) ($question?->marks ?? 0);
        @endphp
        <li class="qr-card {{ $state }}">
            <header class="qr-head">
                <span class="qr-num">{{ $loop->iteration }}</span>
                <div class="qr-title">
                    <h2>{{ $question?->prompt ?? 'This question was removed.' }}</h2>
                    <p>{{ QuestionTypes::label($type) }} · {{ $number($marks) }} {{ $marks === 1.0 ? 'mark' : 'marks' }}@unless ($essay) · scored {{ $number($answer->marks_awarded) }}@endunless</p>
                </div>
                <span class="qr-badge">{{ $essay ? 'Not marked' : ($answer->is_correct ? 'Correct' : 'Incorrect') }}</span>
            </header>

            @if ($question?->scenario)
                <p class="qr-scenario">{{ $question->scenario }}</p>
            @endif

            @if ($question && QuestionTypes::isChoice($type))
                <ul class="qr-options">
                    @foreach ($question->options as $option)
                        @php
                            $picked = $selected->contains($option->id);
                            $key = (bool) $option->is_correct;
                            $row = $key ? 'is-key' : ($picked ? 'is-miss' : '');
                        @endphp
                        <li class="{{ $row }}">
                            <span class="qr-mark" aria-hidden="true">{{ $key ? '✓' : ($picked ? '✗' : '○') }}</span>
                            <span class="qr-text">{{ $option->option_text }}</span>
                            @if ($picked)
                                <em>{{ $pickedLabel ?? 'Your answer' }}</em>
                            @elseif ($key)
                                <em>Correct answer</em>
                            @endif
                        </li>
                    @endforeach
                </ul>
                @if ($selected->isEmpty())
                    <p class="qr-note">No option was selected.</p>
                @endif
            @elseif ($question)
                <div class="qr-written">
                    <p><strong>{{ $pickedLabel ?? 'Your answer' }}</strong> {{ $answer->answer_text ?: 'No answer' }}</p>
                    @if (QuestionTypes::showsAcceptedAnswers($type))
                        <p><strong>Accepted</strong> {{ $question->options->where('is_correct', true)->pluck('option_text')->join(', ') }}</p>
                    @endif
                </div>
            @endif

            @if ($question?->explanation)
                <p class="qr-explain"><strong>Explanation</strong> {{ $question->explanation }}</p>
            @endif
        </li>
    @endforeach
</ol>
