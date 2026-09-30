<?php $__env->startSection('title', 'Footer Menu'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Footer menu control.</h2>
    <p>Footer groups are separated from the header. You can manage Pages and Services links independently.</p>
  </div>
</div>

<div class="form-card">
  <h3>Add Footer Menu Item</h3>
  <form method="POST" action="<?php echo e(route('admin.footer-menu.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="form-grid">
      <div class="field"><label>Group</label><select name="group_key"><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>"><?php echo e($title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      <div class="field"><label>Group title</label><input name="group_title" value="Pages" required></div>
      <div class="field"><label>Label</label><input name="label" required></div>
      <div class="field"><label>Route</label><select name="route_name"><option value="">Use custom URL / plain text</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>"><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
      <div class="field"><label>Custom URL</label><input name="url" placeholder="https:// or /custom-page"></div>
      <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button class="btn primary" type="submit">Add Item</button>
  </form>
</div>

<div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Group</th><th>Label</th><th>Route</th><th>URL</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <form method="POST" action="<?php echo e(route('admin.footer-menu.update', $item)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <td><input name="group_key" value="<?php echo e($item->group_key); ?>" style="width:120px"><input name="group_title" value="<?php echo e($item->group_title); ?>" style="width:130px;margin-top:6px"></td>
            <td><input name="label" value="<?php echo e($item->label); ?>" required></td>
            <td><select name="route_name"><option value="">Custom / plain</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if($item->route_name === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
            <td><input name="url" value="<?php echo e($item->url); ?>"></td>
            <td><label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if($item->is_active): echo 'checked'; endif; ?>> Active</label></td>
            <td class="button-row"><button class="btn small primary" type="submit">Save</button>
          </form>
          <form class="danger-inline" method="POST" action="<?php echo e(route('admin.footer-menu.destroy', $item)); ?>" onsubmit="return confirm('Delete this footer menu item?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="btn small danger" type="submit">Delete</button>
          </form></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/menus/footer.blade.php ENDPATH**/ ?>