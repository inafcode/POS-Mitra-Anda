@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
<style>
    .bar-kontrol {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 32px;
        border-bottom: 1px solid var(--garis);
        padding-bottom: 16px;
    }

    .form-filter {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .input-pencarian {
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 4px;
        padding: 9px 12px;
        font-family: inherit;
        font-size: 15px;
        color: var(--teks);
        width: 300px;
    }

    .select-filter {
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 4px;
        padding: 9px 12px;
        font-family: inherit;
        font-size: 15px;
        color: var(--teks);
    }

    .foto-pratinjau {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 2px;
        border: 1px solid var(--garis);
    }

    .peringatan-stok {
        border-left: 2px solid var(--teks);
        background-color: var(--permukaan);
        padding: 16px 20px;
        font-size: 14px;
        margin-bottom: 48px;
    }

    .btn-aksi-teks {
        background: none;
        border: none;
        color: var(--teks-redup);
        font-family: inherit;
        font-size: 13px;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
    }

    .btn-aksi-teks:hover {
        color: var(--teks);
    }

    .btn-aksi-teks.bahaya:hover {
        color: var(--aksen);
    }

    .paginasi-wadah {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--garis);
    }

    /* 1. Menjinakkan ukuran raksasa SVG bawaan Laravel */
    .paginasi-wadah svg {
        width: 16px;
        height: 16px;
        display: inline-block;
    }

    /* 2. Merapikan tata letak teks dan angka paginasi */
    .paginasi-wadah nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        font-size: 13px;
        color: var(--teks-redup);
    }

    .paginasi-wadah nav p {
        margin: 0;
    }

    /* 3. Gaya untuk tautan angka dan panah */
    .paginasi-wadah a, 
    .paginasi-wadah span[aria-disabled="true"] {
        padding: 4px 8px;
        color: var(--teks-redup);
        text-decoration: none;
    }

    .paginasi-wadah a:hover {
        color: var(--teks);
    }

    /* 4. Menandai halaman yang sedang aktif */
    .paginasi-wadah span[aria-current="page"] > span {
        font-weight: 600;
        color: var(--teks);
        border-bottom: 1px solid var(--teks);
        padding: 4px 8px;
    }

    /* Menyembunyikan elemen bawaan Tailwind yang bertumpuk jika ada */
    .paginasi-wadah .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
        width: 100%;
    }

    /* 1. Atur tinggi wadah tabel agar bisa di-scroll */
    .tabel-wadah {
        max-height: calc(100vh - 260px); /* Menyesuaikan tinggi sisa layar */
        overflow-y: auto;
        border-bottom: 1px solid var(--garis);
    }order-bottom: 1px solid var(--garis);

    /* 2. Buat header tabel tetap menempel di atas saat di-scroll */
    .tabel-wadah thead th {
        position: sticky;
        top: 0;
        background-color: var(--permukaan); /* Warna latar header bawaan desain */
        z-index: 10;
        box-shadow: 0 1px 0 var(--garis); /* Pengganti border-bottom untuk elemen sticky */
    }

    /* 3. Percantik bentuk scrollbar (khusus webkit/Chrome/Edge) */
    .tabel-wadah::-webkit-scrollbar {
        width: 8px;
    }
    .tabel-wadah::-webkit-scrollbar-track {
        background: transparent;
    }
    .tabel-wadah::-webkit-scrollbar-thumb {
        background-color: var(--garis);
        border-radius: 4px;
    }
</style>

<div class="header-halaman">
    <div>
        <h1>Katalog Barang</h1>
        <p class="teks-redup teks-kecil">Kelola daftar produk, jasa, dan ketersediaan stok fisik</p>
    </div>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

<!-- Peringatan Stok Tipis -->
@php
    $stokTipis = $products->filter(function($item) {
        if ($item->tipe !== 'Barang') return false;
        if ($item->pemilik === 'Kado Koeni') {
            return $item->stok <= 0;
        }
        return $item->stok < 5;
    });
@endphp

@if($stokTipis->count() > 0)
<div class="peringatan-stok" style="margin-bottom: 32px;">
    <div style="font-weight: 600; margin-bottom: 8px;">
        ⚠️ Terdapat {{ $stokTipis->count() }} barang yang menipis atau habis
    </div>
    <div class="teks-kecil teks-redup" style="line-height: 1.8; display: flex; flex-wrap: wrap; gap: 8px;">
        @foreach($stokTipis->take(10) as $tipis)
            <a href="/admin/produk/{{ $tipis->id }}/edit" style="color: var(--teks); text-decoration: none; border: 1px solid var(--garis); padding: 2px 8px; border-radius: 4px; background: white;">
                {{ $tipis->nama_item }} <span style="color: {{ $tipis->stok <= 0 ? 'var(--aksen)' : 'var(--teks-redup)' }}; font-weight: bold;">(sisa {{ $tipis->stok }})</span>
            </a>
        @endforeach
        
        @if($stokTipis->count() > 10)
            <span style="padding: 2px 8px; border-radius: 4px; background: transparent; border: 1px dashed var(--garis); color: var(--teks-redup);">
                ...dan {{ $stokTipis->count() - 10 }} barang lainnya
            </span>
        @endif
    </div>
