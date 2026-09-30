<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>> Show this section</label>
  <div class="field"><label>Section label</label><input name="label" value="<?php echo e(old('label', $section->label)); ?>"></div>
  <div class="field full"><label>Headline</label><textarea name="title"><?php echo e(old('title', $section->title)); ?></textarea></div>
  <div class="field full"><label>Lead text</label><textarea name="lead"><?php echo e(old('lead', $section->lead)); ?></textarea></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/partials/standard-section-fields.blade.php ENDPATH**/ ?>