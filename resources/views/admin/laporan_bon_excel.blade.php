<table>
    <!-- Judul Laporan -->
    <tr>
        <th colspan="7" style="font-size: 16px; font-weight: bold; text-align: left;">REKAP BON / PIUTANG - {{ strtoupper($instansi->name) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: left; font-size: 11px; color: #555;">Periode Bulan: {{ date('F Y', strtotime($bulanPilih . '-01')) }} | Mitra Anda & Kado Koeni Seponti</th>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- Header Tabel -->
    <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 11px;">
        <th style="border: 1px solid #000; padding: 5px;">TANGGAL</th>
        <th style="border: 1px solid #000; padding: 5px;">KODE NOTA</th>
        <th style="border: 1px solid #000; padding: 5px;">NAMA BARANG</th>
        <th style="border: 1px solid #000; padding: 5px;">JUMLAH</th>
        <th style="border: 1px solid #000; padding: 5px;">SATUAN</th>
        <th style="border: 1px solid #000; padding: 5px;">HARGA SATUAN</th>
        <th style="border: 1px solid #000; padding: 5px;">TOTAL (RP)</th>
    </tr>

    <!-- Isi Data Detail Per Item Barang -->
    @foreach($transactions as $trx)
        @foreach($trx->details as $det)
        <tr style="font-size: 11px;">
            <td style="border: 1px solid #000; text-align: center;">{{ date('d/m/Y', strtotime($trx->created_at)) }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $trx->kode_nota }}</td>
            <td style="border: 1px solid #000; text-align: left;">{{ $det->nama_item }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $det->qty }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $det->satuan ?? 'pcs' }}</td>
            <!-- Format Angka Rupiah -->
            <td style="border: 1px solid #000; text-align: right; mso-number-format:'#,##0';">Rp {{ number_format($det->harga_jual, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right; mso-number-format:'#,##0';">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    @endforeach

    <!-- Baris Kosong Pemisah -->
    <tr><td colspan="7"></td></tr>

    <!-- Total Keseluruhan -->
    <tr>
        <td colspan="6" style="font-weight: bold; text-align: right; border: 1px solid #000; font-size: 11px;">TOTAL KESELURUHAN TAGIHAN BULAN INI:</td>
        <td style="font-weight: bold; text-align: right; border: 1px solid #000; font-size: 11px; mso-number-format:'#,##0';">Rp {{ number_format($transactions->sum('total_bayar'), 0, ',', '.') }}</td>
    </tr>
</table>