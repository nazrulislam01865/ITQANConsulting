<?php
  $settings = $section->settings ?: [];
  $defaults = config('itqan.pages.contact.form', []);
  $defaultOptions = config('itqan.collections.contact_options', []);
  $defaultSteps = $defaults['steps'] ?? [];
?>

<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>> Show this interactive contact form</label>

  <div class="field"><label>Section label</label><input name="label" value="<?php echo e(old('label', $section->label ?: ($defaults['label'] ?? 'Start a conversation'))); ?>"></div>
  <div class="field full"><label>Section headline</label><textarea name="title" rows="3"><?php echo e(old('title', $section->title ?: ($defaults['title'] ?? ''))); ?></textarea></div>
  <div class="field full"><label>Section introduction</label><textarea name="lead" rows="4"><?php echo e(old('lead', $section->lead ?: ($defaults['intro'] ?? ''))); ?></textarea></div>
</div>

<div style="height:1px;background:rgba(5,7,19,.12);margin:30px 0"></div>

<div class="page-head" style="margin-bottom:18px">
  <div>
    <h2 style="font-size:1.35rem">Wizard step content</h2>
    <p>Edit the title and supporting text shown at each step.</p>
  </div>
</div>

<div class="form-grid">
  <div class="field"><label>Step 1 title</label><input name="settings[problem_step_title]" value="<?php echo e(old('settings.problem_step_title', $settings['problem_step_title'] ?? ($defaultSteps[0]['title'] ?? ''))); ?>"></div>
  <div class="field"><label>Step 1 text</label><textarea name="settings[problem_step_text]" rows="3"><?php echo e(old('settings.problem_step_text', $settings['problem_step_text'] ?? ($defaultSteps[0]['text'] ?? ''))); ?></textarea></div>

  <div class="field"><label>Step 2 title</label><input name="settings[support_step_title]" value="<?php echo e(old('settings.support_step_title', $settings['support_step_title'] ?? ($defaultSteps[1]['title'] ?? ''))); ?>"></div>
  <div class="field"><label>Step 2 text</label><textarea name="settings[support_step_text]" rows="3"><?php echo e(old('settings.support_step_text', $settings['support_step_text'] ?? ($defaultSteps[1]['text'] ?? ''))); ?></textarea></div>

  <div class="field"><label>Step 3 title</label><input name="settings[details_step_title]" value="<?php echo e(old('settings.details_step_title', $settings['details_step_title'] ?? ($defaultSteps[2]['title'] ?? ''))); ?>"></div>
  <div class="field"><label>Step 3 text</label><textarea name="settings[details_step_text]" rows="3"><?php echo e(old('settings.details_step_text', $settings['details_step_text'] ?? ($defaultSteps[2]['text'] ?? ''))); ?></textarea></div>

  <div class="field"><label>Step 4 title</label><input name="settings[message_step_title]" value="<?php echo e(old('settings.message_step_title', $settings['message_step_title'] ?? ($defaultSteps[3]['title'] ?? ''))); ?>"></div>
  <div class="field"><label>Step 4 text</label><textarea name="settings[message_step_text]" rows="3"><?php echo e(old('settings.message_step_text', $settings['message_step_text'] ?? ($defaultSteps[3]['text'] ?? ''))); ?></textarea></div>
</div>

<div style="height:1px;background:rgba(5,7,19,.12);margin:30px 0"></div>

<div class="page-head" style="margin-bottom:18px">
  <div>
    <h2 style="font-size:1.35rem">Choice and dropdown options</h2>
    <p>Enter one option per line. These values are saved with each contact response.</p>
  </div>
</div>

<div class="form-grid">
  <div class="field full"><label>Step 1 problem choices</label><textarea name="settings[problems]" rows="7"><?php echo e(old('settings.problems', $settings['problems'] ?? implode("\n", $defaultOptions['problems'] ?? []))); ?></textarea></div>
  <div class="field full"><label>Step 2 support choices</label><textarea name="settings[needs]" rows="7"><?php echo e(old('settings.needs', $settings['needs'] ?? implode("\n", $defaultOptions['needs'] ?? []))); ?></textarea></div>
  <div class="field"><label>Preferred contact methods</label><textarea name="settings[methods]" rows="6"><?php echo e(old('settings.methods', $settings['methods'] ?? implode("\n", $defaultOptions['methods'] ?? []))); ?></textarea></div>
  <div class="field"><label>Budget ranges</label><textarea name="settings[budgets]" rows="6"><?php echo e(old('settings.budgets', $settings['budgets'] ?? implode("\n", $defaultOptions['budgets'] ?? []))); ?></textarea></div>
</div>

<div style="height:1px;background:rgba(5,7,19,.12);margin:30px 0"></div>

<div class="page-head" style="margin-bottom:18px">
  <div>
    <h2 style="font-size:1.35rem">Submission and success message</h2>
  </div>
</div>

<div class="form-grid">
  <div class="field"><label>Submit button text</label><input name="settings[submit_text]" value="<?php echo e(old('settings.submit_text', $settings['submit_text'] ?? ($defaults['submit_text'] ?? 'Send the messy version'))); ?>"></div>
  <div class="field"><label>Success title</label><input name="settings[success_title]" value="<?php echo e(old('settings.success_title', $settings['success_title'] ?? ($defaults['success_title'] ?? ''))); ?>"></div>
  <div class="field full"><label>Success message</label><textarea name="settings[success_text]" rows="4"><?php echo e(old('settings.success_text', $settings['success_text'] ?? ($defaults['success_text'] ?? ''))); ?></textarea></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/contact-form.blade.php ENDPATH**/ ?>