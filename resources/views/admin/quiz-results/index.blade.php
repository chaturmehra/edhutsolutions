@extends('layouts.admin')

@section('content')
    @php $number = fn ($value) => rtrim(rtrim(number_format((float) $value, 1), '0'), '.'); @endphp
    <section class="panel-head">
        <div>
            <h1>Quiz results</h1>
            <p>Every learner attempt, with score and answers.</p>
        </div>
    </section>
    <section class="panel list-panel">
        <form class="list-tools" method="get">
            <label class="search-shell">
                <span class="visually-hidden">Search</span>
                @include('components.admin.icon', ['name' => 'search'])
                <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search student or email">
            </label>
            <select class="select" name="quiz" aria-label="Quiz" data-auto-submit>
                <option value="">All quizzes</option>
                @foreach ($quizzes as $id => $quizTitle)
                    <option value="{{ $id }}" @selected((int) request('quiz') === $id)>{{ $quizTitle }}</option>
                @endforeach
            </select>
            <select class="select" name="result" aria-label="Result" data-auto-submit>
                <option value="">All results</option>
                <option value="passed" @selected(request('result') === 'passed')>Passed</option>
                <option value="failed" @selected(request('result') === 'failed')>Not passed</option>
                <option value="open" @selected(request('result') === 'open')>In progress</option>
            </select>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Quiz</th><th>Score</th><th>Result</th><th>Submitted</th><th class="cell-actions">Actions</th></tr></thead>
                <tbody>
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td>
                                {{ $attempt->user?->name ?? 'Removed user' }}
                                <span class="meta cell-sub">{{ $attempt->user?->email }}</span>
                            </td>
                            <td>{{ $attempt->quiz?->title ?? 'Removed quiz' }}</td>
                            <td>
                                @if ($attempt->submitted_at)
                                    <strong>{{ $number($attempt->percentage) }}%</strong>
                                    <span class="meta cell-sub">{{ $number($attempt->score) }} / {{ $number($attempt->max_score) }}</span>
                                @else
                                    <span class="meta">—</span>
                                @endif
                            </td>
                            <td>
                                @if (! $attempt->submitted_at)
                                    <span class="tag is-wait">In progress</span>
                                @elseif ($attempt->passed)
                                    <span class="tag">Passed</span>
                                @else
                                    <span class="tag is-bad">Not passed</span>
                                @endif
                            </td>
                            <td>{{ ($attempt->submitted_at ?? $attempt->started_at)?->format('d M Y, h:i A') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="act act-view" href="{{ route('admin.quiz-results.show', $attempt) }}">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No quiz attempts match this search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.pager', ['paginator' => $attempts])
    </section>
@endsection
