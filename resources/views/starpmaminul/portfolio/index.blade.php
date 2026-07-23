@php
    $site = $sections['site'];
    $hero = $sections['hero'];
    $ticker = $sections['ticker'];
    $philosophy = $sections['philosophy'];
    $overview = $sections['overview'];
    $impact = $sections['impact'];
    $work = $sections['work'];
    $caseStudy = $sections['case_study'];
    $journey = $sections['journey'];
    $operatingSystem = $sections['operating_system'];
    $global = $sections['global'];
    $testimonials = $sections['testimonials'];
    $mentorship = $sections['mentorship'];
    $credentials = $sections['credentials'];
    $contact = $sections['contact'];
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="{{ $site['meta_description'] }}" name="description"/>
<meta content="#07100d" name="theme-color"/>
<title>{{ $site['page_title'] }}</title>


    @vite(['resources/css/starpmaminul/portfolio.css', 'resources/js/starpmaminul/portfolio.js'])
</head>
<body>

<div aria-hidden="true" class="loader">
<div class="loader-inner">
<div class="loader-mark"><b>{{ $site['brand_initials'] }}</b><span>{{ $site['loader_tagline'] }}</span></div>
<div class="loader-track"><i></i></div>
</div>
</div>
<div aria-hidden="true" class="noise"></div>
<div aria-hidden="true" class="spotlight"></div>
<div aria-hidden="true" class="progress"><span></span></div>
<header class="site-header" id="siteHeader">
<div class="header-inner">
<a aria-label="Go to top" class="brand{{ !empty($site['brand_logo']) ? ' brand-uploaded' : '' }}" href="#top">
@if(!empty($site['brand_logo']))
<img class="brand-logo-image" src="{{ \App\Support\StarPmAminulMedia::url($site['brand_logo']) }}" alt="{{ $site['brand_name'] }}">
@else
<span class="brand-mark">{{ $site['brand_initials'] }}</span>
<span class="brand-copy">{{ $site['brand_name'] }} <small>{{ $site['brand_credentials'] }}</small></span>
@endif
</a>
<nav aria-label="Primary navigation" class="nav">
@foreach($site['navigation'] as $item)
<a href="{{ $item['target'] }}">{{ $item['label'] }}</a>
@endforeach
</nav>
<div class="header-actions">
<button aria-label="Change color theme" class="icon-button" id="themeToggle" title="Change theme">
<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M12 3a9 9 0 1 0 9 9c0-.4 0-.8-.1-1.2A7 7 0 0 1 12 3Z"></path></svg>
</button>
<a class="header-cta" href="{{ 'mailto:'.$site['header_cta_email'] }}">{{ $site['header_cta_text'] }}<svg fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a>
<button aria-expanded="false" aria-label="Open menu" class="menu-button" id="menuToggle">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 8h16M4 16h16"></path></svg>
</button>
</div>
</div>
</header>
<div class="mobile-menu" id="mobileMenu">
<nav>
@foreach($site['mobile_navigation'] as $item)
<a href="{{ $item['target'] }}">{{ $item['label'] }}</a>
@endforeach
</nav>
</div>
<main id="top">
<section aria-labelledby="heroTitle" class="hero section">
<div class="container">
<div class="hero-grid">
<div class="hero-copy">
<div class="status reveal" data-placeholder="Confirm the exact availability wording you want to show publicly."><i></i> {{ $hero['availability'] }}</div>
<h1 class="hero-name reveal delay-1" id="heroTitle">{{ $hero['name_line_one'] }}<br><span class="stroke">{{ $hero['name_line_two'] }}</span></h1>
<div class="hero-role reveal delay-2">
<span class="eyebrow">{{ $hero['role_label'] }}</span>
<p>{{ $hero['role_primary'] }} <span>{{ $hero['role_secondary'] }}</span></p>
</div>
<div class="hero-actions reveal delay-3">
<a class="button primary" href="#work">{{ $hero['primary_button'] }}<svg fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a>
<button class="button secondary" id="printResume">{{ $hero['secondary_button'] }}<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v7H7z"></path></svg>
</button>
</div>
</div>
<div class="portrait-stage reveal delay-2" data-placeholder="Replace this visual with a high-resolution professional portrait. The site already works without it.">
<div class="portrait-card">
<div class="portrait-lines"></div>
<div aria-label="{{ $hero['portrait_alt'] }}" class="portrait-art">@if($hero['portrait'])<img class="portrait-photo" src="{{ \App\Support\StarPmAminulMedia::url($hero['portrait']) }}" alt="{{ $hero['portrait_alt'] }}">@else<svg aria-hidden="true" role="img" viewbox="0 0 500 620">
<defs>
<lineargradient id="g1" x1="0" x2="1" y1="0" y2="1"><stop offset="0" stop-color="#7af2ad" stop-opacity=".96"></stop><stop offset="1" stop-color="#226242" stop-opacity=".28"></stop></lineargradient>
<lineargradient id="g2" x1="0" x2="1" y1="1" y2="0"><stop offset="0" stop-color="#0b1511"></stop><stop offset="1" stop-color="#1c4a35"></stop></lineargradient>
<filter id="blur"><fegaussianblur stddeviation="18"></fegaussianblur></filter>
</defs>
<circle cx="260" cy="190" fill="none" r="142" stroke="#73f0a9" stroke-opacity=".28"></circle>
<circle cx="260" cy="190" fill="url(#g1)" filter="url(#blur)" opacity=".24" r="96"></circle>
<path d="M170 202c0-69 38-118 91-118s91 49 91 118c0 76-41 128-91 128s-91-52-91-128Z" fill="#0c1712" stroke="#73f0a9" stroke-opacity=".48"></path>
<path d="M117 567c14-135 63-219 143-219 79 0 128 84 143 219" fill="url(#g2)" stroke="#73f0a9" stroke-opacity=".28"></path>
<path d="M205 184c12-57 33-84 63-84 31 0 56 29 66 88-22-26-44-35-67-35-24 0-44 10-62 31Z" fill="#183b2c"></path>
<path d="M218 224c13 7 27 10 43 10 17 0 32-4 45-11" fill="none" stroke="#73f0a9" stroke-linecap="round" stroke-opacity=".55"></path>
<path d="M260 235v36" stroke="#73f0a9" stroke-opacity=".34"></path>
<path d="M235 288c18 11 36 11 53 0" fill="none" stroke="#73f0a9" stroke-linecap="round" stroke-opacity=".5"></path>
<path d="M202 368l58 53 58-53" fill="none" stroke="#73f0a9" stroke-opacity=".54"></path>
<path d="M260 421v146" stroke="#73f0a9" stroke-opacity=".18"></path>
<text fill="#73f0a9" fill-opacity=".22" font-family="Arial" font-size="84" font-weight="800" text-anchor="middle" x="260" y="525">AMI</text>
</svg>@endif</div>
<div class="portrait-nameplate">
<div><strong>{{ $hero['profile_name'] }}</strong><small>{{ $hero['profile_role'] }}</small></div>
<span class="portrait-number">{{ $hero['profile_number'] }}</span>
</div>
</div>
@foreach($hero['stats'] as $stat)
<div class="orbit-card {{ ['orbit-a', 'orbit-b', 'orbit-c'][$loop->index] ?? 'orbit-extra' }}" @if($loop->index > 2) style="--orbit-index: {{ $loop->index - 3 }}" @endif><b>{{ $stat['value'] }}</b><small>{{ $stat['label'] }}</small></div>
@endforeach
</div>
</div>
<div class="hero-foot reveal delay-3">
<div class="scroll-cue"><i></i><span>{{ $hero['scroll_text'] }}</span></div>
<span>{{ $hero['location_text'] }}</span>
</div>
</div>
</section>
<div aria-hidden="true" class="ticker">
<div class="ticker-track">
<div class="ticker-item">@foreach($ticker['items'] as $item)<i></i> {{ $item['label'] }} @endforeach</div>
<div class="ticker-item">@foreach($ticker['items'] as $item)<i></i> {{ $item['label'] }} @endforeach</div>
</div>
</div>
<section class="philosophy-cosmos portfolio-surface-a" id="about" aria-labelledby="philosophy-title">
<div class="philosophy-cosmos__noise" aria-hidden="true"></div>
<div class="philosophy-cosmos__container">
<div class="philosophy-cosmos__copy reveal">
<span class="philosophy-cosmos__index">{{ $philosophy['section_index'] }}</span>
<h2 class="philosophy-cosmos__title" id="philosophy-title">{{ $philosophy['title_prefix'] }} <em>{{ $philosophy['title_emphasis'] }}</em></h2>
<p class="philosophy-cosmos__lead">{{ $philosophy['description'] }}</p>
<div class="philosophy-cosmos__manifesto">
<span class="philosophy-cosmos__manifesto-mark" aria-hidden="true">{{ $philosophy['manifesto_mark'] }}</span>
<p>{{ $philosophy['manifesto_intro'] }} <strong>{{ $philosophy['manifesto_core'] }}</strong>. {{ $philosophy['manifesto_body'] }}</p>
</div>
</div>
<div class="philosophy-cosmos__visual-wrap reveal delay-2" aria-label="Leadership values orbiting around {{ $philosophy['center_line_one'].' '.$philosophy['center_line_two'] }}">
<div class="philosophy-cosmos__visual" id="philosophyVisual">
<div class="philosophy-cosmos__visual-glow" aria-hidden="true"></div>
<span class="philosophy-cosmos__axis philosophy-cosmos__axis--a" aria-hidden="true"></span>
<span class="philosophy-cosmos__axis philosophy-cosmos__axis--b" aria-hidden="true"></span>
<span class="philosophy-cosmos__axis philosophy-cosmos__axis--c" aria-hidden="true"></span>
<span class="philosophy-cosmos__axis philosophy-cosmos__axis--d" aria-hidden="true"></span>
<div class="philosophy-cosmos__ring philosophy-cosmos__ring--outer" aria-hidden="true"></div>
<div class="philosophy-cosmos__ring philosophy-cosmos__ring--mid" aria-hidden="true"></div>
<div class="philosophy-cosmos__ring philosophy-cosmos__ring--inner" aria-hidden="true"></div>
<div class="philosophy-cosmos__orbit-label" aria-hidden="true">
<svg viewBox="0 0 400 400" role="presentation"><defs><path id="orbitTextPath" d="M 200,200 m -164,0 a 164,164 0 1,1 328,0 a 164,164 0 1,1 -328,0"/></defs><text><textPath href="#orbitTextPath" startOffset="0%">{{ $philosophy['orbit_text'] }}</textPath></text></svg>
</div>
@for($satellite = 1; $satellite <= 6; $satellite++)
<span class="philosophy-cosmos__satellite philosophy-cosmos__satellite--{{ $satellite }}" aria-hidden="true"></span>
@endfor
<div class="philosophy-cosmos__lane philosophy-cosmos__lane--one" data-philosophy-lane>
@foreach($philosophy['values'] as $value)
@if($loop->index % 2 === 0)
<button class="philosophy-cosmos__node" type="button" data-philosophy-node data-node-index="{{ $loop->index }}" data-node-count="{{ $loop->count }}" aria-label="{{ $value['name'] }}: {{ $value['meaning'] }}">
<span class="philosophy-cosmos__node-inner"><span><b>{{ $value['name'] }}</b><small>{{ $value['meaning'] }}</small><em>{{ $value['detail'] ?? '' }}</em></span></span>
</button>
@endif
@endforeach
</div>
<div class="philosophy-cosmos__lane philosophy-cosmos__lane--two" data-philosophy-lane>
@foreach($philosophy['values'] as $value)
@if($loop->index % 2 === 1)
<button class="philosophy-cosmos__node" type="button" data-philosophy-node data-node-index="{{ $loop->index }}" data-node-count="{{ $loop->count }}" aria-label="{{ $value['name'] }}: {{ $value['meaning'] }}">
<span class="philosophy-cosmos__node-inner"><span><b>{{ $value['name'] }}</b><small>{{ $value['meaning'] }}</small><em>{{ $value['detail'] ?? '' }}</em></span></span>
</button>
@endif
@endforeach
</div>
<div class="philosophy-cosmos__core"><div><small>{{ $philosophy['center_label'] }}</small><strong>{{ $philosophy['center_line_one'] }}<br>{{ $philosophy['center_line_two'] }}</strong><span>{{ $philosophy['center_caption_line_one'] }}<br>{{ $philosophy['center_caption_line_two'] }}</span></div></div>
<div class="philosophy-cosmos__caption" aria-hidden="true"><i></i>{{ $philosophy['interaction_caption'] }}</div>
</div>
</div>
</div>
</section>
<section class="section overview portfolio-surface-b">
<div class="container overview-grid">
<div class="sticky-intro reveal">
<span class="section-index">{{ $overview['section_index'] }}</span>
<h2>{{ $overview['title'] }}</h2>
<p class="lead">{{ $overview['description'] }}</p>
</div>
<div class="cap-list">
@foreach($overview['capabilities'] as $capability)
<article class="cap-item reveal">
<span class="cap-num">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
<div><h3>{{ $capability['title'] }}</h3><p>{{ $capability['description'] }}</p></div>
<span class="cap-icon">
@switch($loop->index % 5)
@case(0)<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M4 17l5-5 4 4 7-9"></path><path d="M15 7h5v5"></path></svg>@break
@case(1)<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><rect height="16" rx="2" width="18" x="3" y="4"></rect><path d="M3 9h18M8 9v11"></path></svg>@break
@case(2)<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>@break
@case(3)<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M4 4h16v16H4z"></path><path d="M4 9h16M9 9v11"></path></svg>@break
@default<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M3 10h18M5 10V7l7-4 7 4v3M5 10v8M9 10v8M15 10v8M19 10v8M3 21h18"></path></svg>
@endswitch
</span>
</article>
@endforeach
</div>
</div>
</section>
<section class="impact section portfolio-surface-a" id="impact">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index">{{ $impact['section_index'] }}</span><h2 class="section-title medium">{{ $impact['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $impact['description'] }}</p>
</div>
<div class="impact-grid">
@foreach($impact['metrics'] as $metric)
<article class="metric-card reveal {{ ($loop->index % 4) ? 'delay-'.($loop->index % 4) : '' }}"><div class="metric-value"><span>{{ $metric['prefix'] }}</span><span class="counter" data-target="{{ $metric['value'] }}" data-decimals="{{ $metric['decimals'] }}">0</span><span>{{ $metric['suffix'] }}</span></div><p>{{ $metric['description'] }}</p></article>
@endforeach
</div>
</div>
</section>
<section class="work section portfolio-surface-b" id="work">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index">{{ $work['section_index'] }}</span><h2 class="section-title">{{ $work['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $work['description'] }}</p>
</div>
<div class="projects">
@foreach($work['projects'] as $project)
<article aria-label="Open {{ $project['title'] }} project details" class="project-card reveal {{ ([0, 1, 0, 1, 2][$loop->index] ?? ($loop->index % 3)) ? 'delay-'.([0, 1, 0, 1, 2][$loop->index] ?? ($loop->index % 3)) : '' }}" data-project="{{ trim((string) ($project['key'] ?? '')) ?: 'project-'.$loop->index }}" role="button" tabindex="0">
<div><div class="project-top"><span class="project-label">{{ $project['label'] }}</span><span class="project-arrow"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"></path></svg></span></div><h3>{{ $project['title'] }}</h3><p>{{ $project['summary'] }}</p></div>
<div class="project-stats">@foreach(array_filter(array_map('trim', explode('|', (string) $project['tags']))) as $tag)<span>{{ $tag }}</span>@endforeach</div>
</article>
@endforeach
</div>
</div>
</section>
<section class="case-study section portfolio-surface-a">
<div class="container case-grid">
<div class="case-sticky reveal">
<span class="section-index">{{ $caseStudy['section_index'] }}</span>
<h2>{{ $caseStudy['title'] }}</h2>
<p class="lead">{{ $caseStudy['description'] }}</p>
</div>
<div>
<div class="case-visual reveal">
<div class="dashboard-bar"><div class="dashboard-dots"><i></i><i></i><i></i></div><span>{{ $caseStudy['dashboard_label'] }}</span></div>
<div class="dashboard-body">
<div class="dash-kpis">
@foreach($caseStudy['kpis'] as $kpi)
<div class="dash-kpi"><b>{{ $kpi['value'] }}</b><small>{{ $kpi['label'] }}</small></div>
@endforeach
</div>
<div class="dash-chart"><span class="chart-title">{{ $caseStudy['chart_title'] }}</span><div class="bars"><i style="--h:23%"></i><i style="--h:34%"></i><i style="--h:31%"></i><i style="--h:46%"></i><i style="--h:55%"></i><i style="--h:68%"></i><i style="--h:76%"></i><i style="--h:88%"></i><i style="--h:100%"></i></div></div>
</div>
</div>
<div class="case-steps">
@foreach($caseStudy['steps'] as $step)
<article class="case-step reveal"><span>{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></article>
@endforeach
</div>
</div>
</div>
</section>
<section class="journey section portfolio-surface-b" id="journey">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index">{{ $journey['section_index'] }}</span><h2 class="section-title medium">{{ $journey['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $journey['description'] }}</p>
</div>
<div class="timeline">
<div class="timeline-line"></div>
@foreach($journey['items'] as $item)
<article class="timeline-item reveal"><span class="timeline-year">{{ $item['period'] }}</span><div class="timeline-content"><div><h3>{{ $item['role'] }}</h3><div class="timeline-company">{{ $item['company'] }}</div></div><p class="timeline-copy">{{ $item['intro'] }} <b>{{ $item['highlight'] }}</b></p></div></article>
@endforeach
</div>
</div>
</section>
<section class="section operating-system portfolio-surface-a">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index">{{ $operatingSystem['section_index'] }}</span><h2 class="section-title medium">{{ $operatingSystem['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $operatingSystem['description'] }}</p>
</div>
<div class="system-grid">
@foreach($operatingSystem['steps'] as $step)
<article class="system-card reveal {{ ($loop->index % 4) ? 'delay-'.($loop->index % 4) : '' }}"><span class="system-no">{{ $step['label'] }}</span><div><div class="system-icon">
@switch($loop->index % 4)
@case(0)<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>@break
@case(1)<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"></path></svg>@break
@case(2)<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M8 12h8M12 8v8"></path><circle cx="12" cy="12" r="9"></circle></svg>@break
@default<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m5 12 4 4L19 6"></path></svg>
@endswitch
</div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></article>
@endforeach
</div>
</div>
</section>
<section class="global section portfolio-surface-b">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index">{{ $global['section_index'] }}</span><h2 class="section-title medium">{{ $global['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $global['description'] }}</p>
</div>
<div class="global-board reveal">
<svg aria-hidden="true" fill="none" viewbox="0 0 1200 470">
<path d="M80 268C210 202 258 238 355 195c91-40 126-117 244-89 108 26 115 122 218 135 99 12 145-51 301-4" opacity=".55" stroke="#73f0a9" stroke-dasharray="8 9" stroke-width="2"></path>
<circle cx="178" cy="230" fill="#73f0a9" r="9"></circle><circle cx="635" cy="121" fill="#e9bd67" r="9"></circle><circle cx="868" cy="238" fill="#73f0a9" r="9"></circle><circle cx="971" cy="231" fill="#73f0a9" r="9"></circle>
<circle cx="178" cy="230" opacity=".35" r="25" stroke="#73f0a9"></circle><circle cx="635" cy="121" opacity=".35" r="25" stroke="#e9bd67"></circle><circle cx="868" cy="238" opacity=".35" r="25" stroke="#73f0a9"></circle><circle cx="971" cy="231" opacity=".35" r="25" stroke="#73f0a9"></circle>
<path d="M92 86c59-18 127-22 177 15 37 28 30 64 71 91 44 29 88 12 120 42 27 26 17 60-12 82-48 36-103 12-154 32-41 16-66 53-105 47-53-8-52-75-87-104-35-29-96-27-108-73-13-53 46-115 98-132Z" opacity=".12" stroke="currentColor"></path>
<path d="M541 65c56-33 136-29 181 19 31 34 21 75 50 110 34 41 94 38 113 84 18 44-14 98-59 114-54 20-97-19-145-7-49 12-73 72-123 69-55-4-72-78-120-103-43-23-99 1-127-37-28-39 1-96 41-121 47-29 83-8 121-33 38-25 25-69 68-95Z" opacity=".12" stroke="currentColor"></path>
</svg>
<div class="country-tags">@foreach($global['countries'] as $country)<span>{{ $country['name'] }}</span>@endforeach</div>
<div class="global-caption"><h3>{{ $global['caption_title'] }}</h3><p>{{ $global['caption_description'] }}</p></div>
</div>
</div>
</section>
<section class="voices section portfolio-surface-a" id="voices">
<div class="container voice-shell">
<div class="voice-intro reveal">
<span class="section-index">{{ $testimonials['section_index'] }}</span>
<h2>{{ $testimonials['title'] }}</h2>
@if(count($testimonials['items']) > 1)<div class="voice-controls"><button aria-label="Previous testimonial" class="voice-button" id="voicePrev"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m15 18-6-6 6-6"></path></svg></button><button aria-label="Next testimonial" class="voice-button" id="voiceNext"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m9 18 6-6-6-6"></path></svg></button></div>@endif
</div>
<div aria-live="polite" class="voice-stage reveal delay-2">
@foreach($testimonials['items'] as $testimonial)
<article class="testimonial {{ $loop->first ? 'active' : '' }}"><div><div class="quote-mark">“</div><blockquote>{{ $testimonial['quote'] }}</blockquote></div><div class="person"><div class="person-avatar">{{ $testimonial['initials'] }}</div><div><b>{{ $testimonial['name'] }}</b><small>{{ $testimonial['role'] }}</small></div></div></article>
@endforeach
</div>
</div>
</section>
<section class="section mentor portfolio-surface-b">
<div class="container">
<div class="mentor-grid">
<article class="mentor-main reveal"><span class="section-index">{{ $mentorship['section_index'] }}</span><h2>{{ $mentorship['title'] }}</h2><p>{{ $mentorship['description'] }}</p></article>
<div class="mentor-side">
@foreach($mentorship['cards'] as $card)
<article class="mentor-card reveal delay-{{ ($loop->index % 2) + 1 }}"><small>{{ $card['label'] }}</small><h3>{{ $card['title'] }}</h3><p>{{ $card['description'] }}</p></article>
@endforeach
</div>
</div>
</div>
</section>
<section class="credentials section portfolio-surface-a" id="credentials">
<div class="container">
<div class="section-head credentials-head">
<div class="reveal"><span class="section-index">{{ $credentials['section_index'] }}</span><h2 class="section-title">{{ $credentials['title'] }}</h2></div>
<p class="lead reveal delay-2">{{ $credentials['description'] }}</p>
</div>
<div class="credential-grid">
@foreach($credentials['certifications'] as $certification)
<article class="credential-card reveal {{ $loop->index % 2 ? 'delay-1' : '' }}">
<div class="credential-top"><span class="eyebrow">{{ $certification['label'] }}</span><x-starpmaminul.portfolio.credential-logo :type="$certification['logo_type'] ?? 'award'" :image="$certification['logo'] ?? null" :alt="($certification['title'] ?? 'Certification').' logo'" /></div>
<h3>{{ $certification['title'] }}</h3><p>{{ $certification['description'] }}</p>
</article>
@endforeach
<article class="credential-card wide reveal">
<div class="education-layout">
<x-starpmaminul.portfolio.credential-logo :type="$credentials['education_logo_type'] ?? 'uiu'" :image="$credentials['education_logo'] ?? null" :alt="($credentials['education_title'] ?? 'Education').' logo'" />
<div><span class="eyebrow">{{ $credentials['education_label'] }}</span><h3>{{ $credentials['education_title'] }}</h3><p>{{ $credentials['education_description'] }}</p><div class="credential-tags">@foreach($credentials['education_tags'] as $tag)<span>{{ $tag['name'] }}</span>@endforeach</div></div>
</div>
</article>
</div>
@if(count($credentials['tools']) > 0)
<div class="tool-marquee-shell reveal" aria-label="Tools and delivery methods">
<div class="tool-marquee">
@for($track = 0; $track < 2; $track++)
<div class="tool-track" @if($track === 1) aria-hidden="true" @endif>
@foreach($credentials['tools'] as $tool)
<div class="tool-item"><x-starpmaminul.portfolio.tool-logo :type="$tool['logo_type'] ?? 'generic'" :image="$tool['logo'] ?? null" :alt="($tool['name'] ?? 'Tool').' logo'" /><span class="tool-copy"><b>{{ $tool['name'] }}</b><small>{{ $tool['description'] }}</small></span></div>
@endforeach
</div>
@endfor
</div>
</div>
@endif
</div>
</section>
<section class="contact section portfolio-surface-b" id="contact">
<div class="container contact-inner">
<span class="section-index reveal">{{ $contact['section_index'] }}</span>
<h2 class="reveal delay-1">{{ $contact['title_prefix'] }} <span>{{ $contact['title_emphasis'] }}</span></h2>
<div class="contact-copy">
<p class="lead reveal delay-2">{{ $contact['description'] }}</p>
<div class="contact-links reveal delay-3">
@foreach($contact['links'] as $link)
<a class="contact-link" href="{{ $link['url'] }}" @if(str_starts_with((string) $link['url'], 'http')) target="_blank" rel="noopener" @endif><span>{{ $link['label'] }}</span>{{ $link['text'] }}</a>
@endforeach
</div>
</div>
<div class="contact-footer"><span>{{ $contact['footer_identity'] }}</span><span>{{ $contact['footer_quote'] }}</span></div>
</div>
</section>
</main>
<aside aria-label="Page tools" class="floating-tools">
<button aria-label="Show content notes" class="floating-tool" id="notesToggle" title="Show content notes"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 4h16v12H8l-4 4V4Z"></path><path d="M8 8h8M8 12h5"></path></svg></button>
<button aria-label="Back to top" class="floating-tool" id="backTop" title="Back to top"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m6 15 6-6 6 6"></path></svg></button>
</aside>
<aside aria-live="polite" class="notes-panel" id="notesPanel">
<h3>{{ $site['notes_title'] }}</h3>
<p>{{ $site['notes_description'] }}</p>
<div class="notes-list">@foreach($site['notes'] as $note)<div>{{ $note['text'] }}</div>@endforeach</div>
<button class="button secondary" id="editNotes" style="margin-top:14px;width:100%">{{ $site['notes_button'] }}</button>
</aside>
<div aria-hidden="true" class="project-drawer" id="projectDrawer">
<div class="drawer-backdrop" data-close-drawer=""></div>
<aside aria-labelledby="drawerTitle" aria-modal="true" class="drawer-panel" role="dialog">
<button aria-label="Close project details" class="drawer-close" data-close-drawer=""><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m7 7 10 10M17 7 7 17"></path></svg></button>
<span class="eyebrow" id="drawerLabel">{{ $work['drawer_placeholder_label'] }}</span>
<h2 id="drawerTitle">{{ $work['drawer_placeholder_title'] }}</h2>
<p id="drawerIntro">{{ $work['drawer_placeholder_description'] }}</p>
<div class="drawer-details" id="drawerDetails"></div>
</aside>
</div>


<script>window.portfolioProjects = @json($projects);</script>
</body>
</html>
