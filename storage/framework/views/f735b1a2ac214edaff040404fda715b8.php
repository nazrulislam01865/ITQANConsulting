<?php
  $itemTypes = [
    'chip' => 'Short line chip',
    'button' => 'Button',
    'social_link' => 'Social media link',
    'ticker' => 'Marquee item',
    'paragraph' => 'Paragraph',
    'card' => 'Card',
    'service_card' => 'Service preview card',
    'step' => 'Working step',
    'problem' => 'Problem / response',
    'testimonial' => 'Testimonial',
    'value' => 'How we think principle',
    'work' => 'Work card',
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/partials/item-type-select.blade.php ENDPATH**/ ?>