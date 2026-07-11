@extends('frontend.layouts.app')

@section('content')
<main id="home" class="page active">
  <section class="hero home-hero itqan-banner" data-itqan-banner>
    @include('frontend.partials.hero-space')
    <div class="container hero-grid">
      <div class="hero-copy reveal">
        <div class="label">{{ $page['hero']['label'] }}</div>
        <h1>{!! $page['hero']['title'] !!}</h1>
        <p>{{ $page['hero']['description'] }}</p>
        <div class="short-lines" aria-label="Hero message">
          @foreach($page['hero']['chips'] as $chip)<span>{{ $chip }}</span>@endforeach
        </div>
        <div class="button-row">
          @foreach($page['hero']['buttons'] as $button)
            @include('frontend.partials.button', ['button' => $button, 'class' => $button['class'] ?? ''])
          @endforeach
        </div>
        <div class="social-row with-label">
          <span>{{ $page['hero']['social_label'] }}</span>
          <div class="social-row">@include('frontend.partials.social-links')</div>
        </div>
      </div>
    </div>
    <section class="code-ticker-wrap" aria-label="Code style message slider">
      <div class="code-ticker">
        <div class="code-track">
          @foreach(array_merge($page['hero']['ticker'], $page['hero']['ticker']) as $item)
            <span class="code-item">{!! $item !!}</span>
          @endforeach
        </div>
      </div>
    </section>
  </section>

  @php
    $clarityItems = collect($page['problems']['items'] ?? [])->values();
    $firstClarityItem = $clarityItems->first() ?? [
      'problem' => 'Our process is undocumented',
      'summary' => 'Important work depends on people remembering what to do, when to do it, and who should approve it.',
      'services' => ['Process interviews', 'Workflow mapping', 'Roles and responsibility', 'Practical documentation'],
    ];
  @endphp

  <section class="section template-clarity-section template-grid-bg" id="clarity-check" data-clarity-check>
    <div class="container">
      <div class="template-section-intro reveal">
        <span class="template-section-label">{{ $page['problems']['label'] }}</span>
        <h2>{{ $page['problems']['title'] }}</h2>
        <p>{{ $page['problems']['lead'] }}</p>
      </div>

      <div class="template-diagnostic-wrap reveal">
        <div class="template-problem-choices" role="list" aria-label="Business clarity problems">
          @foreach($clarityItems as $item)
            <button
              class="template-problem-option {{ $loop->first ? 'active' : '' }}"
              type="button"
              data-clarity-index="{{ $loop->index }}"
              aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
            >
              <span class="template-option-icon">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <span>{{ $item['problem'] }}</span>
            </button>
          @endforeach
        </div>

        <div class="template-diagnostic-result" aria-live="polite">
          <span class="template-result-eyebrow">A practical place to begin</span>
          <h3 data-clarity-title>{{ $firstClarityItem['problem'] }}</h3>
          <p data-clarity-summary>{{ $firstClarityItem['summary'] ?? '' }}</p>
          <div class="template-result-list" data-clarity-services>
            @foreach(($firstClarityItem['services'] ?? []) as $service)
              <div class="template-result-item">{{ $service }}</div>
            @endforeach
          </div>
          <a class="btn blue" href="{{ route('contact') }}">Discuss this problem <span aria-hidden="true">→</span></a>
        </div>
      </div>
    </div>

    <script type="application/json" data-clarity-data>{!! json_encode($clarityItems->all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
  </section>

  <section class="section template-founder-section" id="founder-message">
    <div class="container template-founder-grid">
      <div class="template-founder-visual reveal" aria-label="Founder portrait">
        <div class="template-founder-avatar {{ ! empty($page['founder']['image_url']) ? 'has-image' : '' }}">
          @if(! empty($page['founder']['image_url']))
            <img src="{{ $page['founder']['image_url'] }}" alt="{{ $page['founder']['name'] }}" loading="lazy">
          @else
            <svg viewBox="0 0 120 120" aria-hidden="true">
              <circle cx="60" cy="38" r="24" fill="none" stroke="currentColor" stroke-width="2"/>
              <path d="M25 108c4-25 18-39 35-39s31 14 35 39" fill="none" stroke="currentColor" stroke-width="2"/>
              <path d="M40 84h40" stroke="currentColor" stroke-width="2"/>
            </svg>
          @endif
        </div>
        <div class="template-founder-signature">
          <strong>{{ $page['founder']['name'] }}</strong>
          <span>{{ $page['founder']['role'] }}</span>
        </div>
      </div>

      <div class="template-founder-copy reveal">
        <span class="template-section-label">{{ $page['founder']['label'] }}</span>
        <blockquote><span class="template-quote-mark">“</span>{{ $page['founder']['title'] }}</blockquote>
        @foreach($page['founder']['paragraphs'] as $paragraph)
          <p>{{ $paragraph }}</p>
        @endforeach
      </div>
    </div>
  </section>

  @php
    $whyItems = collect($page['who']['cards'] ?? [])->values();
    $serviceItems = collect($page['services_preview']['items'] ?? [])->values();
  @endphp

  <section class="section template-why-section" id="why-itqan" data-why-itqan>
    <div class="container">
      <div class="template-why-intro reveal">
        <span class="template-section-label">{{ $page['who']['label'] }}</span>
        <h2>{{ $page['who']['title'] }}</h2>
        <p>{{ $page['who']['lead'] }}</p>
      </div>

      <div class="template-why-layout">
        <div class="template-why-sticky reveal">
          <div class="template-transformation-stage" data-why-stage>
            <div class="template-stage-grid" aria-hidden="true"></div>
            <div class="template-stage-label" data-why-stage-label>Problem / scattered work</div>

            <div class="template-messy-cloud" aria-hidden="true">
              <span style="--x:8%;--y:10%;--r:-8deg">Calls</span>
              <span style="--x:56%;--y:4%;--r:5deg">Excel</span>
              <span style="--x:28%;--y:29%;--r:2deg">Memory</span>
              <span style="--x:68%;--y:34%;--r:-6deg">Messages</span>
              <span style="--x:9%;--y:58%;--r:7deg">Approvals</span>
              <span style="--x:45%;--y:67%;--r:-3deg">Reports</span>
              <span style="--x:72%;--y:78%;--r:8deg">Follow-up</span>
            </div>

            <div class="template-clear-flow" aria-hidden="true">
              <div class="template-flow-node"><span>01</span><strong>Understand the real work</strong></div>
              <div class="template-flow-node"><span>02</span><strong>Map roles, data, and decisions</strong></div>
              <div class="template-flow-node"><span>03</span><strong>Define the right system or process</strong></div>
              <div class="template-flow-node"><span>04</span><strong>Support practical execution</strong></div>
            </div>
          </div>
        </div>

        <div class="template-why-stories">
          @foreach($whyItems as $item)
            <article
              class="template-story-card {{ $loop->first ? 'active' : '' }}"
              tabindex="0"
              data-why-story
              data-stage-label="{{ $item['stage_label'] ?? $item['title'] }}"
            >
              <span class="template-story-index">{{ $item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['text'] }}</p>
              <div class="template-story-response">
                <small>ITQAN response</small>
                <span>{{ $item['response'] ?? 'We help organize the work into a clearer and more practical system.' }}</span>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="section template-services-section template-grid-bg" id="home-services-preview" data-services-explorer>
    <div class="container">
      <div class="template-services-intro reveal">
        <span class="template-section-label">{{ $page['services_preview']['label'] }}</span>
        <h2>{{ $page['services_preview']['title'] }}</h2>
        <p>{{ $page['services_preview']['lead'] }}</p>
      </div>

      <div class="template-services-layout">
        <div class="template-service-menu reveal" role="tablist" aria-label="ITQAN services" data-service-menu>
          @foreach($serviceItems as $item)
            @php($serviceNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT))
            <button
              type="button"
              id="home-service-tab-{{ $loop->iteration }}"
              class="template-service-tab {{ $loop->first ? 'active' : '' }}"
              role="tab"
              data-service-tab
              data-service-target="home-service-{{ $loop->iteration }}"
              aria-controls="home-service-{{ $loop->iteration }}"
              aria-selected="{{ $loop->first ? 'true' : 'false' }}"
              tabindex="{{ $loop->first ? '0' : '-1' }}"
            >
              <span>{{ $serviceNumber }}</span>
              <strong>{{ $item['title'] }}</strong>
            </button>
          @endforeach
        </div>

        <div class="template-service-panels" data-service-panels>
          @foreach($serviceItems as $item)
            @php($serviceNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT))
            <article
              class="template-service-panel reveal {{ $loop->first ? 'active' : '' }}"
              id="home-service-{{ $loop->iteration }}"
              role="tabpanel"
              tabindex="-1"
              aria-labelledby="home-service-tab-{{ $loop->iteration }}"
              data-service-panel
              data-number="{{ $serviceNumber }}"
            >
              <div class="template-service-graphic" aria-hidden="true">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
                <i class="dot"></i>
                <i class="dot"></i>
                <i class="dot"></i>
              </div>
              <h3>{{ $item['title'] }}</h3>
              <p>{{ $item['text'] }}</p>
              <div class="template-service-meta">
                <div class="template-meta-card">
                  <small>Common problem</small>
                  <span>{{ $item['common_problem'] ?? 'The work lacks a clear structure, owner, or practical next step.' }}</span>
                </div>
                <div class="template-meta-card">
                  <small>Possible deliverables</small>
                  <span>{{ $item['deliverables'] ?? 'A clear scope, practical recommendations, and an actionable delivery plan.' }}</span>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </div>

      <div class="template-services-action reveal">
        @include('frontend.partials.button', ['button' => $page['services_preview']['button'], 'class' => 'blue'])
      </div>
    </div>
  </section>

  <section class="section template-process-section" id="our-way-of-working" data-process-section>
    <div class="container">
      <div class="template-process-intro reveal">
        <span class="template-section-label">{{ $page['working']['label'] }}</span>
        <h2>{!! $page['working']['title'] !!}</h2>
        <p>{{ $page['working']['intro'] }}</p>
      </div>

      <div class="template-process-shell" data-process-shell>
        <svg class="template-process-line" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true">
          <path class="base" d="M20,60 C180,10 250,90 420,45 S700,80 980,40" fill="none" stroke-width="3" pathLength="1" />
          <path class="progress" data-process-progress d="M20,60 C180,10 250,90 420,45 S700,80 980,40" fill="none" stroke-width="4" stroke-linecap="round" pathLength="1" />
        </svg>

        <div class="template-process-grid">
          @foreach($page['working']['items'] as $item)
            @php($stepNumber = str_pad((string) ($item['num'] ?: $loop->iteration), 2, '0', STR_PAD_LEFT))
            <article class="template-process-step" data-process-step>
              <div class="template-process-node">{{ $stepNumber }}</div>
              <div class="template-process-copy">
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['text'] }}</p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  @if(data_get($page, 'testimonials.is_active', true))
  <section class="section template-testimonials-section template-grid-bg" id="client-words" data-testimonial-slider>
    <div class="container">
      <div class="template-testimonials-intro reveal">
        <span class="template-section-label">{{ $page['testimonials']['label'] }}</span>
        <h2>{{ $page['testimonials']['title'] }}</h2>
        <p>{{ $page['testimonials']['lead'] ?? 'A calm slider with enough time to read. Autoplay pauses on hover, focus, and touch.' }}</p>
      </div>

      <div class="template-testimonial-shell reveal" data-testimonial-shell aria-roledescription="carousel" aria-label="Client words">
        <div class="template-testimonial-track" data-testimonial-track aria-live="polite">
          @foreach($collections['testimonials'] as $testimonial)
            <div class="template-testimonial-slide" data-testimonial-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
              <article class="template-testimonial-card">
                <div class="template-quote-symbol" aria-hidden="true">“</div>
                <h3>{{ $testimonial['title'] }}</h3>
                <blockquote>{{ $testimonial['text'] }}</blockquote>
                <div class="template-client-row">
                  <div class="template-client-info">
                    <strong>{{ $testimonial['author'] }}</strong>
                    <span>{{ $testimonial['role'] }}</span>
                  </div>
                  @if(! empty($testimonial['project']))
                    <span class="template-project-pill">{{ $testimonial['project'] }}</span>
                  @endif
                </div>
              </article>
            </div>
          @endforeach
        </div>

        @if(count($collections['testimonials']) > 1)
          <div class="template-slider-controls">
            <button class="template-slider-btn" type="button" data-testimonial-prev aria-label="Previous testimonial">←</button>
            <div class="template-slider-dots" data-testimonial-dots aria-label="Choose testimonial"></div>
            <button class="template-slider-btn" type="button" data-testimonial-next aria-label="Next testimonial">→</button>
          </div>
          <div class="template-slider-progress" aria-hidden="true"><span data-testimonial-progress></span></div>
        @endif
      </div>
    </div>
  </section>
  @endif

  <section class="section soft-white">
    <div class="container">
      @include('frontend.partials.section-head', ['label' => $page['works_preview']['label'], 'title' => $page['works_preview']['title'], 'sectionButton' => array_merge($page['works_preview']['button'], ['class' => 'dark'])])
      <div class="card-grid two">
        @foreach(($collections['home_featured_works'] ?? array_slice($collections['works'] ?? [], 0, 4)) as $work)
          @include('frontend.partials.work-card', ['work' => $work, 'pill' => $work['preview_pill'] ?? $work['pill'] ?? '', 'description' => $work['preview_description'] ?? $work['description'] ?? ''])
        @endforeach
      </div>
    </div>
  </section>

  @if(data_get($page, 'values.is_active', true))
  <section class="section template-values-section" id="how-we-think" data-values-section>
    <div class="container template-values-layout">
      <div class="template-values-sticky reveal">
        <span class="template-section-label">{{ $page['values']['label'] ?? 'How We Think' }}</span>
        <h2>{{ $page['values']['title'] ?? 'Principles that shape the work.' }}</h2>
        @if(! empty($page['values']['lead']))
          <p>{{ $page['values']['lead'] }}</p>
        @endif
        <div class="template-value-number" data-value-number aria-hidden="true">
          {{ $page['values']['items'][0]['num'] ?? '01' }}
        </div>
      </div>

      <div class="template-value-cards">
        @foreach(($page['values']['items'] ?? []) as $item)
          <article
            class="template-value-card reveal {{ $loop->first ? 'active' : '' }}"
            tabindex="0"
            data-value-card
            data-value="{{ $item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
          >
            <span class="template-value-mini">{{ $item['mini'] ?? (($item['num'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)) . ' / principle') }}</span>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['text'] }}</p>
            @if(! empty($item['example']))
              <div class="template-value-example">{{ $item['example'] }}</div>
            @endif
          </article>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  @if(data_get($page, 'cta.is_active', true))
    @include('frontend.partials.home-digital-contact', ['cta' => $page['cta']])
  @endif
</main>
@endsection
