@extends('admin.layouts.app')

@section('title', 'Work Order '.$order->reference_number)

@section('content')
<div class="page-head">
  <div>
    <span class="section-key">{{ $order->reference_number }}</span>
    <h2>Work order request.</h2>
    <p>Submitted on {{ $order->created_at?->format('d M Y, h:i A') }} for {{ $order->work_title }}.</p>
  </div>
  <div class="button-row">
    <a class="btn" href="{{ route('admin.work-orders.index') }}">Back to Work Orders</a>
    <form class="danger-inline" method="POST" action="{{ route('admin.work-orders.destroy', $order) }}" onsubmit="return confirm('Delete this work order request?')">
      @csrf
      @method('DELETE')
      <button class="btn danger" type="submit">Delete</button>
    </form>
  </div>
</div>

<div class="grid two">
  <div class="card light">
    <h3>Client</h3>
    <p><strong>Name:</strong> {{ $order->customer_name }}</p>
    <p><strong>Company:</strong> {{ $order->company_name ?: '—' }}</p>
    <p><strong>Email:</strong> <a href="mailto:{{ $order->email }}">{{ $order->email }}</a></p>
    <p><strong>Phone / WhatsApp:</strong> <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></p>
    <p><strong>Preferred contact:</strong> {{ ucfirst($order->preferred_contact_method) }}</p>
  </div>

  <div class="card light">
    <h3>Requested Work</h3>
    <p><strong>Work:</strong> {{ $order->work_title }}</p>
    <p><strong>Category:</strong> {{ $order->work_category ?: '—' }}</p>
    <p><strong>Budget:</strong> {{ $order->budget_range }}</p>
    <p><strong>Timeline:</strong> {{ $order->timeline }}</p>
    <p><strong>Current status:</strong> {{ $order->statusLabel() }}</p>
  </div>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Project Summary</h3>
  <p style="white-space:pre-line">{{ $order->project_summary }}</p>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Important Features or Requirements</h3>
  <p style="white-space:pre-line">{{ $order->requirements ?: 'No additional requirements were provided.' }}</p>
</div>

<form class="form-card" method="POST" action="{{ route('admin.work-orders.update', $order) }}" style="margin-top:18px">
  @csrf
  @method('PUT')
  <div class="form-grid">
    <div class="field">
      <label for="workOrderStatus">Request status</label>
      <select id="workOrderStatus" name="status" required>
        @foreach($statuses as $value => $label)
          <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field full">
      <label for="workOrderNotes">Internal notes</label>
      <textarea id="workOrderNotes" name="internal_notes" maxlength="5000" placeholder="Add follow-up notes, scope details, quotation information, or decisions.">{{ old('internal_notes', $order->internal_notes) }}</textarea>
      <div class="help">These notes are visible only inside the ITQAN admin panel.</div>
    </div>
  </div>
  <button class="btn primary" type="submit">Save Request Update</button>
</form>

<div class="card" style="margin-top:18px">
  <h3>Technical Information</h3>
  <p><strong>First viewed:</strong> {{ $order->viewed_at?->format('d M Y, h:i A') ?: '—' }}</p>
  <p><strong>IP:</strong> {{ $order->ip_address ?: '—' }}</p>
  <p><strong>User Agent:</strong> {{ $order->user_agent ?: '—' }}</p>
</div>
@endsection
