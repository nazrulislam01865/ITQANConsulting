<div class="form-grid">
  <div class="field"><label>Section label</label><input name="label" value="{{ old('label', $section->label) }}"></div>
  <div class="field full"><label>Headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $section->button_text) }}"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Select route</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $section->button_route) === $route)>{{ $route }}</option>@endforeach</select></div>
  <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this section</label>
</div>
