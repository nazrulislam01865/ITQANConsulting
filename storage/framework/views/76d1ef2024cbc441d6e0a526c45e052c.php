<?php ($type = old('item_type', $item->item_type ?? ($defaultType ?? 'card'))); ?>

<?php if($type === 'button'): ?>
  <div class="field"><label>Button text</label><input name="button_text" value="<?php echo e(old('button_text', $item->button_text ?? '')); ?>"></div>
  <div class="field"><label>Button style</label><select name="button_class"><option value="">Default outline</option><option value="blue" <?php if(old('button_class', $item->button_class ?? '') === 'blue'): echo 'selected'; endif; ?>>Blue primary</option><option value="primary" <?php if(old('button_class', $item->button_class ?? '') === 'primary'): echo 'selected'; endif; ?>>White primary</option><option value="dark" <?php if(old('button_class', $item->button_class ?? '') === 'dark'): echo 'selected'; endif; ?>>Dark</option><option value="ghost-light" <?php if(old('button_class', $item->button_class ?? '') === 'ghost-light'): echo 'selected'; endif; ?>>Ghost light</option></select></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $item->button_route ?? '') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://... or #section"></div>
<?php elseif($type === 'card'): ?>
  <div class="field"><label>Title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Text</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php elseif($type === 'mission_card'): ?>
  <div class="field"><label>Badge label</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>" placeholder="Mission"></div>
  <div class="field"><label>Title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Text</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php elseif($type === 'service_area'): ?>
  <div class="field full"><label>Service title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Intro text</label><textarea name="subtitle"><?php echo e(old('subtitle', $item->subtitle ?? '')); ?></textarea></div>
  <div class="field full"><label>Service points</label><textarea name="settings[points]" rows="6"><?php echo e(old('settings.points', $item->settings['points'] ?? '')); ?></textarea></div>
  <div class="field"><label>Button text</label><input name="button_text" value="<?php echo e(old('button_text', $item->button_text ?? '')); ?>"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $item->button_route ?? '') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://... or #section"></div>
<?php elseif($type === 'faq'): ?>
  <div class="field"><label>Question</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field"><label>Short answer</label><input name="subtitle" value="<?php echo e(old('subtitle', $item->subtitle ?? '')); ?>"></div>
  <div class="field full"><label>Answer text</label><textarea name="text" rows="7"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php elseif($type === 'filter'): ?>
  <div class="field full"><label>Filter label</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>" placeholder="Software"></div>
<?php elseif($type === 'work'): ?>
  <div class="field full">
    <label>Work image (16:9)</label>
    <?php if(!empty($item) && !empty($item->settings['image_path'])): ?>
      <div class="image-preview work-image-preview"><img src="<?php echo e(asset('storage/' . $item->settings['image_path'])); ?>" alt="Work image preview"></div>
    <?php endif; ?>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field"><label>Pill label</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>"></div>
  <div class="field"><label>Work title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Description</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field"><label>Categories</label><input name="settings[categories]" value="<?php echo e(old('settings.categories', $item->settings['categories'] ?? '')); ?>" placeholder="software, process"></div>
  <div class="field"><label>Tags</label><input name="settings[tags]" value="<?php echo e(old('settings.tags', $item->settings['tags'] ?? '')); ?>" placeholder="Operations, Reporting"></div>
  <div class="field"><label>Button label</label><input name="button_text" value="<?php echo e(old('button_text', $item->button_text ?? '')); ?>" placeholder="View Case Study"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $item->button_route ?? '') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://... or #case-study"></div>
<?php elseif($type === 'catalog_page'): ?>
  <div class="field"><label>Page type</label><select name="settings[type]"><option value="image" <?php if(old('settings.type', $item->settings['type'] ?? 'image') === 'image'): echo 'selected'; endif; ?>>Image style</option><option value="video" <?php if(old('settings.type', $item->settings['type'] ?? '') === 'video'): echo 'selected'; endif; ?>>Video style</option></select></div>
  <div class="field"><label>Kicker</label><input name="subtitle" value="<?php echo e(old('subtitle', $item->subtitle ?? '')); ?>"></div>
  <div class="field full"><label>Page title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Body text</label><textarea name="text" rows="6"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field full">
    <label>Image media (16:9)</label>
    <?php if(!empty($item) && !empty($item->settings['image_path'])): ?>
      <div class="image-preview catalog-media-preview"><img src="<?php echo e(asset('storage/' . $item->settings['image_path'])); ?>" alt="Catalog image preview"></div>
    <?php endif; ?>
    <input type="file" name="media_image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field full">
    <label>Video media</label>
    <?php if(!empty($item) && !empty($item->settings['video_path'])): ?>
      <div class="admin-file-preview">Current video: <?php echo e(basename($item->settings['video_path'])); ?></div>
    <?php endif; ?>
    <input type="file" name="media_video">
  </div>
  <div class="field full">
    <label>Thumbnail image (16:9)</label>
    <?php if(!empty($item) && !empty($item->settings['thumbnail_path'])): ?>
      <div class="image-preview catalog-media-preview"><img src="<?php echo e(asset('storage/' . $item->settings['thumbnail_path'])); ?>" alt="Catalog thumbnail preview"></div>
    <?php endif; ?>
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp">
  </div>
<?php elseif($type === 'step'): ?>
  <div class="field full"><label>Step text</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
<?php else: ?>
  <div class="field"><label>Title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Text</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/partials/item-fields.blade.php ENDPATH**/ ?>