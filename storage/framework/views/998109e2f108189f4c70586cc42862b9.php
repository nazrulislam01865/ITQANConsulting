<?php if($paginator->hasPages()): ?>
  <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">
    <div class="admin-pagination-summary">
      Showing
      <strong><?php echo e($paginator->firstItem() ?? 0); ?></strong>
      to
      <strong><?php echo e($paginator->lastItem() ?? 0); ?></strong>
      of
      <strong><?php echo e($paginator->total()); ?></strong>
      results
    </div>

    <div class="admin-pagination-links">
      <?php if($paginator->onFirstPage()): ?>
        <span class="admin-pagination-button is-disabled" aria-disabled="true">Previous</span>
      <?php else: ?>
        <a class="admin-pagination-button" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">Previous</a>
      <?php endif; ?>

      <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_string($element)): ?>
          <span class="admin-pagination-ellipsis" aria-hidden="true"><?php echo e($element); ?></span>
        <?php endif; ?>

        <?php if(is_array($element)): ?>
          <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($page == $paginator->currentPage()): ?>
              <span class="admin-pagination-page is-current" aria-current="page"><?php echo e($page); ?></span>
            <?php else: ?>
              <a class="admin-pagination-page" href="<?php echo e($url); ?>" aria-label="Go to page <?php echo e($page); ?>"><?php echo e($page); ?></a>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <?php if($paginator->hasMorePages()): ?>
        <a class="admin-pagination-button" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Next</a>
      <?php else: ?>
        <span class="admin-pagination-button is-disabled" aria-disabled="true">Next</span>
      <?php endif; ?>
    </div>
  </nav>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/partials/pagination.blade.php ENDPATH**/ ?>