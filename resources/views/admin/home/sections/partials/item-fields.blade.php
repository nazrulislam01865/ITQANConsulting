@php($type = old('item_type', $item->item_type ?? ($defaultType ?? 'card')))
@php($hideBadge = $hideBadge ?? false)

@if($type === 'problem')
  <div class="field full"><label>Problem</label><textarea name="settings[problem]">{{ old('settings.problem', $item->settings['problem'] ?? '') }}</textarea></div>
  <div class="field full"><label>ITQAN Response</label><textarea name="settings[response]">{{ old('settings.response', $item->settings['response'] ?? '') }}</textarea></div>
@elseif($type === 'testimonial')
  <div class="field"><label>Quote title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field"><label>Author</label><input name="settings[author]" value="{{ old('settings.author', $item->settings['author'] ?? '') }}"></div>
  <div class="field full"><label>Quote text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field"><label>Role / company</label><input name="settings[role]" value="{{ old('settings.role', $item->settings['role'] ?? '') }}"></div>
@elseif($type === 'work')
  <div class="field full">
    <label>Work image (16:9)</label>
    @if(!empty($item) && !empty($item->settings['image_path']))
      <div class="image-preview work-image-preview"><img src="{{ asset('storage/' . $item->settings['image_path']) }}" alt="Work image preview"></div>
    @endif
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field"><label>Preview pill</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  <div class="field"><label>Work title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Description</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field"><label>Full pill</label><input name="settings[pill]" value="{{ old('settings.pill', $item->settings['pill'] ?? '') }}"></div>
  <div class="field"><label>Categories, comma separated</label><input name="settings[categories]" value="{{ old('settings.categories', $item->settings['categories'] ?? '') }}"></div>
  <div class="field"><label>Tags, comma separated</label><input name="settings[tags]" value="{{ old('settings.tags', $item->settings['tags'] ?? '') }}"></div>
  <div class="field"><label>Button label</label><input name="button_text" value="{{ old('button_text', $item->button_text ?? '') }}" placeholder="View Case Study"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $item->button_route ?? '') === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://... or #case-study"></div>
@elseif($type === 'button')
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $item->button_text ?? '') }}"></div>
  <div class="field"><label>Button style</label><select name="button_class"><option value="">Default outline</option><option value="blue" @selected(old('button_class', $item->button_class ?? '') === 'blue')>Blue primary</option><option value="primary" @selected(old('button_class', $item->button_class ?? '') === 'primary')>White primary</option><option value="dark" @selected(old('button_class', $item->button_class ?? '') === 'dark')>Dark</option><option value="ghost-light" @selected(old('button_class', $item->button_class ?? '') === 'ghost-light')>Ghost light</option></select></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $item->button_route ?? '') === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://... or leave empty when route is selected"></div>
@elseif($type === 'ticker')
  <div class="field full"><label>Marquee text</label><input name="text" value="{{ old('text', $item->text ?? '') }}" placeholder="Example: clearExecution"></div>
@elseif($type === 'social_link')
  @php($platform = old('settings.platform', $item->settings['platform'] ?? $item->badge ?? ''))
  <div class="field"><label>Social platform</label><input name="settings[platform]" value="{{ $platform }}" placeholder="LinkedIn"></div>
  <div class="field"><label>Social media label</label><input name="title" value="{{ old('title', $item->title ?? '') }}" placeholder="ITQAN on LinkedIn"></div>
  <div class="field full"><label>Social media link</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://..."></div>
@elseif($type === 'paragraph')
  <div class="field full"><label>Paragraph</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@elseif($type === 'chip')
  <div class="field full"><label>Short line / chip text</label><input name="title" value="{{ old('title', $item->title ?? '') }}" placeholder="No noise."></div>
@elseif($type === 'service_card' || $type === 'step' || $type === 'card')
  @unless($hideBadge)
    <div class="field"><label>Number / badge</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  @endunless
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@else
  @unless($hideBadge)
    <div class="field"><label>Number / badge</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  @endunless
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@endif
