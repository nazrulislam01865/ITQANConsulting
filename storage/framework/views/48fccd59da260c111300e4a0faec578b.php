<?php $__env->startSection('title','Route Vertices'); ?>
<?php $__env->startSection('content'); ?>
<?php ($mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg'); ?>
<div class="card">
    <h2 style="margin-top:0">Create Route Vertex</h2>
    <p class="small">Vertices are exact road endpoints and junctions. Click the coordinate picker below, then save the vertex.</p>
    <form method="post" action="<?php echo e(route('admin.map.nodes.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <div><label>Name</label><input name="name" required></div>
            <div><label>Code</label><input name="code" placeholder="Generated automatically"></div>
            <div><label>X</label><input id="nodeXCreate" name="x" type="number" step="0.001" min="0" max="<?php echo e($map->width); ?>" required></div>
            <div><label>Y</label><input id="nodeYCreate" name="y" type="number" step="0.001" min="0" max="<?php echo e($map->height); ?>" required></div>
            <div><label>Type</label><select name="node_type"><option value="junction">Junction</option><option value="place">Place connector</option><option value="entry">Entry</option><option value="exit">Exit</option></select></div>
        </div>
        <div class="checkbox-row"><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
        <p><button class="btn" type="submit">Save Vertex</button></p>
    </form>
</div>

<div class="card">
    <h2 style="margin-top:0">Vertex Coordinate Picker</h2>
    <p class="small">Tap or click the exact center of a road junction. Connected path endpoints will use this precise coordinate.</p>
    <div class="drawing-scroll">
        <div id="nodePickerCanvas" class="drawing-canvas" style="width:<?php echo e($map->width); ?>px;height:<?php echo e($map->height); ?>px">
            <img src="<?php echo e(asset($mapImage)); ?>" alt="Resort map" style="width:<?php echo e($map->width); ?>px;height:<?php echo e($map->height); ?>px">
            <div id="nodePickerPin" class="picker-pin" hidden></div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-top:0">Saved Route Vertices</h2>
    <p class="small">Moving a vertex automatically realigns the first or last control point of every connected path.</p>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Vertex</th><th>Position</th><th>Type</th><th>Edit</th></tr></thead>
        <tbody>
        <?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><strong><?php echo e($node->name); ?></strong><br><span class="small"><?php echo e($node->code); ?></span></td>
                <td><?php echo e($node->x); ?>, <?php echo e($node->y); ?></td>
                <td><?php echo e($node->node_type); ?> | <?php echo e($node->is_active ? 'Active' : 'Inactive'); ?></td>
                <td style="min-width:430px">
                    <form method="post" action="<?php echo e(route('admin.map.nodes.update', $node)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="form-grid">
                            <div><label>Name</label><input name="name" value="<?php echo e($node->name); ?>" required></div>
                            <div><label>Code</label><input name="code" value="<?php echo e($node->code); ?>"></div>
                            <div><label>X</label><input name="x" type="number" min="0" max="<?php echo e($map->width); ?>" step="0.001" value="<?php echo e($node->x); ?>" required></div>
                            <div><label>Y</label><input name="y" type="number" min="0" max="<?php echo e($map->height); ?>" step="0.001" value="<?php echo e($node->y); ?>" required></div>
                            <div><label>Type</label><select name="node_type"><option value="junction" <?php if($node->node_type==='junction'): echo 'selected'; endif; ?>>Junction</option><option value="place" <?php if($node->node_type==='place'): echo 'selected'; endif; ?>>Place connector</option><option value="entry" <?php if($node->node_type==='entry'): echo 'selected'; endif; ?>>Entry</option><option value="exit" <?php if($node->node_type==='exit'): echo 'selected'; endif; ?>>Exit</option></select></div>
                        </div>
                        <div class="checkbox-row"><label><input type="checkbox" name="is_active" value="1" <?php if($node->is_active): echo 'checked'; endif; ?>> Active</label></div>
                        <button class="btn secondary" type="submit">Update Vertex</button>
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
    const canvas=document.getElementById('nodePickerCanvas');
    const pin=document.getElementById('nodePickerPin');
    const xInput=document.getElementById('nodeXCreate');
    const yInput=document.getElementById('nodeYCreate');
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

<?php echo $__env->make('admin.map.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/map/nodes.blade.php ENDPATH**/ ?>