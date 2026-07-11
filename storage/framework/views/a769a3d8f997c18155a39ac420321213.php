<?php
  $favicon = \App\Support\Favicon::current();
?>

<?php if($favicon): ?>
  <link rel="icon" type="<?php echo e($favicon['type']); ?>" href="<?php echo e(asset($favicon['path'])); ?>?v=<?php echo e($favicon['version']); ?>">
  <link rel="shortcut icon" type="<?php echo e($favicon['type']); ?>" href="<?php echo e(asset($favicon['path'])); ?>?v=<?php echo e($favicon['version']); ?>">
<?php else: ?>
  
  <link rel="icon" href="data:,">
  <link rel="shortcut icon" href="data:,">
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/favicon.blade.php ENDPATH**/ ?>