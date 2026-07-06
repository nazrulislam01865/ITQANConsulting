<?php if(isset($includeEmail)): ?>
  <?php if($includeEmail): ?>
    <a class="social-link" href="mailto:<?php echo e($site['email']); ?>" aria-label="Email <?php echo e($site['name']); ?>">
      <span class="social-link-fallback" aria-hidden="true">@</span>
    </a>
  <?php endif; ?>
<?php endif; ?>

<?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php
    $href = $social['url'] ?? '#';
    $isExternal = \Illuminate\Support\Str::startsWith($href, ['http://', 'https://']);
    $iconUrl = $social['icon_url'] ?? null;
    $label = $social['label'] ?? ($social['platform'] ?? 'Social link');
  ?>
  <a class="social-link" href="<?php echo e($href); ?>" aria-label="<?php echo e($label); ?>" <?php if($isExternal): ?> target="_blank" rel="noopener" <?php endif; ?>>
    <?php if($iconUrl): ?>
      <img src="<?php echo e($iconUrl); ?>" alt="" loading="lazy" decoding="async" width="18" height="18">
    <?php else: ?>
      <span class="social-link-fallback" aria-hidden="true"><?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($label, 0, 1))); ?></span>
    <?php endif; ?>
  </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/social-links.blade.php ENDPATH**/ ?>