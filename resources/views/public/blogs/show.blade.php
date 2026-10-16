@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/blogs.css') }}?v={{ @filemtime(public_path('assets/css/blogs.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <article class="blog-article">
        <div class="wrap blog-read">
            <p class="blog-crumb"><a href="{{ route('blogs.index') }}">Blogs</a> <span aria-hidden="true">/</span> {{ $blog->title }}</p>
            @if ($blog->category)
                @include('public.blogs.partials.category', ['category' => $blog->category])
            @endif
            <h1>{{ $blog->title }}</h1>
            @if ($blog->published_on)
                <time datetime="{{ $blog->published_on->toDateString() }}">{{ $blog->published_on->format('F j, Y') }}</time>
            @endif
            @if ($blog->image)
                <img class="blog-cover" src="{{ asset($blog->image) }}" alt="{{ $blog->image_alt ?: $blog->title }}">
            @endif
            <div class="blog-body prose">
                {!! $blog->content !!}
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section">
            <div class="wrap">
                <div class="blog-keep">
                    <h2>Keep reading.</h2>
                    <a href="{{ route('blogs.index') }}">All articles</a>
                </div>
                <div class="blog-grid">
                    @foreach ($related as $post)
                        <a class="blog-card" href="{{ route('blogs.show', $post) }}">
                            @if ($post->image)
                                <img src="{{ asset($post->image) }}" alt="{{ $post->image_alt ?: $post->title }}">
                            @endif
                            <div>
                                @if ($post->category)
                                    @include('public.blogs.partials.category', ['category' => $post->category])
                                @endif
                                <h3>{{ $post->title }}</h3>
                                <p>{{ $post->excerpt }}</p>
                                @if ($post->published_on)
                                    <time datetime="{{ $post->published_on->toDateString() }}">{{ $post->published_on->format('M j, Y') }}</time>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('components.public.cta-band')
@endsection
