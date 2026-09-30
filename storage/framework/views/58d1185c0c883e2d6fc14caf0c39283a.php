<section class="hero space-hero itqan-banner" data-itqan-banner>
  <?php echo $__env->make('frontend.partials.hero-space', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <div class="container">
    <div class="hero-copy reveal">
      <div class="label"><?php echo e($hero['label']); ?></div>
      <h1><?php echo e($hero['title']); ?></h1>
      <p><?php echo e($hero['description']); ?></p>
    </div>
  </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/plain-hero.blade.php ENDPATH**/ ?>