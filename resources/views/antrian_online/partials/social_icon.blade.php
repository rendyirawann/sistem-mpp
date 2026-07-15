@php $p = $platform ?? 'website'; @endphp
@switch($p)
    @case('instagram')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.6" cy="6.4" r="1.1" fill="currentColor" stroke="none"/></svg>
        @break
    @case('tiktok')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 3c.35 2.13 1.6 3.6 3.7 3.85v2.96c-1.36.04-2.66-.34-3.78-1.05v5.79a5.9 5.9 0 1 1-5.9-5.9c.32 0 .63.03.94.08v3.06a2.86 2.86 0 1 0 2.01 2.73V3h3.03z"/></svg>
        @break
    @case('facebook')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3l.5-3H14V4.5c0-.85.3-1.5 1.6-1.5H17V.2C16.6.13 15.7 0 14.7 0 12.4 0 11 1.4 11 3.9V6H8v3h3v9h3V9z"/></svg>
        @break
    @case('youtube')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 7.5a3 3 0 0 0-2.1-2.1C19 4.9 12 4.9 12 4.9s-7 0-8.9.5A3 3 0 0 0 1 7.5 31 31 0 0 0 .5 12 31 31 0 0 0 1 16.5a3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 23.5 12 31 31 0 0 0 23 7.5zM9.8 15.3V8.7l5.7 3.3-5.7 3.3z"/></svg>
        @break
    @case('twitter')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.2 2H21l-6.5 7.4L22 22h-6.2l-4.9-6.4L5.3 22H2.5l7-8L2 2h6.3l4.4 5.8L18.2 2zm-2.2 18h1.7L7.9 3.8H6.1L16 20z"/></svg>
        @break
    @case('linkedin')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5A2.5 2.5 0 1 1 5 8.5a2.5 2.5 0 0 1-.02-5zM3.3 9h3.4v11.5H3.3zM9.2 9h3.26v1.57h.05c.45-.86 1.56-1.77 3.2-1.77 3.43 0 4.06 2.26 4.06 5.2v6.5h-3.4v-5.76c0-1.37-.02-3.14-1.91-3.14-1.92 0-2.21 1.5-2.21 3.04v5.86H9.2z"/></svg>
        @break
    @case('whatsapp')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.5-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1-1.4-1-2.6 0-1.2.6-1.8.9-2 .2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 1.9c.1.2.1.3 0 .5l-.4.5c-.2.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.2.1.4.1.5-.1l.7-.9c.2-.2.4-.2.6-.1l1.8.9c.3.1.4.2.5.3 0 .1 0 .6-.2 1.2z"/></svg>
        @break
    @case('telegram')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3 18.6 20c-.2 1-.9 1.3-1.8.8l-4.9-3.6-2.4 2.3c-.3.3-.5.5-1 .5l.3-4.9 8.9-8c.4-.3-.1-.5-.6-.2L6.4 13 1.7 11.5c-1-.3-1-1 .2-1.5l18.4-7.1c.9-.3 1.7.2 1.6 1.4z"/></svg>
        @break
    @case('email')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
        @break
    @default
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
@endswitch
