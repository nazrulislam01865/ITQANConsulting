<?php
  $brandHref = isset($href) ? $href : route('home');
  $brandName = $site['name'] ?? 'ITQAN Consulting';
  $brandTagline = $site['tagline'] ?? '';
  $brandLogo = $site['logo_url'] ?? null;
?>

<a class="brand <?php echo e($brandLogo ? 'brand-image-only' : ''); ?>" href="<?php echo e($brandHref); ?>" aria-label="<?php echo e($brandName); ?> home">
  <?php if($brandLogo): ?>
    <img class="brand-logo-image" src="<?php echo e($brandLogo); ?>" alt="<?php echo e($brandName); ?> logo">
  <?php else: ?>
    <span class="mark"><?php echo e($site['mark'] ?? 'IC'); ?></span>
    <span><?php echo e($brandName); ?><small><?php echo e($brandTagline); ?></small></span>
  <?php endif; ?>
</a>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/brand.blade.php ENDPATH**/ ?>