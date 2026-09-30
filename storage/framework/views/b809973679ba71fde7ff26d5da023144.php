<div class="form-grid">
  <div class="field"><label>Section label</label><input name="label" value="<?php echo e(old('label', $section->label)); ?>"></div>
  <div class="field full"><label>Headline HTML</label><textarea name="title"><?php echo e(old('title', $section->title)); ?></textarea><div class="help">Use &lt;br&gt; only when you want a deliberate line break.</div></div>
  <div class="field full"><label>Intro text</label><textarea name="lead"><?php echo e(old('lead', $section->lead)); ?></textarea></div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>> Show this section</label>
</div>
<div class="form-note">
  The process line, active steps, and scroll animation are automatic. Add or edit the steps below and use Display order to control their sequence.
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/home-working.blade.php ENDPATH**/ ?>