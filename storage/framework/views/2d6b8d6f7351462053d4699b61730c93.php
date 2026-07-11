<?php $__env->startSection('content'); ?>
<main id="home" class="page active">
  <section class="hero home-hero itqan-banner" data-itqan-banner>
    <?php echo $__env->make('frontend.partials.hero-space', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="container hero-grid">
      <div class="hero-copy reveal">
        <div class="label"><?php echo e($page['hero']['label']); ?></div>
        <h1><?php echo $page['hero']['title']; ?></h1>
        <p><?php echo e($page['hero']['description']); ?></p>
        <div class="short-lines" aria-label="Hero message">
          <?php $__currentLoopData = $page['hero']['chips']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($chip); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="button-row">
          <?php $__currentLoopData = $page['hero']['buttons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('frontend.partials.button', ['button' => $button, 'class' => $button['class'] ?? ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="social-row with-label">
          <span><?php echo e($page['hero']['social_label']); ?></span>
          <div class="social-row"><?php echo $__env->make('frontend.partials.social-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        </div>
      </div>
    </div>
    <section class="code-ticker-wrap" aria-label="Code style message slider">
      <div class="code-ticker">
        <div class="code-track">
          <?php $__currentLoopData = array_merge($page['hero']['ticker'], $page['hero']['ticker']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="code-item"><?php echo $item; ?></span>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </section>
  </section>

  <?php
    $clarityItems = collect($page['problems']['items'] ?? [])->values();
    $firstClarityItem = $clarityItems->first() ?? [
      'problem' => 'Our process is undocumented',
      'summary' => 'Important work depends on people remembering what to do, when to do it, and who should approve it.',
      'services' => ['Process interviews', 'Workflow mapping', 'Roles and responsibility', 'Practical documentation'],
    ];
  ?>

  <section class="section template-clarity-section template-grid-bg" id="clarity-check" data-clarity-check>
    <div class="container">
      <div class="template-section-intro reveal">
        <span class="template-section-label"><?php echo e($page['problems']['label']); ?></span>
        <h2><?php echo e($page['problems']['title']); ?></h2>
        <p><?php echo e($page['problems']['lead']); ?></p>
      </div>

      <div class="template-diagnostic-wrap reveal">
        <div class="template-problem-choices" role="list" aria-label="Business clarity problems">
          <?php $__currentLoopData = $clarityItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button
              class="template-problem-option <?php echo e($loop->first ? 'active' : ''); ?>"
              type="button"
              data-clarity-index="<?php echo e($loop->index); ?>"
              aria-pressed="<?php echo e($loop->first ? 'true' : 'false'); ?>"
            >
              <span class="template-option-icon"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
              <span><?php echo e($item['problem']); ?></span>
            </button>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="template-diagnostic-result" aria-live="polite">
          <span class="template-result-eyebrow">A practical place to begin</span>
          <h3 data-clarity-title><?php echo e($firstClarityItem['problem']); ?></h3>
          <p data-clarity-summary><?php echo e($firstClarityItem['summary'] ?? ''); ?></p>
          <div class="template-result-list" data-clarity-services>
            <?php $__currentLoopData = ($firstClarityItem['services'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="template-result-item"><?php echo e($service); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <a class="btn blue" href="<?php echo e(route('contact')); ?>">Discuss this problem <span aria-hidden="true">→</span></a>
        </div>
      </div>
    </div>

    <script type="application/json" data-clarity-data><?php echo json_encode($clarityItems->all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
  </section>

  <section class="section template-founder-section" id="founder-message">
    <div class="container template-founder-grid">
      <div class="template-founder-visual reveal" aria-label="Founder portrait">
        <div class="template-founder-avatar <?php echo e(! empty($page['founder']['image_url']) ? 'has-image' : ''); ?>">
          <?php if(! empty($page['founder']['image_url'])): ?>
            <img src="<?php echo e($page['founder']['image_url']); ?>" alt="<?php echo e($page['founder']['name']); ?>" loading="lazy">
          <?php else: ?>
            <svg viewBox="0 0 120 120" aria-hidden="true">
              <circle cx="60" cy="38" r="24" fill="none" stroke="currentColor" stroke-width="2"/>
              <path d="M25 108c4-25 18-39 35-39s31 14 35 39" fill="none" stroke="currentColor" stroke-width="2"/>
              <path d="M40 84h40" stroke="currentColor" stroke-width="2"/>
            </svg>
          <?php endif; ?>
        </div>
        <div class="template-founder-signature">
          <strong><?php echo e($page['founder']['name']); ?></strong>
          <span><?php echo e($page['founder']['role']); ?></span>
        </div>
      </div>

      <div class="template-founder-copy reveal">
        <span class="template-section-label"><?php echo e($page['founder']['label']); ?></span>
        <blockquote><span class="template-quote-mark">“</span><?php echo e($page['founder']['title']); ?></blockquote>
        <?php $__currentLoopData = $page['founder']['paragraphs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <p><?php echo e($paragraph); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <?php
    $whyItems = collect($page['who']['cards'] ?? [])->values();
    $serviceItems = collect($page['services_preview']['items'] ?? [])->values();
  ?>

  <section class="section template-why-section" id="why-itqan" data-why-itqan>
    <div class="container">
      <div class="template-why-intro reveal">
        <span class="template-section-label"><?php echo e($page['who']['label']); ?></span>
        <h2><?php echo e($page['who']['title']); ?></h2>
        <p><?php echo e($page['who']['lead']); ?></p>
      </div>

      <div class="template-why-layout">
        <div class="template-why-sticky reveal">
          <div class="template-transformation-stage" data-why-stage>
            <div class="template-stage-grid" aria-hidden="true"></div>
            <div class="template-stage-label" data-why-stage-label>Problem / scattered work</div>

            <div class="template-messy-cloud" aria-hidden="true">
              <span style="--x:8%;--y:10%;--r:-8deg">Calls</span>
              <span style="--x:56%;--y:4%;--r:5deg">Excel</span>
              <span style="--x:28%;--y:29%;--r:2deg">Memory</span>
              <span style="--x:68%;--y:34%;--r:-6deg">Messages</span>
              <span style="--x:9%;--y:58%;--r:7deg">Approvals</span>
              <span style="--x:45%;--y:67%;--r:-3deg">Reports</span>
              <span style="--x:72%;--y:78%;--r:8deg">Follow-up</span>
            </div>

            <div class="template-clear-flow" aria-hidden="true">
              <div class="template-flow-node"><span>01</span><strong>Understand the real work</strong></div>
              <div class="template-flow-node"><span>02</span><strong>Map roles, data, and decisions</strong></div>
              <div class="template-flow-node"><span>03</span><strong>Define the right system or process</strong></div>
              <div class="template-flow-node"><span>04</span><strong>Support practical execution</strong></div>
            </div>
          </div>
        </div>

        <div class="template-why-stories">
          <?php $__currentLoopData = $whyItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article
              class="template-story-card <?php echo e($loop->first ? 'active' : ''); ?>"
              tabindex="0"
              data-why-story
              data-stage-label="<?php echo e($item['stage_label'] ?? $item['title']); ?>"
            >
              <span class="template-story-index"><?php echo e($item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
              <h3><?php echo e($item['title']); ?></h3>
              <p><?php echo e($item['text']); ?></p>
              <div class="template-story-response">
                <small>ITQAN response</small>
                <span><?php echo e($item['response'] ?? 'We help organize the work into a clearer and more practical system.'); ?></span>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </section>

  <section class="section template-services-section template-grid-bg" id="home-services-preview" data-services-explorer>
    <div class="container">
      <div class="template-services-intro reveal">
        <span class="template-section-label"><?php echo e($page['services_preview']['label']); ?></span>
        <h2><?php echo e($page['services_preview']['title']); ?></h2>
        <p><?php echo e($page['services_preview']['lead']); ?></p>
      </div>

      <div class="template-services-layout">
        <div class="template-service-menu reveal" role="tablist" aria-label="ITQAN services" data-service-menu>
          <?php $__currentLoopData = $serviceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($serviceNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT)); ?>
            <button
              type="button"
              id="home-service-tab-<?php echo e($loop->iteration); ?>"
              class="template-service-tab <?php echo e($loop->first ? 'active' : ''); ?>"
              role="tab"
              data-service-tab
              data-service-target="home-service-<?php echo e($loop->iteration); ?>"
              aria-controls="home-service-<?php echo e($loop->iteration); ?>"
              aria-selected="<?php echo e($loop->first ? 'true' : 'false'); ?>"
              tabindex="<?php echo e($loop->first ? '0' : '-1'); ?>"
            >
              <span><?php echo e($serviceNumber); ?></span>
              <strong><?php echo e($item['title']); ?></strong>
            </button>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="template-service-panels" data-service-panels>
          <?php $__currentLoopData = $serviceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($serviceNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT)); ?>
            <article
              class="template-service-panel reveal <?php echo e($loop->first ? 'active' : ''); ?>"
              id="home-service-<?php echo e($loop->iteration); ?>"
              role="tabpanel"
              tabindex="-1"
              aria-labelledby="home-service-tab-<?php echo e($loop->iteration); ?>"
              data-service-panel
              data-number="<?php echo e($serviceNumber); ?>"
            >
              <div class="template-service-graphic" aria-hidden="true">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
                <i class="dot"></i>
                <i class="dot"></i>
                <i class="dot"></i>
              </div>
              <h3><?php echo e($item['title']); ?></h3>
              <p><?php echo e($item['text']); ?></p>
              <div class="template-service-meta">
                <div class="template-meta-card">
                  <small>Common problem</small>
                  <span><?php echo e($item['common_problem'] ?? 'The work lacks a clear structure, owner, or practical next step.'); ?></span>
                </div>
                <div class="template-meta-card">
                  <small>Possible deliverables</small>
                  <span><?php echo e($item['deliverables'] ?? 'A clear scope, practical recommendations, and an actionable delivery plan.'); ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <div class="template-services-action reveal">
        <?php echo $__env->make('frontend.partials.button', ['button' => $page['services_preview']['button'], 'class' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
    </div>
  </section>

  <section class="section template-process-section" id="our-way-of-working" data-process-section>
    <div class="container">
      <div class="template-process-intro reveal">
        <span class="template-section-label"><?php echo e($page['working']['label']); ?></span>
        <h2><?php echo $page['working']['title']; ?></h2>
        <p><?php echo e($page['working']['intro']); ?></p>
      </div>

      <div class="template-process-shell" data-process-shell>
        <svg class="template-process-line" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true">
          <path class="base" d="M20,60 C180,10 250,90 420,45 S700,80 980,40" fill="none" stroke-width="3" pathLength="1" />
          <path class="progress" data-process-progress d="M20,60 C180,10 250,90 420,45 S700,80 980,40" fill="none" stroke-width="4" stroke-linecap="round" pathLength="1" />
        </svg>

        <div class="template-process-grid">
          <?php $__currentLoopData = $page['working']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($stepNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT)); ?>
            <article class="template-process-step" data-process-step>
              <div class="template-process-node"><?php echo e($stepNumber); ?></div>
              <div class="template-process-copy">
                <h3><?php echo e($item['title']); ?></h3>
                <p><?php echo e($item['text']); ?></p>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </section>

  <?php if(data_get($page, 'testimonials.is_active', true)): ?>
  <section class="section template-testimonials-section template-grid-bg" id="client-words" data-testimonial-slider>
    <div class="container">
      <div class="template-testimonials-intro reveal">
        <span class="template-section-label"><?php echo e($page['testimonials']['label']); ?></span>
        <h2><?php echo e($page['testimonials']['title']); ?></h2>
        <p><?php echo e($page['testimonials']['lead'] ?? 'A calm slider with enough time to read. Autoplay pauses on hover, focus, and touch.'); ?></p>
      </div>

      <div class="template-testimonial-shell reveal" data-testimonial-shell aria-roledescription="carousel" aria-label="Client words">
        <div class="template-testimonial-track" data-testimonial-track aria-live="polite">
          <?php $__currentLoopData = $collections['testimonials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="template-testimonial-slide" data-testimonial-slide aria-hidden="<?php echo e($loop->first ? 'false' : 'true'); ?>">
              <article class="template-testimonial-card">
                <div class="template-quote-symbol" aria-hidden="true">“</div>
                <h3><?php echo e($testimonial['title']); ?></h3>
                <blockquote><?php echo e($testimonial['text']); ?></blockquote>
                <div class="template-client-row">
                  <div class="template-client-info">
                    <strong><?php echo e($testimonial['author']); ?></strong>
                    <span><?php echo e($testimonial['role']); ?></span>
                  </div>
                  <?php if(! empty($testimonial['project'])): ?>
                    <span class="template-project-pill"><?php echo e($testimonial['project']); ?></span>
                  <?php endif; ?>
                </div>
              </article>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if(count($collections['testimonials']) > 1): ?>
          <div class="template-slider-controls">
            <button class="template-slider-btn" type="button" data-testimonial-prev aria-label="Previous testimonial">←</button>
            <div class="template-slider-dots" data-testimonial-dots aria-label="Choose testimonial"></div>
            <button class="template-slider-btn" type="button" data-testimonial-next aria-label="Next testimonial">→</button>
          </div>
          <div class="template-slider-progress" aria-hidden="true"><span data-testimonial-progress></span></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section soft-white">
    <div class="container">
      <?php echo $__env->make('frontend.partials.section-head', ['label' => $page['works_preview']['label'], 'title' => $page['works_preview']['title'], 'sectionButton' => array_merge($page['works_preview']['button'], ['class' => 'dark'])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="card-grid two">
        <?php $__currentLoopData = ($collections['home_featured_works'] ?? array_slice($collections['works'] ?? [], 0, 4)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $work): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('frontend.partials.work-card', ['work' => $work, 'pill' => $work['preview_pill'] ?? $work['pill'] ?? '', 'description' => $work['preview_description'] ?? $work['description'] ?? ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <?php if(data_get($page, 'values.is_active', true)): ?>
  <section class="section template-values-section" id="how-we-think" data-values-section>
    <div class="container template-values-layout">
      <div class="template-values-sticky reveal">
        <span class="template-section-label"><?php echo e($page['values']['label'] ?? 'How We Think'); ?></span>
        <h2><?php echo e($page['values']['title'] ?? 'Principles that shape the work.'); ?></h2>
        <?php if(! empty($page['values']['lead'])): ?>
          <p><?php echo e($page['values']['lead']); ?></p>
        <?php endif; ?>
        <div class="template-value-number" data-value-number aria-hidden="true">
          <?php echo e($page['values']['items'][0]['num'] ?? '01'); ?>

        </div>
      </div>

      <div class="template-value-cards">
        <?php $__currentLoopData = ($page['values']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article
            class="template-value-card reveal <?php echo e($loop->first ? 'active' : ''); ?>"
            tabindex="0"
            data-value-card
            data-value="<?php echo e($item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?>"
          >
            <span class="template-value-mini"><?php echo e($item['mini'] ?? (($item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)) . ' / principle')); ?></span>
            <h3><?php echo e($item['title']); ?></h3>
            <p><?php echo e($item['text']); ?></p>
            <?php if(! empty($item['example'])): ?>
              <div class="template-value-example"><?php echo e($item['example']); ?></div>
            <?php endif; ?>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if(data_get($page, 'cta.is_active', true)): ?>
    <?php echo $__env->make('frontend.partials.home-digital-contact', ['cta' => $page['cta']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/home.blade.php ENDPATH**/ ?>