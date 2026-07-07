<!DOCTYPE html>
<html lang="en" class="admin-nav-hydrating">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php echo $__env->make('frontend.partials.favicon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <title><?php echo $__env->yieldContent('title', 'Admin'); ?> | ITQAN Consulting</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <meta name="admin-session-timeout-seconds" content="<?php echo e(max(1, (int) config('itqan_security.admin_session_timeout_minutes', 30)) * 60); ?>">
  <meta name="admin-session-expired-url" content="<?php echo e(route('admin.session-expired')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin.css')); ?>?v=admin-favicon-upload-1">
</head>
<body>
  <div class="admin-bg" aria-hidden="true"></div>
  <div class="admin-mobile-overlay" data-admin-sidebar-close aria-hidden="true"></div>
  <header class="admin-mobile-bar" aria-label="Mobile admin header">
    <?php echo $__env->make('admin.partials.brand', [
      'href' => route('admin.dashboard'),
      'title' => 'ITQAN Admin',
      'subtitle' => 'Backend Control',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button class="admin-mobile-menu-toggle" type="button" data-admin-menu-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open admin menu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>
  </header>
  <div class="admin-layout">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="admin-main">
      <?php echo $__env->make('admin.partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="content">
        <?php echo $__env->make('admin.partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
      </div>
    </main>
  </div>
  <script src="<?php echo e(asset('assets/js/admin.js')); ?>?v=admin-favicon-upload-1"></script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/layouts/app.blade.php ENDPATH**/ ?>