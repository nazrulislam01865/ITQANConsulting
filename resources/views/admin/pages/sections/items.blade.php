@php
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
    'contact_form' => [
      'step' => ['title' => 'What happens next steps'],
    ],
    default => [],
  };

  $editItemId = (int) request('edit_item');
  $requestedAddType = request('add_item');
  $baseEditUrl = fn () => route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section]);
@endphp

@foreach($sectionGroups as $type => $group)
  @php
    $items = $section->items->where('item_type', $type)->sortBy('sort_order')->values();
    $editingItem = $items->firstWhere('id', $editItemId);
    $showAddForm = $requestedAddType === $type || ($items->isEmpty() && count($sectionGroups) === 1 && ! $editItemId && ! $requestedAddType);
    $addUrl = route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section, 'add_item' => $type]) . '#section-item-form-' . $type;
    $cancelUrl = $baseEditUrl();
  @endphp

  <section class="item-list-panel">
    <div class="item-list-head">
      <div>
        <h2>{{ $group['title'] }}</h2>
        <p>{{ $items->count() }} saved item(s)</p>
      </div>
      <a class="btn primary" href="{{ $addUrl }}">Add {{ str_replace('_', ' ', $type) }}</a>
    </div>

    @if($items->isEmpty())
      <div class="empty-list-card">No {{ str_replace('_', ' ', $type) }} added yet.</div>
    @else
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
            @foreach($items as $item)
              @php
                $summaryTitle = $item->title ?: $item->button_text ?: $item->badge ?: 'Untitled item';
                $summaryText = $item->subtitle ?: $item->text ?: ($item->settings['points'] ?? null) ?: ($item->settings['tags'] ?? null) ?: ($item->settings['categories'] ?? null);
                $editUrl = route('admin.pages.sections.edit', ['pageKey' => $pageKey, 'section' => $section, 'edit_item' => $item->id]) . '#section-item-form-' . $type;
              @endphp
              <tr class="{{ $editingItem?->is($item) ? 'is-editing' : '' }}">
                <td>
                  <strong>{{ $summaryTitle }}</strong>
                  @if($summaryText)<small>{{ \Illuminate\Support\Str::limit(strip_tags($summaryText), 100) }}</small>@endif
                </td>
                <td><span class="pill">{{ str_replace('_', ' ', $item->item_type) }}</span></td>
                <td>
                  @if($item->button_text)<small>Button: {{ $item->button_text }}</small>@endif
                  @if($item->item_type === 'work' && filter_var($item->settings['featured_on_home'] ?? false, FILTER_VALIDATE_BOOLEAN))<small>Featured on Home</small>@endif
                  @if($item->button_route)<small>Route: {{ $item->button_route }}</small>@endif
                  @if($item->button_url)<small>URL: {{ \Illuminate\Support\Str::limit($item->button_url, 42) }}</small>@endif
                  @if($item->item_type === 'filter')<small>Value auto-generated</small>@endif
                </td>
                <td><span class="pill {{ $item->is_active ? '' : 'off' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                  <div class="item-list-actions">
                    <a class="btn small" href="{{ $editUrl }}">Edit</a>
                    <form method="POST" action="{{ route('admin.pages.items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn danger small" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

  @if($editingItem)
    <form id="section-item-form-{{ $type }}" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.pages.items.update', $editingItem) }}">
      @csrf
      @method('PUT')
      <div class="list-form-head">
        <div>
          <h3>Edit {{ str_replace('_', ' ', $type) }}</h3>
          <p>{{ $editingItem->title ?: $editingItem->button_text ?: 'Selected item' }}</p>
        </div>
        <a class="btn small" href="{{ $cancelUrl }}">Cancel</a>
      </div>
      <div class="form-grid">
        @include('admin.pages.sections.partials.item-type-select', ['item' => $editingItem, 'defaultType' => $type])
        @include('admin.pages.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'defaultType' => $type, 'section' => $section])
        @if($type === 'work')
          <input type="hidden" name="settings[featured_on_home]" value="0">
          <div class="field full checkbox-pair-row">
            <label class="check-row work-feature-check compact-check">
              <input type="checkbox" name="settings[featured_on_home]" value="1" @checked(filter_var(old('settings.featured_on_home', $editingItem->settings['featured_on_home'] ?? false), FILTER_VALIDATE_BOOLEAN))>
              Feature this work on Home page
            </label>
            <label class="check-row compact-check">
              <input type="checkbox" name="is_active" value="1" @checked($editingItem->is_active)>
              Active
            </label>
          </div>
        @else
          <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($editingItem->is_active)> Active</label>
        @endif
      </div>
      <div class="button-row"><button class="btn primary" type="submit">Save Item</button></div>
    </form>
  @endif

  @if($showAddForm)
    <form id="section-item-form-{{ $type }}" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.pages.items.store', $section) }}">
      @csrf
      <div class="list-form-head">
        <div>
          <h3>Add {{ str_replace('_', ' ', $type) }}</h3>
        </div>
        @if(! $items->isEmpty())<a class="btn small" href="{{ $cancelUrl }}">Cancel</a>@endif
      </div>
      <div class="form-grid">
        @include('admin.pages.sections.partials.item-type-select', ['item' => null, 'defaultType' => $type])
        @include('admin.pages.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $type, 'section' => $section])
        @if($type === 'work')
          <input type="hidden" name="settings[featured_on_home]" value="0">
          <div class="field full checkbox-pair-row">
            <label class="check-row work-feature-check compact-check">
              <input type="checkbox" name="settings[featured_on_home]" value="1" @checked(filter_var(old('settings.featured_on_home', false), FILTER_VALIDATE_BOOLEAN))>
              Feature this work on Home page
            </label>
            <label class="check-row compact-check">
              <input type="checkbox" name="is_active" value="1" checked>
              Active
            </label>
          </div>
        @else
          <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        @endif
      </div>
      <button class="btn primary" type="submit">Add Item</button>
    </form>
  @endif
@endforeach
