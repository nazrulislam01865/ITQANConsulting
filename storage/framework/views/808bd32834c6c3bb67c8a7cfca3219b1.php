<?php echo $__env->make('admin.home.sections.partials.standard-section-fields', ['section' => $section], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="form-grid">
  <div class="field"><label>Button text</label><input name="button_text" value="<?php echo e(old('button_text', $section->button_text)); ?>"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Select route</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $section->button_route) === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
</div>
<div class="form-note">
  Each service item supports a short introduction, a common problem, and possible deliverables. Keep the text concise so the explorer remains easy to scan. Use Display order to keep the service tabs and panels synchronized.
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/home-services-preview.blade.php ENDPATH**/ ?>