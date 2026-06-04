<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Halaman Tidak Ditemukan</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-mony-bg flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="text-8xl font-black text-primary/20 mb-4">404</div>
        <h1 class="text-2xl font-bold text-mony-text mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-mony-muted mb-6">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
        <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
