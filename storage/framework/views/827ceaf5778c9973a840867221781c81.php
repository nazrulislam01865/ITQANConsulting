<?php
  $newType = match($section->section_key) {
    'home_hero' => 'chip',
    'home_founder' => 'paragraph',
    'home_who' => 'card',
    'home_problems' => 'problem',
    'home_services_preview' => 'service_card',
    'home_working' => 'step',
    'home_testimonials' => 'testimonial',
    'home_works_preview' => 'work',
    'home_values' => 'value',
    default => 'card',
  };
  $hideBadge = $section->section_key === 'home_who';
?>

<?php if($section->section_key === 'home_hero'): ?>
  <?php echo $__env->make('admin.home.sections.home-hero-items', ['section' => $section, 'routes' => $routes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
  <?php
    $editItemId = (int) request('edit_item');
    $editingItem = $section->items->firstWhere('id', $editItemId);
    $showAddForm = request()->boolean('add_item') || ($section->items->isEmpty() && ! $editingItem);
    $editUrl = fn ($item) => route('admin.home.sections.edit', ['section' => $section, 'edit_item' => $item->id]) . '#section-item-form';
    $addUrl = route('admin.home.sections.edit', ['section' => $section, 'add_item' => 1]) . '#section-item-form';
    $baseUrl = route('admin.home.sections.edit', $section);
  ?>

  <section class="item-list-panel">
    <div class="item-list-head">
      <div>
        <h2>Section items</h2>
        <p><?php echo e($section->items->count()); ?> saved item(s)</p>
      </div>
      <a class="btn primary" href="<?php echo e($addUrl); ?>">Add Item</a>
    </div>

    <?php if($section->items->isEmpty()): ?>
      <div class="empty-list-card">No items added yet.</div>
    <?php else: ?>
      <div class="table-wrap item-list-table-wrap">
        <table class="admin-table item-list-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Type</th>
              <th>Order</th>
              <th>Details</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $section->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $summaryTitle = $item->title ?: $item->button_text ?: $item->badge ?: ($item->settings['problem'] ?? null) ?: 'Untitled item';
                $summaryText = $item->text ?: $item->subtitle ?: ($item->settings['summary'] ?? null) ?: ($item->settings['response'] ?? null) ?: ($item->settings['author'] ?? null) ?: ($item->settings['role'] ?? null);
              ?>
              <tr class="<?php echo e($editingItem?->is($item) ? 'is-editing' : ''); ?>">
                <td>
                  <strong><?php echo e($summaryTitle); ?></strong>
                  <?php if($summaryText): ?><small><?php echo e(\Illuminate\Support\Str::limit(strip_tags($summaryText), 95)); ?></small><?php endif; ?>
                </td>
                <td><span class="pill"><?php echo e(str_replace('_', ' ', $item->item_type)); ?></span></td>
                <td><span class="pill"><?php echo e($item->sort_order); ?></span></td>
                <td>
                  <?php if($item->button_text): ?><small>Button: <?php echo e($item->button_text); ?></small><?php endif; ?>
                  <?php if($item->button_route): ?><small>Route: <?php echo e($item->button_route); ?></small><?php endif; ?>
                  <?php if($item->button_url): ?><small>URL: <?php echo e(\Illuminate\Support\Str::limit($item->button_url, 42)); ?></small><?php endif; ?>
                </td>
                <td><span class="pill <?php echo e($item->is_active ? '' : 'off'); ?>"><?php echo e($item->is_active ? 'Active' : 'Inactive'); ?></span></td>
                <td>
                  <div class="item-list-actions">
                    <a class="btn small" href="<?php echo e($editUrl($item)); ?>">Edit</a>
                    <form method="POST" action="<?php echo e(route('admin.home.items.destroy', $item)); ?>" onsubmit="return confirm('Delete this section item?')">
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
    <form id="section-item-form" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="<?php echo e(route('admin.home.items.update', $editingItem)); ?>">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <div class="list-form-head">
        <div>
          <h3>Edit item</h3>
          <p><?php echo e($editingItem->title ?: $editingItem->button_text ?: 'Selected item'); ?></p>
        </div>
        <a class="btn small" href="<?php echo e($baseUrl); ?>">Cancel</a>
      </div>
      <div class="form-grid">
        <?php echo $__env->make('admin.home.sections.partials.item-type-select', ['item' => $editingItem, 'defaultType' => $newType], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('admin.home.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'hideBadge' => $hideBadge, 'sectionKey' => $section->section_key], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if($editingItem->is_active): echo 'checked'; endif; ?>> Active</label>
      </div>
      <div class="button-row"><button class="btn primary" type="submit">Save Item</button></div>
    </form>
  <?php endif; ?>

  <?php if($showAddForm): ?>
    <form id="section-item-form" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="<?php echo e(route('admin.home.items.store', $section)); ?>">
      <?php echo csrf_field(); ?>
      <div class="list-form-head">
        <div>
          <h3>Add new item</h3>
        </div>
        <?php if(! $section->items->isEmpty()): ?><a class="btn small" href="<?php echo e($baseUrl); ?>">Cancel</a><?php endif; ?>
      </div>
      <div class="form-grid">
        <?php echo $__env->make('admin.home.sections.partials.item-type-select', ['item' => null, 'defaultType' => $newType], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('admin.home.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $newType, 'hideBadge' => $hideBadge, 'sectionKey' => $section->section_key], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
      </div>
      <button class="btn primary" type="submit">Add Item</button>
    </form>
  <?php endif; ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/items.blade.php ENDPATH**/ ?>