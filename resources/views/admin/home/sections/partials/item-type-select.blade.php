@php
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
@endphp

<div class="field">
  <label>Item type</label>
  <select class="item-type-select" name="item_type" required>
    @foreach($itemTypes as $value => $label)
      <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
    @endforeach
  </select>
</div>
