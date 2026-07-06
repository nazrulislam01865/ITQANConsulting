@extends('admin.layouts.app')

@section('title', 'Header Menu')

@section('content')
<div class="page-head">
  <div>
    <h2>Header navigation control.</h2>
    <p>Change top menu labels, status, and route/url without touching the frontend Blade.</p>
  </div>
</div>

<div class="form-card">
  <h3>Add Header Menu Item</h3>
  <form method="POST" action="{{ route('admin.header-menu.store') }}">
    @csrf
    <div class="form-grid">
      <div class="field"><label>Label</label><input name="label" required></div>
      <div class="field"><label>Route</label><select name="route_name"><option value="">Use custom URL</option>@foreach($routes as $route)<option value="{{ $route }}">{{ $route }}</option>@endforeach</select></div>
      <div class="field"><label>Custom URL</label><input name="url" placeholder="https:// or /custom-page"></div>
      <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button class="btn primary" type="submit">Add Item</button>
  </form>
</div>

<div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Label</th><th>Route</th><th>URL</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($items as $item)
        <tr>
          <form method="POST" action="{{ route('admin.header-menu.update', $item) }}">
            @csrf @method('PUT')
            <td><input name="label" value="{{ $item->label }}" required></td>
            <td><select name="route_name"><option value="">Custom URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected($item->route_name === $route)>{{ $route }}</option>@endforeach</select></td>
            <td><input name="url" value="{{ $item->url }}"></td>
            <td><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label></td>
            <td class="button-row"><button class="btn small primary" type="submit">Save</button>
          </form>
          <form class="danger-inline" method="POST" action="{{ route('admin.header-menu.destroy', $item) }}" onsubmit="return confirm('Delete this header menu item?')">
            @csrf @method('DELETE')
            <button class="btn small danger" type="submit">Delete</button>
          </form></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
