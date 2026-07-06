@extends('admin.layouts.app')

@section('title', 'Social Links')

@section('content')
<div class="page-head">
  <div>
    <h2>Social links.</h2>
    <p>Manage social icons once. They are reused in the Home hero, Contact page, Footer, and future social sections.</p>
  </div>
</div>

<div class="form-card" id="add-social-link">
  <h3>Add Social Link</h3>
  <form method="POST" action="{{ route('admin.social-links.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
      <div class="field">
        <label>Platform</label>
        <input name="platform" required placeholder="LinkedIn, Facebook, WhatsApp, Instagram">
      </div>
      <div class="field">
        <label>Label</label>
        <input name="label" required placeholder="ITQAN on LinkedIn">
      </div>
      <div class="field full">
        <label>Social link</label>
        <input name="url" placeholder="https://...">
      </div>
      <div class="field full">
        <label>Icon image, optional</label>
        <div class="social-upload-inline">
          <span class="social-admin-icon-placeholder">Auto</span>
          <input name="icon_image" type="file" accept="image/png,image/jpeg,image/webp">
          <span class="help">Optional. If empty, the system uses the link favicon or platform fallback.</span>
        </div>
      </div>
      <label class="check-row"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
    <button class="btn primary" type="submit">Add Social Link</button>
  </form>
</div>

<div class="table-wrap social-link-table-wrap">
  <table class="admin-table social-link-table">
    <thead>
      <tr>
        <th>Icon</th>
        <th>Platform</th>
        <th>Label</th>
        <th>URL</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $item)
        <tr>
          <form method="POST" action="{{ route('admin.social-links.update', $item) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <td class="social-admin-icon-cell">
              <div class="social-icon-inline">
                <img class="social-admin-icon" src="{{ $item->resolvedIconUrl() }}" alt="{{ $item->label }} icon" loading="lazy">
                <div class="social-icon-control">
                  <strong>{{ $item->iconSourceLabel() }}</strong>
                  <input name="icon_image" type="file" accept="image/png,image/jpeg,image/webp">
                  @if($item->uploadedIconUrl())
                    <label class="check-row compact"><input type="checkbox" name="remove_icon" value="1"> Remove uploaded icon</label>
                  @endif
                </div>
              </div>
            </td>
            <td><input name="platform" value="{{ $item->platform }}" required></td>
            <td><input name="label" value="{{ $item->label }}" required></td>
            <td><input name="url" value="{{ $item->url }}" placeholder="https://..."></td>
            <td><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label></td>
            <td class="button-row"><button class="btn small primary" type="submit">Save</button>
          </form>
          <form class="danger-inline" method="POST" action="{{ route('admin.social-links.destroy', $item) }}" onsubmit="return confirm('Delete this social link?')">
            @csrf
            @method('DELETE')
            <button class="btn small danger" type="submit">Delete</button>
          </form></td>
        </tr>
      @empty
        <tr><td colspan="6">No social links added yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
