@extends('layouts.app')

@section('title', 'Pesanan Instansi')

@section('content')
<style>
    /* Status Dot 6px sesuai panduan desain */
    .dot-status {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }
    .status-menunggu { color: #A87B2A; }
    .status-menunggu .dot-status { background-color: #A87B2A; }
    
    .status-bon { color: #7A6F64; }
    .status-bon .dot-status { background-color: #7A6F64; }

    /* Desain Tabel Minimalis */
    .tabel-rincian {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 12px;
        background-color: var(--permukaan);
        border-radius: 4px;
    }
    .tabel-rincian td {
        padding: 6px 12px;
        border-bottom: 1px solid var(--garis);
    }
    .tabel-rincian tr:last-child td {
        border-bottom: none;
    }

    /* Kontainer Scroll Tabel dengan Sticky Header */
    .wadah-tabel-scroll {
        max-height: 520px;
        overflow-y: auto;
        border-bottom: 1px solid var(--garis);
    }
    .wadah-tabel-scroll table thead th {
        position: sticky;
        top: 0;
        background-color: var(--latar);
        z-index: 10;
        box-shadow: inset 0 -1px 0 var(--garis);
    }

    /* Scrollbar tipis elegan ala ledger */
    .wadah-tabel-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .wadah-tabel-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .wadah-tabel-scroll::-webkit-scrollbar-thumb {
        background-color: var(--garis);
        border-radius: 4px;
    }
</style>

<div class="header-halaman">
    <div>
        <h1>Pesanan Instansi</h1>
        <p class="teks-redup teks-kecil">Daftar tagihan berjalan dan pesanan B2B yang perlu diproses (ACC)</p>
    </div>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

<div class="tabel-wadah">
    <!-- Tambahan wadah scroll dengan batas tinggi 520px -->
    <div class="wadah-tabel-scroll">
        <table class="tabel-data">
            <thead>
                <tr>
                    <th style="width: 120px;">Waktu & Nota</th>
                    <th>Instansi</th>
                    <th style="width: 300px;">Rincian Pesanan</th>
                    <th style="width: 180px;">Status</th>
                    <th class="rata-kanan" style="width: 150px;">Total Tagihan</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $order)
                <tr>
                    <!-- Kolom Waktu & Nota -->
                    <td style="vertical-align: top; padding-top: 16px;">
                        <div class="angka" style="font-weight: 500; font-size: 15px;">{{ $order->kode_nota }}</div>
                        <div class="teks-kecil teks-redup mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    
                    <!-- Kolom Instansi ID -->
                    <td style="vertical-align: top; padding-top: 16px;">
                        <div style="font-weight: 500;">User ID: {{ $order->instansi_id }}</div>
                        <div class="teks-kecil teks-redup mt-1">{{ strtoupper($order->metode_pembayaran) }}</div>
                    </td>
                    
                    <!-- Kolom Rincian -->
                    <td style="vertical-align: top; padding-top: 12px; padding-bottom: 12px;">
                        <table class="tabel-rincian">
                            @foreach($order->details as $item)
                            <tr>
                                <td class="teks-redup">{{ $item->qty }}x</td>
                                <td style="width: 100%;">{{ $item->nama_item }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                    
                    <!-- Kolom Status -->
                    <td style="vertical-align: top; padding-top: 16px; font-weight: 500; font-size: 14px;">
                        @if($order->metode_pembayaran == 'Tunai' || $order->status_pembayaran == 'Menunggu Pembayaran')
                            <div class="status-menunggu">
                                <span class="dot-status"></span>Menunggu Uang Fisik
                            </div>
                        @else
                            <div class="status-bon">
                                <span class="dot-status"></span>Hutang / Bon
                            </div>
                        @endif
                    </td>
                    
                    <!-- Kolom Total -->
                    <td class="rata-kanan angka" style="vertical-align: top; padding-top: 16px; font-weight: 600; font-size: 15px;">
                        Rp {{ number_format($order->total_bayar, 0, ',', '.') }}
                    </td>
                    
                    <!-- Kolom Aksi -->
                    <td style="vertical-align: top; padding-top: 16px; text-align: center;">
                        <form action="/admin/pesanan/{{ $order->id }}/acc" method="POST" onsubmit="return confirm('ACC pesanan ini? Pastikan uang fisik sudah diterima kasir atau ditransfer.')">
                            @csrf
                            <button type="submit" class="btn-sekunder" style="font-size: 12px; padding: 6px 12px;">
                                ACC Lunas
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="state-kosong">Tidak ada pesanan instansi yang perlu diproses saat ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection