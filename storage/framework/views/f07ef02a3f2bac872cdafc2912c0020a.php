<?php
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
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="<?php echo e($site['meta_description']); ?>" name="description"/>
<meta content="#07100d" name="theme-color"/>
<title><?php echo e($site['page_title']); ?></title>


    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/starpmaminul/portfolio.css', 'resources/js/starpmaminul/portfolio.js']); ?>
</head>
<body>

<div aria-hidden="true" class="loader">
<div class="loader-inner">
<div class="loader-mark"><b><?php echo e($site['brand_initials']); ?></b><span><?php echo e($site['loader_tagline']); ?></span></div>
<div class="loader-track"><i></i></div>
</div>
</div>
<div aria-hidden="true" class="noise"></div>
<div aria-hidden="true" class="spotlight"></div>
<div aria-hidden="true" class="progress"><span></span></div>
<header class="site-header" id="siteHeader">
<div class="header-inner">
<a aria-label="Go to top" class="brand<?php echo e(!empty($site['brand_logo']) ? ' brand-uploaded' : ''); ?>" href="#top">
<?php if(!empty($site['brand_logo'])): ?>
<img class="brand-logo-image" src="<?php echo e(\App\Support\StarPmAminulMedia::url($site['brand_logo'])); ?>" alt="<?php echo e($site['brand_name']); ?>">
<?php else: ?>
<span class="brand-mark"><?php echo e($site['brand_initials']); ?></span>
<span class="brand-copy"><?php echo e($site['brand_name']); ?> <small><?php echo e($site['brand_credentials']); ?></small></span>
<?php endif; ?>
</a>
<nav aria-label="Primary navigation" class="nav">
<?php $__currentLoopData = $site['navigation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<a href="<?php echo e($item['target']); ?>"><?php echo e($item['label']); ?></a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<div class="header-actions">
<button aria-label="Change color theme" class="icon-button" id="themeToggle" title="Change theme">
<svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M12 3a9 9 0 1 0 9 9c0-.4 0-.8-.1-1.2A7 7 0 0 1 12 3Z"></path></svg>
</button>
<a class="header-cta" href="<?php echo e('mailto:'.$site['header_cta_email']); ?>"><?php echo e($site['header_cta_text']); ?><svg fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a>
<button aria-expanded="false" aria-label="Open menu" class="menu-button" id="menuToggle">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 8h16M4 16h16"></path></svg>
</button>
</div>
</div>
</header>
<div class="mobile-menu" id="mobileMenu">
<nav>
<?php $__currentLoopData = $site['mobile_navigation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<a href="<?php echo e($item['target']); ?>"><?php echo e($item['label']); ?></a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
</div>
<main id="top">
<section aria-labelledby="heroTitle" class="hero section">
<div class="container">
<div class="hero-grid">
<div class="hero-copy">
<div class="status reveal" data-placeholder="Confirm the exact availability wording you want to show publicly."><i></i> <?php echo e($hero['availability']); ?></div>
<h1 class="hero-name reveal delay-1" id="heroTitle"><?php echo e($hero['name_line_one']); ?><br><span class="stroke"><?php echo e($hero['name_line_two']); ?></span></h1>
<div class="hero-role reveal delay-2">
<span class="eyebrow"><?php echo e($hero['role_label']); ?></span>
<p><?php echo e($hero['role_primary']); ?> <span><?php echo e($hero['role_secondary']); ?></span></p>
</div>
<div class="hero-actions reveal delay-3">
<a class="button primary" href="#work"><?php echo e($hero['primary_button']); ?><svg fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a>
<button class="button secondary" id="printResume"><?php echo e($hero['secondary_button']); ?><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v7H7z"></path></svg>
</button>
</div>
</div>
<div class="portrait-stage reveal delay-2" data-placeholder="Replace this visual with a high-resolution professional portrait. The site already works without it.">
<div class="portrait-card">
<div class="portrait-lines"></div>
<div aria-label="<?php echo e($hero['portrait_alt']); ?>" class="portrait-art"><?php if($hero['portrait']): ?><img class="portrait-photo" src="<?php echo e(\App\Support\StarPmAminulMedia::url($hero['portrait'])); ?>" alt="<?php echo e($hero['portrait_alt']); ?>"><?php else: ?><svg aria-hidden="true" role="img" viewbox="0 0 500 620">
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
</svg><?php endif; ?></div>
<div class="portrait-nameplate">
<div><strong><?php echo e($hero['profile_name']); ?></strong><small><?php echo e($hero['profile_role']); ?></small></div>
<span class="portrait-number"><?php echo e($hero['profile_number']); ?></span>
</div>
</div>
<?php $__currentLoopData = $hero['stats']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="orbit-card <?php echo e(['orbit-a', 'orbit-b', 'orbit-c'][$loop->index] ?? 'orbit-extra'); ?>" <?php if($loop->index > 2): ?> style="--orbit-index: <?php echo e($loop->index - 3); ?>" <?php endif; ?>><b><?php echo e($stat['value']); ?></b><small><?php echo e($stat['label']); ?></small></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<div class="hero-foot reveal delay-3">
<div class="scroll-cue"><i></i><span><?php echo e($hero['scroll_text']); ?></span></div>
<span><?php echo e($hero['location_text']); ?></span>
</div>
</div>
</section>
<div aria-hidden="true" class="ticker">
<div class="ticker-track">
<div class="ticker-item"><?php $__currentLoopData = $ticker['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><i></i> <?php echo e($item['label']); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
<div class="ticker-item"><?php $__currentLoopData = $ticker['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><i></i> <?php echo e($item['label']); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
</div>
</div>
<section class="philosophy-cosmos portfolio-surface-a" id="about" aria-labelledby="philosophy-title">
<div class="philosophy-cosmos__noise" aria-hidden="true"></div>
<div class="philosophy-cosmos__container">
<div class="philosophy-cosmos__copy reveal">
<span class="philosophy-cosmos__index"><?php echo e($philosophy['section_index']); ?></span>
<h2 class="philosophy-cosmos__title" id="philosophy-title"><?php echo e($philosophy['title_prefix']); ?> <em><?php echo e($philosophy['title_emphasis']); ?></em></h2>
<p class="philosophy-cosmos__lead"><?php echo e($philosophy['description']); ?></p>
<div class="philosophy-cosmos__manifesto">
<span class="philosophy-cosmos__manifesto-mark" aria-hidden="true"><?php echo e($philosophy['manifesto_mark']); ?></span>
<p><?php echo e($philosophy['manifesto_intro']); ?> <strong><?php echo e($philosophy['manifesto_core']); ?></strong>. <?php echo e($philosophy['manifesto_body']); ?></p>
</div>
</div>
<div class="philosophy-cosmos__visual-wrap reveal delay-2" aria-label="Leadership values orbiting around <?php echo e($philosophy['center_line_one'].' '.$philosophy['center_line_two']); ?>">
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
<svg viewBox="0 0 400 400" role="presentation"><defs><path id="orbitTextPath" d="M 200,200 m -164,0 a 164,164 0 1,1 328,0 a 164,164 0 1,1 -328,0"/></defs><text><textPath href="#orbitTextPath" startOffset="0%"><?php echo e($philosophy['orbit_text']); ?></textPath></text></svg>
</div>
<?php for($satellite = 1; $satellite <= 6; $satellite++): ?>
<span class="philosophy-cosmos__satellite philosophy-cosmos__satellite--<?php echo e($satellite); ?>" aria-hidden="true"></span>
<?php endfor; ?>
<div class="philosophy-cosmos__lane philosophy-cosmos__lane--one" data-philosophy-lane>
<?php $__currentLoopData = $philosophy['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($loop->index % 2 === 0): ?>
<button class="philosophy-cosmos__node" type="button" data-philosophy-node data-node-index="<?php echo e($loop->index); ?>" data-node-count="<?php echo e($loop->count); ?>" aria-label="<?php echo e($value['name']); ?>: <?php echo e($value['meaning']); ?>">
<span class="philosophy-cosmos__node-inner"><span><b><?php echo e($value['name']); ?></b><small><?php echo e($value['meaning']); ?></small><em><?php echo e($value['detail'] ?? ''); ?></em></span></span>
</button>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="philosophy-cosmos__lane philosophy-cosmos__lane--two" data-philosophy-lane>
<?php $__currentLoopData = $philosophy['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($loop->index % 2 === 1): ?>
<button class="philosophy-cosmos__node" type="button" data-philosophy-node data-node-index="<?php echo e($loop->index); ?>" data-node-count="<?php echo e($loop->count); ?>" aria-label="<?php echo e($value['name']); ?>: <?php echo e($value['meaning']); ?>">
<span class="philosophy-cosmos__node-inner"><span><b><?php echo e($value['name']); ?></b><small><?php echo e($value['meaning']); ?></small><em><?php echo e($value['detail'] ?? ''); ?></em></span></span>
</button>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="philosophy-cosmos__core"><div><small><?php echo e($philosophy['center_label']); ?></small><strong><?php echo e($philosophy['center_line_one']); ?><br><?php echo e($philosophy['center_line_two']); ?></strong><span><?php echo e($philosophy['center_caption_line_one']); ?><br><?php echo e($philosophy['center_caption_line_two']); ?></span></div></div>
<div class="philosophy-cosmos__caption" aria-hidden="true"><i></i><?php echo e($philosophy['interaction_caption']); ?></div>
</div>
</div>
</div>
</section>
<section class="section overview portfolio-surface-b">
<div class="container overview-grid">
<div class="sticky-intro reveal">
<span class="section-index"><?php echo e($overview['section_index']); ?></span>
<h2><?php echo e($overview['title']); ?></h2>
<p class="lead"><?php echo e($overview['description']); ?></p>
</div>
<div class="cap-list">
<?php $__currentLoopData = $overview['capabilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $capability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="cap-item reveal">
<span class="cap-num"><?php echo e(str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
<div><h3><?php echo e($capability['title']); ?></h3><p><?php echo e($capability['description']); ?></p></div>
<span class="cap-icon">
<?php switch($loop->index % 5):
case (0): ?><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M4 17l5-5 4 4 7-9"></path><path d="M15 7h5v5"></path></svg><?php break; ?>
<?php case (1): ?><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><rect height="16" rx="2" width="18" x="3" y="4"></rect><path d="M3 9h18M8 9v11"></path></svg><?php break; ?>
<?php case (2): ?><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg><?php break; ?>
<?php case (3): ?><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M4 4h16v16H4z"></path><path d="M4 9h16M9 9v11"></path></svg><?php break; ?>
<?php default: ?><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><path d="M3 10h18M5 10V7l7-4 7 4v3M5 10v8M9 10v8M15 10v8M19 10v8M3 21h18"></path></svg>
<?php endswitch; ?>
</span>
</article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="impact section portfolio-surface-a" id="impact">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index"><?php echo e($impact['section_index']); ?></span><h2 class="section-title medium"><?php echo e($impact['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($impact['description']); ?></p>
</div>
<div class="impact-grid">
<?php $__currentLoopData = $impact['metrics']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="metric-card reveal <?php echo e(($loop->index % 4) ? 'delay-'.($loop->index % 4) : ''); ?>"><div class="metric-value"><span><?php echo e($metric['prefix']); ?></span><span class="counter" data-target="<?php echo e($metric['value']); ?>" data-decimals="<?php echo e($metric['decimals']); ?>">0</span><span><?php echo e($metric['suffix']); ?></span></div><p><?php echo e($metric['description']); ?></p></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="work section portfolio-surface-b" id="work">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index"><?php echo e($work['section_index']); ?></span><h2 class="section-title"><?php echo e($work['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($work['description']); ?></p>
</div>
<div class="projects">
<?php $__currentLoopData = $work['projects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article aria-label="Open <?php echo e($project['title']); ?> project details" class="project-card reveal <?php echo e(([0, 1, 0, 1, 2][$loop->index] ?? ($loop->index % 3)) ? 'delay-'.([0, 1, 0, 1, 2][$loop->index] ?? ($loop->index % 3)) : ''); ?>" data-project="<?php echo e(trim((string) ($project['key'] ?? '')) ?: 'project-'.$loop->index); ?>" role="button" tabindex="0">
<div><div class="project-top"><span class="project-label"><?php echo e($project['label']); ?></span><span class="project-arrow"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"></path></svg></span></div><h3><?php echo e($project['title']); ?></h3><p><?php echo e($project['summary']); ?></p></div>
<div class="project-stats"><?php $__currentLoopData = array_filter(array_map('trim', explode('|', (string) $project['tags']))); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($tag); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
</article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="case-study section portfolio-surface-a">
<div class="container case-grid">
<div class="case-sticky reveal">
<span class="section-index"><?php echo e($caseStudy['section_index']); ?></span>
<h2><?php echo e($caseStudy['title']); ?></h2>
<p class="lead"><?php echo e($caseStudy['description']); ?></p>
</div>
<div>
<div class="case-visual reveal">
<div class="dashboard-bar"><div class="dashboard-dots"><i></i><i></i><i></i></div><span><?php echo e($caseStudy['dashboard_label']); ?></span></div>
<div class="dashboard-body">
<div class="dash-kpis">
<?php $__currentLoopData = $caseStudy['kpis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="dash-kpi"><b><?php echo e($kpi['value']); ?></b><small><?php echo e($kpi['label']); ?></small></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="dash-chart"><span class="chart-title"><?php echo e($caseStudy['chart_title']); ?></span><div class="bars"><i style="--h:23%"></i><i style="--h:34%"></i><i style="--h:31%"></i><i style="--h:46%"></i><i style="--h:55%"></i><i style="--h:68%"></i><i style="--h:76%"></i><i style="--h:88%"></i><i style="--h:100%"></i></div></div>
</div>
</div>
<div class="case-steps">
<?php $__currentLoopData = $caseStudy['steps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="case-step reveal"><span><?php echo e(str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT)); ?></span><div><h3><?php echo e($step['title']); ?></h3><p><?php echo e($step['description']); ?></p></div></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</div>
</section>
<section class="journey section portfolio-surface-b" id="journey">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index"><?php echo e($journey['section_index']); ?></span><h2 class="section-title medium"><?php echo e($journey['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($journey['description']); ?></p>
</div>
<div class="timeline">
<div class="timeline-line"></div>
<?php $__currentLoopData = $journey['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="timeline-item reveal"><span class="timeline-year"><?php echo e($item['period']); ?></span><div class="timeline-content"><div><h3><?php echo e($item['role']); ?></h3><div class="timeline-company"><?php echo e($item['company']); ?></div></div><p class="timeline-copy"><?php echo e($item['intro']); ?> <b><?php echo e($item['highlight']); ?></b></p></div></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="section operating-system portfolio-surface-a">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index"><?php echo e($operatingSystem['section_index']); ?></span><h2 class="section-title medium"><?php echo e($operatingSystem['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($operatingSystem['description']); ?></p>
</div>
<div class="system-grid">
<?php $__currentLoopData = $operatingSystem['steps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="system-card reveal <?php echo e(($loop->index % 4) ? 'delay-'.($loop->index % 4) : ''); ?>"><span class="system-no"><?php echo e($step['label']); ?></span><div><div class="system-icon">
<?php switch($loop->index % 4):
case (0): ?><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg><?php break; ?>
<?php case (1): ?><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"></path></svg><?php break; ?>
<?php case (2): ?><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M8 12h8M12 8v8"></path><circle cx="12" cy="12" r="9"></circle></svg><?php break; ?>
<?php default: ?><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m5 12 4 4L19 6"></path></svg>
<?php endswitch; ?>
</div><h3><?php echo e($step['title']); ?></h3><p><?php echo e($step['description']); ?></p></div></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="global section portfolio-surface-b">
<div class="container">
<div class="section-head split">
<div class="reveal"><span class="section-index"><?php echo e($global['section_index']); ?></span><h2 class="section-title medium"><?php echo e($global['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($global['description']); ?></p>
</div>
<div class="global-board reveal">
<svg aria-hidden="true" fill="none" viewbox="0 0 1200 470">
<path d="M80 268C210 202 258 238 355 195c91-40 126-117 244-89 108 26 115 122 218 135 99 12 145-51 301-4" opacity=".55" stroke="#73f0a9" stroke-dasharray="8 9" stroke-width="2"></path>
<circle cx="178" cy="230" fill="#73f0a9" r="9"></circle><circle cx="635" cy="121" fill="#e9bd67" r="9"></circle><circle cx="868" cy="238" fill="#73f0a9" r="9"></circle><circle cx="971" cy="231" fill="#73f0a9" r="9"></circle>
<circle cx="178" cy="230" opacity=".35" r="25" stroke="#73f0a9"></circle><circle cx="635" cy="121" opacity=".35" r="25" stroke="#e9bd67"></circle><circle cx="868" cy="238" opacity=".35" r="25" stroke="#73f0a9"></circle><circle cx="971" cy="231" opacity=".35" r="25" stroke="#73f0a9"></circle>
<path d="M92 86c59-18 127-22 177 15 37 28 30 64 71 91 44 29 88 12 120 42 27 26 17 60-12 82-48 36-103 12-154 32-41 16-66 53-105 47-53-8-52-75-87-104-35-29-96-27-108-73-13-53 46-115 98-132Z" opacity=".12" stroke="currentColor"></path>
<path d="M541 65c56-33 136-29 181 19 31 34 21 75 50 110 34 41 94 38 113 84 18 44-14 98-59 114-54 20-97-19-145-7-49 12-73 72-123 69-55-4-72-78-120-103-43-23-99 1-127-37-28-39 1-96 41-121 47-29 83-8 121-33 38-25 25-69 68-95Z" opacity=".12" stroke="currentColor"></path>
</svg>
<div class="country-tags"><?php $__currentLoopData = $global['countries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($country['name']); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
<div class="global-caption"><h3><?php echo e($global['caption_title']); ?></h3><p><?php echo e($global['caption_description']); ?></p></div>
</div>
</div>
</section>
<section class="voices section portfolio-surface-a" id="voices">
<div class="container voice-shell">
<div class="voice-intro reveal">
<span class="section-index"><?php echo e($testimonials['section_index']); ?></span>
<h2><?php echo e($testimonials['title']); ?></h2>
<?php if(count($testimonials['items']) > 1): ?><div class="voice-controls"><button aria-label="Previous testimonial" class="voice-button" id="voicePrev"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m15 18-6-6 6-6"></path></svg></button><button aria-label="Next testimonial" class="voice-button" id="voiceNext"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m9 18 6-6-6-6"></path></svg></button></div><?php endif; ?>
</div>
<div aria-live="polite" class="voice-stage reveal delay-2">
<?php $__currentLoopData = $testimonials['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="testimonial <?php echo e($loop->first ? 'active' : ''); ?>"><div><div class="quote-mark">“</div><blockquote><?php echo e($testimonial['quote']); ?></blockquote></div><div class="person"><div class="person-avatar"><?php echo e($testimonial['initials']); ?></div><div><b><?php echo e($testimonial['name']); ?></b><small><?php echo e($testimonial['role']); ?></small></div></div></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<section class="section mentor portfolio-surface-b">
<div class="container">
<div class="mentor-grid">
<article class="mentor-main reveal"><span class="section-index"><?php echo e($mentorship['section_index']); ?></span><h2><?php echo e($mentorship['title']); ?></h2><p><?php echo e($mentorship['description']); ?></p></article>
<div class="mentor-side">
<?php $__currentLoopData = $mentorship['cards']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="mentor-card reveal delay-<?php echo e(($loop->index % 2) + 1); ?>"><small><?php echo e($card['label']); ?></small><h3><?php echo e($card['title']); ?></h3><p><?php echo e($card['description']); ?></p></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</div>
</section>
<section class="credentials section portfolio-surface-a" id="credentials">
<div class="container">
<div class="section-head credentials-head">
<div class="reveal"><span class="section-index"><?php echo e($credentials['section_index']); ?></span><h2 class="section-title"><?php echo e($credentials['title']); ?></h2></div>
<p class="lead reveal delay-2"><?php echo e($credentials['description']); ?></p>
</div>
<div class="credential-grid">
<?php $__currentLoopData = $credentials['certifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article class="credential-card reveal <?php echo e($loop->index % 2 ? 'delay-1' : ''); ?>">
<div class="credential-top"><span class="eyebrow"><?php echo e($certification['label']); ?></span><?php if (isset($component)) { $__componentOriginal855992184291d5d1fda216c5f68383b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855992184291d5d1fda216c5f68383b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.portfolio.credential-logo','data' => ['type' => $certification['logo_type'] ?? 'award','image' => $certification['logo'] ?? null,'alt' => ($certification['title'] ?? 'Certification').' logo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.portfolio.credential-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certification['logo_type'] ?? 'award'),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($certification['logo'] ?? null),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($certification['title'] ?? 'Certification').' logo')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855992184291d5d1fda216c5f68383b0)): ?>
<?php $attributes = $__attributesOriginal855992184291d5d1fda216c5f68383b0; ?>
<?php unset($__attributesOriginal855992184291d5d1fda216c5f68383b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855992184291d5d1fda216c5f68383b0)): ?>
<?php $component = $__componentOriginal855992184291d5d1fda216c5f68383b0; ?>
<?php unset($__componentOriginal855992184291d5d1fda216c5f68383b0); ?>
<?php endif; ?></div>
<h3><?php echo e($certification['title']); ?></h3><p><?php echo e($certification['description']); ?></p>
</article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<article class="credential-card wide reveal">
<div class="education-layout">
<?php if (isset($component)) { $__componentOriginal855992184291d5d1fda216c5f68383b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855992184291d5d1fda216c5f68383b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.portfolio.credential-logo','data' => ['type' => $credentials['education_logo_type'] ?? 'uiu','image' => $credentials['education_logo'] ?? null,'alt' => ($credentials['education_title'] ?? 'Education').' logo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.portfolio.credential-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($credentials['education_logo_type'] ?? 'uiu'),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($credentials['education_logo'] ?? null),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($credentials['education_title'] ?? 'Education').' logo')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855992184291d5d1fda216c5f68383b0)): ?>
<?php $attributes = $__attributesOriginal855992184291d5d1fda216c5f68383b0; ?>
<?php unset($__attributesOriginal855992184291d5d1fda216c5f68383b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855992184291d5d1fda216c5f68383b0)): ?>
<?php $component = $__componentOriginal855992184291d5d1fda216c5f68383b0; ?>
<?php unset($__componentOriginal855992184291d5d1fda216c5f68383b0); ?>
<?php endif; ?>
<div><span class="eyebrow"><?php echo e($credentials['education_label']); ?></span><h3><?php echo e($credentials['education_title']); ?></h3><p><?php echo e($credentials['education_description']); ?></p><div class="credential-tags"><?php $__currentLoopData = $credentials['education_tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($tag['name']); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
</div>
</article>
</div>
<?php if(count($credentials['tools']) > 0): ?>
<div class="tool-marquee-shell reveal" aria-label="Tools and delivery methods">
<div class="tool-marquee">
<?php for($track = 0; $track < 2; $track++): ?>
<div class="tool-track" <?php if($track === 1): ?> aria-hidden="true" <?php endif; ?>>
<?php $__currentLoopData = $credentials['tools']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="tool-item"><?php if (isset($component)) { $__componentOriginalb99286ddd7d22170fcfbf13f0afd91cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb99286ddd7d22170fcfbf13f0afd91cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.starpmaminul.portfolio.tool-logo','data' => ['type' => $tool['logo_type'] ?? 'generic','image' => $tool['logo'] ?? null,'alt' => ($tool['name'] ?? 'Tool').' logo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('starpmaminul.portfolio.tool-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tool['logo_type'] ?? 'generic'),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tool['logo'] ?? null),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($tool['name'] ?? 'Tool').' logo')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb99286ddd7d22170fcfbf13f0afd91cc)): ?>
<?php $attributes = $__attributesOriginalb99286ddd7d22170fcfbf13f0afd91cc; ?>
<?php unset($__attributesOriginalb99286ddd7d22170fcfbf13f0afd91cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb99286ddd7d22170fcfbf13f0afd91cc)): ?>
<?php $component = $__componentOriginalb99286ddd7d22170fcfbf13f0afd91cc; ?>
<?php unset($__componentOriginalb99286ddd7d22170fcfbf13f0afd91cc); ?>
<?php endif; ?><span class="tool-copy"><b><?php echo e($tool['name']); ?></b><small><?php echo e($tool['description']); ?></small></span></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endfor; ?>
</div>
</div>
<?php endif; ?>
</div>
</section>
<section class="contact section portfolio-surface-b" id="contact">
<div class="container contact-inner">
<span class="section-index reveal"><?php echo e($contact['section_index']); ?></span>
<h2 class="reveal delay-1"><?php echo e($contact['title_prefix']); ?> <span><?php echo e($contact['title_emphasis']); ?></span></h2>
<div class="contact-copy">
<p class="lead reveal delay-2"><?php echo e($contact['description']); ?></p>
<div class="contact-links reveal delay-3">
<?php $__currentLoopData = $contact['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<a class="contact-link" href="<?php echo e($link['url']); ?>" <?php if(str_starts_with((string) $link['url'], 'http')): ?> target="_blank" rel="noopener" <?php endif; ?>><span><?php echo e($link['label']); ?></span><?php echo e($link['text']); ?></a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<div class="contact-footer"><span><?php echo e($contact['footer_identity']); ?></span><span><?php echo e($contact['footer_quote']); ?></span></div>
</div>
</section>
</main>
<aside aria-label="Page tools" class="floating-tools">
<button aria-label="Show content notes" class="floating-tool" id="notesToggle" title="Show content notes"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="M4 4h16v12H8l-4 4V4Z"></path><path d="M8 8h8M8 12h5"></path></svg></button>
<button aria-label="Back to top" class="floating-tool" id="backTop" title="Back to top"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m6 15 6-6 6 6"></path></svg></button>
</aside>
<aside aria-live="polite" class="notes-panel" id="notesPanel">
<h3><?php echo e($site['notes_title']); ?></h3>
<p><?php echo e($site['notes_description']); ?></p>
<div class="notes-list"><?php $__currentLoopData = $site['notes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($note['text']); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
<button class="button secondary" id="editNotes" style="margin-top:14px;width:100%"><?php echo e($site['notes_button']); ?></button>
</aside>
<div aria-hidden="true" class="project-drawer" id="projectDrawer">
<div class="drawer-backdrop" data-close-drawer=""></div>
<aside aria-labelledby="drawerTitle" aria-modal="true" class="drawer-panel" role="dialog">
<button aria-label="Close project details" class="drawer-close" data-close-drawer=""><svg fill="none" stroke="currentColor" stroke-width="1.8" viewbox="0 0 24 24"><path d="m7 7 10 10M17 7 7 17"></path></svg></button>
<span class="eyebrow" id="drawerLabel"><?php echo e($work['drawer_placeholder_label']); ?></span>
<h2 id="drawerTitle"><?php echo e($work['drawer_placeholder_title']); ?></h2>
<p id="drawerIntro"><?php echo e($work['drawer_placeholder_description']); ?></p>
<div class="drawer-details" id="drawerDetails"></div>
</aside>
</div>


<script>window.portfolioProjects = <?php echo json_encode($projects, 15, 512) ?>;</script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/starpmaminul/portfolio/index.blade.php ENDPATH**/ ?>