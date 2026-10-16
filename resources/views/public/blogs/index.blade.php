@extends('layouts.public')

@push('head')
    <link href="{{ asset('assets/css/blogs.css') }}?v={{ @filemtime(public_path('assets/css/blogs.css')) }}" rel="stylesheet">
@endpush

@section('content')
    <section class="blog-hero">
        <div class="wrap blog-intro">
            <p class="kicker">Resources</p>
            <h1>Guides on PMP, PgMP, and PfMP from EdHut.</h1>
            <p>Articles published on edhutsolutions.com about eligibility, who the credentials are for, and why they matter.</p>
        </div>
    </section>

    <section class="section tight">
        <div class="wrap">
            <nav class="blog-tabs" aria-label="Article categories">
                <a class="blog-tab {{ $selected === '' ? 'is-on' : '' }}" href="{{ route('blogs.index') }}">All</a>
                @foreach ($categories as $category)
                    <a class="blog-tab {{ $selected === $category->category ? 'is-on' : '' }}" href="{{ route('blogs.index', ['category' => $category->category]) }}">
                        {{ $category->category }} <span>{{ $category->total }}</span>
                    </a>
                @endforeach
            </nav>
            @if ($posts->isEmpty())
                <p>New articles will appear here.</p>
            @else
                <div class="blog-grid">
                    @foreach ($posts as $post)
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
                {{ $posts->links() }}
            @endif
        </div>
    </section>

    @include('components.public.cta-band')
@endsection
