@switch($name)
    @case('book')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5.5A2.5 2.5 0 0 1 7.5 3H19v16H7.5A2.5 2.5 0 0 0 5 21.5V5.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M5 18.5A2.5 2.5 0 0 1 7.5 16H19" stroke="currentColor" stroke-width="1.7"/></svg>
        @break
    @case('users')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M4 19c.7-2.4 2.6-3.6 5-3.6s4.3 1.2 5 3.6M17 11a2.5 2.5 0 1 0 0-5M16.5 15.5c1.8.3 3.2 1.4 3.5 3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('mail')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('cart')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 6h15l-1.5 8h-12L5 6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M5 6 4 3H2M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM17 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('image')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/><circle cx="9" cy="10" r="1.4" fill="currentColor"/><path d="m6 16 4-3 3 2 2.5-2L18 16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('page')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3.5h7l5 5V20a1.5 1.5 0 0 1-1.5 1.5h-10.5A1.5 1.5 0 0 1 5.5 20V5A1.5 1.5 0 0 1 7 3.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M14 3.8V9h5" stroke="currentColor" stroke-width="1.7"/></svg>
        @break
    @case('search')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('check')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="3.5" width="14" height="17" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m8.5 12 2.2 2.2L15.5 9.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('play')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="m10 9.2 4.8 2.8-4.8 2.8V9.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        @break
    @case('gear')
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M12 3.5v2.2M12 18.3v2.2M4.8 6.8l1.6 1.6M17.6 15.6l1.6 1.6M3.5 12h2.2M18.3 12h2.2M4.8 17.2l1.6-1.6M17.6 8.4l1.6-1.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="4" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="4" y="13" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="13" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
@endswitch
