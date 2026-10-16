@extends('layouts.admin')

@section('content')
    @php
        $source = old('source_type', $video->source_type ?? 'upload');
        $base ??= 'admin.courses';
        $owner ??= $course;
        $noun = $base === 'admin.services' ? 'service' : 'course';
        $back = route($base.'.materials', $owner->id);
    @endphp
    @include('admin.courses.partials.head', [
        'heading' => $video ? 'Edit video' : 'Add video',
        'subtitle' => $owner->title,
        'back' => $back,
        'backLabel' => ucfirst($noun).' materials',
    ])
    <form class="panel form-panel" method="post" action="{{ $video ? route($base.'.videos.update', [$owner->id, $video]) : route($base.'.videos.store', $owner->id) }}" enctype="multipart/form-data">
        @csrf
        @if ($video)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field span-2">
                <span>Title<i class="req">*</i></span>
                <input class="input" type="text" name="title" value="{{ old('title', $video->title ?? '') }}" required>
            </label>
            <label class="field">
                <span>Source<i class="req">*</i></span>
                <select class="select" name="source_type" data-source-type>
                    <option value="upload" @selected($source === 'upload')>Upload MP4</option>
                    <option value="link" @selected($source === 'link')>Video link</option>
                </select>
            </label>
            <label class="field">
                <span>Duration</span>
                <input class="input" type="text" name="duration" value="{{ old('duration', $video->duration ?? '') }}" placeholder="12 min">
            </label>
            <div class="span-2" data-source-panel="upload" @if($source !== 'upload') hidden @endif>
                <label class="field">
                    <span>MP4 file @unless($video && $video->file_path)<i class="req">*</i>@endunless</span>
                    <span class="field-help">MP4 only. Maximum 100 MB. An uploaded video plays inside the {{ $noun }} in a popup, with download turned off.</span>
                    @if ($video && $video->file_path)
                        <span class="field-help">A video is already stored. Choose a file only if you want to replace it.</span>
                    @endif
                    <input class="input" type="file" name="file" accept="video/mp4,.mp4">
                </label>
            </div>
            <div class="span-2" data-source-panel="link" @if($source !== 'link') hidden @endif>
                <label class="field">
                    <span>Video link<i class="req">*</i></span>
                    <span class="field-help">YouTube, Vimeo, Google Drive and Loom links, and direct links to an .mp4 or .webm file, play inside the {{ $noun }} in a popup. Any other link opens in a new tab. A Google Drive file must be shared with "Anyone with the link".</span>
                    <input class="input" type="url" name="external_url" value="{{ old('external_url', $video->external_url ?? '') }}" placeholder="https://">
                </label>
            </div>
            <label class="field">
                <span>Order</span>
                <input class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $video->sort_order ?? $nextOrder) }}">
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $video->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">{{ $video ? 'Save' : 'Save video' }}</button>
            <a class="btn btn-outline" href="{{ $back }}">Cancel</a>
        </div>
    </form>
@endsection
