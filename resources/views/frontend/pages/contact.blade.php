@extends('frontend.layouts.app')

@section('content')
@php
  $cleanContactOption = static function ($value): string {
      $value = str_replace(['<br>', '<br/>', '<br />'], ' ', (string) $value);
      return trim((string) preg_replace('/\s+/', ' ', strip_tags($value)));
  };

  $normalizeContactOption = static function ($value) use ($cleanContactOption): string {
      return strtolower($cleanContactOption($value));
  };

  $serviceSelections = [
      'consulting-business-clarity' => [
          'need' => 'Business process',
          'areas' => ['Business process'],
      ],
      'project-product-support' => [
          'need' => 'Project management',
          'areas' => ['Project management'],
      ],
      'software-web-development' => [
          'need' => 'Software development',
          'areas' => ['Software development', 'Website'],
      ],
      'erp-automation' => [
          'need' => 'ERP',
          'areas' => ['ERP', 'Automation'],
      ],
  ];

  $requestedService = (string) request()->query('service', '');
  $selectedNeed = (string) request()->query('need', '');
  $selectedAreas = request()->query('areas', request()->query('area', []));

  if (is_string($selectedAreas)) {
      $selectedAreas = array_filter(array_map('trim', explode(',', $selectedAreas)));
  }

  if (! is_array($selectedAreas)) {
      $selectedAreas = [];
  }

  if (isset($serviceSelections[$requestedService])) {
      $selectedNeed = $serviceSelections[$requestedService]['need'];
      $selectedAreas = $serviceSelections[$requestedService]['areas'];
  }

  $contactNeeds = $collections['contact_options']['needs'] ?? [];
  $contactAreas = $collections['contact_options']['areas'] ?? [];
  $contactMethods = $collections['contact_options']['methods'] ?? [];

  $needKeys = array_map($normalizeContactOption, $contactNeeds);
  if ($selectedNeed !== '' && ! in_array($normalizeContactOption($selectedNeed), $needKeys, true)) {
      $contactNeeds[] = $selectedNeed;
  }

  $areaKeys = array_map($normalizeContactOption, $contactAreas);
  foreach ($selectedAreas as $selectedArea) {
      $selectedAreaKey = $normalizeContactOption($selectedArea);
      if ($selectedAreaKey !== '' && ! in_array($selectedAreaKey, $areaKeys, true)) {
          $contactAreas[] = $selectedArea;
          $areaKeys[] = $selectedAreaKey;
      }
  }

  if (old('need') !== null) {
      $selectedNeed = (string) old('need');
  }

  if (old('areas') !== null) {
      $selectedAreas = is_array(old('areas')) ? old('areas') : [];
  }

  $selectedNeedKey = $normalizeContactOption($selectedNeed);
  $selectedAreaKeys = array_values(array_filter(array_map($normalizeContactOption, $selectedAreas)));
@endphp

<main id="contact" class="page active">
  @include('frontend.partials.plain-hero', ['hero' => $page['hero']])

  <section class="section soft-white" id="contact-form">
    <div class="container form-wrap">
      <form class="form-card reveal" method="post" action="{{ route('contact.submit') }}" aria-label="Contact form">
        @csrf
        @if(session('status'))
          <div class="form-status success">{{ session('status') }}</div>
        @endif
        @if($errors->any())
          <div class="form-status error">
            <strong>Please correct the highlighted fields.</strong>
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="form-grid">
          <div class="field"><label for="name">Name</label><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" required></div>
          <div class="field"><label for="company">Company Name</label><input id="company" name="company" type="text" value="{{ old('company') }}" placeholder="Company name"></div>
          <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@email.com" required></div>
          <div class="field"><label for="phone">Phone / WhatsApp</label><input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="Phone number"></div>
          <div class="field full">
            <label for="need">What do you need help with?</label>
            <select id="need" name="need">
              @foreach($contactNeeds as $option)
                @php $optionText = $cleanContactOption($option); @endphp
                <option value="{{ $optionText }}" @selected($selectedNeedKey !== '' && $normalizeContactOption($optionText) === $selectedNeedKey)>{{ $optionText }}</option>
              @endforeach
            </select>
          </div>
          <div class="field full">
            <label>Areas</label>
            <div class="checks">
              @foreach($contactAreas as $index => $area)
                @php
                  $areaText = $cleanContactOption($area);
                  $areaSelected = in_array($normalizeContactOption($areaText), $selectedAreaKeys, true);
                @endphp
                <label class="check {{ $areaSelected ? 'selected' : '' }}">
                  <span>{!! $area !!}</span>
                  <input type="checkbox" name="areas[]" value="{{ $areaText }}" @checked($areaSelected)>
                </label>
              @endforeach
            </div>
          </div>
          <div class="field"><label for="budget">Budget range, optional</label><input id="budget" name="budget" type="text" value="{{ old('budget') }}" placeholder="Example: 1–3 lakh BDT"></div>
          <div class="field">
            <label for="method">Preferred contact method</label>
            <select id="method" name="method">
              @foreach($contactMethods as $method)<option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>@endforeach
            </select>
          </div>
          <div class="field full"><label for="message">Short message</label><textarea id="message" name="message" placeholder="Write what is happening in your business..." required>{{ old('message') }}</textarea></div>
        </div>
        <div class="button-row" style="margin-top:18px"><button class="btn dark" type="submit">Send Message</button></div>
      </form>
      <aside class="side-note reveal">
        <div class="label">{{ $page['side_note']['label'] }}</div>
        <h3>{{ $page['side_note']['title'] }}</h3>
        <p class="lead">{{ $page['side_note']['text'] }}</p>
        <ol>
          @foreach($page['side_note']['steps'] as $step)<li>{{ $step }}</li>@endforeach
        </ol>
        <div class="social-row">@include('frontend.partials.social-links', ['includeEmail' => true])</div>
      </aside>
    </div>
  </section>

  @include('frontend.partials.cta', ['cta' => $page['cta']])
</main>
@endsection
