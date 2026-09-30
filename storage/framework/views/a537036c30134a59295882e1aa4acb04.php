<?php $__env->startSection('title','Map Places'); ?>
<?php $__env->startSection('content'); ?>
<?php ($mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg'); ?>
<div class="card">
    <h2 style="margin-top:0">Create Place / POI</h2>
    <p class="small">Click the map picker to fill X and Y, then attach the place to its nearest saved route vertex so it can be used in pathway searches.</p>
    <form method="post" action="<?php echo e(route('admin.map.places.store')); ?>" id="placeCreateForm">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <div><label>Name</label><input name="name" required></div>
            <div><label>Slug</label><input name="slug" placeholder="Generated automatically"></div>
            <div><label>Category</label><select name="map_category_id"><option value="">None</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div><label>Route Vertex</label><select name="map_node_id"><option value="">None</option><?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($node->id); ?>"><?php echo e($node->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div><label>Pin No.</label><input name="pin_number" type="number" min="1"></div>
            <div><label>Icon</label><input name="icon" placeholder="🏨"></div>
            <div><label>X</label><input id="placeXCreate" name="x" type="number" step="0.001" min="0" max="<?php echo e($map->width); ?>" required></div>
            <div><label>Y</label><input id="placeYCreate" name="y" type="number" step="0.001" min="0" max="<?php echo e($map->height); ?>" required></div>
            <div><label>Sort</label><input name="sort_order" type="number" min="0" value="0"></div>
        </div>
        <div style="margin-top:10px"><label>Subtitle</label><input name="subtitle"></div>
        <div style="margin-top:10px"><label>Description</label><textarea name="description"></textarea></div>
        <div class="checkbox-row">
            <label><input type="checkbox" name="is_qr_point" value="1"> QR link point</label>
            <label><input type="checkbox" name="is_active" value="1" checked> Visible</label>
        </div>
        <p><button class="btn" type="submit">Save Place</button></p>
    </form>
</div>

<div class="card">
    <h2 style="margin-top:0">Place Coordinate Picker</h2>
    <p class="small">Tap or click the exact place location. The red marker and create-form coordinates update immediately.</p>
    <div class="drawing-scroll">
        <div id="placePickerCanvas" class="drawing-canvas" style="width:<?php echo e($map->width); ?>px;height:<?php echo e($map->height); ?>px">
            <img src="<?php echo e(asset($mapImage)); ?>" alt="Resort map" style="width:<?php echo e($map->width); ?>px;height:<?php echo e($map->height); ?>px">
            <div id="placePickerPin" class="picker-pin" hidden></div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-top:0">Saved Places</h2>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Place</th><th>Position</th><th>Route Vertex</th><th>Status</th><th>Edit</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $places; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $place): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><strong><?php echo e($place->pin_number); ?>. <?php echo e($place->name); ?></strong><br><span class="small"><?php echo e($place->slug); ?> | <?php echo e($place->category?->name); ?></span></td>
                <td><?php echo e($place->x); ?>, <?php echo e($place->y); ?></td>
                <td><?php echo e($place->routeNode?->name ?? 'Not attached'); ?></td>
                <td><?php echo e($place->is_active ? 'Visible' : 'Hidden'); ?> <?php if($place->is_qr_point): ?> / QR <?php endif; ?></td>
                <td style="min-width:520px">
                    <form method="post" action="<?php echo e(route('admin.map.places.update', $place)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="form-grid">
                            <div><label>Name</label><input name="name" value="<?php echo e($place->name); ?>" required></div>
                            <div><label>Slug</label><input name="slug" value="<?php echo e($place->slug); ?>"></div>
                            <div><label>Category</label><select name="map_category_id"><option value="">None</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->id); ?>" <?php if($place->map_category_id === $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                            <div><label>Route Vertex</label><select name="map_node_id"><option value="">None</option><?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($node->id); ?>" <?php if($place->map_node_id === $node->id): echo 'selected'; endif; ?>><?php echo e($node->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                            <div><label>Pin</label><input name="pin_number" type="number" min="1" value="<?php echo e($place->pin_number); ?>"></div>
                            <div><label>Icon</label><input name="icon" value="<?php echo e($place->icon); ?>"></div>
                            <div><label>X</label><input name="x" type="number" min="0" max="<?php echo e($map->width); ?>" step="0.001" value="<?php echo e($place->x); ?>" required></div>
                            <div><label>Y</label><input name="y" type="number" min="0" max="<?php echo e($map->height); ?>" step="0.001" value="<?php echo e($place->y); ?>" required></div>
                            <div><label>Sort</label><input name="sort_order" type="number" min="0" value="<?php echo e($place->sort_order); ?>"></div>
                        </div>
                        <div style="margin-top:8px"><label>Subtitle</label><input name="subtitle" value="<?php echo e($place->subtitle); ?>"></div>
                        <div style="margin-top:8px"><label>Description</label><textarea name="description"><?php echo e($place->description); ?></textarea></div>
                        <div class="checkbox-row">
                            <label><input type="checkbox" name="is_qr_point" value="1" <?php if($place->is_qr_point): echo 'checked'; endif; ?>> QR point</label>
                            <label><input type="checkbox" name="is_active" value="1" <?php if($place->is_active): echo 'checked'; endif; ?>> Visible</label>
                        </div>
                        <button class="btn secondary" type="submit">Update Place</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    </div>
</div>
<script>
(function(){
    const canvas=document.getElementById('placePickerCanvas');
    const pin=document.getElementById('placePickerPin');
    const xInput=document.getElementById('placeXCreate');
    const yInput=document.getElementById('placeYCreate');
    canvas.addEventListener('click',function(event){
        const rect=canvas.getBoundingClientRect();
        const x=Math.round((event.clientX-rect.left)*(<?php echo e($map->width); ?>/rect.width));
        const y=Math.round((event.clientY-rect.top)*(<?php echo e($map->height); ?>/rect.height));
        xInput.value=x;yInput.value=y;
        pin.hidden=false;pin.style.left=`${(x/<?php echo e($map->width); ?>)*100}%`;pin.style.top=`${(y/<?php echo e($map->height); ?>)*100}%`;
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.map.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/map/places.blade.php ENDPATH**/ ?>