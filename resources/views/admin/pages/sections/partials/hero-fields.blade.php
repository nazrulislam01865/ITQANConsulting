<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this hero section</label>
  <div class="field"><label>Top label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field full"><label>Hero headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>Description</label><textarea name="description">{{ old('description', $section->description) }}</textarea></div>
</div>
