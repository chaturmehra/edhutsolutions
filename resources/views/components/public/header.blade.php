<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $site->get('site_name') }}">
            <span class="brand-mark"><img src="{{ asset($site->get('logo')) }}" alt="{{ $site->get('site_name') }}"></span>
        </a>
        <button class="nav-scrim" type="button" data-nav-close aria-label="Close menu"></button>
        <nav id="site-nav" class="site-nav" data-nav aria-label="Primary">
            <div class="nav-drawer-head">
                <strong>Menu</strong>
                <button class="cart-close" type="button" data-nav-close aria-label="Close menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <div class="nav-scroll">
            <details class="menu menu-mega">
                <summary>Certifications</summary>
                <div class="menu-panel">
                    <div class="nav-sub">
                        @foreach ($navCourses as $course)
                            <a href="{{ route('courses.show', $course) }}">{{ $course->shortLabel() }}®</a>
                        @endforeach
                    </div>
                    <div class="mega mega-certs">
                        <aside class="mega-promo">
                            <span class="mega-badge" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v6c0 4.2 2.8 7.2 7 9 4.2-1.8 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            </span>
                            <h2>Prepare for a PMI certification</h2>
                            <p>PMI-approved instruction for PMP, PgMP, PfMP, and PMI-RMP, online and in person.</p>
                            <a class="btn mega-demo" href="{{ route('contact') }}">Request a Demo <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                            <a class="mega-help" href="{{ route('services.show', 'certification-advisory') }}">
                                <span class="mega-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 10a4 4 0 1 1 8 0v1a4 4 0 0 1-4 4h-1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 19h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
                                </span>
                                <span>
                                    <strong>Not sure which certification is right for you?</strong>
                                    <small>A 30-minute call to match a credential to your experience.</small>
                                    <em>Certification Advisory <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></em>
                                </span>
                            </a>
                        </aside>
                        <div class="mega-main">
                            <div class="mega-perks">
                                <article>
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M4 12h16M12 4c2.2 2.4 3.3 5.1 3.3 8s-1.1 5.6-3.3 8c-2.2-2.4-3.3-5.1-3.3-8s1.1-5.6 3.3-8Z" stroke="currentColor" stroke-width="1.7"/></svg></span>
                                    <strong>PMI Authorized Partner</strong>
                                    <span>PMI-approved instruction</span>
                                </article>
                                <article>
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 16.5 9 12l3 3 8-8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 7h6v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                    <strong>Career preparation</strong>
                                    <span>Project, program, and portfolio paths</span>
                                </article>
                                <article>
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 4h8v3a4 4 0 0 1-8 0V4Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 5H6a3 3 0 0 0 3 3M16 5h2a3 3 0 0 1-3 3M9 14h6M10 18h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                                    <strong>Exam preparation</strong>
                                    <span>Writing help, practice, and PDUs</span>
                                </article>
                                <article>
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 21h8M12 17v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                                    <strong>Flexible study</strong>
                                    <span>Live cohorts or self-paced</span>
                                </article>
                            </div>
                            <p class="mega-kicker"><span>Explore our</span> Certifications</p>
                            <div class="mega-courses">
                                @foreach ($navCourses as $course)
                                    <a class="mega-course" href="{{ route('courses.show', $course) }}">
                                        <span class="mega-icon" aria-hidden="true">
                                            @switch($course->slug)
                                                @case('pgmp-certification-training')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 9.5 12 5l9 4.5-9 4.5L3 9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M7 11.5v4c1.6 1.4 3.2 2 5 2s3.4-.6 5-2v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                                    @break
                                                @case('pfmp-certification-training')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 5h9a3 3 0 0 1 3 3v11H8a2 2 0 0 0-2 2V5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M6 19a2 2 0 0 1 2-2h10" stroke="currentColor" stroke-width="1.7"/></svg>
                                                    @break
                                                @case('pmi-rmp-certification-training')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v6c0 4.2 2.8 7.2 7 9 4.2-1.8 7-4.8 7-9V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                                                    @break
                                                @default
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M6 19c1.2-2.4 3.3-3.6 6-3.6s4.8 1.2 6 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                            @endswitch
                                        </span>
                                        <strong>{{ $course->title }}®</strong>
                                        <span>{{ $course->menuLine() }}</span>
                                        <em>Learn more <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></em>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </details>
            <details class="menu menu-mega">
                <summary>Services</summary>
                <div class="menu-panel">
                    @php
                        $menuServices = $navServices->keyBy('slug');
                        $writing = $menuServices->get('application-writing-support');
                        $selfStudy = $menuServices->get('self-learning-course');
                        $examTool = $menuServices->get('exam-simulation-tool');
                        $pdu = $menuServices->get('pdu-course');
                        $advisory = $menuServices->get('certification-advisory');
                    @endphp
                    <div class="nav-sub nav-sub-services">
                        @foreach ([$writing, $selfStudy, $examTool, $pdu, $advisory] as $service)
                            @if ($service && $loop->index < 3 && $service->plans->isNotEmpty())
                                <div class="nav-sub-group">
                                    <p class="nav-sub-label">{{ $service->title }}</p>
                                    @foreach ($service->plans as $plan)
                                        <a href="{{ route('services.show', $service) }}?plan={{ urlencode($plan->label) }}">{{ $plan->label }}® - {{ $service->title }}</a>
                                    @endforeach
                                </div>
                            @elseif ($service)
                                <a class="nav-sub-single" href="{{ route('services.show', $service) }}">{{ $service->title }}</a>
                            @endif
                        @endforeach
                    </div>
                    <div class="mega mega-services">
                        <aside class="mega-intro">
                            <span class="mega-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <h2>Support along the certification path</h2>
                            <p>Writing help, self-paced study, exam practice, and a short advisory call.</p>
                            <a class="mega-help mega-help-soft" href="{{ route('contact') }}">
                                <span class="mega-icon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 10a4 4 0 1 1 8 0v1a4 4 0 0 1-4 4h-1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M12 19h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
                                </span>
                                <span>
                                    <strong>Not sure what you need?</strong>
                                    <small>Talk through the options before you choose a service.</small>
                                    <em>Talk to advisor <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></em>
                                </span>
                            </a>
                        </aside>
                        @if ($writing)
                            <div class="mega-col">
                                <p class="mega-label">{{ $writing->title }}</p>
                                <p class="mega-sub">Includes a rewrite if PMI rejects the application</p>
                                @foreach ($writing->plans as $plan)
                                    <a class="mega-link" href="{{ route('services.show', $writing) }}?plan={{ urlencode($plan->label) }}">
                                        <span class="mega-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M7 3h7l5 5v13H7V3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3v5h5M9 13h6M9 17h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                                        {{ $plan->label }} - {{ $writing->title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <div class="mega-col">
                            @if ($selfStudy)
                                <section>
                                    <p class="mega-label">{{ $selfStudy->title }}</p>
                                    <p class="mega-sub">Learn at your own pace</p>
                                    @foreach ($selfStudy->plans as $plan)
                                        <a class="mega-link" href="{{ route('services.show', $selfStudy) }}?plan={{ urlencode($plan->label) }}">
                                            <span class="mega-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 5h9a3 3 0 0 1 3 3v11H8a2 2 0 0 0-2 2V5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
                                            {{ $plan->label }} - {{ $selfStudy->title }}
                                        </a>
                                    @endforeach
                                </section>
                            @endif
                            @if ($examTool)
                                <section>
                                    <p class="mega-label">{{ $examTool->title }}</p>
                                    <p class="mega-sub">Practice questions with explained answers</p>
                                    @foreach ($examTool->plans as $plan)
                                        <a class="mega-link" href="{{ route('services.show', $examTool) }}?plan={{ urlencode($plan->label) }}">
                                            <span class="mega-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="3" stroke="currentColor" stroke-width="1.7"/><path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                            {{ $plan->label }} - {{ $examTool->title }}
                                        </a>
                                    @endforeach
                                </section>
                            @endif
                        </div>
                        <div class="mega-col mega-side">
                            @if ($pdu)
                                <a class="mega-side-link" href="{{ route('services.show', $pdu) }}">
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 4h8v3a4 4 0 0 1-8 0V4Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 5H6a3 3 0 0 0 3 3M16 5h2a3 3 0 0 1-3 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                                    <span><strong>{{ $pdu->title }}</strong><small>60-hour PMI-approved PDU course</small></span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                            @if ($advisory)
                                <a class="mega-side-link" href="{{ route('services.show', $advisory) }}">
                                    <span class="mega-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M6 19c1.2-2.4 3.3-3.6 6-3.6s4.8 1.2 6 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
                                    <span><strong>{{ $advisory->title }}</strong><small>30-minute one-to-one consultation</small></span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </details>
            <a href="{{ route('services.show', 'corporate-training') }}">Corporate Training</a>
            <a href="{{ route('blogs.index') }}">Blogs</a>
            <a href="{{ route('contact') }}">Contact Us</a>
            </div>
            <div class="nav-account">
                @auth
                    <a class="drawer-account" href="{{ route('account.dashboard') }}" aria-label="Account">
                        <span class="user-chip">@include('components.public.user-avatar', ['user' => auth()->user()])</span>
                    </a>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign In</a>
                @endauth
            </div>
        </nav>
        <div class="header-tools">
            <a class="btn btn-demo" href="{{ route('contact') }}">Request a Demo <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
            <button class="cart-link" type="button" data-cart-open aria-expanded="false" aria-controls="cart-drawer" aria-label="Cart, {{ $cartItems->count() }} items">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6.5 7h14l-1.4 8.2a1.5 1.5 0 0 1-1.48 1.25H9.1a1.5 1.5 0 0 1-1.47-1.2L6.1 4.8A1.2 1.2 0 0 0 4.93 4H3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="20" r="1.3" fill="currentColor"/><circle cx="17.5" cy="20" r="1.3" fill="currentColor"/></svg>
                <span class="cart-count">{{ $cartItems->count() }}</span>
            </button>
            @auth
                <a class="header-avatar user-chip" href="{{ route('account.dashboard') }}" aria-label="Account">
                    @include('components.public.user-avatar', ['user' => auth()->user()])
                </a>
            @endauth
            <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="site-nav" aria-label="Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
            @guest
                <a class="header-signin" href="{{ route('login') }}">Sign In</a>
            @endguest
        </div>
    </div>
</header>
