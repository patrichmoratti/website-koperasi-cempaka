<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>500 — Kesalahan Server</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-mony-bg flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="text-8xl font-black text-red-200 mb-4">500</div>
        <h1 class="text-2xl font-bold text-mony-text mb-2">Kesalahan Server</h1>
        <p class="text-mony-muted mb-6">Terjadi kesalahan pada server. Silakan coba lagi dalam beberapa saat.</p>
        <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
