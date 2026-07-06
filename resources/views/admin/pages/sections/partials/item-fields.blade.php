@php($type = old('item_type', $item->item_type ?? ($defaultType ?? 'card')))

@if($type === 'button')
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $item->button_text ?? '') }}"></div>
  <div class="field"><label>Button style</label><select name="button_class"><option value="">Default outline</option><option value="blue" @selected(old('button_class', $item->button_class ?? '') === 'blue')>Blue primary</option><option value="primary" @selected(old('button_class', $item->button_class ?? '') === 'primary')>White primary</option><option value="dark" @selected(old('button_class', $item->button_class ?? '') === 'dark')>Dark</option><option value="ghost-light" @selected(old('button_class', $item->button_class ?? '') === 'ghost-light')>Ghost light</option></select></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $item->button_route ?? '') === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://... or #section"></div>
@elseif($type === 'card')
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@elseif($type === 'mission_card')
  <div class="field"><label>Badge label</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}" placeholder="Mission"></div>
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@elseif($type === 'service_area')
  <div class="field full"><label>Service title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Intro text</label><textarea name="subtitle">{{ old('subtitle', $item->subtitle ?? '') }}</textarea></div>
  <div class="field full"><label>Service points</label><textarea name="settings[points]" rows="6">{{ old('settings.points', $item->settings['points'] ?? '') }}</textarea></div>
  <div class="field"><label>Button text</label><input name="button_text" value="{{ old('button_text', $item->button_text ?? '') }}"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $item->button_route ?? '') === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://... or #section"></div>
@elseif($type === 'faq')
  <div class="field"><label>Question</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field"><label>Short answer</label><input name="subtitle" value="{{ old('subtitle', $item->subtitle ?? '') }}"></div>
  <div class="field full"><label>Answer text</label><textarea name="text" rows="7">{{ old('text', $item->text ?? '') }}</textarea></div>
@elseif($type === 'filter')
  <div class="field full"><label>Filter label</label><input name="title" value="{{ old('title', $item->title ?? '') }}" placeholder="Software"></div>
@elseif($type === 'work')
  <div class="field full">
    <label>Work image (16:9)</label>
    @if(!empty($item) && !empty($item->settings['image_path']))
      <div class="image-preview work-image-preview"><img src="{{ asset('storage/' . $item->settings['image_path']) }}" alt="Work image preview"></div>
    @endif
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field"><label>Pill label</label><input name="badge" value="{{ old('badge', $item->badge ?? '') }}"></div>
  <div class="field"><label>Work title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Description</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field"><label>Categories</label><input name="settings[categories]" value="{{ old('settings.categories', $item->settings['categories'] ?? '') }}" placeholder="software, process"></div>
  <div class="field"><label>Tags</label><input name="settings[tags]" value="{{ old('settings.tags', $item->settings['tags'] ?? '') }}" placeholder="Operations, Reporting"></div>
  <div class="field"><label>Button label</label><input name="button_text" value="{{ old('button_text', $item->button_text ?? '') }}" placeholder="View Case Study"></div>
  <div class="field"><label>Button route</label><select name="button_route"><option value="">Use URL</option>@foreach($routes as $route)<option value="{{ $route }}" @selected(old('button_route', $item->button_route ?? '') === $route)>{{ $route }}</option>@endforeach</select></div>
  <div class="field full"><label>Button URL</label><input name="button_url" value="{{ old('button_url', $item->button_url ?? '') }}" placeholder="https://... or #case-study"></div>
@elseif($type === 'catalog_page')
  <div class="field"><label>Page type</label><select name="settings[type]"><option value="image" @selected(old('settings.type', $item->settings['type'] ?? 'image') === 'image')>Image style</option><option value="video" @selected(old('settings.type', $item->settings['type'] ?? '') === 'video')>Video style</option></select></div>
  <div class="field"><label>Kicker</label><input name="subtitle" value="{{ old('subtitle', $item->subtitle ?? '') }}"></div>
  <div class="field full"><label>Page title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Body text</label><textarea name="text" rows="6">{{ old('text', $item->text ?? '') }}</textarea></div>
  <div class="field full">
    <label>Image media (16:9)</label>
    @if(!empty($item) && !empty($item->settings['image_path']))
      <div class="image-preview catalog-media-preview"><img src="{{ asset('storage/' . $item->settings['image_path']) }}" alt="Catalog image preview"></div>
    @endif
    <input type="file" name="media_image" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field full">
    <label>Video media</label>
    @if(!empty($item) && !empty($item->settings['video_path']))
      <div class="admin-file-preview">Current video: {{ basename($item->settings['video_path']) }}</div>
    @endif
    <input type="file" name="media_video">
  </div>
  <div class="field full">
    <label>Thumbnail image (16:9)</label>
    @if(!empty($item) && !empty($item->settings['thumbnail_path']))
      <div class="image-preview catalog-media-preview"><img src="{{ asset('storage/' . $item->settings['thumbnail_path']) }}" alt="Catalog thumbnail preview"></div>
    @endif
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp">
  </div>
@elseif($type === 'step')
  <div class="field full"><label>Step text</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
@else
  <div class="field"><label>Title</label><input name="title" value="{{ old('title', $item->title ?? '') }}"></div>
  <div class="field full"><label>Text</label><textarea name="text">{{ old('text', $item->text ?? '') }}</textarea></div>
@endif
