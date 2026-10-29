@extends('layouts.app')

@section('title', 'Mitra Instansi')

@section('content')
<style>
    .grid-instansi {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 64px;
        align-items: start;
    }

    .form-pendaftaran {
        border-top: 2px solid var(--teks);
        padding-top: 24px;
    }

    .grup-input {
        margin-bottom: 20px;
    }

    .grup-input label {
        display: block;
        font-size: 13px;
        color: var(--teks-redup);
        margin-bottom: 6px;
    }

    .grup-input input,
    .grup-input textarea {
        width: 100%;
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 4px;
        padding: 10px 12px;
        font-family: inherit;
        font-size: 15px;
        color: var(--teks);
        box-sizing: border-box;
    }

    .grup-input input:focus,
    .grup-input textarea:focus {
        outline: 2px solid var(--teks);
        outline-offset: 1px;
        background: #FDFBF7;
    }

    .btn-hapus-instansi {
        background: none;
        border: none;
        color: var(--teks-redup);
        font-family: inherit;
        font-size: 13px;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
    }
    .btn-hapus-instansi:hover {
        color: var(--aksen);
    }

    @media (max-width: 1024px) {
        .grid-instansi {
            grid-template-columns: 1fr;
            gap: 48px;
        }
    }
</style>

<!-- Header Halaman -->
<div class="header-halaman">
    <div>
        <h1>Mitra Instansi</h1>
        <p class="teks-redup teks-kecil">
            Pengelolaan akun pemesan instansi dan buku saldo piutang berjalan
        </p>
    </div>

    <div style="display: flex; gap: 12px;">
        <a href="/admin" class="btn-sekunder">Katalog Barang</a>
        <a href="/kasir" class="btn-sekunder">Buka Kasir</a>
    </div>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="state-error">
    {{ $errors->first() }}
</div>
@endif

<div class="grid-instansi">
    
    <!-- Kolom Kiri: Form Daftarkan Instansi -->
    <div class="form-pendaftaran">
        <h2 style="font-size: 20px; margin-bottom: 8px;">Daftarkan Instansi</h2>
        <p class="teks-kecil teks-redup" style="margin-bottom: 24px;">Buat akun login portal pemesanan mandiri</p>

        <form action="/admin/instansi" method="POST">
            @csrf
            <div class="grup-input">
                <label for="input-name">Nama Instansi / Kantor / Sekolah *</label>
                <input type="text" id="input-name" name="name" placeholder="Contoh: Kantor Desa Seponti" required value="{{ old('name') }}">
            </div>

            <div class="grup-input">
                <label for="input-email">Email Login *</label>
                <input type="email" id="input-email" name="email" placeholder="instansi@mitraanda.com" required value="{{ old('email') }}">
            </div>

            <div class="grup-input">
                <label for="input-password">Kata Sandi Login *</label>
                <input type="text" id="input-password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="grup-input">
                <label for="input-nohp">Nomor WhatsApp PIC / Bendahara</label>
                <input type="text" id="input-nohp" name="no_hp" placeholder="0812xxxx" value="{{ old('no_hp') }}">
            </div>

            <div class="grup-input">
                <label for="input-alamat">Alamat atau Catatan</label>
                <textarea id="input-alamat" name="alamat" rows="2" placeholder="Alamat instansi...">{{ old('alamat') }}</textarea>
            </div>

            <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA PADA LAYAR INI -->
            <button type="submit" class="btn-utama" style="width: 100%; justify-content: center; font-size: 15px; margin-top: 8px;">
                Simpan Akun Instansi
            </button>
        </form>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Instansi Terdaftar -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 20px; border-bottom: 1px solid var(--garis); padding-bottom: 12px;">
            <h2 style="font-size: 20px;">Daftar Rekanan Aktif</h2>
            <span class="teks-kecil teks-redup angka">{{ $instansis->count() }} instansi</span>
        </div>

        <div class="tabel-wadah">
            <table class="tabel-data">
                <thead>
                    <tr>
                        <th>Instansi & Kontak</th>
                        <th>Email Login</th>
                        <th class="rata-kanan">Saldo Piutang</th>
                        <th style="width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instansis as $ins)
                    @php
                        $totalBon = $ins->transactions->sum('total_bayar');
                        $jumlahNota = $ins->transactions->count();
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $ins->name }}</div>
                            <div class="teks-kecil teks-redup" style="margin-top: 2px;">
                                {{ $ins->no_hp ? $ins->no_hp : 'Tanpa nomor' }}
                                @if($ins->alamat) · {{ $ins->alamat }} @endif
                            </div>
                        </td>
                        <td class="teks-kecil teks-redup">
                            {{ $ins->email }}
                        </td>
                        <td class="rata-kanan">
                            <div class="angka font-judul" style="font-size: 17px; font-weight: 500;">
                                Rp {{ number_format($totalBon, 0, ',', '.') }}
                            </div>
                            @if($totalBon > 0)
                                <div class="teks-kecil teks-redup">
                                    {{ $jumlahNota }} nota tertunda
                                </div>
                            @else
                                <div class="teks-kecil teks-redup">
                                    Lunas
                                </div>
                            @endif
                        </td>
                        <td class="rata-kanan">
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                                <a href="/admin/instansi/{{ $ins->id }}/laporan" class="btn-sekunder teks-kecil" style="padding: 4px 10px;">
                                    Buku Tagihan
                                </a>
                                <form action="/admin/instansi/{{ $ins->id }}" method="POST" onsubmit="return confirm('Hapus akun instansi {{ addslashes($ins->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus-instansi">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="state-kosong">Belum ada mitra instansi yang terdaftar di dalam buku kas.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection