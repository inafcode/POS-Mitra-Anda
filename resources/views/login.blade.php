<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mitra Anda & Kado Koeni</title>
    
    <!-- Tipografi: Inter & Fraunces -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- CSS Variables & Tema Dasar -->
    <style>
        :root {
            --latar: #F5EFE6;
            --teks: #292929;
            --teks-redup: #6B6257;
            --garis: #DCD2C2;
            --permukaan: #EDE5D8;
            --aksen: #B8532F;
        }

        body {
            background-color: var(--latar);
            color: var(--teks);
            font-feature-settings: "tnum" 1;
        }

        /* Focus state aksesibilitas */
        a:focus-visible, input:focus-visible, button:focus-visible {
            outline: 2px solid var(--teks);
            outline-offset: 1px;
        }

        /* Input Form Spesifik */
        .input-standar {
            width: 100%;
            background-color: transparent;
            border: 1px solid var(--garis);
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 15px;
            color: var(--teks);
            transition: border-color 150ms ease;
        }
        .input-standar:focus {
            background-color: #FDFBF7;
        }

        /* Tombol Utama Terakota */
        .btn-utama {
            background-color: var(--aksen);
            color: #FBF7F0;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 16px;
            border-radius: 4px;
            transition: opacity 150ms ease-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            cursor: pointer;
            border: none;
        }
        .btn-utama:hover { opacity: 0.9; }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        latar: 'var(--latar)',
                        teks: 'var(--teks)',
                        redup: 'var(--teks-redup)',
                        garis: 'var(--garis)',
                        permukaan: 'var(--permukaan)',
                        aksen: 'var(--aksen)',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Fraunces', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex justify-center items-center p-6">

    <div class="w-full max-w-[360px]">
        
        <!-- Kartu Login Minimalis -->
        <div class="border border-garis rounded bg-latar p-8 mb-6">
            <div class="mb-8">
                <h1 class="font-serif text-3xl text-teks mb-1 tracking-tight">Masuk</h1>
                <p class="text-[13px] text-redup">Akses buku kas dan dashboard toko.</p>
            </div>

            <!-- Pesan Error -->
            @if($errors->any())
                <div class="border-l-2 border-teks bg-permukaan p-3 mb-6 text-[13px] text-teks font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-[13px] text-redup mb-1.5">Email atau Username</label>
                    <!-- Menghapus value hardcode admin agar lebih bersih secara standar -->
                    <input type="email" name="email" value="{{ old('email', 'admin@mitraanda.com') }}" required class="input-standar">
                </div>

                <div>
                    <label class="block text-[13px] text-redup mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required class="input-standar">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-utama">
                        Masuk ke Sistem
                    </button>
                </div>
            </form>
        </div>

        <!-- Tautan Kembali -->
        <div class="text-center">
            <a href="/" class="text-[13px] text-redup hover:text-teks transition-colors">
                ← Kembali ke katalog publik
            </a>
        </div>

    </div>

</body>
</html>