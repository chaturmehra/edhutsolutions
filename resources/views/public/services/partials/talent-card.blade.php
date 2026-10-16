<div class="talent-card">
    <h2>{{ $heroCard['title'] }}</h2>
    <p class="talent-card-sub">{{ $heroCard['subtitle'] }}</p>
    <span class="talent-card-rule" aria-hidden="true"></span>
    <svg class="talent-triangle" viewBox="0 0 400 340" role="img" aria-label="{{ $heroCard['subtitle'] }}: {{ implode(', ', $heroCard['triangle']) }}">
        <defs>
            <path id="talent-left" d="M52 300 L200 44"/>
            <path id="talent-right" d="M200 44 L348 300"/>
            <path id="talent-bottom" d="M52 300 L348 300"/>
        </defs>
        <line class="talent-side talent-side-bottom" x1="52" y1="300" x2="348" y2="300"/>
        <line class="talent-side talent-side-left" x1="52" y1="300" x2="200" y2="44"/>
        <line class="talent-side talent-side-right" x1="200" y1="44" x2="348" y2="300"/>
        <text class="talent-label"><textPath href="#talent-left" startOffset="50%">{{ $heroCard['triangle']['left'] }}</textPath></text>
        <text class="talent-label"><textPath href="#talent-right" startOffset="50%">{{ $heroCard['triangle']['right'] }}</textPath></text>
        <text class="talent-label"><textPath href="#talent-bottom" startOffset="50%">{{ $heroCard['triangle']['bottom'] }}</textPath></text>
    </svg>
    <ul class="talent-points">
        @foreach ($heroCard['points'] as $point)
            <li>
                <span class="talent-check" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => 'check'])</svg></span>
                <strong>{{ $point['title'] }}</strong>
                <span>{{ $point['text'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
