<?php $__env->startSection('title', 'Contact Responses'); ?>

<?php $__env->startSection('content'); ?>
<style>
  .contact-bulk-toolbar{display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:12px;margin:0 0 12px}
  .contact-bulk-toolbar .muted{font-size:.85rem}
  .contact-bulk-toolbar .btn:disabled{opacity:.45;cursor:not-allowed;transform:none;box-shadow:none}
  .admin-table th.contact-select-col,.admin-table td.contact-select-col{width:52px;text-align:center;padding-inline:12px}
  .admin-table input.contact-response-checkbox{appearance:auto;width:18px;height:18px;margin:0;padding:0;border:0;border-radius:4px;vertical-align:middle;accent-color:var(--admin-blue);cursor:pointer}
</style>

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

<?php if($items->count()): ?>
  <form id="contact-bulk-form" class="contact-bulk-toolbar" method="POST" action="<?php echo e(route('admin.contact-submissions.bulk-destroy')); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <span class="muted" id="contact-selected-count" role="status" aria-live="polite">0 selected on this page</span>
    <button class="btn small danger" id="contact-bulk-delete" type="submit" disabled>Delete selected</button>
  </form>
<?php endif; ?>

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th class="contact-select-col">
          <input type="checkbox" class="contact-response-checkbox" id="contact-select-all" aria-label="Select all contact responses on this page" <?php if(!$items->count()): echo 'disabled'; endif; ?>>
        </th>
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
          <td class="contact-select-col">
            <input type="checkbox" class="contact-response-checkbox contact-row-select" name="ids[]" value="<?php echo e($item->id); ?>" form="contact-bulk-form" aria-label="Select response from <?php echo e($item->name); ?>">
          </td>
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
        <tr><td colspan="8">No contact responses yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if($items->hasPages()): ?>
  <?php echo e($items->links('admin.partials.pagination')); ?>

<?php endif; ?>

<?php if($items->count()): ?>
<script>
(() => {
  const form = document.getElementById('contact-bulk-form');
  const selectAll = document.getElementById('contact-select-all');
  const rows = Array.from(document.querySelectorAll('.contact-row-select'));
  const deleteButton = document.getElementById('contact-bulk-delete');
  const selectedCount = document.getElementById('contact-selected-count');

  const updateSelection = () => {
    const count = rows.filter((checkbox) => checkbox.checked).length;
    deleteButton.disabled = count === 0;
    selectedCount.textContent = `${count} selected on this page`;
    selectAll.checked = count === rows.length;
    selectAll.indeterminate = count > 0 && count < rows.length;
  };

  selectAll.addEventListener('change', () => {
    rows.forEach((checkbox) => { checkbox.checked = selectAll.checked; });
    updateSelection();
  });

  rows.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));

  form.addEventListener('submit', (event) => {
    const count = rows.filter((checkbox) => checkbox.checked).length;
    if (count === 0 || !window.confirm(`Permanently delete ${count} selected contact ${count === 1 ? 'response' : 'responses'}? This cannot be undone.`)) {
      event.preventDefault();
    }
  });

  updateSelection();
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/contact-submissions/index.blade.php ENDPATH**/ ?>