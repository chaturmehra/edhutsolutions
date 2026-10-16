@extends('layouts.user')

@section('title', $course->title)

@section('content')
    @include('account.partials.top', ['heading' => $course->title])

    @php
        $overview = $course->overviewParagraphs();
        if ($overview === [] && filled($course->short_description)) {
            $overview = [$course->short_description];
        }
        $modules = $course->sections->filter(fn ($section) => $section->lessons->isNotEmpty())->values();
        $tabs = array_filter([
            'overview' => 'Overview',
            'study-material' => 'Study Material',
            'mock-test' => 'Mock Test',
            'practice-test' => 'Practice Test',
            'video' => $course->videos->isNotEmpty() ? 'Video' : null,
        ]);
    @endphp

    <section class="learn-card learn-tabs" data-tabs="hash">
        <div class="learn-tab-list" role="tablist" aria-label="{{ $course->title }}">
            @foreach ($tabs as $key => $label)
                <button class="learn-tab" type="button" role="tab" id="learn-tab-{{ $key }}" aria-controls="{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="overview" aria-labelledby="learn-tab-overview">
            @forelse ($overview as $paragraph)
                <p>{{ $paragraph }}</p>
            @empty
                <p class="muted">The course overview will appear here when it is published.</p>
            @endforelse
            @if ($modules->isNotEmpty())
                <h3>What this course covers</h3>
                @foreach ($modules as $section)
                    <h4>{{ $section->title }}</h4>
                    <ul class="lesson-list">
                        @foreach ($section->lessons as $lesson)
                            <li>{{ $lesson->title }}</li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="study-material" aria-labelledby="learn-tab-study-material" hidden>
            @include('account.courses.partials.pdf-table', ['pdfs' => $course->pdfs, 'empty' => 'No study material has been published for this course yet.'])
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="mock-test" aria-labelledby="learn-tab-mock-test" hidden>
            @include('account.courses.partials.tests', [
                'tests' => $mockTests,
                'empty' => 'No mock test has been published for this course yet.',
                'note' => 'Every start draws a fresh random set of questions.',
            ])
        </div>

        <div class="learn-tab-panel" role="tabpanel" id="practice-test" aria-labelledby="learn-tab-practice-test" hidden>
            @include('account.courses.partials.tests', [
                'tests' => $practiceTests,
                'empty' => 'No practice test has been published for this course yet.',
                'note' => null,
            ])
        </div>

        @isset($tabs['video'])
            <div class="learn-tab-panel" role="tabpanel" id="video" aria-labelledby="learn-tab-video" hidden>
                @include('account.courses.partials.video-table', ['videos' => $course->videos])
            </div>
        @endisset
    </section>

    @isset($tabs['video'])
        @include('account.courses.partials.video-dialog')
    @endisset
@endsection
