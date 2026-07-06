<?php
  $brandHref = $href ?? route('admin.dashboard');
  $brandName = $adminSite['name'] ?? 'ITQAN Consulting';
  $brandTagline = $adminSite['tagline'] ?? 'Sincere Services. Lasting Results.';
  $brandLogo = $adminSite['logo_url'] ?? null;
  $fallbackTitle = $title ?? 'ITQAN Admin';
  $fallbackSubtitle = $subtitle ?? 'Backend Control';
?>

<a class="admin-brand <?php echo e($brandLogo ? 'admin-brand-image-only' : ''); ?>" href="<?php echo e($brandHref); ?>" aria-label="<?php echo e($brandName); ?> admin home">
  <?php if($brandLogo): ?>
    <img class="admin-brand-logo" src="<?php echo e($brandLogo); ?>" alt="<?php echo e($brandName); ?> logo">
  <?php else: ?>
    <span class="admin-mark">IC</span>
    <span><?php echo e($fallbackTitle); ?><small><?php echo e($fallbackSubtitle); ?></small></span>
  <?php endif; ?>
</a>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/partials/brand.blade.php ENDPATH**/ ?>