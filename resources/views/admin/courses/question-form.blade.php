@extends('layouts.admin')

@section('content')
    @php
        $currentType = old('type', $question->type ?? 'mcq');
        if ($currentType === 'single') {
            $currentType = 'mcq';
        }
        $saved = $question
            ? $question->options->map(fn ($option) => ['id' => $option->id, 'text' => $option->option_text, 'correct' => $option->is_correct])->all()
            : [];
        $options = old('options', $saved);
        if ($options === [] && $currentType === 'true_false') {
            $options = [
                ['id' => '', 'text' => 'True', 'correct' => true],
                ['id' => '', 'text' => 'False', 'correct' => false],
            ];
        }
        while (count($options) < 4) {
            $options[] = ['id' => '', 'text' => '', 'correct' => false];
        }
    @endphp
    @include('admin.courses.partials.head', [
        'heading' => $question ? 'Edit question' : 'Add question',
        'back' => route('admin.courses.questions', [$course->id, $quiz->id]),
        'backLabel' => 'Questions',
    ])
    <form class="panel form-panel" method="post" action="{{ $question ? route('admin.courses.questions.update', [$course->id, $quiz->id, $question]) : route('admin.courses.questions.store', [$course->id, $quiz->id]) }}">
        @csrf
        @if ($question)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field">
                <span>Answer type<i class="req">*</i></span>
                <select class="select" name="type" data-answer-type>
                    @foreach (\App\Support\QuestionTypes::labels() as $value => $label)
                        <option value="{{ $value }}" @selected($currentType === $value)>{{ $loop->iteration }}. {{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $question->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field span-2" data-scenario @unless($currentType === 'scenario') hidden @endunless>
                <span>Scenario<i class="req">*</i></span>
                <span class="field-help">The learner reads this situation before the question.</span>
                <textarea class="textarea" name="scenario">{{ old('scenario', $question->scenario ?? '') }}</textarea>
            </label>
            <label class="field span-2">
                <span>Question<i class="req">*</i></span>
                <textarea class="textarea" name="prompt" required>{{ old('prompt', $question->prompt ?? '') }}</textarea>
            </label>
            <label class="field">
                <span>Marks<i class="req">*</i></span>
                <input class="input" type="number" name="marks" min="0" step="0.01" value="{{ old('marks', $question->marks ?? 1) }}" required>
            </label>
            <label class="field">
                <span>Negative marks</span>
                <span class="field-help">Leave empty to use the quiz penalty.</span>
                <input class="input" type="number" name="negative_marks" min="0" step="0.01" value="{{ old('negative_marks', $question->negative_marks ?? '') }}">
            </label>
            <label class="field span-2">
                <span>Explanation</span>
                <textarea class="textarea" name="explanation">{{ old('explanation', $question->explanation ?? '') }}</textarea>
            </label>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $question->sort_order ?? $nextOrder) }}">
            </label>
        </div>
        <div data-answer-options @if($currentType === 'essay') hidden @endif>
            <h2 class="option-heading" data-option-heading>{{ in_array($currentType, ['fill_blank', 'short'], true) ? 'Accepted answers' : 'Answer options' }}</h2>
            <p class="field-help" data-option-help></p>
            <div class="option-list" data-option-list>
                @foreach ($options as $index => $option)
                    <div class="option-row" data-option-row>
                        <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] ?? '' }}">
                        <input class="input" type="text" name="options[{{ $index }}][text]" value="{{ $option['text'] ?? '' }}" placeholder="Option {{ $index + 1 }}">
                        <label class="option-correct" data-option-correct>
                            <input type="checkbox" name="options[{{ $index }}][correct]" value="1" @checked(filter_var($option['correct'] ?? false, FILTER_VALIDATE_BOOLEAN))>
                            <span>Correct</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="form-actions">
                <button class="btn btn-outline" type="button" data-option-add>Add option</button>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">{{ $question ? 'Save question' : 'Add question' }}</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.questions', [$course->id, $quiz->id]) }}">Cancel</a>
        </div>
    </form>
@endsection
