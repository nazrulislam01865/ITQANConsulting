@php
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
@endphp

<div class="field">
  <label>Item type</label>
  <select class="item-type-select" name="item_type" required>
    @foreach($itemTypes as $value => $label)
      <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
    @endforeach
  </select>
</div>
