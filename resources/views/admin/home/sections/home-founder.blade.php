<div class="form-grid">
  <div class="field full">
    <label>Left side image</label>
    <input type="file" name="founder_image" accept="image/*">
    @if(! empty($section->settings['image_path']))
      <div class="image-preview"><img src="{{ asset('storage/' . $section->settings['image_path']) }}" alt="Founder section image preview"></div>
    @endif
  </div>
  <div class="field"><label>Section label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field full"><label>Headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>Body text</label><textarea name="description" rows="8">{{ old('description', $section->description) }}</textarea></div>
  <div class="field"><label>Founder name</label><input name="settings[name]" value="{{ old('settings.name', $section->settings['name'] ?? '') }}"></div>
  <div class="field"><label>Founder role</label><input name="settings[role]" value="{{ old('settings.role', $section->settings['role'] ?? '') }}"></div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
</div>
