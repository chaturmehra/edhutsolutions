<article class="course-card">
    <a href="{{ route('courses.show', $course) }}">
        @if ($course->thumbnail)
            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->thumbnail_alt ?: $course->title }}" loading="lazy">
        @endif
        <div class="card-body">
            <p class="meta">{{ $course->category?->name }}</p>
            <h3>{{ $course->title }}</h3>
            <p>{{ $course->short_description }}</p>
            <p class="price">{{ $course->priceLabel() }}</p>
        </div>
    </a>
</article>
