<?php $__env->startSection('content'); ?>
<main class="page active">
  <section class="section">
    <div class="container cta-box reveal visible">
      <h2>PDF package is not installed yet.</h2>
      <p>Run <strong>composer require barryvdh/laravel-dompdf</strong>, then clear cache and try Download PDF again.</p>
      <a href="<?php echo e(route('catalog')); ?>" class="btn primary">Back to Catalog</a>
    </div>
  </section>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/catalog-pdf-unavailable.blade.php ENDPATH**/ ?>