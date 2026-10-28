<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang Admin - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header Ruang Admin & Tombol Navigasi Lengkap -->
        <div class="bg-slate-900 text-white p-4 rounded-xl shadow flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-xl font-black text-amber-400">Ruang Admin / Gudang</h1>
                <p class="text-xs text-slate-400">Kelola stok, harga, dan pemilik barang toko</p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="/kasir" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-4 py-2 rounded text-sm transition">Ke Kasir</a>
                <a href="/admin/instansi" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded text-sm transition">🏢 Mitra Instansi</a>
                <a href="/laporan" class="bg-teal-600 hover:bg-teal-500 text-white font-bold px-4 py-2 rounded text-sm transition">Laporan Cashflow</a>
                <a href="/" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded text-sm transition">Ke Toko Online</a>
                <!-- Tombol Logout -->
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-2 rounded text-sm transition">Keluar</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-3 rounded font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Peringatan Stok Menipis (Opsi 1: Kado Koeni <= 0, Mitra Anda < 5) -->
        @php
            $stokTipis = \App\Models\Product::where('tipe', 'Barang')->get()->filter(function($item) {
                if ($item->pemilik === 'Kado Koeni') {
                    return $item->stok <= 0;
                }
                return $item->stok < 5;
            });
        @endphp

        @if($stokTipis->count() > 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-sm border border-amber-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-amber-600 font-black text-lg">⚠️</span>
                <span class="font-bold text-amber-800 text-sm">PERINGATAN: ADA {{ $stokTipis->count() }} BARANG YANG STOKNYA HABIS / MENIPIS!</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($stokTipis as $st)
                    <span class="text-xs bg-white border border-amber-300 px-2.5 py-1 rounded-md text-slate-800 font-semibold shadow-xs">
                        <b>{{ $st->nama_item }}</b> ({{ $st->pemilik }} - Sisa: <span class="text-red-600 font-black">{{ $st->stok }}</span>)
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Bar Kontrol: Tombol Tambah Barang, Kotak Pencarian, & Filter Toko -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-4">
            
            <!-- Tombol Tambah Barang -->
            <a href="/admin/create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-lg text-sm shadow transition flex items-center gap-1.5">
                ➕ Tambah Barang Baru
            </a>

            <!-- Form Pencarian & Filter Toko -->
            <form action="/admin" method="GET" class="flex flex-wrap items-center gap-2 flex-1 max-w-2xl justify-end">
                
                <!-- Filter Pemilik Toko -->
                <select name="pemilik" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm font-bold bg-gray-50 text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">🏢 Semua Toko</option>
                    <option value="Mitra Anda" {{ $pemilik == 'Mitra Anda' ? 'selected' : '' }}>Mitra Anda</option>
                    <option value="Kado Koeni" {{ $pemilik == 'Kado Koeni' ? 'selected' : '' }}>Kado Koeni</option>
                </select>

                <!-- Input Kata Kunci Pencarian -->
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama barang atau barcode..." class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2 text-sm font-medium focus:ring-2 focus:ring-blue-500 outline-none">
                    @if($search)
                        <a href="/admin" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 font-bold text-xs bg-gray-200 px-1.5 py-0.5 rounded-full" title="Reset Pencarian">✕</a>
                    @endif
                </div>

                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition">
                    Cari
                </button>
            </form>

        </div>

        <!-- Tabel Daftar Barang -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold border-b border-gray-200">
                        <th class="p-4 w-36">Barcode</th>
                        <th class="p-4 w-20 text-center">Foto</th>
                        <th class="p-4">Nama Item & Toko</th>
                        <th class="p-4">Tipe / Kategori</th>
                        <th class="p-4 text-right">Modal</th>
                        <th class="p-4 text-right">Jual Ecer</th>
                        <th class="p-4 text-center w-28">Stok</th>
                        <th class="p-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $item)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-4 font-mono text-xs text-gray-500">{{ $item->barcode ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($item->foto_produk)
                                <img src="{{ asset('storage/' . $item->foto_produk) }}" class="w-10 h-10 object-cover rounded mx-auto border shadow-xs">
                            @else
                                <span class="text-xs text-gray-400 italic">Kosong</span>
                            @endif
                        </td>
                        <td class="p-4 font-bold text-slate-800">
                            {{ $item->nama_item }}
                            <span class="block text-[10px] font-extrabold uppercase mt-1 px-2 py-0.5 rounded w-max {{ $item->pemilik == 'Kado Koeni' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $item->pemilik ?? 'Mitra Anda' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $item->tipe == 'Jasa' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $item->tipe }}
                            </span>
                            <span class="block text-xs text-gray-400 mt-1">{{ $item->kategori ?? '-' }}</span>
                        </td>
                        <td class="p-4 text-right font-medium text-gray-600">Rp{{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                        <td class="p-4 text-right font-bold text-blue-600">Rp{{ number_format($item->harga_jual_ecer, 0, ',', '.') }}</td>
                        <td class="p-4 text-center font-bold">
                            @if($item->tipe == 'Barang')
                                <span class="{{ $item->stok <= 0 ? 'text-red-600 bg-red-50 px-2 py-1 rounded' : 'text-slate-800' }}">
                                    {{ $item->stok }} {{ $item->satuan ?? 'pcs' }}
                                </span>
                            @else
                                <span class="text-purple-600 italic text-xs">Tak Terbatas</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-1.5">
                                <a href="/admin/{{ $item->id }}/edit" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-2.5 py-1 rounded text-xs shadow-xs transition">Edit</a>
                                <form action="/admin/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus barang {{ addslashes($item->nama_item) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-2.5 py-1 rounded text-xs shadow-xs transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400">Tidak ada barang yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Navigasi Halaman (Pagination) -->
        <div class="mt-4">
            {{ $products->links() }}
        </div>

    </div>

</body>
</html>