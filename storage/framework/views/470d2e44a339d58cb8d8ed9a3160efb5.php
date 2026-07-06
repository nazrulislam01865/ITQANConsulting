<?php
  $categories = isset($work['categories']) ? implode(' ', $work['categories']) : '';
  $buttonLabel = $buttonText ?? ($work['button_text'] ?? 'View Case Study');
  $buttonUrl = $work['button_url'] ?? null;
  $buttonRoute = $work['button_route'] ?? null;
  $buttonHref = '#';
  $workImageUrl = $work['image_url'] ?? null;

  if (!empty($buttonUrl)) {
      $buttonHref = $buttonUrl;
  } elseif (!empty($buttonRoute) && \Illuminate\Support\Facades\Route::has($buttonRoute)) {
      $buttonHref = route($buttonRoute);
  }
?>

<article class="work-card reveal" <?php if($categories): ?> data-cats="<?php echo e($categories); ?>" <?php endif; ?>>
  <div class="work-visual <?php echo e($workImageUrl ? 'has-image' : ''); ?>">
    <?php if($workImageUrl): ?>
      <img src="<?php echo e($workImageUrl); ?>" alt="<?php echo e($work['title'] ?? 'Work image'); ?>">
    <?php endif; ?>
  </div>
  <div class="work-copy">
    <span class="pill"><?php echo e($pill ?? ($work['pill'] ?? '')); ?></span>
    <h3><?php echo e($work['title'] ?? ''); ?></h3>
    <p><?php echo e($description ?? ($work['description'] ?? '')); ?></p>
    <?php if(isset($showTags)): ?>
      <?php if($showTags && !empty($work['tags'])): ?>
        <div class="meta">
          <?php $__currentLoopData = $work['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($tag); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    <a class="btn ghost-light small" href="<?php echo e($buttonHref); ?>"><?php echo e($buttonLabel); ?></a>
  </div>
</article>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/work-card.blade.php ENDPATH**/ ?>