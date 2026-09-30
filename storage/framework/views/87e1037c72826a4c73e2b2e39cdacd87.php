<section class="section">
  <div class="container cta-box reveal">
    <h2><?php echo e($cta['title']); ?></h2>
    <p><?php echo e($cta['text']); ?></p>
    <?php echo $__env->make('frontend.partials.button', ['button' => $cta['button'], 'class' => 'primary'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/cta.blade.php ENDPATH**/ ?>