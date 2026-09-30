<?php $__env->startSection('title', 'Contact Responses'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Contact responses.</h2>
    <p>Messages submitted from the public contact form are saved here.</p>
  </div>
  <div class="button-row">
    <span class="pill"><?php echo e($totalCount); ?> total</span>
    <span class="pill <?php echo e($unreadCount > 0 ? '' : 'off'); ?>"><?php echo e($unreadCount); ?> unread</span>
  </div>
</div>

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Need</th>
        <th>Message</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($item->created_at?->format('d M Y, h:i A')); ?></td>
          <td>
            <strong><?php echo e($item->name); ?></strong>
            <?php if($item->company_name): ?>
              <br><span class="muted"><?php echo e($item->company_name); ?></span>
            <?php endif; ?>
          </td>
          <td>
            <a href="mailto:<?php echo e($item->email); ?>"><?php echo e($item->email); ?></a>
            <?php if($item->phone): ?>
              <br><span class="muted"><?php echo e($item->phone); ?></span>
            <?php endif; ?>
          </td>
          <td><?php echo e(collect($item->support_types ?? [])->filter()->join(', ') ?: ($item->need ?: '—')); ?></td>
          <td><?php echo e(\Illuminate\Support\Str::limit($item->message, 90)); ?></td>
          <td><span class="pill <?php echo e($item->status === 'unread' ? '' : 'off'); ?>"><?php echo e(ucfirst($item->status)); ?></span></td>
          <td class="button-row">
            <a class="btn small primary" href="<?php echo e(route('admin.contact-submissions.show', $item)); ?>">View</a>
            <form class="danger-inline" method="POST" action="<?php echo e(route('admin.contact-submissions.destroy', $item)); ?>" onsubmit="return confirm('Delete this contact response?')">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button class="btn small danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7">No contact responses yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if($items->hasPages()): ?>
  <?php echo e($items->links('admin.partials.pagination')); ?>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/contact-submissions/index.blade.php ENDPATH**/ ?>