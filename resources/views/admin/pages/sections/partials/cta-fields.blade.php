<div class="form-grid">
  <label class="check-row full"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))> Show this CTA section</label>
  <div class="field full"><label>CTA headline</label><textarea name="title">{{ old('title', $section->title) }}</textarea></div>
  <div class="field full"><label>CTA text</label><textarea name="lead">{{ old('lead', $section->lead) }}</textarea></div>
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $section->button_text) }}"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Select route</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $section->button_route) === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $section->button_url) }}" placeholder="mailto:... or https://..."></div>
</div>
