@extends('layouts.public')

@section('content')
    <section class="page-hero">
        <div class="wrap">
            <h1>{{ $page->title }}</h1>
            @if ($page->excerpt)
                <p class="lede">{{ $page->excerpt }}</p>
            @endif
        </div>
    </section>
    <section class="section">
        <div class="wrap prose">
            {!! $page->content !!}
        </div>
    </section>
@endsection
