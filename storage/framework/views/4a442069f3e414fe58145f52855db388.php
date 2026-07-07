<section class="hero space-hero">
  <?php echo $__env->make('frontend.partials.hero-space', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <div class="container">
    <div class="hero-copy reveal">
      <div class="label"><?php echo e($hero['label']); ?></div>
      <h1><?php echo e($hero['title']); ?></h1>
      <p><?php echo e($hero['description']); ?></p>
      <?php if(!empty($hero['buttons'])): ?>
        <div class="button-row">
          <?php $__currentLoopData = $hero['buttons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('frontend.partials.button', ['button' => $button, 'class' => $button['class'] ?? ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/page-hero.blade.php ENDPATH**/ ?>