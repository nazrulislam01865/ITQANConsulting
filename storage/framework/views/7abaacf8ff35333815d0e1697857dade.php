<?php $__env->startSection('content'); ?>
<main id="works" class="page active">
  <?php echo $__env->make('frontend.partials.page-hero', ['hero' => $page['hero']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <section class="section soft-white">
    <div class="container">
      <div class="tabs reveal" role="tablist" aria-label="Work filters">
        <?php $__currentLoopData = $collections['work_filters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <button class="tab <?php echo e($index === 0 ? 'active' : ''); ?>" data-filter="<?php echo e($filter['value']); ?>" type="button" role="tab" aria-selected="<?php echo e($index === 0 ? 'true' : 'false'); ?>"><?php echo e($filter['label']); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="card-grid two" id="workGrid">
        <?php $__currentLoopData = $collections['works']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $work): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('frontend.partials.work-card', ['work' => $work, 'showTags' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/works.blade.php ENDPATH**/ ?>