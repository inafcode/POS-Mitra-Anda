@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<style>
    .form-wadah { max-width: 800px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
    .form-baris-penuh { grid-column: 1 / -1; }
    .grup-input label { display: block; font-size: 13px; color: var(--teks-redup); margin-bottom: 6px; }
    .grup-input input[type="text"], .grup-input input[type="number"], .grup-input select {
        width: 100%; background: transparent; border: 1px solid var(--garis); border-radius: 4px; padding: 10px 12px; font-family: inherit; font-size: 15px; color: var(--teks); box-sizing: border-box;
    }
    .grup-input input:focus, .grup-input select:focus { outline: 2px solid var(--teks); outline-offset: 1px; background: #FDFBF7; }
    .input-berkas { font-size: 13px; color: var(--teks-redup); }
</style>

<div class="header-halaman">
    <div>
        <h1>Tambah Barang</h1>
        <p class="teks-redup teks-kecil">Pencatatan barang fisik atau jasa baru ke dalam katalog toko</p>
    </div>
    <a href="/admin/produk" class="btn-sekunder">Batal & Kembali</a>
</div>

<div class="form-wadah">
    <form action="/admin" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="grup-input form-baris-penuh">
                <label for="foto_produk">Foto Produk (Opsional)</label>
                <input type="file" id="foto_produk" name="foto_produk" accept="image/*" class="input-berkas">
            </div>
            <div class="grup-input">
                <label for="barcode">Kode Barcode (Opsional)</label>
                <input type="text" id="barcode" name="barcode" placeholder="Scan atau ketik barcode" value="{{ old('barcode') }}">
            </div>
            <div class="grup-input">
                <label for="nama_item">Nama Barang / Jasa *</label>
                <input type="text" id="nama_item" name="nama_item" required placeholder="Contoh: Kertas A4 70gr" value="{{ old('nama_item') }}">
            </div>
            <div class="grup-input">
                <label for="tipe">Tipe Item *</label>
                <select id="tipe" name="tipe">
                    <option value="Barang">Fisik (Barang)</option>
                    <option value="Jasa">Non-Fisik (Jasa)</option>
                </select>
            </div>
            <div class="grup-input">
                <label for="pemilik">Milik Toko *</label>
                <select id="pemilik" name="pemilik">
                    <option value="Mitra Anda">Mitra Anda</option>
                    <option value="Kado Koeni">Kado Koeni</option>
                </select>
            </div>
            <div class="grup-input">
                <label for="kategori">Kategori</label>
                <input type="text" id="kategori" name="kategori" placeholder="Contoh: ATK" value="{{ old('kategori') }}">
            </div>
            <div class="grup-input">
                <label for="satuan">Satuan</label>
                <input type="text" id="satuan" name="satuan" placeholder="Contoh: Rim, Pcs" value="{{ old('satuan') }}">
            </div>
            <div class="grup-input">
                <label for="harga_modal">Harga Modal (HPP)</label>
                <input type="number" id="harga_modal" name="harga_modal" placeholder="0" class="angka" value="{{ old('harga_modal') }}">
            </div>
            <div class="grup-input">
                <label for="harga_jual_ecer">Harga Jual Eceran *</label>
                <input type="number" id="harga_jual_ecer" name="harga_jual_ecer" required placeholder="0" class="angka" value="{{ old('harga_jual_ecer') }}">
            </div>
            <div class="grup-input">
                <label for="harga_jual_grosir">Harga Jual Grosir</label>
                <input type="number" id="harga_jual_grosir" name="harga_jual_grosir" placeholder="0" class="angka" value="{{ old('harga_jual_grosir') }}">
            </div>
            <div class="grup-input">
                <label for="syarat_grosir">Min. Beli Grosir (Syarat)</label>
                <input type="number" id="syarat_grosir" name="syarat_grosir" placeholder="Contoh: 10" class="angka" value="{{ old('syarat_grosir') }}">
            </div>
            <div class="grup-input form-baris-penuh">
                <label for="stok">Stok Awal</label>
                <input type="number" id="stok" name="stok" placeholder="0" class="angka" value="{{ old('stok', 0) }}">
            </div>
        </div>
        <div style="border-top: 1px solid var(--garis); padding-top: 24px; text-align: right;">
            <button type="submit" class="btn-utama">Simpan Barang ke Gudang</button>
        </div>
    </form>
</div>
@endsection