<?php $__env->startSection('title','Map Preview'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2 style="margin-top:0">Guest Pathway Preview</h2>
    <p class="small">These links open the same mobile-friendly guest map and display a pathway calculated from the saved database edges.</p>
    <a class="btn" href="<?php echo e(route('external-guest-map.index')); ?>?from=reception&to=pool" target="_blank">Reception → Family Pool</a>
    <a class="btn secondary" href="<?php echo e(route('external-guest-map.index')); ?>?from=parking&to=restaurant" target="_blank">Parking → Restaurant</a>
    <a class="btn secondary" href="<?php echo e(route('external-guest-map.index')); ?>?from=villas&to=cafe" target="_blank">Garden Villas → Lake Café</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.map.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/map/preview.blade.php ENDPATH**/ ?>