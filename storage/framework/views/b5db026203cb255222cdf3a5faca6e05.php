<?php
  $homeOpen = request()->routeIs('admin.home.*');
  $currentHomeSection = request()->route('section');
  $currentPageKey = request()->route('pageKey');
  $currentPageSection = request()->route('section');
?>

<aside class="sidebar" id="admin-sidebar">
  <?php echo $__env->make('admin.partials.brand', [
    'href' => route('admin.dashboard'),
    'title' => 'ITQAN Admin',
    'subtitle' => 'Backend Control',
  ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <nav class="sidebar-nav" aria-label="Admin navigation">
    <a class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a>

    <div class="nav-group <?php echo e($homeOpen ? 'open' : ''); ?>" data-nav-group>
      <button class="nav-parent <?php echo e($homeOpen ? 'active' : ''); ?>" type="button" data-nav-toggle data-storage-key="itqan-admin-home-menu" aria-expanded="<?php echo e($homeOpen ? 'true' : 'false'); ?>">
        <span>Home Page</span>
        <span class="nav-caret" aria-hidden="true">⌄</span>
      </button>

      <div class="submenu" aria-label="Home page section submenu">
        <a class="<?php echo e(request()->routeIs('admin.home.index') ? 'active' : ''); ?>" href="<?php echo e(route('admin.home.index')); ?>">Overview</a>

        <?php $__currentLoopData = ($adminHomeSections ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homeSection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $isCurrentSection = request()->routeIs('admin.home.sections.edit')
              && $currentHomeSection instanceof \App\Models\HomeSection
              && $currentHomeSection->is($homeSection);
          ?>
          <a class="<?php echo e($isCurrentSection ? 'active' : ''); ?>" href="<?php echo e(route('admin.home.sections.edit', $homeSection)); ?>">
            <span><?php echo e($homeSection->admin_title); ?></span>
            <small><?php echo e($homeSection->items_count); ?> item(s)</small>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    <?php $__currentLoopData = ($adminPageGroups ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageKey => $pageGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $pageOpen = request()->routeIs('admin.pages.*') && $currentPageKey === $pageKey;
      ?>
      <div class="nav-group <?php echo e($pageOpen ? 'open' : ''); ?>" data-nav-group>
        <button class="nav-parent <?php echo e($pageOpen ? 'active' : ''); ?>" type="button" data-nav-toggle data-storage-key="itqan-admin-<?php echo e($pageKey); ?>-menu" aria-expanded="<?php echo e($pageOpen ? 'true' : 'false'); ?>">
          <span><?php echo e($pageGroup['label']); ?></span>
          <span class="nav-caret" aria-hidden="true">⌄</span>
        </button>

        <div class="submenu" aria-label="<?php echo e($pageGroup['label']); ?> section submenu">
          <a class="<?php echo e(request()->routeIs('admin.pages.index') && $currentPageKey === $pageKey ? 'active' : ''); ?>" href="<?php echo e(route('admin.pages.index', $pageKey)); ?>">Overview</a>

          <?php $__currentLoopData = $pageGroup['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageSection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $isCurrentPageSection = request()->routeIs('admin.pages.sections.edit')
                && $currentPageKey === $pageKey
                && $currentPageSection instanceof \App\Models\PageSection
                && $currentPageSection->is($pageSection);
            ?>
            <a class="<?php echo e($isCurrentPageSection ? 'active' : ''); ?>" href="<?php echo e(route('admin.pages.sections.edit', [$pageKey, $pageSection])); ?>">
              <span><?php echo e($pageSection->admin_title); ?></span>
              <small><?php echo e($pageSection->items_count); ?> item(s)</small>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <a class="<?php echo e(request()->routeIs('admin.site-settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.site-settings.edit')); ?>">Site Settings, Logo &amp; Favicon</a>
    <a class="<?php echo e(request()->routeIs('admin.header-menu.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.header-menu.index')); ?>">Header Menu</a>
    <a class="<?php echo e(request()->routeIs('admin.footer-menu.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.footer-menu.index')); ?>">Footer Menu</a>
    <a class="<?php echo e(request()->routeIs('admin.contact-submissions.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.contact-submissions.index')); ?>">Contact Responses</a>
    <a class="<?php echo e(request()->routeIs('admin.social-links.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.social-links.index')); ?>">Social Links</a>
    <a href="<?php echo e(route('home')); ?>" target="_blank" rel="noopener">View Website</a>
  </nav>
</aside>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/partials/sidebar.blade.php ENDPATH**/ ?>