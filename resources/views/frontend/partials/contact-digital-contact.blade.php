@php
  $routeName = $cta['button']['route'] ?? null;
  $primaryHref = $cta['button']['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) : '#');
  $primaryExternal = \Illuminate\Support\Str::startsWith($primaryHref, ['http://', 'https://']);
  $vcard = $cta['vcard'] ?? [];
@endphp

<section class="contact-digital-card-wrap" aria-labelledby="contactDigitalCardTitle">
  <div class="container">
    <div class="contact-digital-card reveal">
      <div class="contact-digital-copy">
        <div class="contact-digital-label">{{ $cta['eyebrow'] }}</div>
        <h2 id="contactDigitalCardTitle">{{ $cta['title'] }}</h2>
        <p>{{ $cta['text'] }}</p>

        <div class="contact-digital-actions">
          <a
            class="contact-digital-button primary"
            href="{{ $primaryHref }}"
            @if($primaryExternal) target="_blank" rel="noopener noreferrer" @endif
          >{{ $cta['button']['text'] }}</a>

          <button class="contact-digital-button" id="saveContactPageDigitalCardButton" type="button">
            {{ $cta['save_button_text'] }}
          </button>
        </div>
      </div>

      <div class="contact-digital-qr-area">
        <div class="contact-digital-qr-shell" aria-label="{{ $cta['qr_alt'] }}">
          <img src="{{ $cta['qr_image_url'] }}" alt="{{ $cta['qr_alt'] }}" loading="lazy">
        </div>
        <p class="contact-digital-qr-note">{{ $cta['qr_caption'] }}</p>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
  (() => {
    const saveButton = document.getElementById('saveContactPageDigitalCardButton');
    if (!saveButton) return;

    const contact = @json($vcard, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    const requestedFileName = @json($cta['contact_file_name'] ?? 'md-aminul-islam-itqan-consulting.vcf');

    const escapeVCardValue = (value) => String(value ?? '')
      .replace(/\\/g, '\\\\')
      .replace(/\r?\n/g, '\\n')
      .replace(/;/g, '\\;')
      .replace(/,/g, '\\,');

    const addLine = (lines, key, value) => {
      if (String(value ?? '').trim() !== '') {
        lines.push(`${key}:${escapeVCardValue(value)}`);
      }
    };

    saveButton.addEventListener('click', () => {
      const lines = ['BEGIN:VCARD', 'VERSION:3.0'];
      const familyName = escapeVCardValue(contact.last_name);
      const givenName = escapeVCardValue(contact.first_name);
      const credentials = escapeVCardValue(contact.credentials);

      lines.push(`N:${familyName};${givenName};;;${credentials}`);
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
