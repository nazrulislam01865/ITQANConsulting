<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this catalog viewer</label>
  <div class="field"><label>Viewer eyebrow</label><input name="settings[viewer_eyebrow]" value="{{ old('settings.viewer_eyebrow', $section->settings['viewer_eyebrow'] ?? 'Catalog Viewer') }}"></div>
  <div class="field"><label>Viewer title</label><input name="settings[viewer_title]" value="{{ old('settings.viewer_title', $section->settings['viewer_title'] ?? 'ITQAN Service Profile') }}"></div>
</div>
