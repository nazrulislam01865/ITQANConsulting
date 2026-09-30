<?php
  $routeName = $cta['button']['route'] ?? null;
  $primaryHref = $cta['button']['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) : '#');
  $primaryExternal = \Illuminate\Support\Str::startsWith($primaryHref, ['http://', 'https://']);
  $vcard = $cta['vcard'] ?? [];
?>

<section class="section home-digital-contact-wrap" aria-labelledby="digitalContactTitle">
  <div class="container">
    <div class="digital-contact-card reveal">
      <div class="digital-contact-inner">
        <div class="digital-contact-copy">
          <div class="digital-contact-eyebrow">
            <span class="digital-contact-eyebrow-dot" aria-hidden="true"></span>
            <?php echo e($cta['eyebrow']); ?>

          </div>

          <h2 id="digitalContactTitle"><?php echo e($cta['title']); ?></h2>
          <p class="digital-contact-text"><?php echo e($cta['text']); ?></p>

          <div class="digital-contact-actions">
            <a
              class="digital-contact-button primary"
              href="<?php echo e($primaryHref); ?>"
              <?php if($primaryExternal): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
            ><?php echo e($cta['button']['text']); ?></a>

            <button class="digital-contact-button" id="saveDigitalContactButton" type="button">
              <?php echo e($cta['save_button_text']); ?>

            </button>
          </div>
        </div>

        <div class="digital-contact-qr-column">
          <div class="digital-contact-qr-frame" aria-label="<?php echo e($cta['qr_alt']); ?>">
            <img src="<?php echo e($cta['qr_image_url']); ?>" alt="<?php echo e($cta['qr_alt']); ?>" loading="lazy">
          </div>
          <p class="digital-contact-qr-caption"><?php echo e($cta['qr_caption']); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
  (() => {
    const saveButton = document.getElementById('saveDigitalContactButton');
    if (!saveButton) return;

    const contact = <?php echo json_encode($vcard, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512) ?>;
    const requestedFileName = <?php echo json_encode($cta['contact_file_name'] ?? 'ITQAN-Consulting-Digital-Card.vcf', 15, 512) ?>;

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

      const safeBaseName = String(requestedFileName || 'ITQAN-Consulting-Digital-Card.vcf')
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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/frontend/partials/home-digital-contact.blade.php ENDPATH**/ ?>