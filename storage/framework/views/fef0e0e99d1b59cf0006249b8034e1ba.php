<?php $__env->startPush('head'); ?>
<script>window.ITQAN_CATALOG_PAGES = <?php echo json_encode($collections['catalog_pages'], 15, 512) ?>;</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main id="catalog" class="page active">
  <?php echo $__env->make('frontend.partials.page-hero', ['hero' => $page['hero']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <section class="section" id="catalog-viewer">
    <div class="container">
      <div class="catalog-stage reveal" id="catalogStage">
        <div class="catalog-top">
          <div><div class="eyebrow"><?php echo e($page['viewer']['eyebrow'] ?? 'Catalog Viewer'); ?></div><div class="catalog-title" id="catalogTitle"><?php echo e($page['viewer']['title'] ?? 'ITQAN Service Profile'); ?></div></div>
          <div class="catalog-actions"><button class="btn small catalog-fullscreen-action" id="fullBtn" type="button">Fullscreen</button><a class="btn small" href="<?php echo e(route('catalog.download')); ?>">Download PDF</a></div>
        </div>
        <div class="book-wrap" id="bookWrap">
          <button class="catalog-control prev" id="prevPage" type="button" aria-label="Previous page">‹</button>
          <div class="book" id="book">
            <article class="page-sheet left" id="leftPage"></article>
            <article class="page-sheet right" id="rightPage"></article>
          </div>
          <button class="catalog-control next" id="nextPage" type="button" aria-label="Next page">›</button>
        </div>
        <div class="page-indicator" id="pageIndicator">Page 1 of <?php echo e(count($collections['catalog_pages'])); ?></div>
        <div class="thumbs" id="thumbs" aria-label="Catalog thumbnails"></div>
      </div>
    </div>
  </section>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/catalog.blade.php ENDPATH**/ ?>