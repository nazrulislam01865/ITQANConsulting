<?php $__env->startSection('content'); ?>
<main id="about" class="page active">
  <?php echo $__env->make('frontend.partials.page-hero', ['hero' => $page['hero']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <section class="section light">
    <div class="container split top">
      <div class="reveal"><div class="label"><?php echo e($page['story']['label']); ?></div><h2 class="headline"><?php echo e($page['story']['title']); ?></h2></div>
      <div class="reveal story-copy">
        <?php $__currentLoopData = $page['story']['paragraphs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="lead"><?php echo e($paragraph); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-head reveal"><div><div class="label"><?php echo e($page['beliefs']['label']); ?></div><h2 class="headline"><?php echo e($page['beliefs']['title']); ?></h2></div></div>
      <div class="card-grid four">
        <?php $__currentLoopData = $page['beliefs']['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="glass-card reveal"><div class="icon-circle"><?php echo e($item['num']); ?></div><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </section>

  <section class="section soft-white">
    <div class="container card-grid two">
      <?php $__currentLoopData = $page['mission_vision']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="card reveal"><span class="num"><?php echo e($card['num']); ?></span><h3><?php echo e($card['title']); ?></h3><p><?php echo e($card['text']); ?></p></article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </section>

  <?php echo $__env->make('frontend.partials.cta', ['cta' => $page['cta']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/pages/about.blade.php ENDPATH**/ ?>