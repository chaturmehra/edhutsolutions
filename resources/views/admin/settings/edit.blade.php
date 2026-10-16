@extends('layouts.admin')

@section('content')
    <section class="panel-head">
        <div>
            <h1>Site settings</h1>
            <p>Update the contact details, stats, and copy used across the public site.</p>
        </div>
    </section>
    <form method="post" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        @foreach ($groups as $name => $group)
            <section class="panel form-panel settings-card" id="settings-{{ $name }}">
                <div class="settings-card-head">
                    <h2>{{ $group['title'] }}</h2>
                    <p>{{ $group['text'] }}</p>
                </div>
                <div class="form-grid">
                    @foreach ($group['settings'] as $setting)
                        @php
                            $long = strlen((string) $setting->value) > 80;
                        @endphp
                        <label class="field {{ $long ? 'span-2' : '' }}">
                            <span>{{ $labels[$setting->key] ?? ucfirst(str_replace('_', ' ', $setting->key)) }}</span>
                            @if ($long)
                                <textarea class="textarea" name="settings[{{ $setting->key }}]">{{ $setting->value }}</textarea>
                            @else
                                <input class="input" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                            @endif
                        </label>
                    @endforeach
                </div>
            </section>
        @endforeach
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Save</button>
            <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">Cancel</a>
        </div>
    </form>
@endsection
