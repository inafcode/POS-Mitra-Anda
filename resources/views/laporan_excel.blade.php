<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>Detail Transaksi ({{ date('d/m/Y', strtotime($tanggal)) }})</h2>
    <table border="1">
        <thead>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <th>Id</th>
                <th>Id Transaksi</th>
                <th>Metode Bayar</th>
                <th>Nama Barang</th>
                <th>Pemilik</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>SubTotal</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $d)
            <tr style="background-color: {{ $d->pemilik == 'Kado Koeni' ? '#ffff00' : '#ffffff' }};">
                <td>#{{ $d->id + 8000 }}</td>
                <td>{{ $d->transaction->kode_nota ?? '-' }}</td>
                <td>{{ $d->transaction->metode_pembayaran ?? 'Tunai' }}</td>
                <td>{{ $d->nama_item }}</td>
                <td>{{ $d->pemilik }}</td>
                <td>{{ $d->qty }}</td>
                <td>{{ $d->satuan ?? 'pcs' }}</td>
                <td>{{ $d->harga_jual }}</td>
                <td>{{ $d->subtotal }}</td>
                <td>{{ $d->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>

    <!-- Tabel Rekap Cashflow Laci -->
    <table border="1">
        <thead>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <th>TOKO</th>
                <th>MODAL</th>
                <th>PEMASUKAN (TUNAI)</th>
                <th>PENGELUARAN</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color: #ffff00; font-weight: bold;">KADO KOENI</td>
                <td>{{ $modalAwalKado }}</td>
                <td>{{ $pemasukanKado }}</td>
                <td>{{ $pengeluaranKado }}</td>
                <td style="font-weight: bold;">{{ $totalKado }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">MITRA ANDA</td>
                <td>{{ $modalAwalMitra }}</td>
                <td>{{ $pemasukanMitra }}</td>
                <td>{{ $pengeluaranMitra }}</td>
                <td style="font-weight: bold;">{{ $totalMitra }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f3f4f6;">
                <td colspan="4">TOTAL UANG DI LACI</td>
                <td>{{ $grandTotal }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <!-- Tabel Hasil & Sisa Modal Besok -->
    <table border="1">
        <thead>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <th colspan="2">Pengeluaran</th>
                <th></th>
                <th colspan="2">Hasil (Diambil)</th>
                <th></th>
                <th colspan="2">Modal (Sisa Laci Besok)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kado Koeni (Total)</td>
                <td>{{ $pengeluaranKado }}</td>
                <td></td>
                <td style="background-color: #ffff00;">Kado Koeni</td>
                <td>{{ $hasilKado }}</td>
                <td></td>
                <td style="background-color: #ffff00;">Kado Koeni</td>
                <td>{{ $sisaModalKado }}</td>
            </tr>
            <tr>
                <td>Mitra Anda (Total)</td>
                <td>{{ $pengeluaranMitra }}</td>
                <td></td>
                <td>Mitra Anda</td>
                <td>{{ $hasilMitra }}</td>
                <td></td>
                <td>Mitra Anda</td>
                <td>{{ $sisaModalMitra }}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="6"></td>
                <td>Total Menginap</td>
                <td>{{ $totalSisaModal }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>