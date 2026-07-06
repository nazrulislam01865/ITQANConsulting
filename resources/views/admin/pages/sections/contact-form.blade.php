<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
  <div class="field"><label>Side note label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field"><label>Side note title</label><input name="title" value="{{ old('title', $section->title) }}"></div>
  <div class="field full"><label>Side note text</label><textarea name="lead">{{ old('lead', $section->lead) }}</textarea></div>
  <div class="field full"><label>Need dropdown options</label><textarea name="settings[needs]" rows="6">{{ old('settings.needs', $section->settings['needs'] ?? '') }}</textarea></div>
  <div class="field full"><label>Area checkbox options</label><textarea name="settings[areas]" rows="6">{{ old('settings.areas', $section->settings['areas'] ?? '') }}</textarea></div>
  <div class="field full"><label>Preferred contact methods</label><textarea name="settings[methods]" rows="5">{{ old('settings.methods', $section->settings['methods'] ?? '') }}</textarea></div>
</div>
