<div class="form-grid">
  <div class="field"><label>Section label</label><input name="label" value="{{ old('label', $section->label) }}" placeholder="Interactive Clarity Check"></div>
  <div class="field full"><label>Headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>Intro text</label><textarea name="lead" rows="4">{{ old('lead', $section->lead) }}</textarea></div>
  <div class="field full">
    <div class="admin-note">
      Each active item becomes one selectable situation. Its explanation and practical starting points update instantly on the public homepage.
    </div>
  </div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
</div>
