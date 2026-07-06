<?php if(session('success')): ?>
  <div class="status"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('status')): ?>
  <div class="status"><?php echo e(session('status')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
  <div class="error-list">
    <strong>Please fix the highlighted issues.</strong>
    <ul>
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/partials/alerts.blade.php ENDPATH**/ ?>