<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan | 404</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, #f3f4f6, #ffffff);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
            text-align: center;
        }
        .container {
            padding: 2rem;
            max-width: 500px;
            animation: fadeIn 0.8s ease-out;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 600;
            margin: 0;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        h1 { font-size: 1.5rem; margin-top: 0; color: #3731a1; }
        p { color: #6b7280; line-height: 1.6; margin-bottom: 2rem; }
        
        .btn {
            display: inline-block;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            background: #4338ca;
        }
        .icon-box {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">🔍</div>
        <p class="error-code">404</p>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Sepertinya Anda tersesat atau halaman telah dipindahkan. Jangan khawatir, klik tombol di bawah untuk kembali.</p>
        
        <a href="/admin" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>
