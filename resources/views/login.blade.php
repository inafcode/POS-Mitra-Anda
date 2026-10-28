<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mitra Anda & Kado Koeni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 h-screen flex justify-center items-center">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96 border border-gray-200">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-slate-900">🔐 Area Terbatas</h1>
            <p class="text-xs text-gray-500 mt-1">Mitra Anda Seponti & Kado Koeni</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-3 rounded mb-4 text-xs font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email / Username</label>
                <input type="email" name="email" value="admin@mitraanda.com" required class="w-full border border-gray-300 rounded-lg p-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full border border-gray-300 rounded-lg p-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-lg text-sm shadow-lg transition">
                MASUK KE SISTEM
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-xs font-bold text-gray-400 hover:text-slate-700">← Kembali ke Katalog Depan</a>
        </div>
    </div>

</body>
</html>