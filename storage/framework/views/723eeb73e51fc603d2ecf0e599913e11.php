<?php $__env->startSection('title', 'Contact Response'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-head">
  <div>
    <h2>Contact response.</h2>
    <p>Submitted on <?php echo e($submission->created_at?->format('d M Y, h:i A')); ?>.</p>
  </div>
  <div class="button-row">
    <a class="btn" href="<?php echo e(route('admin.contact-submissions.index')); ?>">Back to Responses</a>
    <form class="danger-inline" method="POST" action="<?php echo e(route('admin.contact-submissions.destroy', $submission)); ?>" onsubmit="return confirm('Delete this contact response?')">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
      <button class="btn danger" type="submit">Delete</button>
    </form>
  </div>
</div>

<div class="grid two">
  <div class="card light">
    <h3>Sender</h3>
    <p><strong>Name:</strong> <?php echo e($submission->name); ?></p>
    <p><strong>Company:</strong> <?php echo e($submission->company_name ?: '—'); ?></p>
    <p><strong>Email:</strong> <a href="mailto:<?php echo e($submission->email); ?>"><?php echo e($submission->email); ?></a></p>
    <p><strong>Phone / WhatsApp:</strong> <?php echo e($submission->phone ?: '—'); ?></p>
  </div>

  <div class="card light">
    <h3>Request Details</h3>
    <p><strong>Support requested:</strong> <?php echo e(collect($submission->support_types ?? [])->filter()->join(', ') ?: ($submission->need ?: '—')); ?></p>
    <p><strong>Problem areas:</strong> <?php echo e(collect($submission->areas ?? [])->filter()->join(', ') ?: '—'); ?></p>
    <p><strong>Budget:</strong> <?php echo e($submission->budget_range ?: '—'); ?></p>
    <p><strong>Preferred Method:</strong> <?php echo e($submission->preferred_contact_method ?: '—'); ?></p>
  </div>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Message</h3>
  <p style="white-space:pre-line"><?php echo e($submission->message); ?></p>
</div>

<div class="card" style="margin-top:18px">
  <h3>Technical Info</h3>
  <p><strong>IP:</strong> <?php echo e($submission->ip_address ?: '—'); ?></p>
  <p><strong>User Agent:</strong> <?php echo e($submission->user_agent ?: '—'); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/contact-submissions/show.blade.php ENDPATH**/ ?>