@extends('layouts.app')

@section('title', 'Ubah Data Barang')

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
    .pratinjau-lama { display: flex; align-items: center; gap: 16px; padding: 12px; border: 1px solid var(--garis); border-radius: 4px; margin-bottom: 12px; }
    .input-berkas { font-size: 13px; color: var(--teks-redup); }
</style>

<div class="header-halaman">
    <div>
        <h1>Ubah Data Barang</h1>
        <p class="teks-redup teks-kecil">{{ $product->nama_item }}</p>
    </div>
    <a href="/admin/produk" class="btn-sekunder">Batal & Kembali</a>
</div>

<div class="form-wadah">
    <form action="/admin/{{ $product->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="grup-input form-baris-penuh">
                <label>Foto Produk</label>
                @if($product->foto_produk)
                    <div class="pratinjau-lama">
                        <img src="{{ asset('storage/' . $product->foto_produk) }}" style="width: 56px; height: 56px; object-fit: cover; border-radius: 2px; border: 1px solid var(--garis);" alt="">
                        <div class="teks-kecil teks-redup">Foto aktif saat ini. Unggah berkas baru bila ingin menggantinya.</div>
                    </div>
                @endif
                <input type="file" name="foto_produk" accept="image/*" class="input-berkas">
            </div>
            <div class="grup-input">
                <label for="barcode">Kode Barcode</label>
                <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
            </div>
            <div class="grup-input">
                <label for="nama_item">Nama Barang / Jasa *</label>
                <input type="text" id="nama_item" name="nama_item" required value="{{ old('nama_item', $product->nama_item) }}">
            </div>
            <div class="grup-input">
                <label for="tipe">Tipe Item *</label>
                <select id="tipe" name="tipe">
                    <option value="Barang" {{ $product->tipe == 'Barang' ? 'selected' : '' }}>Fisik (Barang)</option>
                    <option value="Jasa" {{ $product->tipe == 'Jasa' ? 'selected' : '' }}>Non-Fisik (Jasa)</option>
                </select>
            </div>
            <div class="grup-input">
                <label for="pemilik">Milik Toko *</label>
                <select id="pemilik" name="pemilik">
                    <option value="Mitra Anda" {{ $product->pemilik == 'Mitra Anda' ? 'selected' : '' }}>Mitra Anda</option>
                    <option value="Kado Koeni" {{ $product->pemilik == 'Kado Koeni' ? 'selected' : '' }}>Kado Koeni</option>
                </select>
            </div>
            <div class="grup-input">
                <label for="kategori">Kategori</label>
                <input type="text" id="kategori" name="kategori" value="{{ old('kategori', $product->kategori) }}">
            </div>
            <div class="grup-input">
                <label for="satuan">Satuan</label>
                <input type="text" id="satuan" name="satuan" value="{{ old('satuan', $product->satuan) }}">
            </div>
            <div class="grup-input">
                <label for="harga_modal">Harga Modal (HPP)</label>
                <input type="number" id="harga_modal" name="harga_modal" class="angka" value="{{ old('harga_modal', $product->harga_modal) }}">
            </div>
            <div class="grup-input">
                <label for="harga_jual_ecer">Harga Jual Eceran *</label>
                <input type="number" id="harga_jual_ecer" name="harga_jual_ecer" required class="angka" value="{{ old('harga_jual_ecer', $product->harga_jual_ecer) }}">
            </div>
            <div class="grup-input">
                <label for="harga_jual_grosir">Harga Jual Grosir</label>
                <input type="number" id="harga_jual_grosir" name="harga_jual_grosir" class="angka" value="{{ old('harga_jual_grosir', $product->harga_jual_grosir) }}">
            </div>
            <div class="grup-input">
                <label for="syarat_grosir">Min. Beli Grosir (Syarat)</label>
                <input type="number" id="syarat_grosir" name="syarat_grosir" class="angka" value="{{ old('syarat_grosir', $product->syarat_grosir) }}">
            </div>
            <div class="grup-input form-baris-penuh">
                <label for="stok">Stok Tersedia</label>
                <input type="number" id="stok" name="stok" class="angka" value="{{ old('stok', $product->stok) }}">
            </div>
        </div>
        <div style="border-top: 1px solid var(--garis); padding-top: 24px; text-align: right;">
            <button type="submit" class="btn-utama">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection