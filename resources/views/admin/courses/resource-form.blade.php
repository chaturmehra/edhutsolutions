@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $resource ? 'Edit resource' : 'New resource',
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $resource ? route('admin.courses.resources.update', [$course->id, $resource->id]) : route('admin.courses.resources.store', $course->id) }}" enctype="multipart/form-data">
        @csrf
        @if ($resource)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field span-2">
                <span>Title<i class="req">*</i></span>
                <input class="input" type="text" name="title" maxlength="255" value="{{ old('title', $resource->title ?? '') }}" required>
                @error('title')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field span-2">
                <span>Short description</span>
                <textarea class="textarea" name="description" rows="3" maxlength="2000">{{ old('description', $resource->description ?? '') }}</textarea>
                @error('description')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field span-2">
                <span>PDF (optional)</span>
                @if ($resource?->hasFile())
                    <span class="field-help">Current file: {{ $resource->original_name ?: 'PDF' }}. Choose a new file only if you want to replace it. Maximum 20 MB. Anyone visiting the course page can open this PDF.</span>
                @else
                    <span class="field-help">PDF only, maximum 20 MB. With a PDF, the title on the course page opens the file. Anyone visiting the course page can open it.</span>
                @endif
                <input class="input" type="file" name="file" accept="application/pdf,.pdf">
                @error('file')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            @if ($resource?->hasFile())
                <label class="field-check span-2">
                    <input type="checkbox" name="remove_file" value="1" @checked(old('remove_file'))>
                    Remove the current PDF
                </label>
            @endif
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $resource->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $resource->status ?? 'active') === $value)>{{ $label }}</option>
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
