@php
  $newType = match($section->section_key) {
    'home_hero' => 'chip',
    'home_founder' => 'paragraph',
    'home_who' => 'card',
    'home_problems' => 'problem',
    'home_services_preview' => 'service_card',
    'home_working' => 'step',
    'home_testimonials' => 'testimonial',
    'home_works_preview' => 'work',
    default => 'card',
  };
  $hideBadge = $section->section_key === 'home_who';
@endphp

@if($section->section_key === 'home_hero')
  @include('admin.home.sections.home-hero-items', ['section' => $section, 'routes' => $routes])
@else
  @php
    $editItemId = (int) request('edit_item');
    $editingItem = $section->items->firstWhere('id', $editItemId);
    $showAddForm = request()->boolean('add_item') || ($section->items->isEmpty() && ! $editingItem);
    $editUrl = fn ($item) => route('admin.home.sections.edit', ['section' => $section, 'edit_item' => $item->id]) . '#section-item-form';
    $addUrl = route('admin.home.sections.edit', ['section' => $section, 'add_item' => 1]) . '#section-item-form';
    $baseUrl = route('admin.home.sections.edit', $section);
  @endphp

  <section class="item-list-panel">
    <div class="item-list-head">
      <div>
        <h2>Section items</h2>
        <p>{{ $section->items->count() }} saved item(s)</p>
      </div>
      <a class="btn primary" href="{{ $addUrl }}">Add Item</a>
    </div>

    @if($section->items->isEmpty())
      <div class="empty-list-card">No items added yet.</div>
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
            @foreach($section->items as $item)
              @php
                $summaryTitle = $item->title ?: $item->button_text ?: $item->badge ?: ($item->settings['problem'] ?? null) ?: 'Untitled item';
                $summaryText = $item->text ?: $item->subtitle ?: ($item->settings['response'] ?? null) ?: ($item->settings['author'] ?? null) ?: ($item->settings['role'] ?? null);
              @endphp
              <tr class="{{ $editingItem?->is($item) ? 'is-editing' : '' }}">
                <td>
                  <strong>{{ $summaryTitle }}</strong>
                  @if($summaryText)<small>{{ \Illuminate\Support\Str::limit(strip_tags($summaryText), 95) }}</small>@endif
                </td>
                <td><span class="pill">{{ str_replace('_', ' ', $item->item_type) }}</span></td>
                <td>
                  @if($item->button_text)<small>Button: {{ $item->button_text }}</small>@endif
                  @if($item->button_route)<small>Route: {{ $item->button_route }}</small>@endif
                  @if($item->button_url)<small>URL: {{ \Illuminate\Support\Str::limit($item->button_url, 42) }}</small>@endif
                </td>
                <td><span class="pill {{ $item->is_active ? '' : 'off' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                  <div class="item-list-actions">
                    <a class="btn small" href="{{ $editUrl($item) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.home.items.destroy', $item) }}" onsubmit="return confirm('Delete this section item?')">
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
    <form id="section-item-form" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.home.items.update', $editingItem) }}">
      @csrf
      @method('PUT')
      <div class="list-form-head">
        <div>
          <h3>Edit item</h3>
          <p>{{ $editingItem->title ?: $editingItem->button_text ?: 'Selected item' }}</p>
        </div>
        <a class="btn small" href="{{ $baseUrl }}">Cancel</a>
      </div>
      <div class="form-grid">
        @include('admin.home.sections.partials.item-type-select', ['item' => $editingItem, 'defaultType' => $newType])
        @include('admin.home.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'hideBadge' => $hideBadge])
        <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($editingItem->is_active)> Active</label>
      </div>
      <div class="button-row"><button class="btn primary" type="submit">Save Item</button></div>
    </form>
  @endif

  @if($showAddForm)
    <form id="section-item-form" class="form-card list-form-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.home.items.store', $section) }}">
      @csrf
      <div class="list-form-head">
        <div>
          <h3>Add new item</h3>
        </div>
        @if(! $section->items->isEmpty())<a class="btn small" href="{{ $baseUrl }}">Cancel</a>@endif
      </div>
      <div class="form-grid">
        @include('admin.home.sections.partials.item-type-select', ['item' => null, 'defaultType' => $newType])
        @include('admin.home.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $newType, 'hideBadge' => $hideBadge])
        <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
      </div>
      <button class="btn primary" type="submit">Add Item</button>
    </form>
  @endif
@endif
