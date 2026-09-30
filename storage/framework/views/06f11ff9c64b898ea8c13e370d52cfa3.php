<?php ($type = old('item_type', $item->item_type ?? ($defaultType ?? 'card'))); ?>
<?php ($hideBadge = $hideBadge ?? false); ?>
<?php ($sectionKey = $sectionKey ?? ''); ?>

<?php if($type === 'problem'): ?>
  <div class="field full"><label>Situation / option title</label><textarea name="settings[problem]"><?php echo e(old('settings.problem', $item->settings['problem'] ?? $item->title ?? '')); ?></textarea></div>
  <div class="field full"><label>Explanation shown after selection</label><textarea name="settings[summary]" rows="4"><?php echo e(old('settings.summary', $item->settings['summary'] ?? $item->settings['response'] ?? $item->text ?? '')); ?></textarea></div>
  <div class="field full"><label>Practical starting points (one per line)</label><textarea name="settings[services]" rows="6"><?php echo e(old('settings.services', $item->settings['services'] ?? '')); ?></textarea><small>These appear as the bullet list on the right side of the interactive clarity check.</small></div>
<?php elseif($type === 'testimonial'): ?>
  <div class="field"><label>Quote title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="<?php echo e(old('sort_order', $item->sort_order ?? '')); ?>" placeholder="10"></div>
  <div class="field"><label>Author</label><input name="settings[author]" value="<?php echo e(old('settings.author', $item->settings['author'] ?? '')); ?>"></div>
  <div class="field"><label>Role / company</label><input name="settings[role]" value="<?php echo e(old('settings.role', $item->settings['role'] ?? '')); ?>"></div>
  <div class="field"><label>Project pill</label><input name="settings[project]" value="<?php echo e(old('settings.project', $item->settings['project'] ?? '')); ?>" placeholder="Fleet operations"></div>
  <div class="field full"><label>Quote text</label><textarea name="text" rows="5"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php elseif($type === 'value'): ?>
  <div class="field"><label>Number</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>" placeholder="01"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="<?php echo e(old('sort_order', $item->sort_order ?? '')); ?>" placeholder="10"></div>
  <div class="field full"><label>Small principle label</label><input name="settings[mini]" value="<?php echo e(old('settings.mini', $item->settings['mini'] ?? '')); ?>" placeholder="01 / clarity before execution"></div>
  <div class="field full"><label>Principle headline</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Description</label><textarea name="text" rows="4"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field full"><label>Practical example</label><textarea name="settings[example]" rows="3"><?php echo e(old('settings.example', $item->settings['example'] ?? '')); ?></textarea></div>
<?php elseif($type === 'work'): ?>
  <div class="field full">
    <label>Work image (16:9)</label>
    <?php if(!empty($item) && !empty($item->settings['image_path'])): ?>
      <div class="image-preview work-image-preview"><img src="<?php echo e(asset('storage/' . $item->settings['image_path'])); ?>" alt="Work image preview"></div>
    <?php endif; ?>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field"><label>Preview pill</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>"></div>
  <div class="field"><label>Work title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Description</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field"><label>Full pill</label><input name="settings[pill]" value="<?php echo e(old('settings.pill', $item->settings['pill'] ?? '')); ?>"></div>
  <div class="field"><label>Categories, comma separated</label><input name="settings[categories]" value="<?php echo e(old('settings.categories', $item->settings['categories'] ?? '')); ?>"></div>
  <div class="field"><label>Tags, comma separated</label><input name="settings[tags]" value="<?php echo e(old('settings.tags', $item->settings['tags'] ?? '')); ?>"></div>
  <div class="field"><label>Button label</label><input name="button_text" value="<?php echo e(old('button_text', $item->button_text ?? '')); ?>" placeholder="View Case Study"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $item->button_route ?? '') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://... or #case-study"></div>
