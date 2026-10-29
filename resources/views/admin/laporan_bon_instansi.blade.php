@extends('layouts.app')

@section('title', 'Buku Tagihan - ' . $instansi->name)

@section('content')
<style>
    .metrik-baris {
        display: flex;
        gap: 64px;
        align-items: baseline;
        margin-bottom: 48px;
        padding-bottom: 32px;
        border-bottom: 1px solid var(--garis);
    }
    .metrik-item {
        flex: 1;
    }
    .metrik-item:not(:first-child) {
        border-left: 1px solid var(--garis);
        padding-left: 64px;
    }
    .filter-bulan {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }
    .input-bulan {
        background: transparent;
        border: 1px solid var(--garis);
        border-radius: 4px;
        padding: 6px 12px;
        font-family: inherit;
        font-size: 15px;
        color: var(--teks);
    }
    .input-bulan:focus {
        outline: 2px solid var(--teks);
        outline-offset: 1px;
    }
    .aksi-tabel {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 16px;
    }
    .aksi-kanan {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .checkbox-kustom {
        width: 18px;
        height: 18px;
        accent-color: var(--teks);
        cursor: pointer;
    }
</style>

<div class="header-halaman">
    <div>
        <div class="teks-kecil teks-redup" style="text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Buku Tagihan Instansi</div>
        <h1>{{ $instansi->name }}</h1>
        <p class="teks-redup teks-kecil">
            {{ $instansi->no_hp ? 'Telepon: ' . $instansi->no_hp : 'Tanpa nomor telepon' }} 
            @if($instansi->alamat) — {{ $instansi->alamat }} @endif
        </p>
    </div>
    
    <div style="display: flex; gap: 12px;">
        <a href="/admin/instansi/{{ $instansi->id }}/export?bulan={{ $bulanPilih }}" class="btn-sekunder">Ekspor Excel Bulan Ini</a>
        <a href="/admin/instansi" class="btn-sekunder">Kembali</a>
    </div>
</div>

@if(session('success'))
<div style="padding: 14px 20px; border-left: 2px solid var(--teks); background-color: var(--permukaan); font-size: 14px; margin-bottom: 32px;">
    {{ session('success') }}
</div>
@endif

<!-- Barisan Metrik & Filter -->
<div class="metrik-baris">
    <div class="metrik-item">
        <label class="teks-kecil teks-redup uppercase">Periode Tagihan</label>
        <form method="GET" action="/admin/instansi/{{ $instansi->id }}/laporan" class="filter-bulan">
            <input type="month" name="bulan" value="{{ $bulanPilih }}" class="input-bulan">
            <button type="submit" class="btn-sekunder" style="padding: 7px 16px;">Terapkan</button>
        </form>
    </div>

    <div class="metrik-item">
        <div class="teks-kecil teks-redup uppercase mb-1">Total Tagihan Aktif</div>
        <div class="angka font-judul" style="font-size: 32px; color: var(--aksen);">
            Rp {{ number_format($totalHutangAktif, 0, ',', '.') }}
        </div>
        <div class="teks-kecil teks-redup">Termasuk sisa tunggakan bulan sebelumnya</div>
    </div>

    <div class="metrik-item">
        <div class="teks-kecil teks-redup uppercase mb-1">Belanja Bulan Ini</div>
        <div class="angka font-judul" style="font-size: 32px;">
            Rp {{ number_format($totalBelanjaBulanIni, 0, ',', '.') }}
        </div>
        <div class="teks-kecil teks-redup">{{ date('F Y', strtotime($bulanPilih . '-01')) }}</div>
    </div>
</div>

<!-- Form Pelunasan -->
<form action="/admin/instansi/{{ $instansi->id }}/pelunasan-massal" method="POST">
    @csrf
    <input type="hidden" name="bulan" value="{{ $bulanPilih }}">

    <div class="aksi-tabel">
        <div>
            <h2 style="font-size: 20px;">Rincian Transaksi</h2>
            <div class="teks-kecil teks-redup mt-1">
                Total {{ $transactions->count() }} transaksi tercatat bulan ini.
            </div>
        </div>
        
        <div class="aksi-kanan">
            <button type="button" id="btnCheckAll" class="btn-sekunder">Pilih Semua</button>
            <a href="/admin/instansi/{{ $instansi->id }}/kwitansi?bulan={{ $bulanPilih }}" target="_blank" class="btn-sekunder">Cetak Kwitansi</a>
            <!-- SATU-SATUNYA TOMBOL AKSEN TERAKOTA PADA HALAMAN INI -->
            <button type="submit" class="btn-utama">Simpan Pelunasan</button>
        </div>
    </div>

    <div class="tabel-wadah">
        <table class="tabel-data">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">Pilih</th>
                    <th style="width: 140px;">Tanggal & Nota</th>
                    <th>Nama Barang</th>
                    <th style="text-align: center; width: 60px;">Qty</th>
                    <th style="text-align: center; width: 80px;">Satuan</th>
                    <th class="rata-kanan" style="width: 120px;">Harga</th>
                    <th class="rata-kanan" style="width: 140px;">Jumlah</th>
                    <th style="width: 140px;">Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    @foreach($trx->details as $index => $det)
                    <tr style="{{ $trx->status_pembayaran === 'Lunas' ? 'background-color: var(--permukaan);' : '' }}">
                        <!-- Checkbox di baris pertama nota saja -->
                        <td style="text-align: center; vertical-align: middle;">
                            @if($index === 0)
                                <input type="checkbox" name="nota_ids[]" value="{{ $trx->id }}" {{ $trx->status_pembayaran === 'Lunas' ? 'checked' : '' }} class="nota-checkbox checkbox-kustom">
                            @endif
                        </td>

                        <!-- Tanggal & Kode Nota -->
                        <td>
                            @if($index === 0)
                                <div class="angka">{{ date('d/m/Y', strtotime($trx->created_at)) }}</div>
                                <div class="teks-kecil teks-redup angka" style="margin-top: 2px;">{{ $trx->kode_nota }}</div>
                            @endif
                        </td>

                        <!-- Rincian -->
                        <td style="font-weight: 500;">{{ $det->nama_item }}</td>
                        <td class="angka" style="text-align: center;">{{ $det->qty }}</td>
                        <td class="teks-kecil teks-redup" style="text-align: center;">{{ $det->satuan ?? 'pcs' }}</td>
                        <td class="rata-kanan angka teks-redup">Rp {{ number_format($det->harga_jual, 0, ',', '.') }}</td>
                        <td class="rata-kanan angka" style="font-weight: 500;">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</td>

                        <!-- Status & Link Nota -->
                        <td style="vertical-align: middle;">
                            @if($index === 0)
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @if($trx->status_pembayaran === 'Lunas')
                                        <span class="status-grup status-selesai"><span class="status-dot"></span>Lunas</span>
                                    @elseif($trx->status_pembayaran === 'Menunggu Pembayaran')
                                        <span class="status-grup status-proses"><span class="status-dot"></span>Verifikasi</span>
                                    @else
                                        <span class="status-grup status-proses"><span class="status-dot"></span>Perlu proses</span>
                                    @endif
                                    <a href="/admin/nota/instansi/{{ $trx->id }}" target="_blank" class="teks-kecil teks-redup" style="text-decoration: underline;">Lihat Nota</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="8">
                        <div class="state-kosong" style="text-align: center;">Tidak ada transaksi pesanan yang tercatat pada bulan ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.getElementById('btnCheckAll')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.nota-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih';
    });
</script>
@endsection