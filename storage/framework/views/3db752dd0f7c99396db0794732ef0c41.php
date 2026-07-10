<?php
  $socialIconPaths = [
    'linkedin' => '<path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.36 8.1h4.28V23H.36V8.1zM7.55 8.1h4.1v2.04h.06c.57-1.08 1.96-2.22 4.03-2.22 4.31 0 5.1 2.84 5.1 6.53V23h-4.27v-7.58c0-1.8-.03-4.12-2.51-4.12-2.51 0-2.9 1.96-2.9 3.99V23H7.55V8.1z"/>',
    'facebook' => '<path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.52 1.49-3.91 3.77-3.91 1.09 0 2.23.2 2.23.2v2.47h-1.25c-1.24 0-1.63.77-1.63 1.57v1.88h2.78l-.44 2.91h-2.34V22C18.34 21.24 22 17.08 22 12.06z"/>',
    'youtube' => '<path d="M23.5 6.2a3 3 0 0 0-2.1-2.12C19.55 3.58 12 3.58 12 3.58s-7.55 0-9.4.5A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.12c1.85.5 9.4.5 9.4.5s7.55 0 9.4-.5a3 3 0 0 0 2.1-2.12A31.5 31.5 0 0 0 24 12a31.5 31.5 0 0 0-.5-5.8zM9.75 15.54V8.46L16 12l-6.25 3.54z"/>',
    'whatsapp' => '<path d="M20.52 3.48A11.9 11.9 0 0 0 12.06 0C5.49 0 .14 5.34.14 11.91c0 2.1.55 4.16 1.6 5.97L0 24l6.3-1.65a11.9 11.9 0 0 0 5.76 1.47h.01c6.57 0 11.92-5.34 11.92-11.91a11.86 11.86 0 0 0-3.47-8.43zM12.07 21.8h-.01a9.88 9.88 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.64-.24-.37a9.86 9.86 0 0 1-1.51-5.27c0-5.46 4.44-9.9 9.91-9.9a9.85 9.85 0 0 1 7 2.9 9.85 9.85 0 0 1 2.9 7c0 5.46-4.45 9.89-9.91 9.89zm5.43-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47a8.9 8.9 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.88.12.57-.08 1.76-.72 2-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35z"/>',
    'instagram' => '<path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5zm4.25 3.2A4.8 4.8 0 1 1 12 16.8a4.8 4.8 0 0 1 0-9.6zm0 2A2.8 2.8 0 1 0 12 14.8a2.8 2.8 0 0 0 0-5.6zm5.05-2.65a1.1 1.1 0 1 1-1.1 1.1 1.1 1.1 0 0 1 1.1-1.1z"/>',
    'email' => '<path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm-.4 4.25-7.07 5.3a.88.88 0 0 1-1.06 0L4.4 8.25V6.6l7.6 5.7 7.6-5.7v1.65z"/>',
  ];

  $renderSocialSvg = static function (string $key, string $label) use ($socialIconPaths): string {
      $normalizedKey = strtolower(trim($key));
      $normalizedKey = str_replace([' ', '-', '.', '/'], '_', $normalizedKey);
      $path = $socialIconPaths[$normalizedKey] ?? '<circle cx="12" cy="12" r="7"/>';

      return '<svg class="social-inline-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" role="img" aria-label="'.e($label).'">'.$path.'</svg>';
  };
?>

<?php if(isset($includeEmail)): ?>
  <?php if($includeEmail && filled($site['email'] ?? null)): ?>
    <a class="social-link" href="mailto:<?php echo e($site['email']); ?>" aria-label="Email <?php echo e($site['name']); ?>">
      <?php echo $renderSocialSvg('email', 'Email'); ?>

    </a>
  <?php endif; ?>
<?php endif; ?>

<?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php
    $href = $social['url'] ?? '#';
    $isExternal = \Illuminate\Support\Str::startsWith($href, ['http://', 'https://']);
    $iconUrl = $social['icon_url'] ?? null;
    $iconSource = $social['icon_source'] ?? '';
    $platform = $social['platform'] ?? '';
    $label = $social['label'] ?? ($platform ?: 'Social link');
    $shouldUseInlineSvg = $iconSource === 'Platform fallback' || \Illuminate\Support\Str::startsWith((string) $iconUrl, 'data:image/svg');
  ?>
  <a class="social-link" href="<?php echo e($href); ?>" aria-label="<?php echo e($label); ?>" <?php if($isExternal): ?> target="_blank" rel="noopener" <?php endif; ?>>
    <?php if($iconUrl && ! $shouldUseInlineSvg): ?>
      <img src="<?php echo e($iconUrl); ?>" alt="" loading="lazy" decoding="async" width="18" height="18">
    <?php else: ?>
      <?php echo $renderSocialSvg((string) $platform, (string) $label); ?>

    <?php endif; ?>
  </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/social-links.blade.php ENDPATH**/ ?>