<?php elseif($type === 'button'): ?>
  <div class="field"><label>Button text</label><input name="button_text" value="<?php echo e(old('button_text', $item->button_text ?? '')); ?>"></div>
  <div class="field"><label>Button style</label><select name="button_class"><option value="">Default outline</option><option value="blue" <?php if(old('button_class', $item->button_class ?? '') === 'blue'): echo 'selected'; endif; ?>>Blue primary</option><option value="primary" <?php if(old('button_class', $item->button_class ?? '') === 'primary'): echo 'selected'; endif; ?>>White primary</option><option value="dark" <?php if(old('button_class', $item->button_class ?? '') === 'dark'): echo 'selected'; endif; ?>>Dark</option><option value="ghost-light" <?php if(old('button_class', $item->button_class ?? '') === 'ghost-light'): echo 'selected'; endif; ?>>Ghost light</option></select></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option><?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($route); ?>" <?php if(old('button_route', $item->button_route ?? '') === $route): echo 'selected'; endif; ?>><?php echo e($route); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
  <div class="field"><label>Button URL</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://... or leave empty when route is selected"></div>
<?php elseif($type === 'ticker'): ?>
  <div class="field full"><label>Marquee text</label><input name="text" value="<?php echo e(old('text', $item->text ?? '')); ?>" placeholder="Example: clearExecution"></div>
<?php elseif($type === 'social_link'): ?>
  <?php ($platform = old('settings.platform', $item->settings['platform'] ?? $item->badge ?? '')); ?>
  <div class="field"><label>Social platform</label><input name="settings[platform]" value="<?php echo e($platform); ?>" placeholder="LinkedIn"></div>
  <div class="field"><label>Social media label</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>" placeholder="ITQAN on LinkedIn"></div>
  <div class="field full"><label>Social media link</label><input name="button_url" value="<?php echo e(old('button_url', $item->button_url ?? '')); ?>" placeholder="https://..."></div>
<?php elseif($type === 'paragraph'): ?>
  <div class="field full"><label>Paragraph</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php elseif($type === 'chip'): ?>
  <div class="field full"><label>Short line / chip text</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>" placeholder="No noise."></div>
<?php elseif($type === 'service_card'): ?>
  <div class="field"><label>Number / badge</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>" placeholder="01"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="<?php echo e(old('sort_order', $item->sort_order ?? '')); ?>" placeholder="10"></div>
  <div class="field full"><label>Service title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Service introduction</label><textarea name="text" rows="4"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field full"><label>Common problem</label><textarea name="settings[common_problem]" rows="3"><?php echo e(old('settings.common_problem', $item->settings['common_problem'] ?? '')); ?></textarea></div>
  <div class="field full"><label>Possible deliverables</label><textarea name="settings[deliverables]" rows="3"><?php echo e(old('settings.deliverables', $item->settings['deliverables'] ?? '')); ?></textarea></div>
<?php elseif($type === 'card' && $sectionKey === 'home_who'): ?>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="<?php echo e(old('sort_order', $item->sort_order ?? '')); ?>" placeholder="10"></div>
  <div class="field full"><label>Problem headline</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Problem description</label><textarea name="text" rows="4"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
  <div class="field full"><label>ITQAN response</label><textarea name="settings[response]" rows="3"><?php echo e(old('settings.response', $item->settings['response'] ?? '')); ?></textarea></div>
  <div class="field full"><label>Visual stage label</label><input name="settings[stage_label]" value="<?php echo e(old('settings.stage_label', $item->settings['stage_label'] ?? '')); ?>" placeholder="The full picture becomes visible"></div>
<?php elseif($type === 'step' || $type === 'card'): ?>
  <?php if (! ($hideBadge)): ?>
    <div class="field"><label>Number / badge</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>"></div>
  <?php endif; ?>
  <?php if($type === 'step'): ?>
    <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="<?php echo e(old('sort_order', $item->sort_order ?? '')); ?>" placeholder="10"></div>
  <?php endif; ?>
  <div class="field"><label>Title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Text</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php else: ?>
  <?php if (! ($hideBadge)): ?>
    <div class="field"><label>Number / badge</label><input name="badge" value="<?php echo e(old('badge', $item->badge ?? '')); ?>"></div>
  <?php endif; ?>
  <div class="field"><label>Title</label><input name="title" value="<?php echo e(old('title', $item->title ?? '')); ?>"></div>
  <div class="field full"><label>Text</label><textarea name="text"><?php echo e(old('text', $item->text ?? '')); ?></textarea></div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/partials/item-fields.blade.php ENDPATH**/ ?>