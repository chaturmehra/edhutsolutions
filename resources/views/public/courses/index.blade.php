@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/services.css') }}?v={{ @filemtime(public_path('assets/css/services.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/courses.css') }}?v={{ @filemtime(public_path('assets/css/courses.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="svc-hero has-world-map">
        <div class="wrap svc-intro">
            <p class="svc-pill"><span class="dot" aria-hidden="true"></span> {{ $courses->total() }} PMI {{ \Illuminate\Support\Str::plural('credential', $courses->total()) }} · PMI Authorized Training Partner</p>
            <h1>Choose your <span>next credential.</span></h1>
            <p>PMP, PgMP, PfMP, and PMI-RMP preparation with PMI Authorized Trainers. Classroom fees are shared on request, then an administrator approves access to the course files.</p>
        </div>
    </section>

    <section class="section svc-list">
        <div class="wrap course-browse">
            <form class="course-filter-card" method="get" action="{{ route('courses.index') }}">
                <h2>Filter</h2>
                <label class="field"><span>Search</span><input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search programs"></label>
                <label class="field"><span>Category</span>
                    <select class="select" name="category">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field"><span>Sort</span>
                    <select class="select" name="sort">
                        <option value="path" @selected(request('sort', 'path') === 'path')>Certification path</option>
                        <option value="title" @selected(request('sort') === 'title')>Title</option>
                        <option value="latest" @selected(request('sort') === 'latest')>Newest</option>
                    </select>
                </label>
                <button class="btn btn-primary" type="submit">Apply</button>
                <a class="btn btn-ghost" href="{{ route('courses.index') }}">Clear filters</a>
            </form>
            <div class="course-results">
            <div class="svc-grid">
                @forelse ($courses as $course)
                    <article class="svc-card">
                        <span class="svc-icon course-mark">{{ $course->shortLabel() }}</span>
                        <h2><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></h2>
                        <p>{{ $course->short_description }}</p>
                        <ul class="svc-fees">
                            @if ($course->duration)<li><span>{{ $course->duration }}</span></li>@endif
                            @if ($course->level)<li><span>{{ $course->level }}</span></li>@endif
                            <li><span>{{ $course->language }}</span></li>
                        </ul>
                        <div class="svc-card-foot">
                            @if (in_array($course->id, $owned['enrolled'], true))
                                <a class="course-owned" href="{{ route('learn.show', $course) }}"><span aria-hidden="true">✓</span> Continue learning</a>
                            @elseif (in_array($course->id, $owned['pending'], true))
                                <span class="course-owned is-wait"><span aria-hidden="true">✓</span> Already purchased</span>
                            @else
                                <p>{{ $course->priceLabel() }}</p>
                            @endif
                            <a class="svc-go" href="{{ route('courses.show', $course) }}" aria-label="View {{ $course->title }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <p>No active courses match that search.</p>
                @endforelse
            </div>
            {{ $courses->links() }}
            </div>
        </div>
    </section>

    @include('components.public.cta-band')
@endsection
