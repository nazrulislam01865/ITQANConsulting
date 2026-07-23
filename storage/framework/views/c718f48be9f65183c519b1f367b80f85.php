<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo $__env->yieldContent('title', 'Portfolio Admin'); ?> · Md Aminul Islam Portfolio</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/starpmaminul/admin.css', 'resources/js/starpmaminul/admin.js']); ?>
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <span class="sidebar-brand-mark">AMI</span>
                <div>
                    <strong>Portfolio Admin</strong>
                    <small>Content management</small>
                </div>
            </div>

            <nav class="sidebar-nav" aria-label="Admin navigation">
                <a href="<?php echo e(route('starpmaminul.admin.dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('starpmaminul.admin.dashboard') ? 'active' : ''); ?>">
                    <span class="sidebar-icon">⌂</span>
                    Dashboard
                </a>

                <div class="sidebar-heading">Portfolio sections</div>
                <?php $__currentLoopData = config('starpmaminul.sections', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('starpmaminul.admin.sections.edit', $key)); ?>" class="sidebar-link <?php echo e(request()->route('sectionKey') === $key ? 'active' : ''); ?>">
                        <span class="sidebar-icon"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                        <span><?php echo e($section['label']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>

            <div class="sidebar-footer">
                <a href="<?php echo e(route('starpmaminul.portfolio')); ?>" target="_blank" rel="noopener" class="sidebar-preview">View public site ↗</a>
                <form method="POST" action="<?php echo e(route('starpmaminul.admin.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="sidebar-logout">Sign out</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
                <div>
                    <span class="topbar-kicker">Md Aminul Islam Portfolio</span>
                    <h1><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                </div>
                <div class="topbar-user">
                    <span><?php echo e(strtoupper(substr(auth('starpmaminul')->user()->name, 0, 1))); ?></span>
                    <div>
                        <strong><?php echo e(auth('starpmaminul')->user()->name); ?></strong>
                        <small><?php echo e(auth('starpmaminul')->user()->email); ?></small>
                    </div>
                </div>
            </header>

            <main class="admin-content">
                <?php if(session('status')): ?>
                    <div class="alert alert-success" role="status">
                        <span>✓</span>
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/starpmaminul/admin/layouts/app.blade.php ENDPATH**/ ?>