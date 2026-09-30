<?php $__env->startSection('content'); ?>
<section class="auth-shell">
  <div class="auth-card">
    <?php echo $__env->make('admin.partials.brand', [
      'href' => route('home'),
      'title' => 'ITQAN Consulting',
      'subtitle' => 'Sincere Services. Lasting Results.',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <h1 class="auth-title">Admin access for clear control.</h1>
    <p class="auth-lead">Sign in to manage the ITQAN website content, logo, header menu, footer menu, and home page sections.</p>

    <?php echo $__env->make('admin.partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <form method="POST" action="<?php echo e(route('admin.login.store')); ?>" novalidate>
      <?php echo csrf_field(); ?>
      <div class="field">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="admin@example.com" autocomplete="email" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="Enter password" autocomplete="current-password" required minlength="8">
      </div>
      <label class="check-row">
        <input type="checkbox" name="remember" value="1" <?php if(old('remember')): echo 'checked'; endif; ?>>
        Keep me signed in on this device
      </label>
      <button class="btn primary" type="submit">Sign in securely</button>
    </form>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/auth/login.blade.php ENDPATH**/ ?>