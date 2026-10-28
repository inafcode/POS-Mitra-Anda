<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Bon - {{ $transaction->instansi->name ?? 'Instansi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8 flex flex-col items-center">

    <!-- Tombol Cetak (Hilang saat diprint) -->
    <div class="no-print mb-6 flex gap-4">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow transition">
            🖨️ Cetak Nota (Print)
        </button>
        <a href="javascript:history.back()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-6 py-2.5 rounded-lg shadow transition">
            ← Kembali
        </a>
    </div>

    <!-- Lembar Nota Utama (Format Kertas A4 / Setengah Halaman) -->
    <div class="bg-white w-[210mm] min-h-[140mm] p-8 shadow-xl border border-gray-300 relative text-slate-900 flex flex-col justify-between">
        
        <div>
            <!-- Header / Kop Toko -->
            <div class="border-b-4 border-slate-900 pb-4 mb-6 flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-slate-900 text-amber-400 font-black text-xl px-2.5 py-1 rounded">MA</span>
                        <h2 class="text-xl font-black tracking-wider uppercase text-slate-900">MITRA ANDA & KADO KOENI</h2>
                    </div>
                    <p class="text-xs text-gray-600 font-medium">
                        <b>MENYEDIAKAN:</b> ATK, Fotokopi, Laminating, Jilid, Print, Cetak Foto, Scan, Materai, Alat Percetakan, dll.<br>
                        <b>MENERIMA PESANAN:</b> Undangan, Buku Yasin, Banner / Spanduk, Poster, Stempel, Kaos Seragam, dll[cite: 4].
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold uppercase bg-slate-100 px-3 py-1 rounded border border-gray-300">Nota: {{ $transaction->kode_nota }}</span>
                    <div class="text-xs text-gray-500 mt-2">Tanggal Cetak: {{ date('d/m/Y') }}</div>
                </div>
            </div>

            <!-- Nama Instansi Tujuan -->
            <div class="mb-4 bg-slate-50 p-3 rounded-lg border border-gray-200 flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase block">Kepada Yth. Instansi / Unit:</span>
                    <h3 class="text-lg font-black text-slate-800 uppercase">{{ $transaction->instansi->name ?? 'Umum' }}</h3>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold text-gray-400 uppercase block">Status Pembayaran:</span>
                    <span class="px-3 py-1 rounded font-black text-xs uppercase {{ $transaction->status_pembayaran === 'Lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $transaction->status_pembayaran }}
                    </span>
                </div>
            </div>

            <!-- Tabel Rincian Barang (Persis Format Excel) -->
            <table class="w-full text-left border-collapse border border-slate-900 text-sm mb-6">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold text-xs uppercase">
                        <th class="border border-slate-900 p-2.5 w-28 text-center">Tanggal</th>
                        <th class="border border-slate-900 p-2.5">Nama Barang</th>
                        <th class="border border-slate-900 p-2.5 w-24 text-center">Banyaknya</th>
                        <th class="border border-slate-900 p-2.5 w-20 text-center">Satuan</th>
                        <th class="border border-slate-900 p-2.5 w-32 text-right">Harga</th>
                        <th class="border border-slate-900 p-2.5 w-36 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->details as $det)
                    <tr class="border border-slate-900">
                        <td class="border border-slate-900 p-2 text-center text-xs font-mono">{{ date('d/m/Y', strtotime($transaction->created_at)) }}</td>
                        <td class="border border-slate-900 p-2 font-semibold">{{ $det->nama_item }}</td>
                        <td class="border border-slate-900 p-2 text-center font-bold">{{ $det->qty }}</td>
                        <td class="border border-slate-900 p-2 text-center text-gray-600">{{ $det->satuan ?? 'pcs' }}</td>
                        <td class="border border-slate-900 p-2 text-right">Rp{{ number_format($det->harga_jual, 0, ',', '.') }}</td>
                        <td class="border border-slate-900 p-2 text-right font-bold">Rp{{ number_format($det->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <!-- Total & Kotak Status LUNAS (Persis Standar Excel King) -->
            <div class="flex justify-between items-end border-t-2 border-slate-900 pt-4">
                <!-- Kotak Status Lunas / Belum Lunas -->
                <div>
                    @if($transaction->status_pembayaran === 'Lunas')
                        <div class="bg-yellow-300 text-slate-900 font-black text-center py-2 px-12 rounded shadow-sm border border-yellow-500 tracking-widest text-base">
                            LUNAS
                        </div>
                    @else
                        <div class="bg-red-500 text-white font-black text-center py-2 px-8 rounded shadow-sm border border-red-700 tracking-wider text-xs">
                            BELUM LUNAS (BON / TEMPO)
                        </div>
                    @endif
                </div>

                <!-- Total Keseluruhan -->
                <div class="border border-slate-900 bg-slate-50 p-3 rounded flex items-center gap-6">
                    <span class="font-black text-sm uppercase text-slate-700">JUMLAH TOTAL:</span>
                    <span class="text-2xl font-black text-blue-600">Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>