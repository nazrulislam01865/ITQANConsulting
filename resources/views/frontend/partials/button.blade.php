@php
  $buttonClass = trim('btn ' . ($button['class'] ?? $class ?? ''));
  $routeName = $button['route'] ?? null;
  $href = $button['url'] ?? (($routeName && Route::has($routeName)) ? route($routeName) . ($button['anchor'] ?? '') : '#');
@endphp
<a class="{{ $buttonClass }}" href="{{ $href }}">{{ $button['text'] }}</a>
