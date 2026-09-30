<?php $__env->startSection('title','Map Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2 style="margin-top:0">Map Settings</h2>
    <p class="small">Upload or replace the illustrated resort map. Places, route vertices, and curved path control points are stored separately in the database.</p>
    <form method="post" action="<?php echo e(route('admin.map.settings.update')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="form-grid">
            <div><label>Name</label><input name="name" value="<?php echo e($map->name); ?>" required></div>
            <div><label>Map Type</label><select name="map_type"><option value="image" <?php if($map->map_type === 'image'): echo 'selected'; endif; ?>>Image / 2.5D</option><option value="svg" <?php if($map->map_type === 'svg'): echo 'selected'; endif; ?>>SVG</option><option value="template_svg" <?php if($map->map_type === 'template_svg'): echo 'selected'; endif; ?>>Built-in SVG template</option><option value="3d" <?php if($map->map_type === '3d'): echo 'selected'; endif; ?>>3D preview</option></select></div>
            <div><label>Exact Width</label><input name="width" type="number" value="<?php echo e($map->width); ?>" required></div>
            <div><label>Exact Height</label><input name="height" type="number" value="<?php echo e($map->height); ?>" required></div>
            <div><label>Meters per pixel</label><input name="meters_per_pixel" type="number" step="0.0001" value="<?php echo e($map->meters_per_pixel); ?>" required></div>
            <div><label>Walk meters/min</label><input name="walk_meters_per_minute" type="number" value="<?php echo e($map->walk_meters_per_minute); ?>" required></div>
            <div><label>Buggy meters/min</label><input name="buggy_meters_per_minute" type="number" value="<?php echo e($map->buggy_meters_per_minute); ?>" required></div>
        </div>
        <div style="margin-top:10px"><label>Current map file</label><input name="map_file" value="<?php echo e($map->map_file); ?>" placeholder="Built-in template is used when blank"></div>
        <div style="margin-top:10px"><label>Upload new map image</label><input name="map_upload" type="file" accept="image/*"></div>
        <p class="small">The width and height must match the coordinate system used by saved places and paths.</p>
        <p><button class="btn" type="submit">Save Map Settings</button></p>
    </form>
</div>
<div class="card">
    <h2 style="margin-top:0">Current Map Preview</h2>
    <div class="map-preview"><img src="<?php echo e($map->map_file ? asset($map->map_file) : asset('assets/itqan-external-guest-map/template-map.svg')); ?>" alt="Map preview"></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.map.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/map/settings.blade.php ENDPATH**/ ?>