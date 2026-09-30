<?php
  $itemTypes = [
    'button' => 'Button',
    'card' => 'Card',
    'mission_card' => 'Mission / vision card',
    'service_area' => 'Service card',
    'faq' => 'FAQ item',
    'work' => 'Work card',
    'filter' => 'Filter tab',
    'catalog_page' => 'Catalog page',
    'step' => 'Step item',
    'option' => 'Option',
  ];
  $selectedType = old('item_type', $item->item_type ?? ($defaultType ?? 'card'));
?>

<div class="field">
  <label>Item type</label>
  <select class="item-type-select" name="item_type" required>
    <?php $__currentLoopData = $itemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($value); ?>" <?php if($selectedType === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/pages/sections/partials/item-type-select.blade.php ENDPATH**/ ?>