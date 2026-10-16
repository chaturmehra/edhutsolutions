@extends('layouts.admin')

@section('content')
    @include('admin.courses.partials.head', [
        'heading' => $cohort ? 'Edit cohort' : 'New cohort',
        'back' => route('admin.courses.materials', $course->id),
        'backLabel' => 'Course materials',
    ])
    @php
        $sessionDates = old('session_dates', $cohort?->session_dates ?? []);
        $sessionDates = array_values(array_filter((array) $sessionDates));
        if ($sessionDates === []) {
            $sessionDates = [''];
        }
    @endphp
    <form class="panel form-panel" method="post" action="{{ $cohort ? route('admin.courses.cohorts.update', [$course->id, $cohort->id]) : route('admin.courses.cohorts.store', $course->id) }}">
        @csrf
        @if ($cohort)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label class="field">
                <span>Start date<i class="req">*</i></span>
                <input class="input" type="date" name="starts_on" value="{{ old('starts_on', $cohort?->starts_on?->toDateString()) }}" required>
            </label>
            <label class="field">
                <span>End date</span>
                <input class="input" type="date" name="ends_on" value="{{ old('ends_on', $cohort?->ends_on?->toDateString()) }}">
            </label>
            <label class="field">
                <span>Class time</span>
                <span class="field-help">For example "8:00 AM - 1:00 PM (GMT+5:30)".</span>
                <input class="input" type="text" name="time_label" value="{{ old('time_label', $cohort->time_label ?? '') }}" maxlength="120">
            </label>
            <label class="field">
                <span>Hours and sessions</span>
                <span class="field-help">For example "35 hours / 7 sessions".</span>
                <input class="input" type="text" name="hours_label" value="{{ old('hours_label', $cohort->hours_label ?? '') }}" maxlength="120">
            </label>
            <label class="field">
                <span>Delivery mode</span>
                <span class="field-help">For example "Virtual (Weekend)".</span>
                <input class="input" type="text" name="delivery_mode" value="{{ old('delivery_mode', $cohort->delivery_mode ?? '') }}" maxlength="120">
            </label>
            <label class="field">
                <span>Trainer</span>
                <span class="field-help">Pick from <a href="{{ route('admin.modules.index', 'trainers') }}">Trainers</a>. With no trainer, learners see "{{ \App\Models\CourseSchedule::DEFAULT_TRAINER }}".</span>
                <select class="select" name="trainer_id">
                    <option value="">{{ \App\Models\CourseSchedule::DEFAULT_TRAINER }} (default)</option>
                    @foreach ($trainers as $trainerOption)
                        <option value="{{ $trainerOption->id }}" @selected((string) old('trainer_id', $cohort->trainer_id ?? '') === (string) $trainerOption->id)>{{ $trainerOption->name }}{{ $trainerOption->status === 'active' ? '' : ' (inactive, shows the default)' }}</option>
                    @endforeach
                </select>
                @error('trainer_id')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label class="field">
                <span>Status<i class="req">*</i></span>
                <select class="select" name="status">
                    @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $cohort->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <h2 class="option-heading">Session dates</h2>
        <p class="field-help">Optional. Learners see these under "View dates" when they pick this cohort.</p>
        <div class="date-list" data-date-list>
            @foreach ($sessionDates as $sessionDate)
                <div class="date-row" data-date-row>
                    <input class="input" type="date" name="session_dates[]" value="{{ $sessionDate }}" aria-label="Session date">
                    <button class="act act-danger" type="button" data-date-remove>Remove</button>
                </div>
            @endforeach
        </div>
        <div class="form-actions">
            <button class="btn btn-outline" type="button" data-date-add>Add date</button>
        </div>
        <div class="form-actions">
            <button class="btn btn-purple" type="submit">Save</button>
            <a class="btn btn-outline" href="{{ route('admin.courses.materials', $course->id) }}">Cancel</a>
        </div>
    </form>
@endsection
