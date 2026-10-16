@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $module ? 'Edit agenda module' : 'New agenda module',
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $module ? route('admin.courses.modules.update', [$course->id, $module->id]) : route('admin.courses.modules.store', $course->id) }}">
        @csrf
        @if ($module)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field span-2">
                <span>Module title<i class="req">*</i></span>
                <input class="input" type="text" name="title" maxlength="255" value="{{ old('title', $module->title ?? '') }}" placeholder="e.g. Strategic Program Alignment" required>
                @error('title')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field span-2">
                <span>Topics<i class="req">*</i></span>
                <span class="field-help">One topic per line. The order here is the order on the course page.</span>
                <textarea class="textarea" name="topics" rows="8" required>{{ old('topics', $topics) }}</textarea>
                @error('topics')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $module->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $module->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Save</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.materials', $course->id) }}">Cancel</a>
        </div>
    </form>
@endsection
