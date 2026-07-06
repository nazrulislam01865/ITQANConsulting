@php
  $assetDataUri = function (?string $path): ?string {
      if (! $path) {
          return null;
      }

      $fullPath = public_path('storage/' . ltrim($path, '/'));

      if (! is_file($fullPath)) {
          return null;
      }

      $mime = mime_content_type($fullPath) ?: 'image/jpeg';
      return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
  };
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $site['name'] ?? 'ITQAN Consulting' }} Catalog</title>
  <style>
    @page { margin: 28px; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #0e1421; background: #f8f4eb; }
    .cover { padding: 30px 28px 26px; border: 1px solid #d9d4ca; border-radius: 18px; background: #fbf8f0; margin-bottom: 20px; }
    .brand { font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #0878ad; font-weight: 700; margin-bottom: 12px; }
    h1 { font-size: 34px; line-height: 1.05; margin: 0 0 12px; }
    h2 { font-size: 26px; line-height: 1.12; margin: 0 0 12px; }
    p { font-size: 14px; line-height: 1.65; color: #4f5868; margin: 0 0 16px; }
    .page-block { page-break-inside: avoid; padding: 28px; border: 1px solid #ded8ce; border-radius: 18px; background: #fffdf8; margin: 0 0 22px; }
    .media { width: 100%; height: 300px; border-radius: 16px; overflow: hidden; background: #07101f; margin-top: 18px; }
    .media img { width: 100%; height: 300px; object-fit: cover; display: block; }
    .media-placeholder { height: 230px; background: #07101f; border-radius: 16px; color: #fff; display: table; width: 100%; }
    .media-placeholder span { display: table-cell; vertical-align: middle; text-align: center; font-weight: 700; color: #cbd5e1; }
    .page-no { text-align: right; color: #6b7280; font-weight: 700; font-size: 12px; margin-top: 12px; }
  </style>
</head>
<body>
  <section class="cover">
    <div class="brand">{{ $site['name'] ?? 'ITQAN Consulting' }}</div>
    <h1>{{ $catalog['hero']['title'] ?? 'Digital Catalog' }}</h1>
    <p>{{ $catalog['hero']['description'] ?? '' }}</p>
  </section>

  @foreach($catalogPages as $index => $catalogPage)
    @php
      $mediaPath = ($catalogPage['type'] ?? 'image') === 'video'
          ? ($catalogPage['thumbnail_path'] ?? $catalogPage['image_path'] ?? null)
          : ($catalogPage['image_path'] ?? $catalogPage['thumbnail_path'] ?? null);
      $mediaSrc = $assetDataUri($mediaPath);
    @endphp
    <section class="page-block">
      <div class="brand">{{ $catalogPage['kicker'] ?? '' }}</div>
      <h2>{{ $catalogPage['title'] ?? '' }}</h2>
      <p>{{ $catalogPage['body'] ?? '' }}</p>
      @if($mediaSrc)
        <div class="media"><img src="{{ $mediaSrc }}" alt="{{ $catalogPage['title'] ?? 'Catalog media' }}"></div>
      @elseif(($catalogPage['type'] ?? 'image') === 'video')
        <div class="media-placeholder"><span>Video thumbnail not uploaded</span></div>
      @else
        <div class="media-placeholder"><span>Image not uploaded</span></div>
      @endif
      <div class="page-no">{{ $index + 1 }}</div>
    </section>
  @endforeach
</body>
</html>
