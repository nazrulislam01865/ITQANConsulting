<?php
  $settings = $section->settings ?: [];
  $defaults = config('itqan.pages.contact.cta', []);
  $defaultVcard = $defaults['vcard'] ?? [];
  $qrPath = $settings['qr_image_path'] ?? null;
  $qrPreviewUrl = $qrPath
    ? asset('storage/' . ltrim($qrPath, '/'))
    : asset(ltrim($defaults['qr_image_url'] ?? 'images/default-itqan-contact-qr.png', '/'));
?>

<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>> Show this QR contact card beside the form</label>

  <div class="field full">
    <label>QR code image</label>
    <div class="image-preview" style="max-width:240px;background:#08111f;padding:16px;border-radius:22px">
      <img src="<?php echo e($qrPreviewUrl); ?>" alt="Current contact page QR image" style="width:100%;aspect-ratio:1;object-fit:contain">
    </div>
    <input type="file" name="qr_image" accept="image/jpeg,image/png,image/webp">
    <small>Upload the QR image shown on the public contact page. Use a square PNG, JPG, or WebP image; transparent PNG is recommended.</small>
  </div>

  <div class="field"><label>QR card label</label><input name="label" value="<?php echo e(old('label', $section->label)); ?>"></div>
  <div class="field full"><label>QR card headline</label><textarea name="title"><?php echo e(old('title', $section->title)); ?></textarea></div>
  <div class="field full"><label>QR card description</label><textarea name="lead"><?php echo e(old('lead', $section->lead)); ?></textarea></div>

  <div class="field"><label>Primary button text</label><input name="button_text" value="<?php echo e(old('button_text', $section->button_text)); ?>"></div>
  <div class="field">
    <label>Primary button route</label>
    <select name="button_route">
      <option value="">Use custom URL below</option>
      <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($route); ?>" <?php if(old('button_route', $section->button_route) === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="field full"><label>Primary button URL</label><input name="button_url" value="<?php echo e(old('button_url', $section->button_url)); ?>" placeholder="https://wa.me/..."></div>

  <div class="field"><label>Save contact button text</label><input name="settings[save_button_text]" value="<?php echo e(old('settings.save_button_text', array_key_exists('save_button_text', $settings) ? $settings['save_button_text'] : ($defaults['save_button_text'] ?? 'Save Contact'))); ?>"></div>
  <div class="field"><label>Downloaded contact filename</label><input name="settings[contact_file_name]" value="<?php echo e(old('settings.contact_file_name', array_key_exists('contact_file_name', $settings) ? $settings['contact_file_name'] : ($defaults['contact_file_name'] ?? 'md-aminul-islam-itqan-consulting.vcf'))); ?>"></div>
  <div class="field full"><label>QR image alt text</label><input name="settings[qr_alt]" value="<?php echo e(old('settings.qr_alt', array_key_exists('qr_alt', $settings) ? $settings['qr_alt'] : ($defaults['qr_alt'] ?? 'Digital contact QR code'))); ?>"></div>
  <div class="field full"><label>QR caption</label><input name="settings[qr_caption]" value="<?php echo e(old('settings.qr_caption', array_key_exists('qr_caption', $settings) ? $settings['qr_caption'] : ($defaults['qr_caption'] ?? ''))); ?>"></div>
</div>

<div style="height:1px;background:rgba(5,7,19,.12);margin:30px 0"></div>

<div class="page-head" style="margin-bottom:18px">
  <div>
    <h2 style="font-size:1.35rem">Digital contact details</h2>
    <p>These fields build the downloadable .vcf contact file.</p>
  </div>
</div>

<div class="form-grid">
  <div class="field"><label>First name</label><input name="settings[first_name]" value="<?php echo e(old('settings.first_name', array_key_exists('first_name', $settings) ? $settings['first_name'] : ($defaultVcard['first_name'] ?? ''))); ?>"></div>
  <div class="field"><label>Last name</label><input name="settings[last_name]" value="<?php echo e(old('settings.last_name', array_key_exists('last_name', $settings) ? $settings['last_name'] : ($defaultVcard['last_name'] ?? ''))); ?>"></div>
  <div class="field"><label>Full display name</label><input name="settings[full_name]" value="<?php echo e(old('settings.full_name', array_key_exists('full_name', $settings) ? $settings['full_name'] : ($defaultVcard['full_name'] ?? ''))); ?>"></div>
  <div class="field"><label>Credentials</label><input name="settings[credentials]" value="<?php echo e(old('settings.credentials', array_key_exists('credentials', $settings) ? $settings['credentials'] : ($defaultVcard['credentials'] ?? ''))); ?>" placeholder="PMP, CSM"></div>
  <div class="field"><label>Organization</label><input name="settings[organization]" value="<?php echo e(old('settings.organization', array_key_exists('organization', $settings) ? $settings['organization'] : ($defaultVcard['organization'] ?? ''))); ?>"></div>
  <div class="field"><label>Job title</label><input name="settings[job_title]" value="<?php echo e(old('settings.job_title', array_key_exists('job_title', $settings) ? $settings['job_title'] : ($defaultVcard['job_title'] ?? ''))); ?>"></div>
  <div class="field"><label>Phone</label><input name="settings[phone]" value="<?php echo e(old('settings.phone', array_key_exists('phone', $settings) ? $settings['phone'] : ($defaultVcard['phone'] ?? ''))); ?>"></div>
  <div class="field"><label>WhatsApp number</label><input name="settings[whatsapp]" value="<?php echo e(old('settings.whatsapp', array_key_exists('whatsapp', $settings) ? $settings['whatsapp'] : ($defaultVcard['whatsapp'] ?? ''))); ?>"></div>
  <div class="field"><label>Email</label><input type="email" name="settings[email]" value="<?php echo e(old('settings.email', array_key_exists('email', $settings) ? $settings['email'] : ($defaultVcard['email'] ?? ''))); ?>"></div>
  <div class="field"><label>Website</label><input name="settings[website]" value="<?php echo e(old('settings.website', array_key_exists('website', $settings) ? $settings['website'] : ($defaultVcard['website'] ?? ''))); ?>" placeholder="https://..."></div>
  <div class="field full"><label>Contact note</label><textarea name="settings[note]" rows="6"><?php echo e(old('settings.note', array_key_exists('note', $settings) ? $settings['note'] : ($defaultVcard['note'] ?? ''))); ?></textarea></div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/contact-cta.blade.php ENDPATH**/ ?>