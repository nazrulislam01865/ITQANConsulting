<header class="topbar">
  <div>
    <h1><?php echo $__env->yieldContent('title', 'Admin'); ?></h1>
    <div class="help">Signed in as <?php echo e(auth()->user()->name ?? 'Administrator'); ?></div>
  </div>
  <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
    <?php echo csrf_field(); ?>
    <button class="btn small" type="submit">Logout</button>
  </form>
</header>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/partials/topbar.blade.php ENDPATH**/ ?>