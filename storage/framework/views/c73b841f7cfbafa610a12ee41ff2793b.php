<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin sign in · Md Aminul Islam Portfolio</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/starpmaminul/admin.css', 'resources/js/starpmaminul/admin.js']); ?>
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-visual" aria-hidden="true">
            <div class="login-orbit login-orbit-one"></div>
            <div class="login-orbit login-orbit-two"></div>
            <div class="login-visual-content">
                <span class="login-mark">AMI</span>
                <p>Project · Product · ERP · PMO</p>
                <h1>Manage the portfolio with clarity.</h1>
                <span>Section-based content controls. No design changes.</span>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="login-card-head">
                    <span class="login-mobile-mark">AMI</span>
                    <span class="login-eyebrow">Secure administration</span>
                    <h2>Welcome back</h2>
                    <p>Sign in to update portfolio content section by section.</p>
                </div>

                <form method="POST" action="<?php echo e(route('starpmaminul.admin.login.store')); ?>" class="login-form">
                    <?php echo csrf_field(); ?>
                    <label class="form-field">
                        <span>Email address</span>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="email" autofocus required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="form-field">
                        <span>Password</span>
                        <div class="password-wrap">
                            <input type="password" id="loginPassword" name="password" autocomplete="current-password" required>
                            <button type="button" class="password-toggle" data-password-toggle="loginPassword">Show</button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="field-error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="remember-row">
                        <input type="checkbox" name="remember" value="1" <?php echo e(old('remember', true) ? 'checked' : ''); ?>>
                        <span>Keep me signed in</span>
                    </label>

                    <button type="submit" class="primary-action">Sign in to dashboard <span>→</span></button>
                </form>

                <a href="<?php echo e(route('starpmaminul.portfolio')); ?>" class="return-link">← Return to portfolio</a>
            </div>
        </section>
    </main>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/starpmaminul/admin/auth/login.blade.php ENDPATH**/ ?>