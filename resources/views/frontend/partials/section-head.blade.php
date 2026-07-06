<div class="section-head reveal">
  <div>
    @isset($label)<div class="label">{{ $label }}</div>@endisset
    <h2 class="headline">{!! $title !!}</h2>
  </div>
  @isset($lead)<p class="lead">{{ $lead }}</p>@endisset
  @if(! empty($sectionButton))
    @include('frontend.partials.button', ['button' => $sectionButton, 'class' => $sectionButton['class'] ?? ''])
  @endif
</div>
