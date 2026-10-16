<div class="home-stat-row">
    @foreach ([
        ['stat_students', 'Students worldwide'],
        ['stat_pmp', 'PMP Certified Alumni'],
        ['stat_pgmp', 'PgMP Certified Alumni'],
        ['stat_pfmp', 'PfMP Certified Alumni'],
        ['stat_reviews', 'Google 5 Stars Review'],
        ['stat_corporate', 'Corporate Cohort'],
    ] as [$key, $label])
        <article class="home-stat">
            <strong data-count="{{ $site->get($key) }}">{{ $site->get($key) }}</strong>
            <span>{{ $label }}</span>
        </article>
    @endforeach
</div>
