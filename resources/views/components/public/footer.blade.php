<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" aria-label="{{ $site->get('site_name') }}">
                    <img class="footer-logo" src="{{ asset($site->get('logo')) }}" alt="{{ $site->get('site_name') }}">
                </a>
                <p>{{ $site->get('footer_text') }}</p>
                <p class="footer-atp">PMI Authorized Training Partner</p>
            </div>
            <div>
                <h2>Certifications</h2>
                <ul class="footer-links">
                    @foreach ($navCategories as $category)
                        @foreach ($category->courses as $course)
                            <li><a href="{{ route('courses.show', $course) }}">{{ $course->shortLabel() }}®</a></li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            <div>
                <h2>Company</h2>
                <ul class="footer-links">
                    <li><a href="{{ route('pages.show', 'about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('pages.show', 'refund-cancellation-transfer-policy') }}">Refund, Cancellation &amp; Transfer Policy</a></li>
                    <li><a href="{{ route('pages.show', 'privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('pages.show', 'terms') }}">Terms of Use</a></li>
                </ul>
            </div>
            <div>
                <h2>Reach Us</h2>
                <div class="reach-list">
                    <a class="reach-item" href="mailto:{{ $site->get('email') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.6"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <span>{{ $site->get('email') }}</span>
                    </a>
                    <div class="reach-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 4h3l1.2 3.2-1.8 1.1a12 12 0 0 0 5.3 5.3l1.1-1.8L20 13v3a2 2 0 0 1-2.2 2A16 16 0 0 1 4 6.2 2 2 0 0 1 6 4h2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        <span><span class="reach-label">Phone</span><a href="tel:+917042121784">{{ $site->get('phone_display') }}</a></span>
                    </div>
                    <a class="reach-item" href="https://wa.me/{{ $site->get('whatsapp') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21a9 9 0 1 0-7.7-4.4L3 21l4.5-1.2A9 9 0 0 0 12 21Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        <span><span class="reach-label">WhatsApp (preferred mode)</span>{{ $site->get('phone_display') }}</span>
                    </a>
                    <p class="reach-address">{{ $site->get('address') }}</p>
                </div>
                <div class="social-icons">
                    @if (filled($site->get('instagram')))
                        <a href="{{ $site->get('instagram') }}" aria-label="Instagram">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="4" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.6"/><circle cx="17.2" cy="6.8" r="0.8" fill="currentColor"/></svg>
                        </a>
                    @endif
                    @if (filled($site->get('youtube')))
                        <a href="{{ $site->get('youtube') }}" aria-label="YouTube">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="m11 10 4 2-4 2v-4Z" fill="currentColor"/></svg>
                        </a>
                    @endif
                    @if (filled($site->get('facebook')))
                        <a href="{{ $site->get('facebook') }}" aria-label="Facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.6l.4-3H13v-2c0-.6.4-1 1-1Z" fill="currentColor"/></svg>
                        </a>
                    @endif
                    @if (filled($site->get('linkedin')))
                        <a href="{{ $site->get('linkedin') }}" aria-label="LinkedIn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10.5V16M8 8h.01M12 16v-3.2a1.8 1.8 0 0 1 3.2-1.1c.2.3.3.7.3 1.1V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        </a>
                    @endif
                    @if (filled($site->get('x')))
                        <a href="{{ $site->get('x') }}" aria-label="X (Twitter)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4.5h4.2l9.8 15H14.8L5 4.5ZM18.6 4.5 13.4 10.4M10.6 13.6 5.4 19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $site->get('site_name') }}. All rights reserved.</span>
            <span>PMP, PgMP, PfMP and PMI-RMP are registered marks of the Project Management Institute, Inc.</span>
        </div>
    </div>
</footer>
