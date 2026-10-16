<div class="journey" role="group" aria-label="{{ $journey['label'] }}">
    <span class="journey-glow" aria-hidden="true"></span>
    <div class="journey-track">
        @foreach (range(1, count($journey['steps'])) as $arrow)
            <span class="journey-arrow journey-arrow-{{ $arrow }}" aria-hidden="true"></span>
        @endforeach
        <ol class="journey-steps">
            @foreach ($journey['steps'] as $step)
                <li class="journey-step journey-step-{{ $loop->iteration }}">
                    <div class="journey-step-spin">
                        <div class="journey-step-body">
                            <span class="journey-step-num" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <span class="journey-step-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none">@include('components.public.icon', ['name' => $step['icon']])</svg></span>
                            <span class="journey-step-text">{{ $step['text'] }}</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
    <div class="journey-compass" aria-hidden="true">
        <svg class="journey-dial" viewBox="0 0 200 200">
            <defs>
                <radialGradient id="journey-face" cx="50%" cy="40%" r="60%">
                    <stop offset="0" stop-color="#f4f0ff"/>
                    <stop offset="1" stop-color="#e4dcfb"/>
                </radialGradient>
            </defs>
            <circle cx="100" cy="100" r="98" fill="#fff"/>
            <circle cx="100" cy="100" r="86" fill="none" stroke="#e6e0f6" stroke-width="1.5"/>
            <path d="M100 20v12M100 168v12M20 100h12M168 100h12" stroke="#cfc5ee" stroke-width="2" stroke-linecap="round"/>
            <circle cx="100" cy="100" r="62" fill="url(#journey-face)" stroke="#d8cdf6" stroke-width="1.5"/>
        </svg>
        <svg class="journey-needle" viewBox="0 0 200 200">
            <g transform="rotate(-24 100 100)">
                <path d="M100 100L36 104L100 90z" fill="#3b1a9c"/>
                <path d="M100 100L36 104L100 110z" fill="#2a1278"/>
                <path d="M100 100L164 96L100 90z" fill="#7d5cf0"/>
                <path d="M100 100L164 96L100 110z" fill="#5b34ee"/>
            </g>
            <circle cx="100" cy="100" r="11" fill="#2a1278"/>
            <circle cx="100" cy="100" r="6" fill="#fff"/>
            <circle cx="100" cy="100" r="3" fill="#5b34ee"/>
        </svg>
    </div>
</div>
