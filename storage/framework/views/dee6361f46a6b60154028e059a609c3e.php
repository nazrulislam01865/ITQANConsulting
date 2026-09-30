<?php $__env->startSection('content'); ?>
<main id="services" class="page active">
  <?php echo $__env->make('frontend.partials.page-hero', ['hero' => $page['hero']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <section class="section light" id="service-areas">
    <div class="container">
      <div class="service-card-grid">
        <?php $__currentLoopData = $collections['services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="service-card reveal">
            <span class="service-badge"><?php echo e($service['badge']); ?></span>
            <h3><?php echo e($service['title']); ?></h3>
            <p class="service-intro"><?php echo e($service['intro']); ?></p>
            <ul class="service-points">
              <?php $__currentLoopData = $service['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($point); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php
              $contactServiceKeys = [
                  '01' => 'consulting-business-clarity',
                  '02' => 'project-product-support',
                  '03' => 'software-web-development',
                  '04' => 'erp-automation',
                  '05' => 'training-coaching',
                  '06' => 'dedicated-team-support',
              ];

              $serviceSelectionKey = $contactServiceKeys[$service['badge'] ?? '']
                  ?? \Illuminate\Support\Str::slug($service['title'] ?? 'service');

              if (!empty($service['button_url'])) {
                  $serviceButtonHref = $service['button_url'];
              } else {
                  $routeName = (!empty($service['button_route']) && \Illuminate\Support\Facades\Route::has($service['button_route']))
                      ? $service['button_route']
                      : 'contact';

                  $serviceButtonHref = $routeName === 'contact'
                      ? route('contact', ['service' => $serviceSelectionKey]) . '#contact-form'
                      : route($routeName);
              }
            ?>
            <a class="btn" href="<?php echo e($serviceButtonHref); ?>"><?php echo e($service['button']); ?></a>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <section class="section dark-panel">
    <div class="container">
      <div class="section-head reveal"><div><h2 class="headline"><?php echo e($page['faq_title']); ?></h2></div></div>
      <div class="service-faq-wrap reveal">
        <?php $__currentLoopData = $collections['service_faqs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="service-faq faq-item <?php echo e($index === 0 ? 'open' : ''); ?>">
            <button class="faq-q" type="button">
              <span class="faq-no"><?php echo e($index + 1); ?></span>
              <span><strong><?php echo e($faq['question']); ?></strong><span><?php echo e($faq['summary']); ?></span></span>
              <span class="faq-plus">+</span>
            </button>
            <div class="faq-a">
              <?php $__currentLoopData = $faq['answer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($paragraph); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <?php echo $__env->make('frontend.partials.cta', ['cta' => $page['cta']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/services.blade.php ENDPATH**/ ?>