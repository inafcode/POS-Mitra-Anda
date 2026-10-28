<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitra Instansi (B2B) - Mitra Anda & Kado Koeni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header & Navigasi -->
        <div class="bg-slate-900 text-white p-4 rounded-xl shadow flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-xl font-black text-amber-400">🏢 Manajemen Mitra Instansi (B2B)</h1>
                <p class="text-xs text-slate-400">Kelola akun login instansi & pantau saldo bon berjalan</p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="/admin" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded text-sm transition">← Gudang Barang</a>
                <a href="/kasir" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-4 py-2 rounded text-sm transition">Ke Kasir</a>
                <a href="/laporan" class="bg-teal-600 hover:bg-teal-500 text-white font-bold px-4 py-2 rounded text-sm transition">Laporan Cashflow</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-3 rounded font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-3 rounded font-bold text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Kolom Kiri: Form Tambah Instansi Baru -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 h-max">
                <h2 class="font-black text-slate-800 text-base mb-4 border-b pb-2">➕ Daftarkan Instansi Baru</h2>
                <form action="/admin/instansi" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Instansi / Sekolah / Kantor *</label>
                        <input type="text" name="name" placeholder="Contoh: Kantor Desa Seponti" required class="w-full border rounded-lg p-2.5 font-semibold focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email Login Instansi *</label>
                        <input type="email" name="email" placeholder="desa@mitraanda.com" required class="w-full border rounded-lg p-2.5 font-semibold focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Password Login *</label>
                        <input type="text" name="password" placeholder="Minimal 6 karakter" required class="w-full border rounded-lg p-2.5 font-semibold focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">No. WA Bendahara / PIC</label>
                        <input type="text" name="no_hp" placeholder="0812xxxx" class="w-full border rounded-lg p-2.5 font-semibold focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Alamat / Keterangan</label>
                        <textarea name="alamat" rows="2" placeholder="Alamat instansi..." class="w-full border rounded-lg p-2.5 font-semibold focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 rounded-lg shadow transition">
                        SIMPAN AKUN INSTANSI
                    </button>
                </form>
            </div>

            <!-- Kolom Kanan: Tabel Daftar Instansi -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-black text-slate-800">Daftar Instansi Terdaftar ({{ $instansis->count() }})</h2>
                </div>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold border-b border-gray-200">
                            <th class="p-4">Nama Instansi & Kontak</th>
                            <th class="p-4">Akun Login (Email)</th>
                            <th class="p-4 text-right">Total Bon Aktif</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($instansis as $ins)
                        @php
                            $totalBon = $ins->transactions->sum('total_bayar');
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-black text-slate-800 text-base">{{ $ins->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">📞 {{ $ins->no_hp ?? '-' }} | 📍 {{ $ins->alamat ?? '-' }}</div>
                            </td>
                            <td class="p-4 font-mono text-xs font-bold text-blue-600">
                                {{ $ins->email }}
                            </td>
                            <td class="p-4 text-right font-black {{ $totalBon > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                Rp{{ number_format($totalBon, 0, ',', '.') }}
                                @if($totalBon > 0)
                                    <span class="block text-[10px] font-bold text-red-500 uppercase">{{ $ins->transactions->count() }} Nota Belum Lunas</span>
                                @else
                                    <span class="block text-[10px] font-bold text-emerald-500 uppercase">Aman / Lunas</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <a href="/admin/instansi/{{ $ins->id }}/laporan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded text-xs transition">
                                        📋 Buku Tagihan & Order
                                    </a>
                                    <form action="/admin/instansi/{{ $ins->id }}" method="POST" onsubmit="return confirm('Hapus akun instansi {{ addslashes($ins->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 hover:bg-red-600 hover:text-white text-red-600 font-bold px-3 py-1.5 rounded text-xs transition w-full">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400">Belum ada mitra instansi yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</body>
</html>