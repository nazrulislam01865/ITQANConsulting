<?php $__env->startSection('content'); ?>
<main id="home" class="page active">
  <section class="hero home-hero">
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

  <section class="section founder-message-section">
    <div class="container founder-message-card reveal">
      <div class="founder-message-copy">
        <div class="label"><?php echo e($page['founder']['label']); ?></div>
        <h2 class="founder-message-title"><?php echo e($page['founder']['title']); ?></h2>
        <div class="founder-message-body">
          <?php $__currentLoopData = $page['founder']['paragraphs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p><?php echo e($paragraph); ?></p>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="founder-message-signature">
          <?php if(! empty($page['founder']['image_url'])): ?>
            <img src="<?php echo e($page['founder']['image_url']); ?>" alt="<?php echo e($page['founder']['name']); ?>" loading="lazy">
          <?php else: ?>
            <span class="founder-message-avatar-placeholder" aria-hidden="true">IC</span>
          <?php endif; ?>
          <div>
            <strong><?php echo e($page['founder']['name']); ?></strong>
            <span><?php echo e($page['founder']['role']); ?></span>
          </div>
        </div>
      </div>
      <div class="founder-message-art" aria-hidden="true">
        <span class="founder-dot-grid"></span>
        <span class="founder-outline founder-outline-one"></span>
        <span class="founder-outline founder-outline-two"></span>
        <span class="founder-outline founder-outline-three"></span>
      </div>
    </div>
  </section>

  <section class="section light">
    <div class="container">
      <?php echo $__env->make('frontend.partials.section-head', ['label' => $page['who']['label'], 'title' => $page['who']['title'], 'lead' => $page['who']['lead']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="card-grid three">
        <?php $__currentLoopData = $page['who']['cards']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="card reveal"><span class="num"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span><h3><?php echo e($card['title']); ?></h3><p><?php echo e($card['text']); ?></p></article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <section class="section soft-white">
    <div class="container">
      <?php echo $__env->make('frontend.partials.section-head', ['label' => $page['problems']['label'], 'title' => $page['problems']['title'], 'lead' => $page['problems']['lead']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="problem-strip" aria-label="Problem response cards">
        <?php $__currentLoopData = $page['problems']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="problem-card reveal"><div><b>Problem</b><p><?php echo e($item['problem']); ?></p></div><div class="response"><b>ITQAN Response</b><p><?php echo e($item['response']); ?></p></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <section class="section" id="home-services-preview">
    <div class="container">
      <?php echo $__env->make('frontend.partials.section-head', ['label' => $page['services_preview']['label'], 'title' => $page['services_preview']['title'], 'lead' => $page['services_preview']['lead']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="card-grid">
        <?php $__currentLoopData = $page['services_preview']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="glass-card reveal"><div class="icon-circle"><?php echo e($item['num']); ?></div><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="button-row" style="margin-top:26px"><?php echo $__env->make('frontend.partials.button', ['button' => $page['services_preview']['button'], 'class' => 'primary'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    </div>
  </section>

  <section class="section dark-panel working-section">
    <div class="container">
      <div class="working-head reveal">
        <div><div class="label"><?php echo e($page['working']['label']); ?></div><h2 class="headline"><?php echo $page['working']['title']; ?></h2></div>
        <p class="working-intro"><?php echo e($page['working']['intro']); ?></p>
      </div>
      <div class="working-board reveal">
        <div class="working-cards">
          <?php $__currentLoopData = $page['working']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="working-card"><span class="working-no"><?php echo e($item['num']); ?></span><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </section>

  <section class="section light">
    <div class="container">
      <div class="section-head reveal"><div><div class="label"><?php echo e($page['testimonials']['label']); ?></div><h2 class="headline"><?php echo e($page['testimonials']['title']); ?></h2></div></div>
      <div class="testimonial-slider reveal" aria-label="Client words sliding testimonials">
        <div class="testimonial-track">
          <?php $__currentLoopData = array_merge($collections['testimonials'], $collections['testimonials']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="card testimonial-card"><div><div class="quote-mark">“</div><h3><?php echo e($testimonial['title']); ?></h3><p><?php echo e($testimonial['text']); ?></p></div><p class="testimonial-author"><strong><?php echo e($testimonial['author']); ?></strong><?php echo e($testimonial['role']); ?></p></article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </section>

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

  <?php echo $__env->make('frontend.partials.cta', ['cta' => $page['cta']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/home.blade.php ENDPATH**/ ?>