@extends('admin.layouts.app')

@section('title', 'Work Orders')

@section('content')
<div class="page-head">
  <div>
    <h2>Work order requests.</h2>
    <p>Review requests submitted from the public Works page and track each request from first review to completion.</p>
  </div>
  <div class="button-row">
    <span class="pill">{{ $totalCount }} total</span>
    <span class="pill {{ $newCount > 0 ? '' : 'off' }}">{{ $newCount }} new</span>
    <span class="pill {{ $unviewedCount > 0 ? '' : 'off' }}">{{ $unviewedCount }} unviewed</span>
  </div>
</div>

<form class="form-card work-order-filter" method="GET" action="{{ route('admin.work-orders.index') }}">
  <div class="form-grid">
    <div class="field">
      <label for="workOrderSearch">Search requests</label>
      <input id="workOrderSearch" type="search" name="q" value="{{ $search }}" placeholder="Reference, work, client, email, or phone">
    </div>
    <div class="field">
      <label for="workOrderStatus">Status</label>
      <select id="workOrderStatus" name="status">
        <option value="">All statuses</option>
        @foreach($statuses as $value => $label)
          <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="button-row">
    <button class="btn primary" type="submit">Apply Filters</button>
    @if($search !== '' || filled($selectedStatus))
      <a class="btn" href="{{ route('admin.work-orders.index') }}">Clear</a>
    @endif
  </div>
</form>

<div class="table-wrap">
  <table class="admin-table work-order-table">
    <thead>
      <tr>
        <th>Reference / Date</th>
        <th>Requested Work</th>
        <th>Client</th>
        <th>Budget / Timeline</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr class="{{ $order->isUnviewed() ? 'is-unviewed' : '' }}">
          <td>
            <strong>{{ $order->reference_number }}</strong>
            <br><span class="muted">{{ $order->created_at?->format('d M Y, h:i A') }}</span>
            @if($order->isUnviewed())
              <br><span class="pill">Unviewed</span>
            @endif
          </td>
          <td>
            <strong>{{ $order->work_title }}</strong>
            @if($order->work_category)
              <br><span class="muted">{{ $order->work_category }}</span>
            @endif
          </td>
          <td>
            <strong>{{ $order->customer_name }}</strong>
            @if($order->company_name)
              <br><span class="muted">{{ $order->company_name }}</span>
            @endif
            <br><a href="mailto:{{ $order->email }}">{{ $order->email }}</a>
            <br><span class="muted">{{ $order->phone }}</span>
          </td>
          <td>
            {{ $order->budget_range }}
            <br><span class="muted">{{ $order->timeline }}</span>
          </td>
          <td><span class="pill work-order-status status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
          <td class="button-row">
            <a class="btn small primary" href="{{ route('admin.work-orders.show', $order) }}">View</a>
            <form class="danger-inline" method="POST" action="{{ route('admin.work-orders.destroy', $order) }}" onsubmit="return confirm('Delete this work order request?')">
              @csrf
              @method('DELETE')
              <button class="btn small danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6">No work order requests match the current filters.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($orders->hasPages())
  <div style="margin-top:18px">{{ $orders->links() }}</div>
@endif
@endsection
