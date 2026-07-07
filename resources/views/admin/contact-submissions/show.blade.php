@extends('admin.layouts.app')

@section('title', 'Contact Response')

@section('content')
<div class="page-head">
  <div>
    <h2>Contact response.</h2>
    <p>Submitted on {{ $submission->created_at?->format('d M Y, h:i A') }}.</p>
  </div>
  <div class="button-row">
    <a class="btn" href="{{ route('admin.contact-submissions.index') }}">Back to Responses</a>
    <form class="danger-inline" method="POST" action="{{ route('admin.contact-submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this contact response?')">
      @csrf
      @method('DELETE')
      <button class="btn danger" type="submit">Delete</button>
    </form>
  </div>
</div>

<div class="grid two">
  <div class="card light">
    <h3>Sender</h3>
    <p><strong>Name:</strong> {{ $submission->name }}</p>
    <p><strong>Company:</strong> {{ $submission->company_name ?: '—' }}</p>
    <p><strong>Email:</strong> <a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></p>
    <p><strong>Phone / WhatsApp:</strong> {{ $submission->phone ?: '—' }}</p>
  </div>

  <div class="card light">
    <h3>Request Details</h3>
    <p><strong>Need:</strong> {{ $submission->need ?: '—' }}</p>
    <p><strong>Areas:</strong> {{ is_array($submission->areas) && count($submission->areas) ? implode(', ', $submission->areas) : '—' }}</p>
    <p><strong>Budget:</strong> {{ $submission->budget_range ?: '—' }}</p>
    <p><strong>Preferred Method:</strong> {{ $submission->preferred_contact_method ?: '—' }}</p>
  </div>
</div>

<div class="card light" style="margin-top:18px">
  <h3>Message</h3>
  <p style="white-space:pre-line">{{ $submission->message }}</p>
</div>

<div class="card" style="margin-top:18px">
  <h3>Technical Info</h3>
  <p><strong>IP:</strong> {{ $submission->ip_address ?: '—' }}</p>
  <p><strong>User Agent:</strong> {{ $submission->user_agent ?: '—' }}</p>
</div>
@endsection
