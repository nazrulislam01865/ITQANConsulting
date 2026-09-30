<?php $__env->startSection('title', 'Social Links'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Social links.</h2>
    <p>Manage social icons once. They are reused in the Home hero, Contact page, Footer, and future social sections.</p>
  </div>
</div>

<div class="form-card" id="add-social-link">
  <h3>Add Social Link</h3>
  <form method="POST" action="<?php echo e(route('admin.social-links.store')); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="form-grid">
      <div class="field">
        <label>Platform</label>
        <input name="platform" required placeholder="LinkedIn, Facebook, WhatsApp, Instagram">
      </div>
      <div class="field">
        <label>Label</label>
        <input name="label" required placeholder="ITQAN on LinkedIn">
      </div>
      <div class="field full">
        <label>Social link</label>
        <input name="url" placeholder="https://...">
      </div>
      <div class="field full">
        <label>Icon image, optional</label>
        <div class="social-upload-inline">
          <span class="social-admin-icon-placeholder">Auto</span>
          <input name="icon_image" type="file" accept="image/png,image/jpeg,image/webp">
          <span class="help">Optional. If empty, the system uses the link favicon or platform fallback.</span>
        </div>
      </div>
      <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button class="btn primary" type="submit">Add Social Link</button>
  </form>
</div>

<div class="table-wrap social-link-table-wrap">
  <table class="admin-table social-link-table">
    <thead>
      <tr>
        <th>Icon</th>
        <th>Platform</th>
        <th>Label</th>
        <th>URL</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <form method="POST" action="<?php echo e(route('admin.social-links.update', $item)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <td class="social-admin-icon-cell">
              <div class="social-icon-inline">
                <img class="social-admin-icon" src="<?php echo e($item->resolvedIconUrl()); ?>" alt="<?php echo e($item->label); ?> icon" loading="lazy">
                <div class="social-icon-control">
                  <strong><?php echo e($item->iconSourceLabel()); ?></strong>
                  <input name="icon_image" type="file" accept="image/png,image/jpeg,image/webp">
                  <?php if($item->uploadedIconUrl()): ?>
                    <label class="check-row compact"><input type="checkbox" name="remove_icon" value="1"> Remove uploaded icon</label>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td><input name="platform" value="<?php echo e($item->platform); ?>" required></td>
            <td><input name="label" value="<?php echo e($item->label); ?>" required></td>
            <td><input name="url" value="<?php echo e($item->url); ?>" placeholder="https://..."></td>
            <td><label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if($item->is_active): echo 'checked'; endif; ?>> Active</label></td>
            <td class="button-row"><button class="btn small primary" type="submit">Save</button>
          </form>
          <form class="danger-inline" method="POST" action="<?php echo e(route('admin.social-links.destroy', $item)); ?>" onsubmit="return confirm('Delete this social link?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="btn small danger" type="submit">Delete</button>
          </form></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6">No social links added yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/social-links/index.blade.php ENDPATH**/ ?>