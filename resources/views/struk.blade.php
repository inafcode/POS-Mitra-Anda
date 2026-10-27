<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - Mitra Anda Seponti</title>
    <style>
        /* CSS Khusus untuk Printer Thermal */
        @page { margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 58mm; /* Ukuran standar kertas kasir kecil */
            font-size: 12px; 
            margin: 0 auto; 
            padding: 10px; 
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .garis { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { w-full; border-collapse: collapse; width: 100%; }
        td { vertical-align: top; padding-bottom: 5px; }
        .item-name { display: block; margin-bottom: 2px; }
    </style>
</head>
<!-- Perintah onload="window.print()" akan otomatis membuka jendela print komputer -->
<body onload="window.print();">

    <div class="text-center">
        <h2 class="bold" style="margin: 0; font-size: 16px;">MITRA ANDA SEPONTI</h2>
        <p style="margin: 5px 0;">Jl. Rahadi Oesman Komplek Masjid<br>Telp: 0822-5533-2087</p>
    </div>

    <div class="garis"></div>
    <p style="margin: 0;">Tgl: {{ date('d-m-Y H:i') }}</p>
    <p style="margin: 0;">Kasir: Admin</p>
    <div class="garis"></div>

    <table>
        @foreach($cartData as $item)
        <tr>
            <td colspan="3"><span class="item-name">{{ $item['nama_item'] }}</span></td>
        </tr>
        <tr>
            <td>{{ $item['qty'] }}x</td>
            <td>Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>
            <td class="text-right">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="garis"></div>

    <table>
        <tr class="bold text-right" style="font-size: 14px;">
            <td>TOTAL:</td>
            <td>Rp{{ number_format($totalBayar, 0, ',', '.') }}</td>
        </tr>
        <tr class="text-right">
            <td>TUNAI:</td>
            <td>Rp{{ number_format($uangTunai, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold text-right">
            <td>KEMBALI:</td>
            <td>Rp{{ number_format($kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="garis"></div>
    
    <div class="text-center">
        <p>Terima Kasih Atas<br>Kunjungan Anda</p>
        <p style="font-size: 10px;">Barang yang sudah dibeli<br>tidak dapat dikembalikan</p>
    </div>

</body>
</html>