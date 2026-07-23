@extends('frontend.layouts.app')

@section('content')
<main id="works" class="page active">
  @include('frontend.partials.page-hero', ['hero' => $page['hero']])

  <section class="section soft-white">
    <div class="container">
      @if(session('work_order_status'))
        <div class="work-order-success reveal visible" role="status">
          <strong>{{ session('work_order_status') }}</strong>
          @if(session('work_order_reference'))
            <span>Reference: {{ session('work_order_reference') }}</span>
          @endif
        </div>
      @endif

      <div class="tabs reveal" role="tablist" aria-label="Work filters">
        @foreach($collections['work_filters'] as $index => $filter)
          <button class="tab {{ $index === 0 ? 'active' : '' }}" data-filter="{{ $filter['value'] }}" type="button" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $filter['label'] }}</button>
        @endforeach
      </div>
      <div class="card-grid two" id="workGrid">
        @foreach($collections['works'] as $work)
          @include('frontend.partials.work-card', ['work' => $work, 'showTags' => true, 'showOrder' => true])
        @endforeach
      </div>
    </div>
  </section>

  @include('frontend.partials.work-order-modal')
</main>
@endsection
