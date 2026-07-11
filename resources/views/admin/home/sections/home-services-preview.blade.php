@include('admin.home.sections.partials.standard-section-fields', ['section' => $section])
<div class="form-grid">
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $section->button_text) }}"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Select route</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $section->button_route) === $route)>{{ $route }}</option>@endforeach</select></div>
</div>
<div class="form-note">
  Each service item supports a short introduction, a common problem, and possible deliverables. Keep the text concise so the explorer remains easy to scan. Use Display order to keep the service tabs and panels synchronized.
</div>
