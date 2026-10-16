@switch($slug)
    @case('self-learning-course')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6.5 12 4l8 2.5v6c0 3.6-2.8 6.6-8 8.5-5.2-1.9-8-4.9-8-8.5v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('exam-simulation-tool')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="6" y="3" width="12" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('pdu-course')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 8.5A8 8 0 1 1 4 13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M4 8V4h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('certification-advisory')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M6 19.2c1.1-2.5 3.2-3.7 6-3.7s4.9 1.2 6 3.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        @break
    @case('corporate-training')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V8l8-4 8 4v11" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 19v-5h6v5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        @break
    @default
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3.5h7l5 5V20a1.5 1.5 0 0 1-1.5 1.5h-10.5A1.5 1.5 0 0 1 5.5 20V5A1.5 1.5 0 0 1 7 3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3.8V9h5.2M8 13h8M8 17h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
@endswitch
