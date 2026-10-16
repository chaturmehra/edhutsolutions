<section class="panel-head">
    <div>
        <h1>{{ $heading }}</h1>
        <p>{{ $subtitle ?? $course->title }}</p>
    </div>
    <a class="btn btn-outline" href="{{ $back }}">{{ $backLabel ?? 'Back' }}</a>
</section>
