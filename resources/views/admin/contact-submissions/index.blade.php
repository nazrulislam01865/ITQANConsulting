@extends('admin.layouts.app')

@section('title', 'Contact Responses')

@section('content')
<style>
  .contact-bulk-toolbar{display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:12px;margin:0 0 12px}
  .contact-bulk-toolbar .muted{font-size:.85rem}
  .contact-bulk-toolbar .btn:disabled{opacity:.45;cursor:not-allowed;transform:none;box-shadow:none}
  .admin-table th.contact-select-col,.admin-table td.contact-select-col{width:52px;text-align:center;padding-inline:12px}
  .admin-table input.contact-response-checkbox{appearance:auto;width:18px;height:18px;margin:0;padding:0;border:0;border-radius:4px;vertical-align:middle;accent-color:var(--admin-blue);cursor:pointer}
</style>

<div class="page-head">
  <div>
    <h2>Contact responses.</h2>
    <p>Messages submitted from the public contact form are saved here.</p>
  </div>
  <div class="button-row">
    <span class="pill">{{ $totalCount }} total</span>
    <span class="pill {{ $unreadCount > 0 ? '' : 'off' }}">{{ $unreadCount }} unread</span>
  </div>
</div>

@if($items->count())
  <form id="contact-bulk-form" class="contact-bulk-toolbar" method="POST" action="{{ route('admin.contact-submissions.bulk-destroy') }}">
    @csrf
    @method('DELETE')
    <span class="muted" id="contact-selected-count" role="status" aria-live="polite">0 selected on this page</span>
    <button class="btn small danger" id="contact-bulk-delete" type="submit" disabled>Delete selected</button>
  </form>
@endif

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th class="contact-select-col">
          <input type="checkbox" class="contact-response-checkbox" id="contact-select-all" aria-label="Select all contact responses on this page" @disabled(!$items->count())>
        </th>
        <th>Date</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Need</th>
        <th>Message</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $item)
        <tr>
          <td class="contact-select-col">
            <input type="checkbox" class="contact-response-checkbox contact-row-select" name="ids[]" value="{{ $item->id }}" form="contact-bulk-form" aria-label="Select response from {{ $item->name }}">
          </td>
          <td>{{ $item->created_at?->format('d M Y, h:i A') }}</td>
          <td>
            <strong>{{ $item->name }}</strong>
            @if($item->company_name)
              <br><span class="muted">{{ $item->company_name }}</span>
            @endif
          </td>
          <td>
            <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
            @if($item->phone)
              <br><span class="muted">{{ $item->phone }}</span>
            @endif
          </td>
          <td>{{ collect($item->support_types ?? [])->filter()->join(', ') ?: ($item->need ?: '—') }}</td>
          <td>{{ \Illuminate\Support\Str::limit($item->message, 90) }}</td>
          <td><span class="pill {{ $item->status === 'unread' ? '' : 'off' }}">{{ ucfirst($item->status) }}</span></td>
          <td class="button-row">
            <a class="btn small primary" href="{{ route('admin.contact-submissions.show', $item) }}">View</a>
            <form class="danger-inline" method="POST" action="{{ route('admin.contact-submissions.destroy', $item) }}" onsubmit="return confirm('Delete this contact response?')">
              @csrf
              @method('DELETE')
              <button class="btn small danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="8">No contact responses yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($items->hasPages())
  {{ $items->links('admin.partials.pagination') }}
@endif

@if($items->count())
<script>
(() => {
  const form = document.getElementById('contact-bulk-form');
  const selectAll = document.getElementById('contact-select-all');
  const rows = Array.from(document.querySelectorAll('.contact-row-select'));
  const deleteButton = document.getElementById('contact-bulk-delete');
  const selectedCount = document.getElementById('contact-selected-count');

  const updateSelection = () => {
    const count = rows.filter((checkbox) => checkbox.checked).length;
    deleteButton.disabled = count === 0;
    selectedCount.textContent = `${count} selected on this page`;
    selectAll.checked = count === rows.length;
    selectAll.indeterminate = count > 0 && count < rows.length;
  };

  selectAll.addEventListener('change', () => {
    rows.forEach((checkbox) => { checkbox.checked = selectAll.checked; });
    updateSelection();
  });

  rows.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));

  form.addEventListener('submit', (event) => {
    const count = rows.filter((checkbox) => checkbox.checked).length;
    if (count === 0 || !window.confirm(`Permanently delete ${count} selected contact ${count === 1 ? 'response' : 'responses'}? This cannot be undone.`)) {
      event.preventDefault();
    }
  });

  updateSelection();
})();
</script>
@endif
@endsection
