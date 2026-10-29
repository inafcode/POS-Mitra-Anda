<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $transaction->kode_nota }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --latar: #F5EFE6;
            --teks: #292929;
            --teks-redup: #6B6257;
            --garis: #DCD2C2;
            --permukaan: #EDE5D8;
        }
        body {
            background-color: var(--latar);
            color: var(--teks);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 14px;
            line-height: 1.6;
        }
        .wadah-nota {
            max-width: 640px;
            width: 100%;
            background-color: #FDFBF7;
            padding: 48px;
            border: 1px solid var(--garis);
            border-radius: 4px;
        }
        .header-nota {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--garis);
            padding-bottom: 24px;
            margin-bottom: 32px;
        }
        .header-nota h1 {
            font-family: 'Fraunces', serif;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 4px 0;
            letter-spacing: 0.02em;
        }
        .info-klien {
            background-color: var(--permukaan);
            padding: 16px 20px;
            border-radius: 2px;
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .info-label { font-size: 12px; color: var(--teks-redup); text-transform: uppercase; margin-bottom: 4px; display: block; }
        table { width: 100%; border-collapse: collapse; font-variant-numeric: tabular-nums; }
        th { border-bottom: 1px solid var(--teks); padding: 12px 8px; text-align: left; font-weight: 500; font-size: 12px; color: var(--teks-redup); }
        td { border-bottom: 1px solid var(--garis); padding: 12px 8px; }
        .rata-kanan { text-align: right; }
        .baris-total {
            display: flex;
            justify-content: flex-end;
            padding-top: 24px;
            margin-top: 16px;
        }
        .total-box {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            width: 280px;
            border-top: 1px solid var(--teks);
            padding-top: 12px;
        }
        .aksi-cetak {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--garis);
            width: 100%;
            max-width: 640px;
        }
        .btn-aksi {
            background-color: transparent;
            border: 1px solid var(--garis);
            color: var(--teks);
            padding: 8px 16px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            border-radius: 2px;
        }
        .btn-aksi:hover { background-color: var(--permukaan); }
        .btn-primer { background-color: var(--teks); color: #FDFBF7; border-color: var(--teks); }
        .btn-primer:hover { background-color: #4A4A4A; }

        @media print {
            body { background: transparent; padding: 0; }
            .wadah-nota { border: none; padding: 0; }
            .aksi-cetak { display: none; }
            .info-klien { background-color: transparent; border: 1px solid var(--garis); }
        }
    </style>
</head>
<body>

    <div class="wadah-nota">
        <div class="header-nota">
            <div>
                <h1>MITRA ANDA & KADO KOENI</h1>
                <div style="font-size: 12px; color: var(--teks-redup);">Pusat Belanja & Kebutuhan Kantor / Instansi</div>
                <div style="font-size: 12px; color: var(--teks-redup);">Seponti, Kalimantan Barat</div>
            </div>
            <div class="rata-kanan">
                <span style="font-size: 11px; text-transform: uppercase; color: var(--teks-redup); border: 1px solid var(--garis); padding: 2px 6px; border-radius: 2px;">Nota Tagihan / Bon</span>
                <div style="font-family: 'Fraunces', serif; font-size: 18px; margin-top: 8px;">{{ $transaction->kode_nota }}</div>
                <div style="font-size: 12px; color: var(--teks-redup);">{{ date('d F Y, H:i', strtotime($transaction->created_at)) }}</div>
            </div>
        </div>

        <div class="info-klien">
            <div>
                <span class="info-label">Klien / Pemesan:</span>
                <div style="font-weight: 500; font-size: 16px;">{{ $transaction->instansi->name ?? 'Pemesan Umum' }}</div>
                <div style="font-size: 13px; color: var(--teks-redup); margin-top: 4px;">{{ $transaction->instansi->no_hp ?? '-' }}</div>
                <div style="font-size: 13px; color: var(--teks-redup);">{{ $transaction->instansi->alamat ?? '-' }}</div>
            </div>
            <div class="rata-kanan">
                <span class="info-label">Status Pembayaran:</span>
                <div style="font-weight: 600; text-transform: uppercase;">
                    {{ $transaction->status_pembayaran }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Barang</th>
                    <th style="text-align: center; width: 60px;">Qty</th>
                    <th style="text-align: center; width: 80px;">Satuan</th>
                    <th class="rata-kanan" style="width: 120px;">Harga Satuan</th>
                    <th class="rata-kanan" style="width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $i => $det)
                <tr>
                    <td style="color: var(--teks-redup);">{{ $i + 1 }}</td>
                    <td style="font-weight: 500;">{{ $det->nama_item }}</td>
                    <td style="text-align: center;">{{ $det->qty }}</td>
                    <td style="text-align: center; color: var(--teks-redup);">{{ $det->satuan ?? 'pcs' }}</td>
                    <td class="rata-kanan text-redup">Rp {{ number_format($det->harga_jual, 0, ',', '.') }}</td>
                    <td class="rata-kanan" style="font-weight: 500;">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="baris-total">
            <div class="total-box">
                <span class="info-label" style="margin: 0; color: var(--teks);">Total Tagihan:</span>
                <span style="font-family: 'Fraunces', serif; font-size: 20px; font-weight: 600;">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Tombol hanya tampil di layar monitor -->
    <div class="aksi-cetak">
        <button onclick="window.close()" class="btn-aksi">Tutup Halaman</button>
        <button onclick="window.print()" class="btn-aksi btn-primer">Cetak Nota</button>
    </div>

</body>
</html>