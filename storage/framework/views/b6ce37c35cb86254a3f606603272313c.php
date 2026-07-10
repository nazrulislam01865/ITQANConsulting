<header class="header">
  <div class="container nav">
    <?php echo $__env->make('frontend.partials.brand', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <nav class="navlinks" id="navlinks" aria-label="Main navigation">
      <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($isRoute = !empty($item['route']) && Route::has($item['route'])); ?>
        <?php ($href = $isRoute ? route($item['route']) : ($item['url'] ?? '#')); ?>
        <?php ($isActive = $isRoute && request()->routeIs($item['route'])); ?>
        <a href="<?php echo e($href); ?>" class="<?php echo e($isActive ? 'active' : ''); ?>" <?php if($isActive): ?> aria-current="page" <?php endif; ?>><?php echo e($item['label']); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
    <div class="nav-actions">
      <button class="motion-toggle" id="motionToggle" type="button" aria-pressed="false"><span></span>Motion On</button>
      <?php echo $__env->make('frontend.partials.button', ['button' => $site['primary_cta'], 'class' => 'primary'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <button class="menu-btn" id="menuBtn" type="button" aria-label="Open menu" aria-controls="navlinks" aria-expanded="false">
        <span class="menu-icon" aria-hidden="true">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>
    </div>
  </div>
</header>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/header.blade.php ENDPATH**/ ?>