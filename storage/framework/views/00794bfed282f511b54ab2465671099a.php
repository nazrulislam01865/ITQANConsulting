<?php
  $buttonClass = trim('btn ' . ($button['class'] ?? $class ?? ''));
  $routeName = $button['route'] ?? null;
  $href = $button['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) . ($button['anchor'] ?? '') : '#');
?>
<a class="<?php echo e($buttonClass); ?>" href="<?php echo e($href); ?>"><?php echo e($button['text']); ?></a>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/button.blade.php ENDPATH**/ ?>