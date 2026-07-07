@php
  $favicon = \App\Support\Favicon::current();
@endphp

@if($favicon)
  <link rel="icon" type="{{ $favicon['type'] }}" href="{{ asset($favicon['path']) }}?v={{ $favicon['version'] }}">
  <link rel="shortcut icon" type="{{ $favicon['type'] }}" href="{{ asset($favicon['path']) }}?v={{ $favicon['version'] }}">
@else
  {{-- Prevent browsers from automatically reusing/requesting an old favicon. --}}
  <link rel="icon" href="data:,">
  <link rel="shortcut icon" href="data:,">
@endif
