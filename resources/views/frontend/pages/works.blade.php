@extends('frontend.layouts.app')

@section('content')
<main id="works" class="page active">
  @include('frontend.partials.page-hero', ['hero' => $page['hero']])

  <section class="section soft-white">
    <div class="container">
      <div class="tabs reveal" role="tablist" aria-label="Work filters">
        @foreach($collections['work_filters'] as $index => $filter)
          <button class="tab {{ $index === 0 ? 'active' : '' }}" data-filter="{{ $filter['value'] }}" type="button" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $filter['label'] }}</button>
        @endforeach
      </div>
      <div class="card-grid two" id="workGrid">
        @foreach($collections['works'] as $work)
          @include('frontend.partials.work-card', ['work' => $work, 'showTags' => true])
        @endforeach
      </div>
    </div>
  </section>
</main>
@endsection
