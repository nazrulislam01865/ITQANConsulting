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

  $form = $page['form'] ?? [
      'is_active' => true,
      'label' => 'Start a conversation',
      'title' => 'Send the messy version. We will help organize it.',
      'intro' => 'No pressure. No hard selling. Share what is happening and we will help make the next step clearer.',
      'steps' => [],
      'submit_text' => 'Send the messy version',
      'success_title' => 'Thank you. The first step is clear.',
      'success_text' => 'Your message has been received. ITQAN will contact you soon.',
  ];

  $stepDefaults = [
      ['title' => 'What feels unclear right now?', 'text' => 'Select one or more. It does not need to be perfectly described.'],
      ['title' => 'What kind of support may help?', 'text' => 'Choose what seems closest. ITQAN can help refine the scope later.'],
      ['title' => 'Share the basic details.', 'text' => 'This helps us respond in a useful way.'],
      ['title' => 'Describe the situation in your own words.', 'text' => 'It is okay if the information is incomplete.'],
  ];
  $wizardSteps = array_replace($stepDefaults, $form['steps'] ?? []);

  $problemOptions = $collections['contact_options']['problems'] ?? [
      'Business process',
      'Project delivery',
      'Software or website',
      'ERP or automation',
      'Reporting and data',
      'Team capability',
  ];
  $supportOptions = $collections['contact_options']['needs'] ?? [
      'Consulting first',
      'Plan and manage',
      'Design and build',
      'Review current system',
      'Train the team',
      'Not sure yet',
  ];
  $contactMethods = $collections['contact_options']['methods'] ?? ['WhatsApp', 'Email', 'Phone call', 'Online meeting'];
  $budgetOptions = $collections['contact_options']['budgets'] ?? [
      'Not decided yet',
      'Under BDT 100,000',
      'BDT 100,000 – 300,000',
      'BDT 300,000 – 800,000',
      'Above BDT 800,000',
  ];

  $problemOptions = array_values(array_filter(array_map($cleanContactOption, $problemOptions)));
  $supportOptions = array_values(array_filter(array_map($cleanContactOption, $supportOptions)));
  $contactMethods = array_values(array_filter(array_map($cleanContactOption, $contactMethods)));
  $budgetOptions = array_values(array_filter(array_map($cleanContactOption, $budgetOptions)));

  $serviceSelections = [
      'consulting-business-clarity' => 'Consulting first',
      'project-product-support' => 'Plan and manage',
      'software-web-development' => 'Design and build',
      'erp-automation' => 'Design and build',
      'training-coaching' => 'Train the team',
      'dedicated-team-support' => 'Plan and manage',
  ];

  $requestedService = (string) request()->query('service', '');
  $requestedNeed = (string) request()->query('need', '');
  $preselectedSupport = $serviceSelections[$requestedService] ?? $requestedNeed;

  $oldProblems = old('areas', []);
  $oldProblems = is_array($oldProblems) ? $oldProblems : [$oldProblems];
  $selectedProblemKeys = array_map($normalizeContactOption, $oldProblems);

  $oldSupport = old('support_types', []);
  $oldSupport = is_array($oldSupport) ? $oldSupport : [$oldSupport];
  if ($oldSupport === [] && $preselectedSupport !== '') {
      $oldSupport = [$preselectedSupport];
  }
  $selectedSupportKeys = array_map($normalizeContactOption, $oldSupport);

  if ($preselectedSupport !== '') {
      $preselectedKey = $normalizeContactOption($preselectedSupport);
      $supportKeys = array_map($normalizeContactOption, $supportOptions);
      if ($preselectedKey !== '' && ! in_array($preselectedKey, $supportKeys, true)) {
          $supportOptions[] = $cleanContactOption($preselectedSupport);
      }
  }

  $initialStep = 0;
  if ($errors->hasAny(['message', 'budget'])) {
      $initialStep = 3;
  } elseif ($errors->hasAny(['name', 'company', 'email', 'phone', 'method'])) {
      $initialStep = 2;
  } elseif ($errors->has('support_types')) {
      $initialStep = 1;
  }

  $formActive = (bool) ($form['is_active'] ?? true);
  $cta = $page['cta'] ?? [];
  $ctaActive = (bool) ($cta['is_active'] ?? true);
  $vcard = $cta['vcard'] ?? [];
  $phone = trim((string) ($vcard['phone'] ?? ''));
  $whatsapp = preg_replace('/\D+/', '', (string) ($vcard['whatsapp'] ?? $phone));
  $email = trim((string) ($vcard['email'] ?? ($site['email'] ?? '')));
  $routeName = $cta['button']['route'] ?? null;
  $primaryHref = $cta['button']['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) : null);
  $primaryExternal = $primaryHref && \Illuminate\Support\Str::startsWith($primaryHref, ['http://', 'https://']);
@endphp

<main id="contact" class="page active">
  @include('frontend.partials.plain-hero', ['hero' => $page['hero']])

  @if($formActive)
  <section class="section contact-experience-section" id="contact-form" aria-labelledby="contactExperienceTitle">
    <div class="container">
      <div class="contact-experience-intro reveal">
        <div class="label">{{ $form['label'] }}</div>
        <h2 id="contactExperienceTitle">{{ $form['title'] }}</h2>
        <p>{{ $form['intro'] }}</p>
      </div>

      <div class="contact-experience-grid {{ $ctaActive ? '' : 'single-column' }}">
        <form
          class="contact-wizard-card reveal"
          method="post"
          action="{{ route('contact.submit') }}"
          aria-label="Contact form"
          data-contact-wizard
          data-initial-step="{{ $initialStep }}"
        >
          @csrf

          <div class="contact-wizard-form" @if(session('status')) hidden @endif>
            @if($errors->any())
              <div class="contact-wizard-alert error" role="alert">
                <strong>Please correct the highlighted fields.</strong>
                <ul>
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="contact-wizard-top">
              <div class="contact-wizard-progress" aria-hidden="true"><span></span></div>
              <span class="contact-wizard-count" aria-live="polite">1 of 4</span>
            </div>

            <div class="contact-wizard-step" data-wizard-step="0">
              <h3>{{ $wizardSteps[0]['title'] }}</h3>
              <p>{{ $wizardSteps[0]['text'] }}</p>
              <div class="contact-choice-grid" role="group" aria-label="Business problems">
                @foreach($problemOptions as $option)
                  @php $optionKey = $normalizeContactOption($option); @endphp
                  <label class="contact-choice-chip {{ in_array($optionKey, $selectedProblemKeys, true) ? 'selected' : '' }}">
                    <input type="checkbox" name="areas[]" value="{{ $option }}" @checked(in_array($optionKey, $selectedProblemKeys, true))>
                    <span>{{ $option }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="contact-wizard-step" data-wizard-step="1" hidden>
              <h3>{{ $wizardSteps[1]['title'] }}</h3>
              <p>{{ $wizardSteps[1]['text'] }}</p>
              <div class="contact-choice-grid" role="group" aria-label="Support types">
                @foreach($supportOptions as $option)
                  @php $optionKey = $normalizeContactOption($option); @endphp
                  <label class="contact-choice-chip {{ in_array($optionKey, $selectedSupportKeys, true) ? 'selected' : '' }}">
                    <input type="checkbox" name="support_types[]" value="{{ $option }}" @checked(in_array($optionKey, $selectedSupportKeys, true))>
                    <span>{{ $option }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="contact-wizard-step" data-wizard-step="2" hidden>
              <h3>{{ $wizardSteps[2]['title'] }}</h3>
              <p>{{ $wizardSteps[2]['text'] }}</p>
              <div class="contact-wizard-fields">
                <div class="contact-wizard-field">
                  <label for="name">Name</label>
                  <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" required>
                </div>
                <div class="contact-wizard-field">
                  <label for="company">Company</label>
                  <input id="company" name="company" type="text" value="{{ old('company') }}" placeholder="Company or organization" autocomplete="organization">
                </div>
                <div class="contact-wizard-field">
                  <label for="email">Email</label>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="email" required>
                </div>
                <div class="contact-wizard-field">
                  <label for="phone">Phone / WhatsApp</label>
                  <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="Your number" autocomplete="tel">
                </div>
                <div class="contact-wizard-field full">
                  <label for="method">Preferred contact</label>
                  <select id="method" name="method">
                    @foreach($contactMethods as $method)
                      <option value="{{ $method }}" @selected(old('method') === $method)>{{ $method }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="contact-wizard-step" data-wizard-step="3" hidden>
              <h3>{{ $wizardSteps[3]['title'] }}</h3>
              <p>{{ $wizardSteps[3]['text'] }}</p>
              <div class="contact-wizard-fields">
                <div class="contact-wizard-field full">
                  <label for="message">What is happening, and what would you like to improve?</label>
                  <textarea id="message" name="message" placeholder="Tell us the messy version..." required>{{ old('message') }}</textarea>
                </div>
                <div class="contact-wizard-field full">
                  <label for="budget">Tentative budget range</label>
                  <select id="budget" name="budget">
                    @foreach($budgetOptions as $budget)
                      <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="contact-wizard-nav">
              <button class="contact-wizard-button secondary" type="button" data-wizard-back disabled>Back</button>
              <button
                class="contact-wizard-button primary"
                type="button"
                data-wizard-next
                data-continue-label="Continue"
                data-submit-label="Send message"
              >Continue <span aria-hidden="true">→</span></button>
            </div>

            <noscript><p class="contact-wizard-noscript">Please enable JavaScript to use the step-by-step form.</p></noscript>
          </div>

          <div class="contact-wizard-success {{ session('status') ? 'show' : '' }}" @if(!session('status')) hidden @endif role="status">
            <div class="contact-wizard-success-icon" aria-hidden="true">✓</div>
            <h3>{{ $form['success_title'] }}</h3>
            <p>{{ session('status') ?: $form['success_text'] }}</p>
            <a class="contact-wizard-button secondary" href="{{ route('contact') }}#contact-form">Send another message</a>
          </div>
        </form>

        @if($ctaActive)
          <aside class="contact-qr-card reveal" aria-labelledby="contactQrTitle">
            <div>
              <div class="contact-qr-label">{{ $cta['eyebrow'] ?? 'Save the contact' }}</div>
              <h3 id="contactQrTitle">{{ $cta['title'] ?? 'Keep ITQAN one scan away.' }}</h3>
              <p>{{ $cta['text'] ?? 'Scan the QR code to save the founder’s contact details directly to your phone.' }}</p>
            </div>

            <div class="contact-qr-frame" aria-label="{{ $cta['qr_alt'] ?? 'ITQAN contact QR code' }}">
              <img src="{{ $cta['qr_image_url'] }}" alt="{{ $cta['qr_alt'] ?? 'ITQAN contact QR code' }}" loading="lazy">
              <span class="contact-qr-scan-line" aria-hidden="true"></span>
            </div>

            @if(!empty($cta['qr_caption']))
              <p class="contact-qr-caption">{{ $cta['qr_caption'] }}</p>
            @endif

            <div class="contact-direct-links">
              @if($phone !== '')
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"><span>Call</span><strong>{{ $phone }}</strong></a>
              @endif
              @if($whatsapp !== '')
                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"><span>WhatsApp</span><strong>Open chat</strong></a>
              @endif
              @if($email !== '')
                <a href="mailto:{{ $email }}"><span>Email</span><strong>{{ $email }}</strong></a>
              @endif
            </div>

            <div class="contact-qr-actions">
              <button class="contact-qr-action" id="saveContactQrCardButton" type="button">{{ $cta['save_button_text'] ?? 'Save Contact' }}</button>
              @if($primaryHref)
                <a class="contact-qr-action primary" href="{{ $primaryHref }}" @if($primaryExternal) target="_blank" rel="noopener noreferrer" @endif>{{ $cta['button']['text'] ?? 'Start a Conversation' }}</a>
              @endif
            </div>
          </aside>
        @endif
      </div>
    </div>
  </section>
  @endif
</main>
@endsection

@push('scripts')
<script>
  (() => {
    const saveButton = document.getElementById('saveContactQrCardButton');
    if (!saveButton) return;

    const contact = @json($vcard, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    const requestedFileName = @json($cta['contact_file_name'] ?? 'md-aminul-islam-itqan-consulting.vcf');

    const escapeVCardValue = (value) => String(value ?? '')
      .replace(/\\/g, '\\\\')
      .replace(/\r?\n/g, '\\n')
      .replace(/;/g, '\\;')
      .replace(/,/g, '\\,');

    const addLine = (lines, key, value) => {
      if (String(value ?? '').trim() !== '') lines.push(`${key}:${escapeVCardValue(value)}`);
    };

    saveButton.addEventListener('click', () => {
      const lines = ['BEGIN:VCARD', 'VERSION:3.0'];
      lines.push(`N:${escapeVCardValue(contact.last_name)};${escapeVCardValue(contact.first_name)};;;${escapeVCardValue(contact.credentials)}`);
      addLine(lines, 'FN', contact.full_name);
      addLine(lines, 'ORG', contact.organization);
      addLine(lines, 'TITLE', contact.job_title);
      addLine(lines, 'TEL;TYPE=CELL,VOICE', contact.phone);
      addLine(lines, 'TEL;TYPE=WHATSAPP', contact.whatsapp);
      addLine(lines, 'EMAIL;TYPE=INTERNET,WORK', contact.email);
      addLine(lines, 'URL', contact.website);
      addLine(lines, 'NOTE', contact.note);
      lines.push('END:VCARD');

      const safeBaseName = String(requestedFileName || 'md-aminul-islam-itqan-consulting.vcf')
        .replace(/[^a-zA-Z0-9._-]+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
      const fileName = safeBaseName.toLowerCase().endsWith('.vcf') ? safeBaseName : `${safeBaseName}.vcf`;
      const blob = new Blob([lines.join('\r\n')], { type: 'text/vcard;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = fileName;
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.setTimeout(() => URL.revokeObjectURL(url), 1000);
    });
  })();
</script>
@endpush
