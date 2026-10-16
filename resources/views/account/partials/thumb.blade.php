@php
    $thumbCourse = $course ?? null;
    $thumbFile = $thumbCourse?->thumbnail;
    $thumbSrc = $thumbFile && is_file(public_path($thumbFile)) ? asset($thumbFile) : null;
@endphp
@if ($thumbSrc)
    <img src="{{ $thumbSrc }}" alt="{{ $thumbCourse->thumbnail_alt ?: $thumbCourse->title }}">
@else
    <span class="thumb-fallback">{{ $thumbCourse instanceof \App\Models\Course ? $thumbCourse->shortLabel() : ($thumbCourse?->title ?? 'EdHut') }}</span>
@endif
