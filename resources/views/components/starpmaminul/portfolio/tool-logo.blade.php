@props(['type' => 'generic', 'image' => null, 'alt' => ''])

<span {{ $attributes->class('tool-logo') }} @if(!$image) aria-hidden="true" @endif>
    @if($image)
        <img src="{{ \App\Support\StarPmAminulMedia::url($image) }}" alt="{{ $alt }}">
    @else
    @switch($type)
        @case('jira')
            <svg viewBox="0 0 48 48"><path d="M24 5c5.6 7.6 10.8 11.8 18 15.2C34.8 23.6 29.6 28 24 36c-5.7-8-10.8-12.4-18-15.8C13.2 16.8 18.3 12.6 24 5Z" fill="#2684FF"/><path d="M24 14.2c2.8 3.7 5.4 5.8 9 7.5-3.6 1.7-6.2 3.9-9 7.8-2.8-3.9-5.4-6.1-9-7.8 3.6-1.7 6.2-3.8 9-7.5Z" fill="#fff" fill-opacity=".92"/></svg>
            @break
        @case('ms-project')
            <svg viewBox="0 0 48 48"><rect x="4" y="8" width="28" height="32" rx="4" fill="#31752F"/><path d="M13 15h8.1c5.2 0 8.2 2.7 8.2 7.1 0 4.7-3.3 7.5-8.6 7.5H18V36h-5V15Zm5 4.3v6h2.8c2.2 0 3.5-1 3.5-3.1 0-2-1.2-2.9-3.5-2.9H18Z" fill="#fff"/><path d="M32 13h12v22H32" fill="#5CA64C"/><path d="M35 18h6M35 23h6M35 28h6" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
            @break
        @case('confluence')
            <svg viewBox="0 0 48 48"><path d="M12 11c6.5 5.2 15.2 7 24 5.1l3.2 7.3C27.7 26 16.5 23.5 8 16.7L12 11Z" fill="#1868DB"/><path d="M36 37c-6.5-5.2-15.2-7-24-5.1l-3.2-7.3C20.3 22 31.5 24.5 40 31.3L36 37Z" fill="#4C9AFF"/></svg>
            @break
        @case('excel')
            <svg viewBox="0 0 48 48"><rect x="16" y="7" width="28" height="34" rx="3" fill="#21A366"/><path d="M25 13h14M25 20h14M25 27h14M25 34h14M31 10v28" stroke="#fff" stroke-opacity=".72" stroke-width="1.7"/><rect x="4" y="11" width="24" height="27" rx="3" fill="#107C41"/><path d="m11 18 4.2 6-4.5 7h4.7l2.4-4 2.4 4h4.7l-4.5-7 4.2-6H20l-2.1 3.5-2.1-3.5H11Z" fill="#fff"/></svg>
            @break
        @case('power-bi')
            <svg viewBox="0 0 48 48"><rect x="7" y="25" width="7" height="15" rx="2" fill="#F2C811"/><rect x="16" y="18" width="7" height="22" rx="2" fill="#F2C811" opacity=".92"/><rect x="25" y="11" width="7" height="29" rx="2" fill="#F2C811" opacity=".82"/><rect x="34" y="6" width="7" height="34" rx="2" fill="#F2C811" opacity=".68"/></svg>
            @break
        @case('methodology')
            <svg viewBox="0 0 48 48"><circle cx="15" cy="15" r="7" fill="none" stroke="#73F0A9" stroke-width="3" stroke-dasharray="28 10" stroke-linecap="round"/><path d="M24 10h17M28 18h13M32 26h9" stroke="#E9BD67" stroke-width="3" stroke-linecap="round"/><path d="M9 31h8v8H9zM20 31h8v8h-8zM31 31h8v8h-8z" fill="none" stroke="#4C9AFF" stroke-width="2.3"/></svg>
            @break
        @default
            <svg viewBox="0 0 48 48" fill="none"><rect x="7" y="7" width="34" height="34" rx="10" stroke="#73F0A9" stroke-width="3"/><path d="M15 24h18M24 15v18" stroke="#E9BD67" stroke-width="3" stroke-linecap="round"/></svg>
    @endswitch
    @endif
</span>
