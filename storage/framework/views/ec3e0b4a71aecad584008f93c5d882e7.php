<?php $__env->startSection('title', 'Work Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Work order requests.</h2>
    <p>Review requests submitted from the public Works page and track each request from first review to completion.</p>
  </div>
  <div class="button-row">
    <span class="pill"><?php echo e($totalCount); ?> total</span>
    <span class="pill <?php echo e($newCount > 0 ? '' : 'off'); ?>"><?php echo e($newCount); ?> new</span>
    <span class="pill <?php echo e($unviewedCount > 0 ? '' : 'off'); ?>"><?php echo e($unviewedCount); ?> unviewed</span>
  </div>
</div>

<form class="form-card work-order-filter" method="GET" action="<?php echo e(route('admin.work-orders.index')); ?>">
  <div class="form-grid">
    <div class="field">
      <label for="workOrderSearch">Search requests</label>
      <input id="workOrderSearch" type="search" name="q" value="<?php echo e($search); ?>" placeholder="Reference, work, client, email, or phone">
    </div>
    <div class="field">
      <label for="workOrderStatus">Status</label>
      <select id="workOrderStatus" name="status">
        <option value="">All statuses</option>
        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($value); ?>" <?php if($selectedStatus === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
  </div>
  <div class="button-row">
    <button class="btn primary" type="submit">Apply Filters</button>
    <?php if($search !== '' || filled($selectedStatus)): ?>
      <a class="btn" href="<?php echo e(route('admin.work-orders.index')); ?>">Clear</a>
    <?php endif; ?>
  </div>
</form>

<div class="table-wrap">
  <table class="admin-table work-order-table">
    <thead>
      <tr>
        <th>Reference / Date</th>
        <th>Requested Work</th>
        <th>Client</th>
        <th>Budget / Timeline</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="<?php echo e($order->isUnviewed() ? 'is-unviewed' : ''); ?>">
          <td>
            <strong><?php echo e($order->reference_number); ?></strong>
            <br><span class="muted"><?php echo e($order->created_at?->format('d M Y, h:i A')); ?></span>
            <?php if($order->isUnviewed()): ?>
              <br><span class="pill">Unviewed</span>
            <?php endif; ?>
          </td>
          <td>
            <strong><?php echo e($order->work_title); ?></strong>
            <?php if($order->work_category): ?>
              <br><span class="muted"><?php echo e($order->work_category); ?></span>
            <?php endif; ?>
          </td>
          <td>
            <strong><?php echo e($order->customer_name); ?></strong>
            <?php if($order->company_name): ?>
              <br><span class="muted"><?php echo e($order->company_name); ?></span>
            <?php endif; ?>
            <br><a href="mailto:<?php echo e($order->email); ?>"><?php echo e($order->email); ?></a>
            <br><span class="muted"><?php echo e($order->phone); ?></span>
          </td>
          <td>
            <?php echo e($order->budget_range); ?>

            <br><span class="muted"><?php echo e($order->timeline); ?></span>
          </td>
          <td><span class="pill work-order-status status-<?php echo e($order->status); ?>"><?php echo e($order->statusLabel()); ?></span></td>
          <td class="button-row">
            <a class="btn small primary" href="<?php echo e(route('admin.work-orders.show', $order)); ?>">View</a>
            <form class="danger-inline" method="POST" action="<?php echo e(route('admin.work-orders.destroy', $order)); ?>" onsubmit="return confirm('Delete this work order request?')">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button class="btn small danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6">No work order requests match the current filters.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if($orders->hasPages()): ?>
  <?php echo e($orders->links('admin.partials.pagination')); ?>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/work-orders/index.blade.php ENDPATH**/ ?>