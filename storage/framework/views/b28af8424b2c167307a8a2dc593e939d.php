<?php
  $sectionGroups = match($section->section_key) {
    'about_hero', 'services_hero', 'works_hero', 'catalog_hero' => [
      'button' => ['title' => 'Hero buttons'],
    ],
    'about_beliefs' => [
      'card' => ['title' => 'Belief cards'],
    ],
    'about_mission_vision' => [
      'mission_card' => ['title' => 'Mission and vision cards'],
    ],
    'services_areas' => [
      'service_area' => ['title' => 'Service cards'],
    ],
    'services_faq' => [
      'faq' => ['title' => 'FAQ items'],
    ],
    'works_grid' => [
      'filter' => ['title' => 'Work filter tabs'],
      'work' => ['title' => 'Work cards'],
    ],
    'catalog_viewer' => [
      'catalog_page' => ['title' => 'Catalog pages'],
    ],
    default => [],
  };

  $editItemId = (int) request('edit_item');
  $requestedAddType = request('add_item');
  $baseEditUrl = fn () => route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section]);
?>

<?php $__currentLoopData = $sectionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php
    $items = $section->items->where('item_type', $type)->sortBy('sort_order')->values();
    $editingItem = $items->firstWhere('id', $editItemId);
    $showAddForm = $requestedAddType === $type || ($items->isEmpty() && count($sectionGroups) === 1 && ! $editItemId && ! $requestedAddType);
    $addUrl = route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section, 'add_item' => $type]) . '#section-item-form-' . $type;
    $cancelUrl = $baseEditUrl();
  ?>

  <section class="item-list-panel">
    <div class="item-list-head">
      <div>
        <h2><?php echo e($group['title']); ?></h2>
        <p><?php echo e($items->count()); ?> saved item(s)</p>
      </div>
      <a class="btn primary" href="<?php echo e($addUrl); ?>">Add <?php echo e(str_replace('_', ' ', $type)); ?></a>
    </div>

    <?php if($items->isEmpty()): ?>
      <div class="empty-list-card">No <?php echo e(str_replace('_', ' ', $type)); ?> added yet.</div>
    <?php else: ?>
      <div class="table-wrap item-list-table-wrap">
        <table class="admin-table item-list-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Type</th>
              <th>Details</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $summaryTitle = $item->title ?: $item->button_text ?: $item->badge ?: 'Untitled item';
                $summaryText = $item->subtitle ?: $item->text ?: ($item->settings['points'] ?? null) ?: ($item->settings['tags'] ?? null) ?: ($item->settings['categories'] ?? null);
                $editUrl = route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section, 'edit_item' => $item->id]) . '#section-item-form-' . $type;
              ?>
              <tr class="<?php echo e($editingItem?->is($item) ? 'is-editing' : ''); ?>">
                <td>
                  <strong><?php echo e($summaryTitle); ?></strong>
                  <?php if($summaryText): ?><small><?php echo e(\Illuminate\Support\Str::limit(strip_tags($summaryText), 100)); ?></small><?php endif; ?>
                </td>
                <td><span class="pill"><?php echo e(str_replace('_', ' ', $item->item_type)); ?></span></td>
                <td>
                  <?php if($item->button_text): ?><small>Button: <?php echo e($item->button_text); ?></small><?php endif; ?>
                  <?php if($item->item_type === 'work' && filter_var($item->settings['featured_on_home'] ?? false, FILTER_VALIDATE_BOOLEAN)): ?><small>Featured on Home</small><?php endif; ?>
                  <?php if($item->button_route): ?><small>Route: <?php echo e($item->button_route); ?></small><?php endif; ?>
                  <?php if($item->button_url): ?><small>URL: <?php echo e(\Illuminate\Support\Str::limit($item->button_url, 42)); ?></small><?php endif; ?>
                  <?php if($item->item_type === 'filter'): ?><small>Value auto-generated</small><?php endif; ?>
                </td>
                <td><span class="pill <?php echo e($item->is_active ? '' : 'off'); ?>"><?php echo e($item->is_active ? 'Active' : 'Inactive'); ?></span></td>
                <td>
                  <div class="item-list-actions">
                    <a class="btn small" href="<?php echo e($editUrl); ?>">Edit</a>
                    <form method="POST" action="<?php echo e(route('admin.pages.items.destroy', $item)); ?>" onsubmit="return confirm('Delete this item?')">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button class="btn danger small" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>

  <?php if($editingItem): ?>
    <form id="section-item-form-<?php echo e($type); ?>" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="<?php echo e(route('admin.pages.items.update', $editingItem)); ?>">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <div class="list-form-head">
        <div>
          <h3>Edit <?php echo e(str_replace('_', ' ', $type)); ?></h3>
          <p><?php echo e($editingItem->title ?: $editingItem->button_text ?: 'Selected item'); ?></p>
        </div>
        <a class="btn small" href="<?php echo e($cancelUrl); ?>">Cancel</a>
      </div>
      <div class="form-grid">
        <?php echo $__env->make('admin.pages.sections.partials.item-type-select', ['item' => $editingItem, 'defaultType' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('admin.pages.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'defaultType' => $type, 'section' => $section], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if($type === 'work'): ?>
          <input type="hidden" name="settings[featured_on_home]" value="0">
          <div class="field full checkbox-pair-row">
            <label class="check-row work-feature-check compact-check">
              <input type="checkbox" name="settings[featured_on_home]" value="1" <?php if(filter_var(old('settings.featured_on_home', $editingItem->settings['featured_on_home'] ?? false), FILTER_VALIDATE_BOOLEAN)): echo 'checked'; endif; ?>>
              Feature this work on Home page
            </label>
            <label class="check-row compact-check">
              <input type="checkbox" name="is_active" value="1" <?php if($editingItem->is_active): echo 'checked'; endif; ?>>
              Active
            </label>
          </div>
        <?php else: ?>
          <label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if($editingItem->is_active): echo 'checked'; endif; ?>> Active</label>
        <?php endif; ?>
      </div>
      <div class="button-row"><button class="btn primary" type="submit">Save Item</button></div>
    </form>
  <?php endif; ?>

  <?php if($showAddForm): ?>
    <form id="section-item-form-<?php echo e($type); ?>" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="<?php echo e(route('admin.pages.items.store', $section)); ?>">
      <?php echo csrf_field(); ?>
      <div class="list-form-head">
        <div>
          <h3>Add <?php echo e(str_replace('_', ' ', $type)); ?></h3>
        </div>
        <?php if(! $items->isEmpty()): ?><a class="btn small" href="<?php echo e($cancelUrl); ?>">Cancel</a><?php endif; ?>
      </div>
      <div class="form-grid">
        <?php echo $__env->make('admin.pages.sections.partials.item-type-select', ['item' => null, 'defaultType' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('admin.pages.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $type, 'section' => $section], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if($type === 'work'): ?>
          <input type="hidden" name="settings[featured_on_home]" value="0">
          <div class="field full checkbox-pair-row">
            <label class="check-row work-feature-check compact-check">
              <input type="checkbox" name="settings[featured_on_home]" value="1" <?php if(filter_var(old('settings.featured_on_home', false), FILTER_VALIDATE_BOOLEAN)): echo 'checked'; endif; ?>>
              Feature this work on Home page
            </label>
            <label class="check-row compact-check">
              <input type="checkbox" name="is_active" value="1" checked>
              Active
            </label>
          </div>
        <?php else: ?>
          <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <?php endif; ?>
      </div>
      <button class="btn primary" type="submit">Add Item</button>
    </form>
  <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/items.blade.php ENDPATH**/ ?>