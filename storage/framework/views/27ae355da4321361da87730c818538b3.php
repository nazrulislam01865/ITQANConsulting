<?php $__env->startSection('title', $section->admin_title); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2><?php echo e($section->admin_title); ?></h2>
  </div>
  <a class="btn" href="<?php echo e(route('admin.home.index')); ?>">Back to Home Sections</a>
</div>

<form class="form-card" method="POST" action="<?php echo e(route('admin.home.sections.update', $section)); ?>" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>
  <?php if ($__env->exists('admin.home.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes])) echo $__env->make('admin.home.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <div class="button-row"><button class="btn primary" type="submit">Save Section Content</button></div>
</form>

<?php if (! (in_array($section->section_key, ['home_founder', 'home_works_preview', 'home_cta'], true))): ?>
  <?php echo $__env->make('admin.home.sections.items', ['section' => $section, 'routes' => $routes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/edit-section.blade.php ENDPATH**/ ?>