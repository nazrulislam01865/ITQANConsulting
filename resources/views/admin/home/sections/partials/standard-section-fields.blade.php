<div class="form-grid">
  <div class="field"><label>Section label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field full"><label>Headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>Lead text</label><textarea name="lead">{{ old('lead', $section->lead) }}</textarea></div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
</div>
