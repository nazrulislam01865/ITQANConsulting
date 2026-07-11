<?php
  $heroGroups = [
    'chip' => [
      'title' => 'Short line chips',
      'single' => 'chip',
      'add' => 'Add chip',
    ],
    'button' => [
      'title' => 'Hero buttons',
      'single' => 'button',
      'add' => 'Add button',
    ],
    'ticker' => [
      'title' => 'Marquee items',
      'single' => 'marquee item',
      'add' => 'Add marquee item',
    ],
  ];

  $activeEditItemId = (int) request('edit_item');
  $activeAddType = request('add_type');
  $baseUrl = route('admin.home.sections.edit', $section);

  $summaryFor = function ($item): array {
    $title = $item->title ?: $item->button_text ?: $item->text ?: ($item->settings['platform'] ?? null) ?: 'Untitled item';
    $detail = null;

    if ($item->item_type === 'button') {
      $detail = $item->button_route ?: $item->button_url ?: 'No link set';
    } elseif ($item->item_type === 'ticker') {
      $detail = 'Marquee text';
    } elseif ($item->item_type === 'chip') {
      $detail = 'Short chip';
    }

    return [$title, $detail];
  };
?>

<section id="hero-items" class="hero-items-dashboard">
  <div class="item-list-head hero-dashboard-head">
    <div>
      <h2>Hero quick items</h2>
      <p>Manage hero chips, buttons, and marquee text. Social icons are managed globally from Social Links.</p>
    </div>
  </div>

  <div class="hero-group-grid">
    <?php $__currentLoopData = $heroGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $items = $section->items->where('item_type', $type)->sortBy('sort_order')->values();
        $editingItem = $items->firstWhere('id', $activeEditItemId);
        $isAdding = $activeAddType === $type;
        $groupId = 'hero-items-' . str_replace('_', '-', $type);
        $addUrl = route('admin.home.sections.edit', ['section' => $section, 'add_type' => $type]) . '#' . $groupId;
      ?>

      <section class="hero-admin-panel hero-repeat-panel" id="<?php echo e($groupId); ?>">
        <div class="hero-repeat-head">
          <div>
            <h3><?php echo e($group['title']); ?></h3>
            <span class="muted"><?php echo e($items->count()); ?> item(s)</span>
          </div>
          <a class="btn primary small" href="<?php echo e($addUrl); ?>"><?php echo e($group['add']); ?></a>
        </div>

        <?php if($items->isEmpty()): ?>
          <div class="empty-list-card compact-empty">No <?php echo e($group['single']); ?> added yet.</div>
        <?php else: ?>
          <div class="hero-item-list">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                [$summaryTitle, $summaryDetail] = $summaryFor($item);
                $editUrl = route('admin.home.sections.edit', ['section' => $section, 'edit_item' => $item->id]) . '#' . $groupId;
                $isEditing = $editingItem?->is($item);
              ?>
              <div class="hero-item-row <?php echo e($isEditing ? 'is-editing' : ''); ?>">
                <div class="hero-item-row-main">
                  <strong><?php echo e(\Illuminate\Support\Str::limit(strip_tags($summaryTitle), 70)); ?></strong>
                  <?php if($summaryDetail): ?>
                    <small><?php echo e(\Illuminate\Support\Str::limit(strip_tags($summaryDetail), 80)); ?></small>
                  <?php endif; ?>
                </div>
                <span class="pill <?php echo e($item->is_active ? '' : 'off'); ?>"><?php echo e($item->is_active ? 'Active' : 'Hidden'); ?></span>
                <div class="item-list-actions">
                  <a class="btn small" href="<?php echo e($editUrl); ?>">Edit</a>
                  <form method="POST" action="<?php echo e(route('admin.home.items.destroy', $item)); ?>" onsubmit="return confirm('Delete this <?php echo e($group['single']); ?>?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button class="btn danger small" type="submit">Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>

        <?php if($editingItem): ?>
          <form class="form-card hero-inline-form" method="POST" action="<?php echo e(route('admin.home.items.update', $editingItem)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="list-form-head hero-inline-head">
              <div>
                <h3>Edit <?php echo e($group['single']); ?></h3>
              </div>
              <a class="btn small" href="<?php echo e($baseUrl); ?>#<?php echo e($groupId); ?>">Cancel</a>
            </div>
            <input type="hidden" name="item_type" value="<?php echo e($type); ?>">
            <div class="form-grid">
              <?php echo $__env->make('admin.home.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'defaultType' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <label class="check-row"><input type="checkbox" name="is_active" value="1" <?php if($editingItem->is_active): echo 'checked'; endif; ?>> Active</label>
            </div>
            <button class="btn primary" type="submit">Save <?php echo e($group['single']); ?></button>
          </form>
        <?php endif; ?>

        <?php if($isAdding): ?>
          <form class="form-card hero-inline-form" method="POST" action="<?php echo e(route('admin.home.items.store', $section)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="list-form-head hero-inline-head">
              <div>
                <h3><?php echo e($group['add']); ?></h3>
              </div>
              <a class="btn small" href="<?php echo e($baseUrl); ?>#<?php echo e($groupId); ?>">Cancel</a>
            </div>
            <input type="hidden" name="item_type" value="<?php echo e($type); ?>">
            <div class="form-grid">
              <?php echo $__env->make('admin.home.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            </div>
            <button class="btn primary" type="submit"><?php echo e($group['add']); ?></button>
          </form>
        <?php endif; ?>
      </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</section>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/home-hero-items.blade.php ENDPATH**/ ?>