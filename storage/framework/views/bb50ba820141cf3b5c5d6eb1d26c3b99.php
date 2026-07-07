<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php echo $__env->make('frontend.partials.favicon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <title><?php echo e($page['title'] ?? $site['name']); ?></title>
  <meta name="description" content="<?php echo e($page['meta_description'] ?? $site['description']); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/itqan-template.css')); ?>?v=catalog-actions-1">
  <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
  <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>
  <div class="site-bg" aria-hidden="true"></div>
  <div class="grain" aria-hidden="true"></div>

  <?php echo $__env->make('frontend.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div id="main-content">
    <?php echo $__env->yieldContent('content'); ?>
  </div>

  <?php echo $__env->make('frontend.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/layouts/app.blade.php ENDPATH**/ ?>