@extends('layouts.admin')

@php
    $hasEditor = collect($config['fields'])->contains(fn ($field) => ! empty($field['rich']));
@endphp

@if ($hasEditor)
    @push('head')
        <link href="{{ asset('assets/css/vendor/jodit.min.css') }}?v={{ @filemtime(public_path('assets/css/vendor/jodit.min.css')) }}" rel="stylesheet">
        <script src="{{ asset('assets/js/vendor/jodit.min.js') }}?v={{ @filemtime(public_path('assets/js/vendor/jodit.min.js')) }}" defer></script>
        <script src="{{ asset('assets/js/admin-editor.js') }}?v={{ @filemtime(public_path('assets/js/admin-editor.js')) }}" defer></script>
    @endpush
@endif

@section('content')
    @php
        $noun = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::singular($config['label']));
    @endphp
    <section class="panel-head">
        <div>
            <h1>{{ $record ? 'Edit' : 'Create' }} {{ $noun }}</h1>
            <p>{{ $record ? 'Update this '.$noun.'.' : 'Add a new '.$noun.'.' }}</p>
        </div>
        @if ($module === 'courses' && $record)
            <a class="btn btn-outline" href="{{ route('admin.courses.materials', $record->id) }}">Quizzes, PDFs, and videos</a>
        @elseif ($module === 'services' && $record)
            <a class="btn btn-outline" href="{{ route('admin.services.materials', $record->id) }}">PDFs and videos</a>
        @endif
    </section>
    <form method="post" action="{{ $record ? route('admin.modules.update', [$module, $record->id]) : route('admin.modules.store', $module) }}" enctype="multipart/form-data">
        @csrf
        @if ($record)
            @method('PUT')
        @endif
        @foreach (['details' => 'Details', 'seo' => 'SEO'] as $group => $heading)
            @php
                $fields = collect($config['fields'])->filter(fn ($field) => ($field['group'] ?? 'details') === $group);
            @endphp
            @if ($fields->isNotEmpty())
                <section class="panel form-panel">
                    <h2>{{ $heading }}</h2>
                    <div class="form-grid">
                        @foreach ($fields as $name => $field)
                            @php
                                $only = $field['only'] ?? null;
                                if (($only === 'create' && $record) || ($only === 'edit' && ! $record)) {
                                    continue;
                                }
                                $options = $field['options'] ?? [];
                                if (is_callable($options)) {
                                    $options = $options();
                                }
                                $stored = $group === 'seo' ? ($record?->seo?->{$name} ?? '') : ($record?->{$name} ?? null);
                                $value = old($name, $stored ?? ($field['type'] === 'select' && $name === 'status' ? 'active' : ''));
                                if ($value instanceof \DateTimeInterface) {
                                    $value = $value->format('Y-m-d');
                                }
                                $required = in_array('required', $field['rules'] ?? [], true) || ($field['type'] === 'file' && ! $record && $name === 'logo') || ($field['type'] === 'files' && ! $record);
                                $wide = in_array($field['type'], ['textarea', 'file', 'files'], true);
                            @endphp
                            @if ($field['type'] === 'checklist')
                                @php
                                    $checked = array_map('strval', (array) old($name, $record ? $record->{$field['relation']}->pluck('id')->all() : []));
                                @endphp
                                <fieldset class="field field-checklist span-2">
                                    <legend>{{ $field['label'] }}</legend>
                                    <div class="checklist">
                                        @forelse ($options as $optionValue => $optionLabel)
                                            <label class="field-check">
                                                <input type="checkbox" name="{{ $name }}[]" value="{{ $optionValue }}" @checked(in_array((string) $optionValue, $checked, true))>
                                                <span>{{ $optionLabel }}</span>
                                            </label>
                                        @empty
                                            <p>Nothing to choose yet.</p>
                                        @endforelse
                                    </div>
                                    @error($name)<small class="field-error">{{ $message }}</small>@enderror
                                </fieldset>
                                @continue
                            @endif
                            @if (! empty($field['rich']))
                                <div class="editor-field span-2">
                                    <label class="editor-label" for="field-{{ $name }}">{{ $field['label'] }}@if($required)<i class="req">*</i>@endif</label>
                                    <textarea class="textarea" id="field-{{ $name }}" name="{{ $name }}" data-editor>{{ $value }}</textarea>
                                </div>
                                @continue
                            @endif
                            <label class="field {{ $wide ? 'span-2' : '' }} {{ $field['type'] === 'checkbox' ? 'field-check' : '' }}">
                                <span>{{ $field['label'] }}@if($required)<i class="req">*</i>@endif</span>
                                @if ($field['type'] === 'textarea')
                                    <textarea class="textarea" name="{{ $name }}">{{ $value }}</textarea>
                                @elseif ($field['type'] === 'select')
                                    <select class="select" name="{{ $name }}">
                                        @foreach ($options as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'checkbox')
                                    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, (bool) ($record?->{$name} ?? false)))>
                                @elseif ($field['type'] === 'date')
                                    <input class="input" type="date" name="{{ $name }}" value="{{ $value }}">
                                @elseif ($field['type'] === 'file' || $field['type'] === 'files')
                                    @php $preview = $record?->thumbnail ?? $record?->image ?? $record?->logo ?? null; @endphp
                                    @if ($record && filled($preview))
                                        <img class="form-preview" src="{{ asset($preview) }}" alt="">
                                    @endif
                                    <input class="input" type="file" name="{{ $field['type'] === 'files' ? 'images[]' : $name }}" accept="image/*" @if($field['type'] === 'files') multiple @endif>
                                @else
                                    <input class="input" type="{{ $field['type'] === 'number' ? 'number' : 'text' }}" name="{{ $name }}" value="{{ $value }}" @if($field['type'] === 'number') step="0.01" @endif>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">{{ $record ? 'Save' : 'Create' }}</button>
            @unless ($record)
                <button class="btn btn-outline" type="submit" name="another" value="1">Create & create another</button>
            @endunless
            <a class="btn btn-outline" href="{{ route('admin.modules.index', $module) }}">Cancel</a>
        </div>
    </form>
@endsection
