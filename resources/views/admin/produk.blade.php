@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
<style>
    .bar-kontrol {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }

    .form-filter {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .input-pencarian {
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 4px;
        padding: 9px 12px;
        font-family: inherit;
        font-size: 15px;
        color: var(--teks);
        min-width: 260px;
    }

    .input-pencarian:focus,
    .select-filter:focus {
        outline: 2px solid var(--teks);
        outline-offset: 1px;
        background: #FDFBF7;
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
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 2px;
        border: 1px solid var(--garis);
    }

    .peringatan-stok {
        border-left: 2px solid var(--teks);
        background-color: var(--permukaan);
        padding: 16px 20px;
        font-size: 14px;
        margin-bottom: 32px;
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
</style>

<!-- Header Halaman -->
<div class="header-halaman">
    <div>
        <h1>Katalog Barang</h1>
        <p class="teks-redup teks-kecil">
            Kelola persediaan barang fisik, jasa, dan pembaruan harga
        </p>
    </div>

    <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA PADA LAYAR INI -->
    <a href="/admin/create" class="btn-utama">Tambah Barang</a>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

<!-- Peringatan Stok Menipis -->
@php
    $stokTipis = \App\Models\Product::where('tipe', 'Barang')->get()->filter(function($item) {
        if ($item->pemilik === 'Kado Koeni') {
            return $item->stok <= 0;
        }
        return $item->stok < 5;
    });
@endphp

@if($stokTipis->count() > 0)
<div class="peringatan-stok">
    <div style="font-weight: 500; margin-bottom: 4px;">
        Peringatan: Ada {{ $stokTipis->count() }} barang yang stoknya menipis atau habis.
    </div>
    <div class="teks-kecil teks-redup">
        @foreach($stokTipis as $st)
            <span>{{ $st->nama_item }} ({{ $st->pemilik }} — sisa {{ $st->stok }})</span>@if(!$loop->last), @endif
        @endforeach
    </div>
</div>
@endif

<!-- Bar Pencarian & Filter Toko -->
<div class="bar-kontrol">
    <form action="/admin/produk" method="GET" class="form-filter" style="width: 100%; justify-content: space-between;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <select name="pemilik" onchange="this.form.submit()" class="select-filter">
                <option value="">Semua Unit Toko</option>
                <option value="Mitra Anda" {{ ($pemilik ?? '') == 'Mitra Anda' ? 'selected' : '' }}>Mitra Anda</option>
                <option value="Kado Koeni" {{ ($pemilik ?? '') == 'Kado Koeni' ? 'selected' : '' }}>Kado Koeni</option>
            </select>

            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama barang atau barcode..." class="input-pencarian">

            <button type="submit" class="btn-sekunder">Cari</button>
            
            @if(!empty($search) || !empty($pemilik))
                <a href="/admin/produk" class="btn-aksi-teks">Atur ulang</a>
            @endif
        </div>

        <span class="teks-kecil teks-redup angka">{{ $products->total() }} barang terdaftar</span>
    </form>
</div>

<!-- Tabel Daftar Barang -->
<div class="tabel-wadah">
    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">Foto</th>
                <th style="width: 130px;">Barcode</th>
                <th>Nama Barang & Toko</th>
                <th style="width: 130px;">Kategori</th>
                <th class="rata-kanan" style="width: 120px;">Harga Modal</th>
                <th class="rata-kanan" style="width: 120px;">Harga Jual</th>
                <th class="rata-kanan" style="width: 100px;">Stok</th>
                <th style="width: 120px; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $item)
            <tr>
                <td style="text-align: center; vertical-align: middle;">
                    @if($item->foto_produk)
                        <img src="{{ asset('storage/' . $item->foto_produk) }}" class="foto-pratinjau" alt="">
                    @else
                        <span class="teks-kecil teks-redup">—</span>
                    @endif
                </td>
                <td class="angka teks-redup teks-kecil font-mono">
                    {{ $item->barcode ?? '—' }}
                </td>
                <td>
                    <div style="font-weight: 500;">{{ $item->nama_item }}</div>
                    <div class="teks-kecil teks-redup" style="margin-top: 2px;">
                        {{ $item->pemilik ?? 'Mitra Anda' }}
                    </div>
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
                <td class="rata-kanan">
                    <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: baseline;">
                        <a href="/admin/{{ $item->id }}/edit" class="btn-aksi-teks">Ubah</a>
                        <form action="/admin/{{ $item->id }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus barang {{ addslashes($item->nama_item) }} dari katalog?')">
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

<div class="paginasi-wadah">
    {{ $products->links() }}
</div>
@endsection