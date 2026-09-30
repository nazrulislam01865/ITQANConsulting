<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <?php echo $__env->make('frontend.partials.brand', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <p style="margin-top:18px"><?php echo e($site['description']); ?></p>
      </div>
      <?php if(!empty($footer['menus'])): ?>
        <?php $__currentLoopData = $footer['menus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div>
            <h3><?php echo e($menu['title']); ?></h3>
            <ul>
              <?php $__currentLoopData = $menu['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                  <?php if(!empty($item['route']) && Route::has($item['route'])): ?>
                    <a href="<?php echo e(route($item['route'])); ?>"><?php echo e($item['label']); ?></a>
                  <?php elseif(!empty($item['url'])): ?>
                    <a href="<?php echo e($item['url']); ?>"><?php echo e($item['label']); ?></a>
                  <?php else: ?>
                    <?php echo e($item['label']); ?>

                  <?php endif; ?>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
        <div>
          <h3>Pages</h3>
          <ul>
            <?php $__currentLoopData = $navigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><a href="<?php echo e(route($item['route'])); ?>"><?php echo e($item['label'] === 'Catalog' ? 'Digital Catalog' : $item['label']); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
        <div>
          <h3>Services</h3>
          <ul>
            <?php $__currentLoopData = $footer['service_links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($service); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>
      <div>
        <h3>Contact</h3>
        <ul>
          <li><a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a></li>
          <li><?php echo e($site['address']); ?></li>
        </ul>
        <div class="social-row">
          <?php echo $__env->make('frontend.partials.social-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
      </div>
    </div>
    <div class="footer-bottom"><span><?php echo e($footer['bottom_left']); ?></span><span><?php echo e($footer['copyright']); ?></span></div>
  </div>
</footer>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/footer.blade.php ENDPATH**/ ?>