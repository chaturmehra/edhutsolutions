@extends('layouts.admin')

@section('content')
    @php
        $base ??= 'admin.courses';
        $owner ??= $course;
        $back = route($base.'.materials', $owner->id);
    @endphp
    @include('admin.courses.partials.head', [
        'heading' => $pdf ? 'Edit PDF' : 'Upload PDF',
        'subtitle' => $owner->title,
        'back' => $back,
        'backLabel' => $base === 'admin.services' ? 'Service materials' : 'Course materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $pdf ? route($base.'.pdfs.update', [$owner->id, $pdf]) : route($base.'.pdfs.store', $owner->id) }}" enctype="multipart/form-data">
        @csrf
        @if ($pdf)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field span-2">
                <span>Title<i class="req">*</i></span>
                <input class="input" type="text" name="title" value="{{ old('title', $pdf->title ?? '') }}" required>
            </label>
            <label class="field span-2">
                <span>Description</span>
                <span class="field-help">Shown beside the title in the learner's Study Material tab.</span>
                <textarea class="textarea" name="description" rows="3" maxlength="1000">{{ old('description', $pdf->description ?? '') }}</textarea>
            </label>
            <label class="field span-2">
                <span>PDF file @unless($pdf)<i class="req">*</i>@endunless</span>
                @if ($pdf)
                    <span class="field-help">Current file: {{ $pdf->original_name ?: 'PDF' }}. Choose a new file only if you want to replace it. Maximum 20 MB.</span>
                @else
                    <span class="field-help">PDF only. Maximum 20 MB.</span>
                @endif
                <input class="input" type="file" name="file" accept="application/pdf,.pdf" @unless($pdf) required @endunless>
            </label>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $pdf->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $pdf->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">{{ $pdf ? 'Save' : 'Upload' }}</button>
            <a class="btn btn-outline" href="{{ $back }}">Cancel</a>
        </div>
    </form>
@endsection
