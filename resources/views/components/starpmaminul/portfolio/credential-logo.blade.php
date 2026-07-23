@props(['type' => 'award', 'image' => null, 'alt' => ''])

<div {{ $attributes->class(['credential-logo', 'uiu-logo' => $type === 'uiu']) }} @if(!$image) aria-hidden="true" @endif>
    @if($image)
        <img src="{{ \App\Support\StarPmAminulMedia::url($image) }}" alt="{{ $alt }}">
    @else
    @switch($type)
        @case('pmp')
            <svg viewBox="0 0 64 64" role="img">
                <defs>
                    <linearGradient id="pmpGradient" x1="8" y1="5" x2="57" y2="58" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#5AE0A1"/><stop offset="1" stop-color="#167D55"/>
                    </linearGradient>
                </defs>
                <circle cx="32" cy="32" r="28" fill="none" stroke="url(#pmpGradient)" stroke-width="4"/>
                <circle cx="32" cy="32" r="21" fill="none" stroke="currentColor" stroke-opacity=".22" stroke-width="1.5"/>
                <path d="M18 22h12c7 0 11 3.7 11 9.3 0 5.9-4.4 9.7-11.3 9.7H25v8h-7V22Zm7 6v7h4.3c3 0 4.6-1.2 4.6-3.6 0-2.3-1.6-3.4-4.6-3.4H25Z" fill="url(#pmpGradient)"/>
                <path d="m41.5 39.5 3.1 3.2 6.2-7" fill="none" stroke="#E9BD67" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            @break
        @case('csm')
            <svg viewBox="0 0 64 64" role="img">
                <defs>
                    <linearGradient id="scrumGradient" x1="11" y1="8" x2="54" y2="56" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#FFB85A"/><stop offset="1" stop-color="#E8642A"/>
                    </linearGradient>
                </defs>
                <circle cx="32" cy="32" r="27" fill="none" stroke="url(#scrumGradient)" stroke-width="4" stroke-dasharray="130 40" stroke-linecap="round"/>
                <path d="M21 23c4.6-5.2 13-6.1 18.7-2.2M43 41c-4.6 5.2-13 6.1-18.7 2.2" fill="none" stroke="url(#scrumGradient)" stroke-width="3.5" stroke-linecap="round"/>
                <circle cx="23" cy="39" r="4.3" fill="#E9BD67"/><circle cx="41" cy="25" r="4.3" fill="#73F0A9"/>
                <path d="m20.6 28.7 2.6-6 6 2.6M43.4 35.3l-2.6 6-6-2.6" fill="none" stroke="url(#scrumGradient)" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            @break
        @case('uiu')
            <svg viewBox="0 0 72 72" role="img">
                <defs>
                    <linearGradient id="uiuGradient" x1="10" y1="7" x2="61" y2="65" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E9BD67"/><stop offset="1" stop-color="#A66F1F"/>
                    </linearGradient>
                </defs>
                <path d="M36 5 62 15v19c0 16-10 27-26 33C20 61 10 50 10 34V15L36 5Z" fill="none" stroke="url(#uiuGradient)" stroke-width="3"/>
                <path d="M21 26v13c0 8.6 5.6 13 15 13s15-4.4 15-13V26h-7v12.7c0 4.7-2.8 7.1-8 7.1s-8-2.4-8-7.1V26h-7Z" fill="url(#uiuGradient)"/>
                <path d="M19 19h34" stroke="#73F0A9" stroke-width="3" stroke-linecap="round"/><circle cx="36" cy="18.5" r="3.2" fill="#73F0A9"/>
            </svg>
            @break
        @case('education')
            <svg viewBox="0 0 64 64" role="img" fill="none">
                <path d="m7 24 25-13 25 13-25 13L7 24Z" fill="#E9BD67"/><path d="M18 31v13c8 7 20 7 28 0V31" stroke="#73F0A9" stroke-width="4" stroke-linejoin="round"/><path d="M57 25v18" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
            @break
        @default
            <svg viewBox="0 0 64 64" role="img" fill="none">
                <circle cx="32" cy="27" r="18" stroke="#73F0A9" stroke-width="4"/><path d="m23 44-4 14 13-7 13 7-4-14" fill="#E9BD67" fill-opacity=".85"/><path d="m24 27 5 5 11-12" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
    @endswitch
    @endif
</div>
