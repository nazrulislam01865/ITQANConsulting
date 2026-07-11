@extends('admin.layouts.app')

@section('title', 'Contact Responses')

@section('content')
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

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
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
        <tr><td colspan="7">No contact responses yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($items->hasPages())
  <div style="margin-top:18px">{{ $items->links() }}</div>
@endif
@endsection
