<div class="form-grid">
  <div class="field full">
    <label>Founder portrait</label>
    <input type="file" name="founder_image" accept="image/jpeg,image/png,image/webp">
    <small>The uploaded image is shown only inside the portrait frame, matching the template.</small>
    @if(! empty($section->settings['image_path']))
      <div class="image-preview"><img src="{{ asset('storage/' . $section->settings['image_path']) }}" alt="Founder section image preview"></div>
    @endif
  </div>
  <div class="field"><label>Section label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field full"><label>Founder quote / headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>Message paragraphs</label><textarea name="description" rows="8">{{ old('description', $section->description) }}</textarea></div>
  <div class="field"><label>Founder name</label><input name="settings[name]" value="{{ old('settings.name', $section->settings['name'] ?? '') }}"></div>
  <div class="field"><label>Founder role</label><input name="settings[role]" value="{{ old('settings.role', $section->settings['role'] ?? '') }}"></div>
  <div class="field"><label>Digital resume link text</label><input name="button_text" value="{{ old('button_text', $section->button_text ?: 'View my digital resume') }}" maxlength="80"></div>
  <div class="field"><label>Digital resume route</label><input name="button_route" value="{{ old('button_route', $section->button_route ?: 'starpmaminul.portfolio') }}" maxlength="80"><small>Keep <code>starpmaminul.portfolio</code> to open the founder digital resume at /starpmaminul.</small></div>
  <div class="field full"><label>Optional custom URL</label><input name="button_url" value="{{ old('button_url', $section->button_url) }}" maxlength="255" placeholder="Leave empty to use the route above"><small>A custom URL overrides the route.</small></div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
</div>
