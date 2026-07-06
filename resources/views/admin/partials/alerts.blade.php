@if(session('success'))
  <div class="status">{{ session('success') }}</div>
@endif
@if(session('status'))
  <div class="status">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="error-list">
    <strong>Please fix the highlighted issues.</strong>
    <ul>
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
