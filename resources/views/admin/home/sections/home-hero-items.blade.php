@php
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
@endphp

<section id="hero-items" class="hero-items-dashboard">
  <div class="item-list-head hero-dashboard-head">
    <div>
      <h2>Hero quick items</h2>
      <p>Manage hero chips, buttons, and marquee text. Social icons are managed globally from Social Links.</p>
    </div>
  </div>

  <div class="hero-group-grid">
    @foreach($heroGroups as $type => $group)
      @php
        $items = $section->items->where('item_type', $type)->sortBy('sort_order')->values();
        $editingItem = $items->firstWhere('id', $activeEditItemId);
        $isAdding = $activeAddType === $type;
        $groupId = 'hero-items-' . str_replace('_', '-', $type);
        $addUrl = route('admin.home.sections.edit', ['section' => $section, 'add_type' => $type]) . '#' . $groupId;
      @endphp

      <section class="hero-admin-panel hero-repeat-panel" id="{{ $groupId }}">
        <div class="hero-repeat-head">
          <div>
            <h3>{{ $group['title'] }}</h3>
            <span class="muted">{{ $items->count() }} item(s)</span>
          </div>
          <a class="btn primary small" href="{{ $addUrl }}">{{ $group['add'] }}</a>
        </div>

        @if($items->isEmpty())
          <div class="empty-list-card compact-empty">No {{ $group['single'] }} added yet.</div>
        @else
          <div class="hero-item-list">
            @foreach($items as $item)
              @php
                [$summaryTitle, $summaryDetail] = $summaryFor($item);
                $editUrl = route('admin.home.sections.edit', ['section' => $section, 'edit_item' => $item->id]) . '#' . $groupId;
                $isEditing = $editingItem?->is($item);
              @endphp
              <div class="hero-item-row {{ $isEditing ? 'is-editing' : '' }}">
                <div class="hero-item-row-main">
                  <strong>{{ \Illuminate\Support\Str::limit(strip_tags($summaryTitle), 70) }}</strong>
                  @if($summaryDetail)
                    <small>{{ \Illuminate\Support\Str::limit(strip_tags($summaryDetail), 80) }}</small>
                  @endif
                </div>
                <span class="pill {{ $item->is_active ? '' : 'off' }}">{{ $item->is_active ? 'Active' : 'Hidden' }}</span>
                <div class="item-list-actions">
                  <a class="btn small" href="{{ $editUrl }}">Edit</a>
                  <form method="POST" action="{{ route('admin.home.items.destroy', $item) }}" onsubmit="return confirm('Delete this {{ $group['single'] }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger small" type="submit">Delete</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        @if($editingItem)
          <form class="form-card hero-inline-form" method="POST" action="{{ route('admin.home.items.update', $editingItem) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="list-form-head hero-inline-head">
              <div>
                <h3>Edit {{ $group['single'] }}</h3>
              </div>
              <a class="btn small" href="{{ $baseUrl }}#{{ $groupId }}">Cancel</a>
            </div>
            <input type="hidden" name="item_type" value="{{ $type }}">
            <div class="form-grid">
              @include('admin.home.sections.partials.item-fields', ['item' => $editingItem, 'routes' => $routes, 'defaultType' => $type])
              <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($editingItem->is_active)> Active</label>
            </div>
            <button class="btn primary" type="submit">Save {{ $group['single'] }}</button>
          </form>
        @endif

        @if($isAdding)
          <form class="form-card hero-inline-form" method="POST" action="{{ route('admin.home.items.store', $section) }}" enctype="multipart/form-data">
            @csrf
            <div class="list-form-head hero-inline-head">
              <div>
                <h3>{{ $group['add'] }}</h3>
              </div>
              <a class="btn small" href="{{ $baseUrl }}#{{ $groupId }}">Cancel</a>
            </div>
            <input type="hidden" name="item_type" value="{{ $type }}">
            <div class="form-grid">
              @include('admin.home.sections.partials.item-fields', ['item' => null, 'routes' => $routes, 'defaultType' => $type])
              <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            </div>
            <button class="btn primary" type="submit">{{ $group['add'] }}</button>
          </form>
        @endif
      </section>
    @endforeach
  </div>
</section>
