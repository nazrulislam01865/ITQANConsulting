<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Website control center.</h2>
    <p>Manage the dynamic parts of the ITQAN frontend without touching the template design.</p>
  </div>
  <a class="btn primary" href="<?php echo e(route('admin.home.index')); ?>">Edit Home Page</a>
</div>

<div class="grid three">
  <div class="card"><div class="metric"><?php echo e($homeSections); ?></div><h3>Home Sections</h3><p>Every frontend home section is separated for admin editing.</p></div>
  <div class="card"><div class="metric"><?php echo e($pageSections); ?></div><h3>Inner Page Sections</h3><p>About, Services, Works, Catalog, and Contact sections are editable separately.</p></div>
  <div class="card"><div class="metric"><?php echo e($headerMenuItems); ?></div><h3>Header Menu</h3><p>Control the top navigation labels and links.</p></div>
  <div class="card"><div class="metric"><?php echo e($footerMenuItems); ?></div><h3>Footer Menu</h3><p>Control footer page and service link groups separately.</p></div>
  <a class="card" href="<?php echo e(route('admin.contact-submissions.index')); ?>">
    <div class="metric"><?php echo e($contactResponses); ?></div>
    <h3>Contact Responses</h3>
    <p><?php echo e($unreadContactResponses); ?> unread message(s) from the public contact form.</p>
  </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/dashboard/index.blade.php ENDPATH**/ ?>