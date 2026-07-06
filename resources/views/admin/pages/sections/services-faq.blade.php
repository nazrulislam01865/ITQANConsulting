<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
  <div class="field full"><label>Headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
</div>
