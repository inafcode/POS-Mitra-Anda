<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Bon - {{ $instansi->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Header & Navigasi -->
        <div class="bg-slate-900 text-white p-6 rounded-xl shadow flex flex-wrap justify-between items-center gap-4">
            <div>
                <span class="text-xs bg-amber-400 text-slate-900 font-black px-2.5 py-1 rounded uppercase">Laporan Khusus Instansi</span>
                <h1 class="text-2xl font-black mt-1 text-white">{{ $instansi->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">📞 {{ $instansi->no_hp ?? '-' }} | 📍 {{ $instansi->alamat ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/admin/instansi/{{ $instansi->id }}/export?bulan={{ $bulanPilih }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition flex items-center gap-1">
                    📥 Export Excel Bulan Ini
                </a>
                <a href="/admin/instansi" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-lg text-sm transition">
                    ← Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-3 rounded font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Bar Filter Bulan & Kartu Akumulasi Sisa Bon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            
            <!-- Kotak Filter Bulan -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <label class="text-xs font-bold text-gray-500 uppercase block mb-1">Pilih Periode Bulan:</label>
                <form method="GET" action="/admin/instansi/{{ $instansi->id }}/laporan" class="flex gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilih }}" class="border rounded-lg px-3 py-1.5 text-sm font-bold bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500 flex-1">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-1.5 rounded-lg text-sm transition">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Total Akumulasi Sisa Bon Belum Lunas (Semua Bulan) -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <span class="text-xs font-bold text-gray-400 uppercase block">Total Akumulasi Sisa Bon (Aktif)</span>
                <span class="text-2xl font-black text-red-600">Rp{{ number_format($totalHutangAktif, 0, ',', '.') }}</span>
                <span class="block text-[10px] text-gray-400">Termasuk tunggakan bulan lalu (jika ada)</span>
            </div>

            <!-- Total Belanja Bulan Yang Dipilih -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <span class="text-xs font-bold text-gray-400 uppercase block">Belanja Bulan {{ date('F Y', strtotime($bulanPilih . '-01')) }}</span>
                <span class="text-2xl font-black text-slate-800">Rp{{ number_format($totalBelanjaBulanIni, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Form Berbasis Checkbox & Tombol Simpan -->
        <form action="/admin/instansi/{{ $instansi->id }}/pelunasan-massal" method="POST">
            @csrf
            <!-- 1. Wajib mengirim bulan aktif agar hanya bulan ini yang diproses -->
            <input type="hidden" name="bulan" value="{{ $bulanPilih }}">

            <!-- Bar Aksi Simpan Perubahan Status (Centang / Undo) -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-4 mb-6">
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Pengaturan Status Bon (Centang untuk Lunas)</h3>
                    <p class="text-xs text-gray-500">Centang kotak pada nota yang dibayar, hilangkan centang untuk meng-undo, lalu klik Simpan.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="btnCheckAll" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2.5 rounded-lg text-sm transition">
                        ✓ Centang Semua
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-4 py-2.5 rounded-lg shadow text-sm transition">
                        💾 SIMPAN PELUNASAN
                    </button>
                    <!-- Tombol Cetak Kwitansi Tanda Terima -->
                    <a href="/admin/instansi/{{ $instansi->id }}/kwitansi?bulan={{ $bulanPilih }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-amber-400 font-black px-4 py-2.5 rounded-lg shadow text-sm transition border border-slate-700">
                        🖨️ CETAK KWITANSI
                    </a>
                </div>
            </div>

            <!-- Tabel Riwayat Nota / Bon Berdasarkan Bulan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-black text-slate-800">Daftar Transaksi Bon Periode: {{ date('F Y', strtotime($bulanPilih . '-01')) }} ({{ $transactions->count() }})</h2>
                </div>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold border-b border-gray-200 text-xs uppercase">
                            <th class="p-3.5 text-center w-16">Pilih</th>
                            <th class="p-3.5">Tanggal</th>
                            <th class="p-3.5">Nama Barang</th>
                            <th class="p-3.5 text-center">Jumlah Barang</th>
                            <th class="p-3.5 text-center">Satuan</th>
                            <th class="p-3.5 text-right">Harga</th>
                            <th class="p-3.5 text-right">Jumlah</th>
                            <th class="p-3.5 text-center">Status / Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            @foreach($trx->details as $index => $det)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 {{ $trx->status_pembayaran === 'Lunas' ? 'bg-emerald-50/50' : '' }}">
                                
                                <!-- Checkbox hanya muncul di baris pertama nota -->
                                <td class="p-3.5 text-center align-middle">
                                    @if($index === 0)
                                        <input type="checkbox" name="nota_ids[]" value="{{ $trx->id }}" {{ $trx->status_pembayaran === 'Lunas' ? 'checked' : '' }} class="nota-checkbox w-5 h-5 accent-blue-600 cursor-pointer" title="Centang untuk melunasi nota {{ $trx->kode_nota }}">
                                    @endif
                                </td>

                                <!-- Tanggal & Kode Nota -->
                                <td class="p-3.5 font-mono text-xs text-gray-600 align-middle">
                                    @if($index === 0)
                                        {{ date('d/m/Y', strtotime($trx->created_at)) }}
                                        <span class="block text-[10px] text-gray-400 font-bold">Nota: {{ $trx->kode_nota }}</span>
                                    @else
                                        <span class="text-gray-300">↳ (satu nota)</span>
                                    @endif
                                </td>

                                <!-- Nama Barang -->
                                <td class="p-3.5 font-bold text-slate-800">
                                    {{ $det->nama_item }}
                                </td>

                                <!-- Qty -->
                                <td class="p-3.5 text-center font-bold">
                                    {{ $det->qty }}
                                </td>

                                <!-- Satuan -->
                                <td class="p-3.5 text-center text-gray-600">
                                    {{ $det->satuan ?? 'pcs' }}
                                </td>

                                <!-- Harga Satuan -->
                                <td class="p-3.5 text-right font-medium text-gray-700">
                                    Rp{{ number_format($det->harga_jual, 0, ',', '.') }}
                                </td>

                                <!-- Subtotal -->
                                <td class="p-3.5 text-right font-black text-slate-900">
                                    Rp{{ number_format($det->subtotal, 0, ',', '.') }}
                                </td>

                                <!-- Status & Tombol Cetak Nota -->
                                <td class="p-3.5 text-center align-middle">
                                    @if($index === 0)
                                        <div class="flex justify-center items-center gap-2">
                                            <span class="px-2.5 py-1 rounded font-black text-[10px] uppercase {{ $trx->status_pembayaran === 'Lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $trx->status_pembayaran }}
                                            </span>
                                            <a href="/admin/nota/instansi/{{ $trx->id }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-2.5 py-1 rounded text-xs transition">
                                                Nota
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">Tidak ada transaksi bon pada bulan {{ date('F Y', strtotime($bulanPilih . '-01')) }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <script>
            document.getElementById('btnCheckAll')?.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.nota-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                this.textContent = allChecked ? '✓ Centang Semua Nota' : '✕ Batal Centang Semua';
            });
        </script>

    </div>

</body>
</html>