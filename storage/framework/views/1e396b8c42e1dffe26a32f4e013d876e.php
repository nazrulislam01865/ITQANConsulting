<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>> Show this CTA section</label>
  <div class="field full"><label>CTA headline</label><textarea name="title"><?php echo e(old('title', $section->title)); ?></textarea></div>
  <div class="field full"><label>CTA text</label><textarea name="lead"><?php echo e(old('lead', $section->lead)); ?></textarea></div>
  <div class="field"><label>Button text</label><input name="button_text" value="<?php echo e(old('button_text', $section->button_text)); ?>"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Select route</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $section->button_route) === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $section->button_url)); ?>" placeholder="mailto:... or https://..."></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/partials/cta-fields.blade.php ENDPATH**/ ?>