<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashflow Harian - Mitra Anda & Kado Koeni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header & Pilih Tanggal -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-teal-800">Detail Transaksi & Cashflow</h1>
                <p class="text-xs text-gray-500">Baris Kuning = <b>Kado Koeni</b> | Baris Putih = <b>Mitra Anda</b></p>
            </div>

            <form action="/laporan" method="GET" class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Tanggal Cashflow:</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="border-2 border-teal-600 rounded px-3 py-1.5 font-bold text-slate-800">
            </form>

            <div class="flex gap-2">
                <a href="/kasir" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-4 py-2 rounded text-sm shadow-sm">Ke Kasir</a>
                <a href="/admin" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-4 py-2 rounded text-sm shadow-sm">Ke Gudang</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-600 text-emerald-800 p-3 rounded font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- TABEL 1: DETAIL TRANSAKSI (PERSIS EXCEL) -->
        <div class="bg-white shadow-sm border border-gray-400 overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-800 border-b-2 border-gray-400 font-bold">
                        <th class="p-2 border-r border-gray-300">Id</th>
                        <th class="p-2 border-r border-gray-300">Id Transaksi</th>
                        <th class="p-2 border-r border-gray-300">Nama Barang</th>
                        <th class="p-2 border-r border-gray-300 text-center">Pemilik</th>
                        <th class="p-2 border-r border-gray-300 text-center">Qty</th>
                        <th class="p-2 border-r border-gray-300 text-center">Satuan</th>
                        <th class="p-2 border-r border-gray-300 text-right">Harga</th>
                        <th class="p-2 border-r border-gray-300 text-right">SubTotal</th>
                        <th class="p-2 text-center">Tanggal Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $d)
                    <!-- Otomatis Warna Kuning jika milik Kado Koeni, Putih jika Mitra Anda -->
                    <tr class="border-b border-gray-300 {{ $d->pemilik == 'Kado Koeni' ? 'bg-[#ffff00] font-medium' : 'bg-white' }}">
                        <td class="p-2 border-r border-gray-300 font-mono">#{{ $d->id + 8000 }}</td>
                        <td class="p-2 border-r border-gray-300 font-mono font-bold">
                         {{ $d->transaction->kode_nota ?? '-' }}
                         @if(($d->transaction->metode_pembayaran ?? 'Tunai') !== 'Tunai')
                             <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-cyan-600 text-white rounded font-sans">
                                 {{ strtoupper($d->transaction->metode_pembayaran) }} - REKENING
                             </span>
                         @endif
                     </td>
                        <td class="p-2 border-r border-gray-300">{{ $d->nama_item }}</td>
                        <td class="p-2 border-r border-gray-300 text-center text-xs font-bold">{{ $d->pemilik }}</td>
                        <td class="p-2 border-r border-gray-300 text-center">{{ $d->qty }}</td>
                        <td class="p-2 border-r border-gray-300 text-center">{{ $d->satuan ?? 'pcs' }}</td>
                        <td class="p-2 border-r border-gray-300 text-right">Rp{{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                        <td class="p-2 border-r border-gray-300 text-right font-bold">Rp{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td class="p-2 text-center font-mono text-xs">{{ $d->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-6 text-center text-gray-500">Belum ada barang terjual pada tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nonTunaiKado > 0 || $nonTunaiMitra > 0)
        <div class="bg-cyan-50 border border-cyan-300 p-3 rounded text-xs text-cyan-900 flex justify-between items-center">
            <span>ℹ️ <b>Info Pembayaran Non-Tunai (QRIS/Transfer Hari Ini - Tidak Masuk Laci):</b></span>
            <div class="space-x-4 font-bold">
                <span>Rekening Kado Koeni: Rp {{ number_format($nonTunaiKado, 0, ',', '.') }}</span>
                <span>|</span>
                <span>Rekening Mitra Anda: Rp {{ number_format($nonTunaiMitra, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
        
        <!-- TABEL 2: REKAP TOKO, MODAL, PEMASUKAN, PENGELUARAN, TOTAL -->
        <form action="/laporan/cashflow" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="bg-white shadow-sm border border-gray-400 overflow-x-auto mb-6">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-400 font-bold text-gray-800">
                            <th class="p-3 border-r border-gray-300 w-48">TOKO</th>
                            <th class="p-3 border-r border-gray-300 text-right">MODAL (Sisa Kemarin)</th>
                            <th class="p-3 border-r border-gray-300 text-right">PEMASUKAN (Otomatis)</th>
                            <th class="p-3 border-r border-gray-300 text-right">PENGELUARAN</th>
                            <th class="p-3 text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris Kado Koeni -->
                        <tr class="border-b border-gray-300">
                            <td class="p-3 border-r border-gray-300 font-bold bg-yellow-100">KADO KOENI</td>
                            <td class="p-3 border-r border-gray-300 text-right">
                                Rp <input type="number" name="modal_awal_kado" value="{{ $cashflow->modal_awal_kado }}" class="w-32 border border-gray-300 rounded px-2 py-1 text-right font-bold bg-gray-50 focus:bg-white">
                            </td>
                            <td class="p-3 border-r border-gray-300 text-right font-bold">Rp {{ number_format($pemasukanKado, 0, ',', '.') }}</td>
                            <td class="p-3 border-r border-gray-300 text-right text-red-600 font-bold">
                                {{ $pengeluaranKado > 0 ? 'Rp ' . number_format($pengeluaranKado, 0, ',', '.') : 'Rp -' }}
                            </td>
                            <td class="p-3 text-right font-black text-base">Rp {{ number_format($totalKado, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Baris Mitra Anda -->
                        <tr class="border-b border-gray-400">
                            <td class="p-3 border-r border-gray-300 font-bold">MITRA ANDA</td>
                            <td class="p-3 border-r border-gray-300 text-right">
                                Rp <input type="number" name="modal_awal_mitra" value="{{ $cashflow->modal_awal_mitra }}" class="w-32 border border-gray-300 rounded px-2 py-1 text-right font-bold bg-gray-50 focus:bg-white">
                            </td>
                            <td class="p-3 border-r border-gray-300 text-right font-bold">Rp {{ number_format($pemasukanMitra, 0, ',', '.') }}</td>
                            <td class="p-3 border-r border-gray-300 text-right text-red-600 font-bold">
                                {{ $pengeluaranMitra > 0 ? 'Rp ' . number_format($pengeluaranMitra, 0, ',', '.') : 'Rp -' }}
                            </td>
                            <td class="p-3 text-right font-black text-base">Rp {{ number_format($totalMitra, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Baris Total Gabungan -->
                        <tr class="bg-gray-50 font-black">
                            <td colspan="4" class="p-3 border-r border-gray-300">TOTAL UANG DI LACI</td>
                            <td class="p-3 text-right text-lg text-teal-800">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 3 KOTAK BAWAH PERSIS EXCEL: PENGELUARAN, HASIL (SETOR), MODAL (SISA LACI BESOK) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                
                <!-- Kotak 1: Daftar Pengeluaran (Bisa ditambah di bagian bawah) -->
                <div class="bg-white border border-gray-400 shadow-sm">
                    <div class="bg-gray-100 p-2 text-center font-bold border-b border-gray-400 text-sm">Pengeluaran</div>
                    <table class="w-full text-sm border-collapse">
                        <tbody>
                            @forelse($expenses as $exp)
                            <tr class="border-b border-gray-300">
                                <td class="p-2 border-r border-gray-300 font-semibold text-xs">{{ $exp->pemilik }}</td>
                                <td class="p-2 border-r border-gray-300">{{ $exp->keterangan }}</td>
                                <td class="p-2 text-right font-bold">
                                    Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                                    <button type="button" onclick="hapusPengeluaran({{ $exp->id }})" class="ml-2 text-red-500 hover:text-red-700 font-black text-xs" title="Hapus">[X]</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-400 text-xs">Tidak ada pengeluaran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Kotak 2: HASIL (Uang Bulat yang Diambil/Disetor Hari Ini) -->
                <div class="bg-white border border-gray-400 shadow-sm">
                    <div class="bg-gray-100 p-2 text-center font-bold border-b border-gray-400 text-sm">Hasil (Diambil Hari Ini)</div>
                    <table class="w-full text-sm border-collapse">
                        <tr class="border-b border-gray-300">
                            <td class="p-2.5 border-r border-gray-300 font-bold bg-yellow-50">Kado Koeni</td>
                            <td class="p-2 text-right">
                                Rp <input type="number" name="hasil_diambil_kado" value="{{ $cashflow->hasil_diambil_kado }}" class="w-36 border-2 border-amber-400 rounded px-2 py-1 text-right font-extrabold text-slate-900">
                            </td>
                        </tr>
                        <tr class="border-b border-gray-300">
                            <td class="p-2.5 border-r border-gray-300 font-bold">Mitra Anda</td>
                            <td class="p-2 text-right">
                                Rp <input type="number" name="hasil_diambil_mitra" value="{{ $cashflow->hasil_diambil_mitra }}" class="w-36 border-2 border-blue-400 rounded px-2 py-1 text-right font-extrabold text-slate-900">
                            </td>
                        </tr>
                    </table>
                    <div class="p-2 bg-gray-50 text-right">
                        <button type="submit" class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-2 px-4 rounded text-xs shadow transition">
                            💾 SIMPAN BAGI HASIL & HITUNG SISA MODAL
                        </button>
                    </div>
                </div>

                <!-- Kotak 3: MODAL (Sisa Uang di Laci untuk Besok) -->
                <div class="bg-white border border-gray-400 shadow-sm">
                    <div class="bg-gray-100 p-2 text-center font-bold border-b border-gray-400 text-sm">Modal (Sisa di Laci untuk Besok)</div>
                    <table class="w-full text-sm border-collapse">
                        <tr class="border-b border-gray-300">
                            <td class="p-2.5 border-r border-gray-300 font-bold bg-yellow-50">Kado Koeni</td>
                            <td class="p-2.5 text-right font-bold">Rp {{ number_format($sisaModalKado, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-gray-400">
                            <td class="p-2.5 border-r border-gray-300 font-bold">Mitra Anda</td>
                            <td class="p-2.5 text-right font-bold">Rp {{ number_format($sisaModalMitra, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-amber-50 font-black">
                            <td class="p-2.5 border-r border-gray-300 text-xs text-gray-600">Total Menginap di Laci:</td>
                            <td class="p-2.5 text-right text-base text-teal-900">Rp {{ number_format($totalSisaModal, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </form>

        <!-- Form Kecil untuk Tambah Pengeluaran (Misal: Laundry 30.000) -->
        <div class="bg-white p-4 rounded border border-gray-300 shadow-sm mt-4">
            <h3 class="text-xs font-bold uppercase text-gray-600 mb-3">➕ Catat Pengeluaran Toko Hari Ini (Contoh: Laundry, Plastik, dll)</h3>
            <form action="/laporan/pengeluaran" method="POST" class="flex flex-wrap gap-3 items-center">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <select name="pemilik" class="border border-gray-300 rounded px-3 py-1.5 text-sm font-bold">
                    <option value="Kado Koeni">Beban Kado Koeni</option>
                    <option value="Mitra Anda">Beban Mitra Anda</option>
                </select>
                <input type="text" name="keterangan" placeholder="Nama Pengeluaran (misal: Laundry)" required class="border border-gray-300 rounded px-3 py-1.5 text-sm flex-1">
                <input type="number" name="nominal" placeholder="Nominal (Rp)" required class="border border-gray-300 rounded px-3 py-1.5 text-sm w-40">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-1.5 rounded text-sm">Tambah Pengeluaran</button>
            </form>
        </div>

    </div>

    <form id="form-hapus-exp" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <script>
        function hapusPengeluaran(id) {
            if (confirm('Hapus catatan pengeluaran ini?')) {
                let form = document.getElementById('form-hapus-exp');
                form.action = '/laporan/pengeluaran/' + id;
                form.submit();
            }
        }
    </script>

</body>
</html>