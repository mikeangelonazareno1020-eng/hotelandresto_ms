<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Expired</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        html, body { height: 100%; margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji'; }
        .wrap { display: flex; align-items: center; justify-content: center; height: 100%; background: #0f172a; color: #fff; }
        .card { background: #111827; padding: 2rem 2.5rem; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,.35); max-width: 32rem; text-align: center; }
        h1 { font-size: 1.75rem; margin: 0 0 .75rem; }
        p { color: #9ca3af; margin: 0.25rem 0 1.25rem; }
        a { display: inline-block; background: #a8432b; color: #fff; padding: .625rem 1rem; text-decoration: none; border-radius: .5rem; }
        a:hover { background: #8c3722; }
    </style>
    <script>
        if ('serviceWorker' in navigator) {
            // Ensure no SW interferes with page-expired behavior
            navigator.serviceWorker.getRegistrations().then(regs => regs.forEach(r => r.unregister()));
        }
    </script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="turbolinks-cache-control" content="no-cache">
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Page Expired</h1>
            <p>For your security, this page is no longer available.</p>
            <a href="{{ route('login.form') }}">Go to Login</a>
        </div>
    </div>
</body>
</html>

