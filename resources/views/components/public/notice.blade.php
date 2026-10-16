<div class="notice" data-notice role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="notice-title">
    <div class="notice-card">
        <span class="notice-mark" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 3 4.5 6.2v5.3c0 4.4 3 7.6 7.5 9.5 4.5-1.9 7.5-5.1 7.5-9.5V6.2L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 8v4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 16h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
        </span>
        <h2 id="notice-title">{{ $site->get('notice_title') }}</h2>
        <p>{{ $site->get('notice_body') }}</p>
        <div class="notice-contacts">
            <a href="mailto:{{ $site->get('email') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $site->get('email') }}
            </a>
            <a href="https://{{ $site->get('website') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M4 12h16M12 4c2.2 2.4 3.3 5.1 3.3 8s-1.1 5.6-3.3 8c-2.2-2.4-3.3-5.1-3.3-8s1.1-5.6 3.3-8Z" stroke="currentColor" stroke-width="1.7"/></svg>
                {{ $site->get('website') }}
            </a>
            <a href="tel:+917042121784">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 4h3l1.2 3.2-1.8 1.1a12 12 0 0 0 5.3 5.3l1.1-1.8L20 13v3a2 2 0 0 1-2.2 2A16 16 0 0 1 5 6.2 2 2 0 0 1 7 4h1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                {{ $site->get('phone_display') }}
            </a>
        </div>
        <button class="btn btn-primary" type="button" data-notice-close>I understand</button>
    </div>
</div>
