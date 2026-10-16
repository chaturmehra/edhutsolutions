<div class="home-shots" data-shot-track>
    @foreach ($corporateImages as $shot)
        <button class="home-shot" type="button" data-gallery-open data-full="{{ asset($shot->image) }}" data-alt="{{ $shot->alt_text ?: $shot->title }}" aria-label="{{ $shot->alt_text ?: $shot->title }}">
            <img src="{{ asset($shot->image) }}" alt="{{ $shot->alt_text ?: $shot->title }}" loading="lazy">
        </button>
    @endforeach
</div>
<div class="shot-dots" data-shot-dots></div>
