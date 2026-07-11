<?php $__env->startSection('title', $section->admin_title); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2><?php echo e($section->admin_title); ?></h2>
    <p><?php echo e($pageTitle); ?></p>
  </div>
  <a class="btn" href="<?php echo e(route('admin.pages.index', $pageKey)); ?>">Back to <?php echo e($pageTitle); ?></a>
</div>

<form class="form-card" method="POST" action="<?php echo e(route('admin.pages.sections.update', $section)); ?>" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>
  <?php if ($__env->exists('admin.pages.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes])) echo $__env->make('admin.pages.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <div class="button-row"><button class="btn primary" type="submit">Save Section Content</button></div>
</form>

<?php echo $__env->make('admin.pages.sections.items', ['section' => $section, 'routes' => $routes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/edit-section.blade.php ENDPATH**/ ?>