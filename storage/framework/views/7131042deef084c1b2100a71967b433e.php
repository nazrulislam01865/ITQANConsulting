<?php $__env->startSection('title', 'Home Page'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Home page sections.</h2>
    <p>Each frontend home section is controlled separately. The frontend design classes stay untouched.</p>
  </div>
</div>

<div class="grid two">
  <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <article class="card section-card">
      <div>
        <div class="section-key"><?php echo e($section->section_key); ?></div>
        <h3><?php echo e($section->admin_title); ?></h3>
        <p><?php echo e($section->label ?: 'No label set'); ?> • <?php echo e($section->items_count); ?> item(s)</p>
        <div style="margin-top:12px"><span class="pill <?php echo e($section->is_active ? '' : 'off'); ?>"><?php echo e($section->is_active ? 'Active' : 'Hidden'); ?></span></div>
      </div>
      <a class="btn primary" href="<?php echo e(route('admin.home.sections.edit', $section)); ?>">Edit</a>
    </article>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/index.blade.php ENDPATH**/ ?>