@switch($name)
    @case('building')
        <path d="M4 21V5l8-2v18M12 9h8v12M2.5 21h19M7.5 8h1M7.5 12h1M7.5 16h1M15.5 13h1M15.5 17h1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('target')
        <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="1.2" fill="currentColor"/>
        @break
    @case('team')
        <circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 19c.9-3 3-4.6 5.5-4.6s4.6 1.6 5.5 4.6M15.5 5.2a3 3 0 0 1 0 5.6M17.5 14.6c1.6.6 2.6 2.1 3 4.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        @break
    @case('users')
        <path d="M16 19v-1.5a3.5 3.5 0 00-3.5-3.5h-5A3.5 3.5 0 004 17.5V19M10 10.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM20 19v-1.5a3.5 3.5 0 00-2.5-3.35M15.5 3.65a3.5 3.5 0 010 6.7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('person-check')
        <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3 19.5c.9-3.1 3.3-4.8 6-4.8s5.1 1.7 6 4.8M15.5 11.5l2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('trainer')
        <circle cx="10" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M4 19.5c.9-3.1 3.3-4.8 6-4.8 1.3 0 2.5.4 3.5 1.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="17.5" cy="17" r="2.3" stroke="currentColor" stroke-width="1.6"/>
        @break
    @case('document')
        <rect x="4" y="4" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 9h8M8 12.5h8M8 16h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        @break
    @case('edit')
        <path d="M14 4H6.5A1.5 1.5 0 0 0 5 5.5v13A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M17.5 3.5l3 3-7 7H10.5v-3l7-7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('globe')
        <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 12h17M12 3.5c2.3 2.4 3.5 5.2 3.5 8.5s-1.2 6.1-3.5 8.5c-2.3-2.4-3.5-5.2-3.5-8.5S9.7 5.9 12 3.5z" stroke="currentColor" stroke-width="1.7"/>
        @break
    @case('check')
        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('clock')
        <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        @break
    @case('badge-check')
        <path d="M12 3l2.1 1.6 2.6-.2.9 2.5 2.3 1.3-.6 2.6.9 2.5-2.1 1.6-.5 2.6-2.6.3L12 21l-2.1-1.6-2.6.2-.9-2.5-2.3-1.3.6-2.6-.9-2.5 2.1-1.6.5-2.6 2.6-.3L12 3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m8.8 12 2.2 2.2 4.2-4.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('user-cog')
        <circle cx="10" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M3.5 20c.6-3.4 3.2-5.5 6.5-5.5 1 0 1.9.2 2.7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="17.5" cy="17.5" r="2" stroke="currentColor" stroke-width="1.6"/><path d="M17.5 13.8v1.2M17.5 20v1.2M13.8 17.5H15M20 17.5h1.2M14.9 14.9l.8.8M19.3 19.3l.8.8M14.9 20.1l.8-.8M19.3 15.7l.8-.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        @break
    @case('user')
        <circle cx="12" cy="8" r="3.8" stroke="currentColor" stroke-width="1.7"/><path d="M4.5 20c.8-3.6 3.8-5.8 7.5-5.8s6.7 2.2 7.5 5.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        @break
    @case('map')
        <path d="M9 4.5L3.5 6.5v13L9 17.5l6 2 5.5-2v-13L15 6.5l-6-2zM9 4.5v13M15 6.5v13" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('calendar')
        <rect x="4" y="5.5" width="16" height="14.5" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M4 10h16M8.5 3.5v4M15.5 3.5v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8.5 13.5h.01M12 13.5h.01M15.5 13.5h.01M8.5 16.5h.01M12 16.5h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        @break
    @case('trend')
        <path d="M4 17l6-6 4 4 6-7M14 8h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('bulb')
        <path d="M9 18h6M10 21h4M12 3a6 6 0 00-3.6 10.8c.6.45 1 1.15 1 1.9V16h5.2v-.3c0-.75.4-1.45 1-1.9A6 6 0 0012 3z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('play')
        <path d="M8 5.5v13l10-6.5-10-6.5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        @break
    @case('layers')
        <path d="M12 3l8 4.5-8 4.5-8-4.5L12 3zM4 12l8 4.5 8-4.5M4 16.5L12 21l8-4.5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('layers-2')
        <path d="M12 3l8 4.5-8 4.5-8-4.5L12 3zM4 12l8 4.5 8-4.5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('clipboard')
        <rect x="5" y="4" width="14" height="17" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M9 4V3h6v1M9 11l2 2 4-4M9 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('clipboard-check')
        <rect x="5" y="4" width="14" height="17" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M9 11l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('book')
        <path d="M3 5.5C5.5 4 8.5 4 12 6c3.5-2 6.5-2 9-.5V19c-2.5-1.5-5.5-1.5-9 .5-3.5-2-6.5-2-9-.5V5.5zM12 6v13.5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('folder')
        <path d="M4 6.5A1.5 1.5 0 015.5 5H10l2 2h6.5A1.5 1.5 0 0120 8.5v9a1.5 1.5 0 01-1.5 1.5h-13A1.5 1.5 0 014 17.5v-11z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
        @break
    @case('shield')
        <path d="M12 3l7 3v5.5c0 4.2-2.9 7.9-7 9.5-4.1-1.6-7-5.3-7-9.5V6l7-3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('refresh')
        <path d="M19.5 12a7.5 7.5 0 01-13.3 4.8M4.5 12a7.5 7.5 0 0113.3-4.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M18.5 3.5v4h-4M5.5 20.5v-4h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('mail')
        <rect x="3.5" y="5.5" width="17" height="13" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('monitor')
        <rect x="3.5" y="4.5" width="17" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M9 20h6M12 16.5V20M9.5 10.5l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('cap')
        <path d="M2.5 9.5L12 5l9.5 4.5L12 14 2.5 9.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M6.5 11.5v4c1.4 1.4 3.3 2 5.5 2s4.1-.6 5.5-2v-4M21.5 9.5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        @break
    @case('chart')
        <path d="M4 20h16M7 16v-4M11.5 16V8M16 16v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        @break
    @default
        <circle cx="12" cy="12" r="3" fill="currentColor"/>
@endswitch
