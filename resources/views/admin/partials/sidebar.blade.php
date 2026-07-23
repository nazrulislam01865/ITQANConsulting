@php
  $homeOpen = request()->routeIs('admin.home.*');
  $currentHomeSection = request()->route('section');
  $currentPageKey = request()->route('pageKey');
  $currentPageSection = request()->route('section');
@endphp

<aside class="sidebar" id="admin-sidebar">
  @include('admin.partials.brand', [
    'href' => route('admin.dashboard'),
    'title' => 'ITQAN Admin',
    'subtitle' => 'Backend Control',
  ])

  <nav class="sidebar-nav" aria-label="Admin navigation">
    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>

    <div class="nav-group {{ $homeOpen ? 'open' : '' }}" data-nav-group>
      <button class="nav-parent {{ $homeOpen ? 'active' : '' }}" type="button" data-nav-toggle data-storage-key="itqan-admin-home-menu" aria-expanded="{{ $homeOpen ? 'true' : 'false' }}">
        <span>Home Page</span>
        <span class="nav-caret" aria-hidden="true">⌄</span>
      </button>

      <div class="submenu" aria-label="Home page section submenu">
        <a class="{{ request()->routeIs('admin.home.index') ? 'active' : '' }}" href="{{ route('admin.home.index') }}">Overview</a>

        @foreach(($adminHomeSections ?? collect()) as $homeSection)
          @php
            $isCurrentSection = request()->routeIs('admin.home.sections.edit')
              && $currentHomeSection instanceof \App\Models\HomeSection
              && $currentHomeSection->is($homeSection);
          @endphp
          <a class="{{ $isCurrentSection ? 'active' : '' }}" href="{{ route('admin.home.sections.edit', $homeSection) }}">
            <span>{{ $homeSection->admin_title }}</span>
            <small>{{ $homeSection->items_count }} item(s)</small>
          </a>
        @endforeach
      </div>
    </div>

    @foreach(($adminPageGroups ?? []) as $pageKey => $pageGroup)
      @php
        $pageOpen = request()->routeIs('admin.pages.*') && $currentPageKey === $pageKey;
      @endphp
      <div class="nav-group {{ $pageOpen ? 'open' : '' }}" data-nav-group>
        <button class="nav-parent {{ $pageOpen ? 'active' : '' }}" type="button" data-nav-toggle data-storage-key="itqan-admin-{{ $pageKey }}-menu" aria-expanded="{{ $pageOpen ? 'true' : 'false' }}">
          <span>{{ $pageGroup['label'] }}</span>
          <span class="nav-caret" aria-hidden="true">⌄</span>
        </button>

        <div class="submenu" aria-label="{{ $pageGroup['label'] }} section submenu">
          <a class="{{ request()->routeIs('admin.pages.index') && $currentPageKey === $pageKey ? 'active' : '' }}" href="{{ route('admin.pages.index', $pageKey) }}">Overview</a>

          @foreach($pageGroup['sections'] as $pageSection)
            @php
              $isCurrentPageSection = request()->routeIs('admin.pages.sections.edit')
                && $currentPageKey === $pageKey
                && $currentPageSection instanceof \App\Models\PageSection
                && $currentPageSection->is($pageSection);
            @endphp
            <a class="{{ $isCurrentPageSection ? 'active' : '' }}" href="{{ route('admin.pages.sections.edit', [$pageKey, $pageSection]) }}">
              <span>{{ $pageSection->admin_title }}</span>
              <small>{{ $pageSection->items_count }} item(s)</small>
            </a>
          @endforeach
        </div>
      </div>
    @endforeach

    <a class="{{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}" href="{{ route('admin.site-settings.edit') }}">Site Settings, Logo &amp; Favicon</a>
    <a class="{{ request()->routeIs('admin.header-menu.*') ? 'active' : '' }}" href="{{ route('admin.header-menu.index') }}">Header Menu</a>
    <a class="{{ request()->routeIs('admin.footer-menu.*') ? 'active' : '' }}" href="{{ route('admin.footer-menu.index') }}">Footer Menu</a>
    <a class="{{ request()->routeIs('admin.contact-submissions.*') ? 'active' : '' }}" href="{{ route('admin.contact-submissions.index') }}">Contact Responses</a>
    <a class="{{ request()->routeIs('admin.work-orders.*') ? 'active' : '' }}" href="{{ route('admin.work-orders.index') }}">Work Orders</a>
    <a class="{{ request()->routeIs('admin.social-links.*') ? 'active' : '' }}" href="{{ route('admin.social-links.index') }}">Social Links</a>
    <a href="{{ route('home') }}" target="_blank" rel="noopener">View Website</a>
  </nav>
</aside>
