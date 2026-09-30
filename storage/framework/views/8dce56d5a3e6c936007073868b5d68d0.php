<?php $__env->startSection('title','Palace Map Admin'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2 style="margin-top:0"><?php echo e($map->name); ?></h2>
    <p class="small">Map size: <?php echo e($map->width); ?> × <?php echo e($map->height); ?> | Scale: <?php echo e($map->meters_per_pixel); ?> m/px</p>
    <a href="<?php echo e(route('admin.map.preview')); ?>" class="btn">Open Guest Preview</a>
    <form method="post" action="<?php echo e(route('admin.map.reset-seed')); ?>" style="display:inline" onsubmit="return confirm('Reset all map places, vertices, and path geometry from the seed file?')">
        <?php echo csrf_field(); ?>
        <button class="btn secondary" type="submit">Reset Saved Demo Data</button>
    </form>
</div>
<div class="grid">
    <div class="metric"><b><?php echo e($placesCount); ?></b><span>Saved Places</span></div>
    <div class="metric"><b><?php echo e($activePlacesCount); ?></b><span>Visible Places</span></div>
    <div class="metric"><b><?php echo e($nodesCount); ?></b><span>Route Vertices</span></div>
    <div class="metric"><b><?php echo e($edgesCount); ?></b><span>Saved Path Edges</span></div>
    <div class="metric"><b><?php echo e($activeEdgesCount); ?></b><span>Visible Path Edges</span></div>
</div>
<div class="card">
    <h3>Map data model</h3>
    <p>Places are guest-visible destinations. Route vertices are exact junction coordinates. Path connections store the curved control points between those vertices. Every saved path endpoint is automatically snapped to its selected vertex, which prevents gaps and overlapping route joins.</p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.map.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/map/dashboard.blade.php ENDPATH**/ ?>