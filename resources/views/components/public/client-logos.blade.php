<div class="home-logo-grid">
    <div class="logo-track">
        @foreach ($clients as $client)
            <div class="logo-tile home-lift"><img src="{{ asset($client->logo) }}" alt="{{ $client->name }}" loading="lazy"></div>
        @endforeach
        @foreach ($clients as $client)
            <div class="logo-tile home-lift is-clone" aria-hidden="true"><img src="{{ asset($client->logo) }}" alt="" loading="lazy"></div>
        @endforeach
    </div>
</div>
