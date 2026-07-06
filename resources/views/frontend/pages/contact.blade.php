@extends('frontend.layouts.app')

@section('content')
<main id="contact" class="page active">
  @include('frontend.partials.plain-hero', ['hero' => $page['hero']])

  <section class="section soft-white">
    <div class="container form-wrap">
      <form class="form-card reveal" method="post" action="#" aria-label="Contact form">
        @csrf
        <div class="form-grid">
          <div class="field"><label for="name">Name</label><input id="name" name="name" type="text" placeholder="Your name"></div>
          <div class="field"><label for="company">Company Name</label><input id="company" name="company" type="text" placeholder="Company name"></div>
          <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" placeholder="name@email.com"></div>
          <div class="field"><label for="phone">Phone / WhatsApp</label><input id="phone" name="phone" type="text" placeholder="Phone number"></div>
          <div class="field full">
            <label for="need">What do you need help with?</label>
            <select id="need" name="need">
              @foreach($collections['contact_options']['needs'] as $option)<option>{{ $option }}</option>@endforeach
            </select>
          </div>
          <div class="field full">
            <label>Areas</label>
            <div class="checks">
              @foreach($collections['contact_options']['areas'] as $index => $area)
                <label class="check"><span>{!! $area !!}</span><input type="checkbox" name="areas[]" value="{{ strip_tags($area) }}"></label>
              @endforeach
            </div>
          </div>
          <div class="field"><label for="budget">Budget range, optional</label><input id="budget" name="budget" type="text" placeholder="Example: 1–3 lakh BDT"></div>
          <div class="field">
            <label for="method">Preferred contact method</label>
            <select id="method" name="method">
              @foreach($collections['contact_options']['methods'] as $method)<option>{{ $method }}</option>@endforeach
            </select>
          </div>
          <div class="field full"><label for="message">Short message</label><textarea id="message" name="message" placeholder="Write what is happening in your business..."></textarea></div>
        </div>
        <div class="button-row" style="margin-top:18px"><button class="btn dark" type="button">Send Message</button></div>
      </form>
      <aside class="side-note reveal">
        <div class="label">{{ $page['side_note']['label'] }}</div>
        <h3>{{ $page['side_note']['title'] }}</h3>
        <p class="lead">{{ $page['side_note']['text'] }}</p>
        <ol>
          @foreach($page['side_note']['steps'] as $step)<li>{{ $step }}</li>@endforeach
        </ol>
        <div class="social-row">@include('frontend.partials.social-links', ['includeEmail' => true])</div>
      </aside>
    </div>
  </section>

  @include('frontend.partials.cta', ['cta' => $page['cta']])
</main>
@endsection