</div>
@endif

<div class="bar-kontrol">
    <form action="/admin/produk" method="GET" class="form-filter">
        <select name="pemilik" onchange="this.form.submit()" class="select-filter">
            <option value="">Semua Toko</option>
            <option value="Mitra Anda" {{ request('pemilik') == 'Mitra Anda' ? 'selected' : '' }}>Mitra Anda</option>
            <option value="Kado Koeni" {{ request('pemilik') == 'Kado Koeni' ? 'selected' : '' }}>Kado Koeni</option>
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau barcode..." class="input-pencarian">
        
        <button type="submit" class="btn-sekunder">Cari</button>
        
        @if(request('search') || request('pemilik'))
            <a href="/admin/produk" class="btn-aksi-teks" style="margin-left: 8px;">Atur Ulang</a>
        @endif
    </form>

    <div style="display: flex; gap: 12px;">
        <!-- INPUT FILE TERSEMBUNYI & TOMBOL IMPORT -->
        <form action="/admin/produk/import" method="POST" enctype="multipart/form-data" id="formImportExcel">
            @csrf
            <input type="file" name="file_excel" id="file_excel" accept=".xlsx, .xls" style="display: none;" onchange="document.getElementById('formImportExcel').submit()">
            <button type="button" class="btn-utama" onclick="document.getElementById('file_excel').click()">
                📥 Import Excel
            </button>
        </form>

        <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA -->
        <a href="/admin/produk/create" class="btn-utama">Tambah Barang Baru</a>
    </div>
</div>

<div class="tabel-wadah">
    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">Foto</th>
                <th style="width: 140px;">Barcode</th>
                <th>Nama Barang & Pemilik</th>
                <th style="width: 130px;">Kategori</th>
                <th class="rata-kanan" style="width: 120px;">Harga Modal</th>
                <th class="rata-kanan" style="width: 120px;">Jual Ecer</th>
                <th class="rata-kanan" style="width: 90px;">Stok</th>
                <th style="width: 110px; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $item)
            <tr>
                <td style="text-align: center; vertical-align: middle;">
                    @if($item->foto_produk)
                        <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="Foto {{ $item->nama_item }}" class="foto-pratinjau">
                    @else
                        <span class="teks-kecil teks-redup">—</span>
                    @endif
                </td>
                <td class="angka teks-redup teks-kecil font-mono">
                    {{ $item->barcode ?? '—' }}
                </td>
                <td>
                    <div style="font-weight: 500;">{{ $item->nama_item }}</div>
                    <div class="teks-kecil teks-redup" style="margin-top: 2px;">{{ $item->pemilik ?? 'Mitra Anda' }}</div>
                </td>
                <td class="teks-kecil teks-redup">
                    <div>{{ $item->tipe }}</div>
                    @if($item->kategori)
                        <div>{{ $item->kategori }}</div>
                    @endif
                </td>
                <td class="rata-kanan angka teks-redup">
                    Rp {{ number_format($item->harga_modal, 0, ',', '.') }}
                </td>
                <td class="rata-kanan angka" style="font-weight: 500;">
                    Rp {{ number_format($item->harga_jual_ecer, 0, ',', '.') }}
                </td>
                <td class="rata-kanan angka">
                    @if($item->tipe == 'Barang')
                        <span style="{{ $item->stok < 5 ? 'font-weight: 600;' : '' }}">
                            {{ $item->stok }} <span class="teks-kecil teks-redup">{{ $item->satuan ?? 'pcs' }}</span>
                        </span>
                    @else
                        <span class="teks-kecil teks-redup">Jasa</span>
                    @endif
                </td>
                <td class="rata-kanan" style="vertical-align: middle;">
                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="/admin/produk/{{ $item->id }}/edit" class="btn-aksi-teks">Ubah</a>
                        <form action="/admin/produk/{{ $item->id }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus permanen barang {{ addslashes($item->nama_item) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-aksi-teks bahaya">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="state-kosong">Tidak ada data barang yang ditemukan.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection