@extends('admin.layouts.app')

@section('title', 'Footer Menu')

@section('content')
<div class="page-head">
  <div>
    <h2>Footer menu control.</h2>
    <p>Footer groups are separated from the header. You can manage Pages and Services links independently.</p>
  </div>
</div>

<div class="form-card">
  <h3>Add Footer Menu Item</h3>
  <form method="POST" action="{{ route('admin.footer-menu.store') }}">
    @csrf
    <div class="form-grid">
      <div class="field"><label>Group</label><select name="group_key">@foreach($groups as $key => $title)<option value="{{ $key }}">{{ $title }}</option>@endforeach</select></div>
      <div class="field"><label>Group title</label><input name="group_title" value="Pages" required></div>
      <div class="field"><label>Label</label><input name="label" required></div>
      <div class="field"><label>Route</label><select name="route_name"><option value="">Use custom URL / plain text</option>@foreach($routes as $route)<option value="{{ $route }}">{{ $route }}</option>@endforeach</select></div>
      <div class="field"><label>Custom URL</label><input name="url" placeholder="https:// or /custom-page"></div>
      <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button class="btn primary" type="submit">Add Item</button>
  </form>
</div>

<div class="table-wrap">
  <table class="admin-table">
    <thead><tr><th>Group</th><th>Label</th><th>Route</th><th>URL</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($items as $item)
        <tr>
          <form method="POST" action="{{ route('admin.footer-menu.update', $item) }}">
            @csrf @method('PUT')
            <td><input name="group_key" value="{{ $item->group_key }}" style="width:120px"><input name="group_title" value="{{ $item->group_title }}" style="width:130px;margin-top:6px"></td>
            <td><input name="label" value="{{ $item->label }}" required></td>
            <td><select name="route_name"><option value="">Custom / plain</option>@foreach($routes as $route)<option value="{{ $route }}" @selected($item->route_name === $route)>{{ $route }}</option>@endforeach</select></td>
            <td><input name="url" value="{{ $item->url }}"></td>
            <td><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label></td>
            <td class="button-row"><button class="btn small primary" type="submit">Save</button>
          </form>
          <form class="danger-inline" method="POST" action="{{ route('admin.footer-menu.destroy', $item) }}" onsubmit="return confirm('Delete this footer menu item?')">
            @csrf @method('DELETE')
            <button class="btn small danger" type="submit">Delete</button>
          </form></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
