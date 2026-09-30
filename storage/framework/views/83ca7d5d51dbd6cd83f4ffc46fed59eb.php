<?php
  $routeName = $cta['button']['route'] ?? null;
  $primaryHref = $cta['button']['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) : '#');
  $primaryExternal = \Illuminate\Support\Str::startsWith($primaryHref, ['http://', 'https://']);
  $vcard = $cta['vcard'] ?? [];
?>

<section class="contact-digital-card-wrap" aria-labelledby="contactDigitalCardTitle">
  <div class="container">
    <div class="contact-digital-card reveal">
      <div class="contact-digital-copy">
        <div class="contact-digital-label"><?php echo e($cta['eyebrow']); ?></div>
        <h2 id="contactDigitalCardTitle"><?php echo e($cta['title']); ?></h2>
        <p><?php echo e($cta['text']); ?></p>

        <div class="contact-digital-actions">
          <a
            class="contact-digital-button primary"
            href="<?php echo e($primaryHref); ?>"
            <?php if($primaryExternal): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
          ><?php echo e($cta['button']['text']); ?></a>

          <button class="contact-digital-button" id="saveContactPageDigitalCardButton" type="button">
            <?php echo e($cta['save_button_text']); ?>

          </button>
        </div>
      </div>

      <div class="contact-digital-qr-area">
        <div class="contact-digital-qr-shell" aria-label="<?php echo e($cta['qr_alt']); ?>">
          <img src="<?php echo e($cta['qr_image_url']); ?>" alt="<?php echo e($cta['qr_alt']); ?>" loading="lazy">
        </div>
        <p class="contact-digital-qr-note"><?php echo e($cta['qr_caption']); ?></p>
      </div>
    </div>
  </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
  (() => {
    const saveButton = document.getElementById('saveContactPageDigitalCardButton');
    if (!saveButton) return;

    const contact = <?php echo json_encode($vcard, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512) ?>;
    const requestedFileName = <?php echo json_encode($cta['contact_file_name'] ?? 'md-aminul-islam-itqan-consulting.vcf', 15, 512) ?>;

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
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/contact-digital-contact.blade.php ENDPATH**/ ?>