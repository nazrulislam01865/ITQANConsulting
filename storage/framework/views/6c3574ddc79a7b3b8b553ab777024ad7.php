<div class="section-head reveal">
  <div>
    <?php if(isset($label)): ?><div class="label"><?php echo e($label); ?></div><?php endif; ?>
    <h2 class="headline"><?php echo $title; ?></h2>
  </div>
  <?php if(isset($lead)): ?><p class="lead"><?php echo e($lead); ?></p><?php endif; ?>
  <?php if(! empty($sectionButton)): ?>
    <?php echo $__env->make('frontend.partials.button', ['button' => $sectionButton, 'class' => $sectionButton['class'] ?? ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/section-head.blade.php ENDPATH**/ ?>