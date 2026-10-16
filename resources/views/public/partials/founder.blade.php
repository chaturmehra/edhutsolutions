<section class="home-founder">
    <div class="wrap home-founder-grid">
        <figure class="home-founder-photo">
            <img src="{{ asset('assets/images/brand/hero-mentor.jpg') }}" alt="EdHut mentor">
            <figcaption>
                <strong>EdHut</strong>
                <span>Founder • Global P3M Mentor • Author</span>
            </figcaption>
        </figure>
        <div class="home-founder-copy">
            <div class="home-founder-head">
                <p class="kicker">Meet the founder</p>
                <h2>Helping project, program and portfolio professionals earn <em>PMI credentials.</em></h2>
            </div>
            <div class="home-glass-grid">
                <article class="home-glass"><strong>{{ $site->get('stat_students') }}</strong><span>Professionals trained worldwide</span></article>
                <article class="home-glass"><strong>{{ $site->get('stat_pmp') }}</strong><span>PMP certified alumni</span></article>
                <article class="home-glass"><strong>{{ $site->get('stat_pgmp') }}</strong><span>PgMP certified alumni</span></article>
                <article class="home-glass"><strong>{{ $site->get('stat_pfmp') }}</strong><span>PfMP certified alumni</span></article>
                <article class="home-glass"><strong>{{ $site->get('stat_reviews') }}</strong><span>Google 5-star reviews</span></article>
                <article class="home-glass"><strong>{{ $site->get('stat_corporate') }}</strong><span>Corporate cohorts delivered</span></article>
            </div>
            <blockquote>
                <p>Very engaging and interactive sessions for the PMP certification. Mr. Asjad drives focus to the key details. The training was planned and delivered so it could be understood.</p>
                <footer>Philip John</footer>
            </blockquote>
        </div>
    </div>
</section>
