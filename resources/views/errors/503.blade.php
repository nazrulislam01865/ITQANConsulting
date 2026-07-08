<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Maintenance | {{ config('app.name', 'ITQAN') }}</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <style>
    :root{
      --ink:#050713;
      --navy:#08111f;
      --navy-2:#0b1426;
      --text:#f7f8fa;
      --muted:#aeb6c5;
      --line:rgba(255,255,255,.13);
      --blue:#18b8ff;
      --blue-2:#69d4ff;
      --warm:#f5a623;
      --soft:#fbfaf7;
    }

    *{box-sizing:border-box}
    html{min-height:100%}
    body{
      min-height:100vh;
      margin:0;
      display:grid;
      place-items:center;
      padding:28px;
      font-family:Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at 12% 10%, rgba(24,184,255,.16), transparent 28%),
        radial-gradient(circle at 88% 18%, rgba(245,166,35,.12), transparent 30%),
        radial-gradient(circle at 50% 88%, rgba(24,184,255,.11), transparent 34%),
        var(--ink);
      overflow-x:hidden;
    }

    body::before{
      content:"";
      position:fixed;
      inset:0;
      pointer-events:none;
      background-image:
        linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
      background-size:72px 72px;
      mask-image:linear-gradient(to bottom, rgba(0,0,0,.72), transparent 84%);
      opacity:.5;
    }

    .maintenance-shell{
      position:relative;
      z-index:1;
      width:min(100%, 980px);
      border:1px solid var(--line);
      border-radius:34px;
      overflow:hidden;
      background:linear-gradient(145deg, rgba(255,255,255,.1), rgba(255,255,255,.04));
      box-shadow:0 30px 90px rgba(0,0,0,.38), inset 0 1px 0 rgba(255,255,255,.16);
    }

    .maintenance-shell::before{
      content:"";
      position:absolute;
      inset:-1px;
      pointer-events:none;
      background:
        radial-gradient(circle at 82% 16%, rgba(24,184,255,.22), transparent 28%),
        radial-gradient(circle at 12% 88%, rgba(245,166,35,.15), transparent 30%);
    }

    .maintenance-card{
      position:relative;
      display:grid;
      grid-template-columns:1.05fr .95fr;
      gap:24px;
      padding:42px;
      align-items:center;
    }

    .brand{
      display:inline-flex;
      align-items:center;
      gap:12px;
      color:#fff;
      font-weight:900;
      letter-spacing:-.04em;
      margin-bottom:34px;
    }

    .brand-mark{
      width:44px;
      height:44px;
      display:grid;
      place-items:center;
      border-radius:15px;
      color:#04101d;
      font-weight:900;
      font-family:Georgia, serif;
      background:linear-gradient(135deg, rgba(24,184,255,.95), rgba(245,166,35,.88));
      box-shadow:0 16px 38px rgba(24,184,255,.2);
      position:relative;
      overflow:hidden;
    }

    .brand-mark::after{
      content:"";
      position:absolute;
      inset:5px;
      border-radius:11px;
      border:1px solid rgba(255,255,255,.58);
    }

    .brand-copy small{
      display:block;
      margin-top:2px;
      color:var(--muted);
      font-size:.64rem;
      letter-spacing:.16em;
      text-transform:uppercase;
    }

    .status-pill{
      display:inline-flex;
      align-items:center;
      gap:10px;
      margin-bottom:20px;
      padding:9px 13px;
      border-radius:999px;
      border:1px solid rgba(255,255,255,.13);
      background:rgba(255,255,255,.07);
      color:var(--blue-2);
      font-size:.72rem;
      font-weight:900;
      letter-spacing:.17em;
      text-transform:uppercase;
    }

    .status-pill::before{
      content:"";
      width:8px;
      height:8px;
      border-radius:50%;
      background:var(--warm);
      box-shadow:0 0 18px rgba(245,166,35,.72);
    }

    h1{
      margin:0 0 18px;
      max-width:640px;
      font-family:"Space Grotesk", Inter, system-ui, sans-serif;
      font-size:clamp(2.7rem, 7vw, 5.7rem);
      line-height:.92;
      letter-spacing:-.075em;
    }

    p{
      max-width:570px;
      margin:0;
      color:#cbd5e1;
      font-size:clamp(1rem, 1.35vw, 1.16rem);
      line-height:1.75;
    }

    .actions{
      display:flex;
      flex-wrap:wrap;
      gap:12px;
      margin-top:28px;
    }

    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-height:46px;
      padding:13px 18px;
      border-radius:999px;
      border:1px solid rgba(255,255,255,.18);
      color:var(--ink);
      background:#f9fbff;
      font-weight:900;
      text-decoration:none;
      cursor:pointer;
      transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .btn:hover,
    .btn:focus-visible{
      transform:translateY(-2px);
      box-shadow:0 18px 45px rgba(24,184,255,.13);
      outline:none;
    }

    .btn.secondary{
      color:#fff;
      background:rgba(255,255,255,.08);
      border-color:rgba(255,255,255,.16);
    }

    .visual{
      min-height:360px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:28px;
      background:linear-gradient(160deg, rgba(8,17,31,.86), rgba(5,7,19,.92));
      box-shadow:inset 0 1px 0 rgba(255,255,255,.1);
      padding:24px;
      display:grid;
      align-content:end;
      gap:18px;
      overflow:hidden;
      position:relative;
    }

    .visual::before,
    .visual::after{
      content:"";
      position:absolute;
      border-radius:999px;
      filter:blur(22px);
      opacity:.72;
    }

    .visual::before{
      width:150px;
      height:150px;
      left:28px;
      top:34px;
      background:rgba(24,184,255,.24);
    }

    .visual::after{
      width:145px;
      height:145px;
      right:36px;
      top:78px;
      background:rgba(245,166,35,.19);
    }

    .window{
      position:relative;
      z-index:1;
      min-height:170px;
      border-radius:24px;
      border:1px solid rgba(255,255,255,.1);
      background:rgba(255,255,255,.045);
      padding:18px;
    }

    .window-top{
      display:flex;
      gap:8px;
      margin-bottom:34px;
    }

    .window-top span{
      width:10px;
      height:10px;
      border-radius:50%;
      background:rgba(255,255,255,.24);
    }

    .window-top span:nth-child(2){background:rgba(24,184,255,.72)}
    .window-top span:nth-child(3){background:rgba(245,166,35,.72)}

    .line{
      height:12px;
      border-radius:999px;
      background:rgba(255,255,255,.1);
      margin-bottom:12px;
      overflow:hidden;
    }

    .line.short{width:64%}
    .line.tiny{width:42%}

    .line.progress{
      height:8px;
      margin-top:28px;
      background:rgba(255,255,255,.13);
    }

    .line.progress span{
      display:block;
      width:72%;
      height:100%;
      border-radius:inherit;
      background:linear-gradient(90deg,var(--warm),var(--blue));
    }

    .note{
      position:relative;
      z-index:1;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      border:1px solid rgba(255,255,255,.1);
      border-radius:20px;
      background:rgba(255,255,255,.055);
      padding:16px;
      color:#dbeafe;
      font-size:.88rem;
      font-weight:800;
    }

    .note span:last-child{
      color:var(--blue-2);
      white-space:nowrap;
    }

    @media (prefers-reduced-motion: reduce){
      *, *::before, *::after{
        animation:none!important;
        transition:none!important;
        scroll-behavior:auto!important;
      }
    }

    @media (max-width: 820px){
      body{padding:18px}
      .maintenance-card{grid-template-columns:1fr;padding:28px;gap:28px}
      .brand{margin-bottom:26px}
      .visual{min-height:280px}
    }

    @media (max-width: 520px){
      .maintenance-card{padding:22px}
      .maintenance-shell{border-radius:26px}
      .actions{display:grid}
      .btn{width:100%}
      .note{align-items:flex-start;flex-direction:column}
      .note span:last-child{white-space:normal}
    }
  </style>
