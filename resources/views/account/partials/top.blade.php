<header class="learn-top">
    <div>
        <h1>{{ $heading }}</h1>
        <p>Welcome back, {{ auth()->user()->name }} <span>· {{ auth()->user()->learnerCode() }}</span></p>
    </div>
    <a class="btn btn-browse" href="{{ route('courses.index') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Browse courses
    </a>
</header>
