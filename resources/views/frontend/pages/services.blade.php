@extends('frontend.layouts.app')

@section('content')
<main id="services" class="page active">
  @include('frontend.partials.page-hero', ['hero' => $page['hero']])

  <section class="section light" id="service-areas">
    <div class="container">
      <div class="service-card-grid">
        @foreach($collections['services'] as $service)
          <article class="service-card reveal">
            <span class="service-badge">{{ $service['badge'] }}</span>
            <h3>{{ $service['title'] }}</h3>
            <p class="service-intro">{{ $service['intro'] }}</p>
            <ul class="service-points">
              @foreach($service['points'] as $point)<li>{{ $point }}</li>@endforeach
            </ul>
            @php
              $contactServiceKeys = [
                  '01' => 'consulting-business-clarity',
                  '02' => 'project-product-support',
                  '03' => 'software-web-development',
                  '04' => 'erp-automation',
                  '05' => 'training-coaching',
                  '06' => 'dedicated-team-support',
              ];

              $serviceSelectionKey = $contactServiceKeys[$service['badge'] ?? '']
                  ?? \Illuminate\Support\Str::slug($service['title'] ?? 'service');

              if (!empty($service['button_url'])) {
                  $serviceButtonHref = $service['button_url'];
              } else {
                  $routeName = (!empty($service['button_route']) && \Illuminate\Support\Facades\Route::has($service['button_route']))
                      ? $service['button_route']
                      : 'contact';

                  $serviceButtonHref = $routeName === 'contact'
                      ? route('contact', ['service' => $serviceSelectionKey]) . '#contact-form'
                      : route($routeName);
              }
            @endphp
            <a class="btn" href="{{ $serviceButtonHref }}">{{ $service['button'] }}</a>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section dark-panel">
    <div class="container">
      <div class="section-head reveal"><div><h2 class="headline">{{ $page['faq_title'] }}</h2></div></div>
      <div class="service-faq-wrap reveal">
        @foreach($collections['service_faqs'] as $index => $faq)
          <div class="service-faq faq-item {{ $index === 0 ? 'open' : '' }}">
            <button class="faq-q" type="button">
              <span class="faq-no">{{ $index + 1 }}</span>
              <span><strong>{{ $faq['question'] }}</strong><span>{{ $faq['summary'] }}</span></span>
              <span class="faq-plus">+</span>
            </button>
            <div class="faq-a">
              @foreach($faq['answer'] as $paragraph)<p>{{ $paragraph }}</p>@endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @include('frontend.partials.cta', ['cta' => $page['cta']])
</main>
@endsection
