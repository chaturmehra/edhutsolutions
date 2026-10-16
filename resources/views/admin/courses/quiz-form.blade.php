@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $quiz ? 'Edit quiz' : 'Create quiz',
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $quiz ? route('admin.courses.quizzes.update', [$course->id, $quiz->id]) : route('admin.courses.quizzes.store', $course->id) }}">
        @csrf
        @if ($quiz)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field span-2">
                <span>Title<i class="req">*</i></span>
                <input class="input" type="text" name="title" value="{{ old('title', $quiz->title ?? '') }}" required>
            </label>
            <label class="field">
                <span>Slug</span>
                <span class="field-help">Leave this empty to build it from the title.</span>
                <input class="input" type="text" name="slug" value="{{ old('slug', $quiz->slug ?? '') }}">
            </label>
            <label class="field">
                <span>Test type<i class="req">*</i></span>
                <span class="field-help">Learners find mock tests and practice tests under separate tabs.</span>
                <select class="select" name="kind">
                    @foreach (\App\Models\Quiz::KINDS as $value => $label)
                        <option value="{{ $value }}" @selected(old('kind', $quiz->kind ?? 'practice') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span>Questions per attempt</span>
                <span class="field-help">Mock tests draw this many questions at random on every start, in a new order. Leave empty to use every active question.</span>
                <input class="input" type="number" name="questions_per_attempt" min="1" value="{{ old('questions_per_attempt', $quiz->questions_per_attempt ?? '') }}">
            </label>
            <label class="field">
                <span>Pass percentage<i class="req">*</i></span>
                <input class="input" type="number" name="pass_percentage" min="0" max="100" step="0.01" value="{{ old('pass_percentage', $quiz->pass_percentage ?? 70) }}" required>
            </label>
            <label class="field span-2">
                <span>Description</span>
                <textarea class="textarea" name="description">{{ old('description', $quiz->description ?? '') }}</textarea>
            </label>
            <label class="field">
                <span>Negative marking</span>
                <span class="field-help">Points removed for a wrong answer when a question does not set its own penalty.</span>
                <input class="input" type="number" name="negative_marking" min="0" step="0.01" value="{{ old('negative_marking', $quiz->negative_marking ?? 0) }}">
            </label>
            <label class="field">
                <span>Time limit (minutes)</span>
                <input class="input" type="number" name="time_limit_minutes" min="1" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes ?? '') }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $quiz->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">{{ $quiz ? 'Save' : 'Create and add questions' }}</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.materials', $course->id) }}">Cancel</a>
        </div>
    </form>
@endsection
