<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terlalu Cepat | 429</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #fef2f2; height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center; color: #991b1b; text-align: center; }
        .container { padding: 2rem; max-width: 500px; animation: fadeIn 0.8s ease-out; }
        .error-code { font-size: 8rem; font-weight: 600; margin: 0; background: linear-gradient(135deg, #f87171, #dc2626); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; }
        h1 { font-size: 1.5rem; margin-top: 0; color: #b91c1c; }
        p { color: #991b1b; opacity: 0.8; line-height: 1.6; margin-bottom: 2rem; }
        .btn { display: inline-block; background: #ef4444; color: white; text-decoration: none; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3); }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.4); background: #dc2626; }
        .icon-box { font-size: 4rem; margin-bottom: 1rem; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">🚦</div>
        <p class="error-code">429</p>
        <h1>Terlalu Banyak Permintaan</h1>
        <p>Sistem mendeteksi aktivitas yang terlalu cepat. Silakan tunggu beberapa saat sebelum mencoba kembali.</p>
        <a href="/admin" class="btn">Coba Lagi Nanti</a>
    </div>
</body>
</html>