</head>
<body>
  <main class="maintenance-shell" role="main" aria-labelledby="maintenance-title">
    <section class="maintenance-card">
      <div>
        <div class="brand" aria-label="{{ config('app.name', 'ITQAN') }}">
          <span class="brand-mark">I</span>
          <span class="brand-copy">
            {{ config('app.name', 'ITQAN') }}
            <small>Digital clarity</small>
          </span>
        </div>

        <span class="status-pill">Maintenance mode</span>
        <h1 id="maintenance-title">We&rsquo;ll be back shortly.</h1>
        <p>
          The website is currently undergoing scheduled maintenance. We are improving the experience and will restore access as soon as possible.
        </p>

        <div class="actions" aria-label="Maintenance actions">
          <a class="btn" href="{{ url()->current() }}">Try again</a>
          <a class="btn secondary" href="mailto:{{ config('mail.from.address', 'hello@itqanconsulting.com') }}">Contact support</a>
        </div>
      </div>

      <div class="visual" aria-hidden="true">
        <div class="window">
          <div class="window-top"><span></span><span></span><span></span></div>
          <div class="line"></div>
          <div class="line short"></div>
          <div class="line tiny"></div>
          <div class="line progress"><span></span></div>
        </div>
        <div class="note">
          <span>System update in progress</span>
          <span>HTTP 503</span>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
