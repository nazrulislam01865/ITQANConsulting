<?php $__env->startSection('title', 'Work Order '.$order->reference_number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <span class="section-key"><?php echo e($order->reference_number); ?></span>
    <h2>Work order request.</h2>
    <p>Submitted on <?php echo e($order->created_at?->format('d M Y, h:i A')); ?> for <?php echo e($order->work_title); ?>.</p>
  </div>
  <div class="button-row">
    <a class="btn" href="<?php echo e(route('admin.work-orders.index')); ?>">Back to Work Orders</a>
    <form class="danger-inline" method="POST" action="<?php echo e(route('admin.work-orders.destroy', $order)); ?>" onsubmit="return confirm('Delete this work order request?')">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
      <button class="btn danger" type="submit">Delete</button>
    </form>
  </div>
</div>

<div class="grid two">
  <div class="card light">
    <h3>Client</h3>
    <p><strong>Name:</strong> <?php echo e($order->customer_name); ?></p>
    <p><strong>Company:</strong> <?php echo e($order->company_name ?: '—'); ?></p>
    <p><strong>Email:</strong> <a href="mailto:<?php echo e($order->email); ?>"><?php echo e($order->email); ?></a></p>
    <p><strong>Phone / WhatsApp:</strong> <a href="tel:<?php echo e($order->phone); ?>"><?php echo e($order->phone); ?></a></p>
    <p><strong>Preferred contact:</strong> <?php echo e(ucfirst($order->preferred_contact_method)); ?></p>
  </div>

  <div class="card light">
    <h3>Requested Work</h3>
    <p><strong>Work:</strong> <?php echo e($order->work_title); ?></p>
    <p><strong>Category:</strong> <?php echo e($order->work_category ?: '—'); ?></p>
    <p><strong>Budget:</strong> <?php echo e($order->budget_range); ?></p>
    <p><strong>Timeline:</strong> <?php echo e($order->timeline); ?></p>
    <p><strong>Current status:</strong> <?php echo e($order->statusLabel()); ?></p>
  </div>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Project Summary</h3>
  <p style="white-space:pre-line"><?php echo e($order->project_summary); ?></p>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Important Features or Requirements</h3>
  <p style="white-space:pre-line"><?php echo e($order->requirements ?: 'No additional requirements were provided.'); ?></p>
</div>

<form class="form-card" method="POST" action="<?php echo e(route('admin.work-orders.update', $order)); ?>" style="margin-top:18px">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>
  <div class="form-grid">
    <div class="field">
      <label for="workOrderStatus">Request status</label>
      <select id="workOrderStatus" name="status" required>
        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($value); ?>" <?php if(old('status', $order->status) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="field full">
      <label for="workOrderNotes">Internal notes</label>
      <textarea id="workOrderNotes" name="internal_notes" maxlength="5000" placeholder="Add follow-up notes, scope details, quotation information, or decisions."><?php echo e(old('internal_notes', $order->internal_notes)); ?></textarea>
      <div class="help">These notes are visible only inside the ITQAN admin panel.</div>
    </div>
  </div>
  <button class="btn primary" type="submit">Save Request Update</button>
</form>

<div class="card" style="margin-top:18px">
  <h3>Technical Information</h3>
  <p><strong>First viewed:</strong> <?php echo e($order->viewed_at?->format('d M Y, h:i A') ?: '—'); ?></p>
  <p><strong>IP:</strong> <?php echo e($order->ip_address ?: '—'); ?></p>
  <p><strong>User Agent:</strong> <?php echo e($order->user_agent ?: '—'); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/work-orders/show.blade.php ENDPATH**/ ?>