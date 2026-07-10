@extends('admin.map.layout')
@section('title','Map Preview')
@section('content')
<div class="card">
    <h2 style="margin-top:0">Guest Pathway Preview</h2>
    <p class="small">These links open the same mobile-friendly guest map and display a pathway calculated from the saved database edges.</p>
    <a class="btn" href="{{ route('external-guest-map.index') }}?from=reception&to=pool" target="_blank">Reception → Family Pool</a>
    <a class="btn secondary" href="{{ route('external-guest-map.index') }}?from=parking&to=restaurant" target="_blank">Parking → Restaurant</a>
    <a class="btn secondary" href="{{ route('external-guest-map.index') }}?from=villas&to=cafe" target="_blank">Garden Villas → Lake Café</a>
</div>
@endsection
