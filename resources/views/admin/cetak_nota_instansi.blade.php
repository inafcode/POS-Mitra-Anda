<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $transaction->kode_nota }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6 flex flex-col items-center">

    <div class="max-w-2xl w-full bg-white p-8 rounded-xl shadow-sm border border-gray-200 space-y-6">
        
        <!-- Header Nota -->
        <div class="flex justify-between items-start border-b pb-4">
            <div>
                <h1 class="text-xl font-black text-slate-900">MITRA ANDA & KADO KOENI</h1>
                <p class="text-xs text-gray-500">Pusat Belanja & Kebutuhan Kantor / Instansi</p>
                <p class="text-xs text-gray-500">Seponti, Kalimantan Barat</p>
            </div>
            <div class="text-right">
                <span class="bg-amber-100 text-amber-800 text-xs font-black px-2.5 py-1 rounded uppercase">Nota B2B / Bon</span>
                <h2 class="text-base font-mono font-bold mt-1 text-slate-800">{{ $transaction->kode_nota }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ date('d/m/Y H:i', strtotime($transaction->created_at)) }}</p>
            </div>
        </div>

        <!-- Info Pelanggan -->
        <div class="bg-slate-50 p-3.5 rounded-lg border border-gray-100 text-xs space-y-1">
            <p><span class="font-bold text-gray-600">Kepada Instansi:</span> <span class="font-black text-slate-800 text-sm">{{ $transaction->instansi->name ?? '-' }}</span></p>
            <p><span class="font-bold text-gray-600">No. HP / PIC:</span> {{ $transaction->instansi->no_hp ?? '-' }}</p>
            <p><span class="font-bold text-gray-600">Alamat:</span> {{ $transaction->instansi->alamat ?? '-' }}</p>
            <p><span class="font-bold text-gray-600">Status Pembayaran:</span> 
                <span class="font-black {{ $transaction->status_pembayaran === 'Lunas' ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ strtoupper($transaction->status_pembayaran) }}
                </span>
            </p>
        </div>

        <!-- Tabel Rincian Barang -->
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-100 text-slate-700 font-bold border-b">
                    <th class="p-2.5">No</th>
                    <th class="p-2.5">Nama Barang</th>
                    <th class="p-2.5 text-center">Qty</th>
                    <th class="p-2.5 text-center">Satuan</th>
                    <th class="p-2.5 text-right">Harga Satuan</th>
                    <th class="p-2.5 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $i => $det)
                <tr class="border-b border-gray-100">
                    <td class="p-2.5 text-center">{{ $i + 1 }}</td>
                    <td class="p-2.5 font-bold text-slate-800">{{ $det->nama_item }}</td>
                    <td class="p-2.5 text-center font-bold">{{ $det->qty }}</td>
                    <td class="p-2.5 text-center text-gray-600">{{ $det->satuan ?? 'pcs' }}</td>
                    <td class="p-2.5 text-right">Rp {{ number_format($det->harga_jual, 0, ',', '.') }}</td>
                    <td class="p-2.5 text-right font-black">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Tagihan -->
        <div class="flex justify-end pt-2">
            <div class="w-64 space-y-1 text-xs">
                <div class="flex justify-between font-black text-sm border-t pt-2">
                    <span>TOTAL TAGIHAN:</span>
                    <span class="text-blue-600">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi (Print & Tutup) -->
        <div class="flex justify-between items-center pt-4 border-t print:hidden">
            <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg text-xs transition">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-xs transition flex items-center gap-1.5">
                🖨️ Cetak / Print Nota
            </button>
        </div>

    </div>

</body>
</html>