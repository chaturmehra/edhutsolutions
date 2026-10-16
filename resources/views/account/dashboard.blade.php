@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
    @include('account.partials.top', ['heading' => 'Dashboard'])

    <section class="stat-row" aria-label="Learning summary">
        <article class="stat">
            <span class="stat-icon is-purple" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 9.5 12 5l8 4.5-8 4.5L4 9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8 12v4.2c1.3 1 2.6 1.5 4 1.5s2.7-.5 4-1.5V12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <strong>{{ str_pad((string) $enrolledCount, 2, '0', STR_PAD_LEFT) }}</strong>
                <span>Enrolled courses</span>
            </div>
        </article>
        <article class="stat">
            <span class="stat-icon is-blue" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 20h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <strong>{{ str_pad((string) $openCount, 2, '0', STR_PAD_LEFT) }}</strong>
                <span>Open courses</span>
            </div>
        </article>
        <article class="stat">
            <span class="stat-icon is-green" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/></svg>
            </span>
            <div>
                <strong>{{ str_pad((string) $passedCount, 2, '0', STR_PAD_LEFT) }}</strong>
                <span>Quizzes passed</span>
            </div>
        </article>
        <article class="stat">
            <span class="stat-icon is-orange" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v4l2.5 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <strong>{{ str_pad((string) $pendingCount, 2, '0', STR_PAD_LEFT) }}</strong>
                <span>Pending orders</span>
            </div>
        </article>
    </section>

    <section class="learn-card">
        <header class="card-head">
            <h2>Study files</h2>
            <a href="{{ route('account.courses.index') }}">All my courses</a>
        </header>
        @if ($files->isEmpty())
            <p class="muted">PDFs and videos appear here after an administrator approves a course that has study files.</p>
        @else
            <div class="file-list">
                @foreach ($files as $file)
                    <a class="file-row" href="{{ $file['href'] }}">
                        <span class="file-kind">{{ $file['kind'] }}</span>
                        <span>
                            <strong>{{ $file['title'] }}</strong>
                            <small>{{ $file['course'] }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <div class="learn-split">
        <section class="learn-card">
            <header class="card-head">
                <h2>Learning</h2>
                @if ($current)
                    <span class="pill is-ok">Approved</span>
                @endif
            </header>
            @if ($current?->course)
                <p class="insight-kicker">{{ $current->course->shortLabel() }}</p>
                <h3>{{ $current->course->title }}</h3>
                <p class="muted">This course is open. Study files stay available while the enrollment is active.</p>
                <a class="btn btn-primary" href="{{ route('learn.show', $current->course) }}">Continue learning</a>
            @else
                <h3>No open course yet</h3>
                <p class="muted">Place an order from a course page. You can open the files only after an administrator approves it.</p>
                <a class="btn btn-primary" href="{{ route('courses.index') }}">Browse courses</a>
            @endif
        </section>
        <section class="learn-card">
            <header class="card-head">
                <h2>Recent activity</h2>
            </header>
            @forelse ($activity as $item)
                <a class="activity-row" href="{{ $item['href'] }}">
                    <span class="dot is-{{ $item['tone'] }}" aria-hidden="true"></span>
                    <span>
                        <strong>{{ $item['title'] }}</strong>
                        <small>{{ $item['meta'] }}@if ($item['at']) · {{ $item['at']->diffForHumans() }}@endif</small>
                    </span>
                </a>
            @empty
                <p class="muted">Orders and quiz results will show up here.</p>
            @endforelse
        </section>
    </div>

    <div class="learn-split">
        <section class="learn-card">
            <header class="card-head">
                <h2>Latest quiz</h2>
                <a href="{{ route('account.quizzes.index') }}">View all</a>
            </header>
            @if ($latestAttempt?->quiz)
                <p class="score-line {{ $latestAttempt->passed ? 'is-pass' : 'is-fail' }}">{{ rtrim(rtrim(number_format((float) $latestAttempt->percentage, 1), '0'), '.') }}%</p>
                <p>{{ $latestAttempt->quiz->title }}</p>
                <p class="muted">{{ $latestAttempt->passed ? 'Passed' : 'Not passed' }}</p>
                <a class="btn btn-line" href="{{ route('quizzes.results', [$latestAttempt->quiz, $latestAttempt]) }}">Review result</a>
            @else
                <p class="muted">No quiz has been submitted yet. Quizzes for an approved course are listed under Quizzes.</p>
            @endif
        </section>
        <section class="learn-card">
            <header class="card-head">
                <h2>Latest order</h2>
                <a href="{{ route('account.orders.index') }}">View all</a>
            </header>
            @if ($latestOrder)
                <p><a href="{{ route('account.orders.show', $latestOrder) }}">{{ $latestOrder->number }}</a></p>
                <p class="muted">{{ ucfirst($latestOrder->status) }} · {{ $latestOrder->items->pluck('title')->join(', ') }}</p>
            @else
                <p class="muted">You have not placed an order yet.</p>
            @endif
        </section>
    </div>

    <section class="learn-block">
        <header class="card-head">
            <h2>Recently enrolled</h2>
            <a href="{{ route('account.courses.index') }}">My courses</a>
        </header>
        @if ($recent->isEmpty())
            <p class="muted">Approved courses will appear here.</p>
        @else
            <div class="rec-grid">
                @foreach ($recent as $enrollment)
                    @if ($enrollment->course)
                        <article class="program-card">
                            <div class="program-media">
                                @include('account.partials.thumb', ['course' => $enrollment->course])
                                <span class="pill {{ $enrollment->status === 'active' ? 'is-ok' : 'is-wait' }}">{{ $enrollment->status === 'active' ? 'Approved' : ucfirst($enrollment->status) }}</span>
                            </div>
                            <h3>{{ $enrollment->course->title }}</h3>
                            <p>{{ $enrollment->course->duration ?: $enrollment->course->level }}</p>
                            @if ($enrollment->status === 'active')
                                <a class="btn btn-primary" href="{{ route('learn.show', $enrollment->course) }}">Continue</a>
                            @endif
                        </article>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    <section class="learn-block">
        <header class="card-head">
            <h2>Other programs</h2>
            <a href="{{ route('courses.index') }}">All courses</a>
        </header>
        <div class="rec-grid">
            @foreach ($recommended as $course)
                <article class="program-card">
                    <div class="program-media">
                        @include('account.partials.thumb', ['course' => $course])
                    </div>
                    <h3>{{ $course->title }}</h3>
                    <p class="price">{{ $course->priceLabel() }}</p>
                    <a class="btn btn-line" href="{{ route('courses.show', $course) }}">View course</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection
