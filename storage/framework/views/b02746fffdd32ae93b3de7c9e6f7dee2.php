<div class="hero-admin-shell">
  <section class="hero-admin-panel hero-admin-main-panel">
    <div class="hero-admin-panel-head">
      <div>
        <span class="section-key">Hero content</span>
        <h3>Main message</h3>
      </div>
      <label class="hero-toggle-row">
        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $section->is_active)): echo 'checked'; endif; ?>>
        <span>Show hero section</span>
      </label>
    </div>

    <div class="hero-visual-lock-note" role="note">
      <strong>Banner visual is fixed for the whole website.</strong>
      <span>The template-style dark background, moving connected particles, and mouse-following glow are applied automatically to every page banner. Banner image upload is intentionally unavailable.</span>
    </div>

    <div class="form-grid hero-main-grid">
      <div class="field">
        <label>Top label</label>
        <input name="label" value="<?php echo e(old('label', $section->label)); ?>" placeholder="ITQAN Consulting">
      </div>

      <div class="field full">
        <label>Large title</label>
        <textarea class="hero-title-input" name="title"><?php echo e(old('title', $section->title)); ?></textarea>
        <div class="help compact-help">Allowed formatting: span, br, strong, em.</div>
      </div>

      <div class="field full">
        <label>Description</label>
        <textarea class="short" name="description"><?php echo e(old('description', $section->description)); ?></textarea>
      </div>

      <div class="field">
        <label>Social label</label>
        <input name="settings[social_label]" value="<?php echo e(old('settings.social_label', $section->settings['social_label'] ?? 'Connect with ITQAN')); ?>" placeholder="Connect with ITQAN">
      </div>
    </div>
  </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/admin/home/sections/home-hero.blade.php ENDPATH**/ ?>