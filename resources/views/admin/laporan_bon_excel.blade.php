<table>
    <tr>
        <th colspan="7" style="font-size: 16px; font-weight: bold; text-align: left;">REKAP TAGIHAN PIUTANG — {{ strtoupper($instansi->name) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: left; font-size: 11px; color: #555555;">Periode: {{ date('F Y', strtotime($bulanPilih . '-01')) }} | Dicetak: {{ date('d/m/Y H:i') }}</th>
    </tr>
    <tr><td colspan="7"></td></tr>

    <tr style="background-color: #EDE5D8; font-weight: bold; text-align: left;">
        <th style="border: 1px solid #DCD2C2; padding: 6px;">Tanggal</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px;">Kode Nota</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px;">Nama Barang</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px; text-align: center;">Jumlah</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px; text-align: center;">Satuan</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px; text-align: right;">Harga Satuan</th>
        <th style="border: 1px solid #DCD2C2; padding: 6px; text-align: right;">Total (Rp)</th>
    </tr>

    @foreach($transactions as $trx)
        @foreach($trx->details as $det)
        <tr>
            <td style="border: 1px solid #DCD2C2;">{{ date('d/m/Y', strtotime($trx->created_at)) }}</td>
            <td style="border: 1px solid #DCD2C2;">{{ $trx->kode_nota }}</td>
            <td style="border: 1px solid #DCD2C2;">{{ $det->nama_item }}</td>
            <td style="border: 1px solid #DCD2C2; text-align: center;">{{ $det->qty }}</td>
            <td style="border: 1px solid #DCD2C2; text-align: center;">{{ $det->satuan ?? 'pcs' }}</td>
            <td style="border: 1px solid #DCD2C2; text-align: right; mso-number-format:'#,##0';">{{ $det->harga_jual }}</td>
            <td style="border: 1px solid #DCD2C2; text-align: right; mso-number-format:'#,##0';">{{ $det->subtotal }}</td>
        </tr>
        @endforeach
    @endforeach

    <tr><td colspan="7"></td></tr>
    <tr>
        <td colspan="6" style="font-weight: bold; text-align: right; border: 1px solid #DCD2C2;">TOTAL TAGIHAN PERIODE INI:</td>
        <td style="font-weight: bold; text-align: right; border: 1px solid #DCD2C2; mso-number-format:'#,##0';">{{ $transactions->sum('total_bayar') }}</td>
    </tr>
</table>