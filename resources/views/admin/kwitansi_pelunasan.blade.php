<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pelunasan - {{ $instansi->name }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; padding: 20px; max-width: 600px; margin: auto; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; }
        .header p { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #ccc; padding: 8px 4px; text-align: left; }
        .text-right { text-align: right; }
        .total-box { margin-top: 20px; border-top: 2px dashed #000; padding-top: 10px; font-size: 16px; font-weight: bold; text-align: right; }
        .ttd-box { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; }
        .ttd-box div { width: 45%; }
        .ttd-line { margin-top: 60px; border-bottom: 1px solid #000; }
        @media print { body { padding: 0; margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>KWITANSI TANDA TERIMA</h2>
        <p><strong>TOKO MITRA ANDA SEPONTI</strong></p>
        <p>Pelunasan Bon Periode: {{ date('F Y', strtotime($bulanPilih . '-01')) }}</p>
    </div>

    <p>Telah terima dari : <strong>{{ $instansi->name }}</strong></p>
    
    <table>
        <thead>
            <tr>
                <th>Tgl Nota</th>
                <th>Kode Nota</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td>{{ date('d/m/Y', strtotime($trx->created_at)) }}</td>
                <td>{{ $trx->kode_nota }}</td>
                <td class="text-right">Rp{{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center;">Belum ada pelunasan di periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="total-box">
        TOTAL DITERIMA: Rp{{ number_format($totalPelunasan, 0, ',', '.') }}
    </div>
    
    <div class="ttd-box">
        <div>
            <p>Penyetor,</p>
            <div class="ttd-line"></div>
        </div>
        <div>
            <p>Kasir / Penerima,</p>
            <div class="ttd-line"></div>
        </div>
    </div>
</body>
</html>