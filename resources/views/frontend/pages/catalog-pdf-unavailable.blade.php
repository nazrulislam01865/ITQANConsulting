@extends('frontend.layouts.app')

@section('content')
<main class="page active">
  <section class="section">
    <div class="container cta-box reveal visible">
      <h2>PDF package is not installed yet.</h2>
      <p>Run <strong>composer require barryvdh/laravel-dompdf</strong>, then clear cache and try Download PDF again.</p>
      <a href="{{ route('catalog') }}" class="btn primary">Back to Catalog</a>
    </div>
  </section>
</main>
@endsection
