<?php $__env->startSection('title', 'Site Settings, Logo & Favicon'); ?>

<?php $__env->startSection('content'); ?>
<?php
  $favicon = \App\Support\Favicon::current();
?>

<div class="page-head">
  <div>
    <h2>Brand, logo, favicon, and global content.</h2>
    <p>Upload the frontend logo, browser tab favicon, and update global text used by the frontend header and footer.</p>
  </div>
</div>

<form class="form-card" method="POST" action="<?php echo e(route('admin.site-settings.update')); ?>" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>
  <div class="form-grid">
    <div class="field">
      <label for="site_name">Site name</label>
      <input id="site_name" name="site_name" value="<?php echo e(old('site_name', $settings->site_name ?? 'ITQAN Consulting')); ?>" required>
    </div>
    <div class="field">
      <label for="mark_text">Fallback mark text</label>
      <input id="mark_text" name="mark_text" value="<?php echo e(old('mark_text', $settings->mark_text ?? 'IC')); ?>" maxlength="10">
      <div class="help">Used only when no logo image is uploaded.</div>
    </div>
    <div class="field">
      <label for="logo">Logo image</label>
      <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp">
      <div class="help">Recommended: transparent PNG or WEBP. Maximum size: 2 MB.</div>
      <?php if($settings?->logoUrl()): ?>
        <div class="media-preview-block">
          <span>Current logo</span>
          <img class="logo-preview" src="<?php echo e($settings->logoUrl()); ?>" alt="Current logo">
        </div>
      <?php elseif(! empty($settings?->logo_path)): ?>
        <div class="upload-warning">The saved logo file was not found. Please upload the logo again.</div>
      <?php endif; ?>
    </div>
    <div class="field">
      <label for="favicon">Browser favicon</label>
      <input id="favicon" name="favicon" type="file" accept=".ico,image/png,image/jpeg,image/webp">
      <div class="help">Upload the icon shown in browser tabs. Supported: ICO, PNG, WEBP, JPG/JPEG. Maximum size: 1 MB.</div>
      <?php if($favicon): ?>
        <div class="media-preview-block favicon-preview-block">
          <span>Current favicon</span>
          <img class="favicon-preview" src="<?php echo e(asset($favicon['path'])); ?>?v=<?php echo e($favicon['version']); ?>" alt="Current favicon">
          <small><?php echo e($favicon['path']); ?></small>
        </div>
      <?php endif; ?>
    </div>
    <div class="field">
      <label for="tagline">Tagline</label>
      <input id="tagline" name="tagline" value="<?php echo e(old('tagline', $settings->tagline ?? 'Sincere Services. Lasting Results.')); ?>">
    </div>
    <div class="field">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?php echo e(old('email', $settings->email ?? 'hello@itqanconsulting.com')); ?>">
    </div>
    <div class="field">
      <label for="address">Address</label>
      <input id="address" name="address" value="<?php echo e(old('address', $settings->address ?? 'Dhaka, Bangladesh')); ?>">
    </div>
    <div class="field full">
      <label for="description">Footer description</label>
      <textarea id="description" name="description"><?php echo e(old('description', $settings->description ?? 'Practical consulting, technology, and delivery support for businesses in Bangladesh and beyond.')); ?></textarea>
    </div>
    <div class="field">
      <label for="primary_cta_text">Header CTA text</label>
      <input id="primary_cta_text" name="primary_cta_text" value="<?php echo e(old('primary_cta_text', $settings->primary_cta_text ?? 'Book a Consultation')); ?>">
    </div>
    <div class="field">
      <label for="primary_cta_route">Header CTA route</label>
      <select id="primary_cta_route" name="primary_cta_route">
        <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($route); ?>" <?php if(old('primary_cta_route', $settings->primary_cta_route ?? 'contact') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="field">
      <label for="footer_bottom_left">Footer bottom left</label>
      <input id="footer_bottom_left" name="footer_bottom_left" value="<?php echo e(old('footer_bottom_left', $settings->footer_bottom_left ?? 'Consulting | Software Development | ERP | Project Management | Business Automation | Training')); ?>">
    </div>
    <div class="field">
      <label for="copyright">Copyright</label>
      <input id="copyright" name="copyright" value="<?php echo e(old('copyright', $settings->copyright ?? '© 2026 ITQAN Consulting.')); ?>">
    </div>
  </div>
  <div class="button-row"><button class="btn primary" type="submit">Save Site Settings</button></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/site-settings/edit.blade.php ENDPATH**/ ?>