<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $instansi->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            color: #292929; 
            padding: 40px; 
            max-width: 800px; 
            margin: auto; 
            font-size: 14px; 
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            border-bottom: 1px solid #292929; 
            padding-bottom: 24px; 
            margin-bottom: 32px; 
        }
        .header h1 { 
            font-family: 'Fraunces', serif; 
            font-size: 24px; 
            font-weight: 600;
            letter-spacing: 0.05em;
            margin: 0 0 8px 0; 
        }
        .header p { margin: 0; font-size: 13px; color: #6B6257; }
        .meta-info { margin-bottom: 24px; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-variant-numeric: tabular-nums; }
        th { border-bottom: 1px solid #292929; padding: 12px 8px; text-align: left; font-weight: 500; font-size: 13px; color: #6B6257; }
        td { border-bottom: 1px solid #DCD2C2; padding: 12px 8px; }
        .text-right { text-align: right; }
        .total-box { 
            margin-top: 24px; 
            border-top: 2px solid #292929; 
            padding-top: 16px; 
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 24px;
        }
        .total-label { font-size: 14px; color: #6B6257; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-angka { font-family: 'Fraunces', serif; font-size: 24px; font-weight: 600; }
        .ttd-box { margin-top: 64px; display: flex; justify-content: space-between; text-align: center; }
        .ttd-box div { width: 40%; }
        .ttd-line { margin-top: 80px; border-bottom: 1px solid #292929; padding-bottom: 4px; font-weight: 500; }
        @media print { 
            body { padding: 0; margin: 0; max-width: 100%; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>BUKU KAS — KWITANSI PEMBAYARAN</h1>
        <p>Toko Mitra Anda Seponti & Kado Koeni</p>
    </div>

    <div class="meta-info">
        <div>
            <div style="color: #6B6257; font-size: 12px; text-transform: uppercase;">Diterima Dari:</div>
            <div style="font-weight: 600; font-size: 16px;">{{ $instansi->name }}</div>
        </div>
        <div class="text-right">
            <div style="color: #6B6257; font-size: 12px; text-transform: uppercase;">Periode Pelunasan:</div>
            <div>{{ date('F Y', strtotime($bulanPilih . '-01')) }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Tanggal Nota</th>
                <th>Kode Referensi (Nota)</th>
                <th class="text-right">Nominal Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
            <tr>
                <td style="color: #6B6257;">{{ $index + 1 }}</td>
                <td>{{ date('d F Y', strtotime($trx->created_at)) }}</td>
                <td style="font-family: monospace; font-size: 13px;">{{ $trx->kode_nota }}</td>
                <td class="text-right">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center; padding: 24px; color: #6B6257;">Tidak ada catatan tagihan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="total-box">
        <div class="total-label">Total Diterima</div>
        <div class="total-angka">Rp {{ number_format($totalPelunasan ?? $transactions->sum('total_bayar'), 0, ',', '.') }}</div>
    </div>
    
    <div class="ttd-box">
        <div>
            <p style="color: #6B6257;">Pihak Penyetor,</p>
            <div class="ttd-line">( ......................................... )</div>
        </div>
        <div>
            <p style="color: #6B6257;">Kasir / Penerima,</p>
            <div class="ttd-line">( ......................................... )</div>
        </div>
    </div>
</body>
</html>