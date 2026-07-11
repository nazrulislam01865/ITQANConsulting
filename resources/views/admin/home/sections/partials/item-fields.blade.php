@php($type = old('item_type', $item->item_type ?? ($defaultType ?? 'card')))
@php($hideBadge = $hideBadge ?? false)
@php($sectionKey = $sectionKey ?? '')

@if($type === 'problem')
  <div class="field full"><label>Situation / option title</label><textarea name="settings[problem]">{{ old('settings.problem', $item->settings['problem'] ?? $item->title ?? '') }}</textarea></div>
  <div class="field full"><label>Explanation shown after selection</label><textarea name="settings[summary]" rows="4">{{ old('settings.summary', $item->settings['summary'] ?? $item->settings['response'] ?? $item->text ?? '') }}</textarea></div>
  <div class="field full"><label>Practical starting points (one per line)</label><textarea name="settings[services]" rows="6">{{ old('settings.services', $item->settings['services'] ?? '') }}</textarea><small>These appear as the bullet list on the right side of the interactive clarity check.</small></div>
@elseif($type === 'testimonial')
  <div class="field"><label>Quote title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}" placeholder="10"></div>
  <div class="field"><label>Author</label><input name="settings[author]" value="{{ old('settings.author', $item->settings['author'] ?? '') }}"></div>
  <div class="field"><label>Role / company</label><input name="settings[role]" value="{{ old('settings.role', $item->settings['role'] ?? '') }}"></div>
  <div class="field"><label>Project pill</label><input name="settings[project]" value="{{ old('settings.project', $item->settings['project'] ?? '') }}" placeholder="Fleet operations"></div>
  <div class="field full"><label>Quote text</label><textarea name="text" rows="5">{{ old('text', $item->text ?? '') }}</textarea></div>
@elseif($type === 'value')
  <div class="field"><label>Number</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}" placeholder="01"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}" placeholder="10"></div>
  <div class="field full"><label>Small principle label</label><input name="settings[mini]" value="{{ old('settings.mini', $item->settings['mini'] ?? '') }}" placeholder="01 / clarity before execution"></div>
  <div class="field full"><label>Principle headline</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Description</label><textarea name="text" rows="4">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field full"><label>Practical example</label><textarea name="settings[example]" rows="3">{{ old('settings.example', $item->settings['example'] ?? '') }}</textarea></div>
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
@elseif($type === 'service_card')
  <div class="field"><label>Number / badge</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}" placeholder="01"></div>
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}" placeholder="10"></div>
  <div class="field full"><label>Service title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Service introduction</label><textarea name="text" rows="4">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field full"><label>Common problem</label><textarea name="settings[common_problem]" rows="3">{{ old('settings.common_problem', $item->settings['common_problem'] ?? '') }}</textarea></div>
  <div class="field full"><label>Possible deliverables</label><textarea name="settings[deliverables]" rows="3">{{ old('settings.deliverables', $item->settings['deliverables'] ?? '') }}</textarea></div>
@elseif($type === 'card' && $sectionKey === 'home_who')
  <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}" placeholder="10"></div>
  <div class="field full"><label>Problem headline</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Problem description</label><textarea name="text" rows="4">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field full"><label>ITQAN response</label><textarea name="settings[response]" rows="3">{{ old('settings.response', $item->settings['response'] ?? '') }}</textarea></div>
  <div class="field full"><label>Visual stage label</label><input name="settings[stage_label]" value="{{ old('settings.stage_label', $item->settings['stage_label'] ?? '') }}" placeholder="The full picture becomes visible"></div>
@elseif($type === 'step' || $type === 'card')
  @unless($hideBadge)
    <div class="field"><label>Number / badge</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  @endunless
  @if($type === 'step')
    <div class="field"><label>Display order</label><input type="number" min="0" max="999" step="10" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}" placeholder="10"></div>
  @endif
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@else
  @unless($hideBadge)
    <div class="field"><label>Number / badge</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  @endunless
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@endif
