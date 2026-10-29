<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir — Mitra Anda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;500&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --latar: #F5EFE6;
            --teks: #292929;
            --teks-redup: #6B6257;
            --garis: #DCD2C2;
            --permukaan: #EDE5D8;
            --aksen: #B8532F;
            --teks-aksen: #FBF7F0;
            --font-judul: 'Fraunces', serif;
            --font-isi: 'Inter', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--latar);
            color: var(--teks);
            font-family: var(--font-isi);
            font-size: 17px;
            line-height: 1.65;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .angka { font-variant-numeric: tabular-nums; }
        .teks-redup { color: var(--teks-redup); }
        .rata-kanan { text-align: right; }

        input, select {
            background: transparent;
            border: 1px solid var(--garis);
            color: var(--teks);
            font-family: inherit;
            font-size: 15px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: outline 120ms ease;
        }
        input:focus, select:focus { outline: 2px solid var(--teks); outline-offset: 2px; border-color: transparent; }
        
        /* Header Kasir */
        .header-kasir {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 40px;
            border-bottom: 1px solid var(--garis);
            flex-shrink: 0;
        }
        .judul-kasir { font-family: var(--font-judul); font-size: 28px; font-weight: 500; }
        .nav-links a { color: var(--teks-redup); text-decoration: none; font-size: 13px; margin-left: 24px; padding-bottom: 2px; }
        .nav-links a:hover { color: var(--teks); border-bottom: 1px solid var(--teks); }

        /* Area Utama: Keranjang Kiri & Katalog Kanan (2:1) */
        .area-utama {
            flex: 1;
            display: grid;
            grid-template-columns: 2fr 1fr;
            overflow: hidden;
        }

        /* Kolom Kiri: Keranjang */
        .kolom-keranjang {
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--garis);
            overflow: hidden;
        }
        .tabel-keranjang-wadah { flex: 1; overflow-y: auto; padding: 0 40px; }
        table { width: 100%; border-collapse: collapse; font-size: 15px; }
        th { text-align: left; padding: 24px 0 12px 0; border-bottom: 1px solid var(--garis); font-size: 13px; color: var(--teks-redup); font-weight: 400; position: sticky; top: 0; background: var(--latar); }
        td { padding: 16px 0; border-bottom: 1px solid var(--garis); vertical-align: middle; }
        tbody tr:hover { background-color: var(--permukaan); }
        
        .btn-hapus { background: none; border: none; color: var(--teks-redup); cursor: pointer; font-size: 15px; }
        .btn-hapus:hover { color: var(--teks); text-decoration: line-through; }

        /* Panel Footer Kiri (Pembayaran) */
        .panel-bawah {
            border-top: 1px solid var(--garis);
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            background-color: var(--latar);
        }
        
        .grup-bayar { display: flex; gap: 32px; align-items: flex-end; }
        .label-kecil { display: block; font-size: 13px; color: var(--teks-redup); margin-bottom: 8px; }
        .angka-total { font-family: var(--font-judul); font-size: 40px; line-height: 1; margin-top: 4px; }
        .angka-kembali { font-family: var(--font-judul); font-size: 28px; line-height: 1; margin-top: 4px; }

        /* Tombol Utama (Satu-satunya warna Terakota) */
        .btn-utama {
            background-color: var(--aksen);
            color: var(--teks-aksen);
            border: none;
            border-radius: 4px;
            padding: 16px 32px;
            font-family: var(--font-judul);
            font-size: 20px;
            cursor: pointer;
            transition: background-color 120ms ease;
        }
        .btn-utama:hover { background-color: #9A4325; }

        /* Kolom Kanan: Daftar Stok */
        .kolom-stok { display: flex; flex-direction: column; overflow: hidden; }
        .header-stok { padding: 24px 40px; border-bottom: 1px solid var(--garis); }
        .input-cari { width: 100%; border: none; border-bottom: 2px solid var(--teks); border-radius: 0; padding: 8px 0; background: transparent; font-size: 17px; font-family: var(--font-judul); }
        .input-cari:focus { outline: none; border-color: var(--aksen); }
        
        .list-stok { flex: 1; overflow-y: auto; padding: 0 40px 40px 40px; list-style: none; }
        .item-stok { display: flex; justify-content: space-between; align-items: baseline; padding: 16px 0; border-bottom: 1px solid var(--garis); cursor: pointer; }
        .item-stok:hover { background-color: var(--permukaan); }
        
        .hasil-pencarian-box { position: absolute; background: var(--latar); border: 1px solid var(--garis); width: 100%; top: 100%; left: 0; z-index: 10; max-height: 300px; overflow-y: auto; display: none; }
        
        /* Utility state */
        .state-kosong { padding: 40px 0; font-size: 15px; color: var(--teks-redup); text-align: center; }

        @media (max-width: 1024px) {
        body {
            height: auto;
            overflow-y: auto;
        }
        .header-kasir {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
        }
        .area-utama {
            grid-template-columns: 1fr; /* Tumpuk keranjang ke atas, katalog ke bawah */
            overflow: visible;
        }
        .kolom-keranjang {
            border-right: none;
            border-bottom: 1px solid var(--garis);
        }
        .tabel-keranjang-wadah {
            padding: 0 20px;
            max-height: 400px;
        }
        .panel-bawah {
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            padding: 24px 20px;
        }
        .grup-bayar {
            width: 100%;
            flex-wrap: wrap;
            gap: 16px;
        }
        .btn-utama {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .kolom-stok {
            overflow: visible;
        }
        .header-stok, .list-stok {
            padding: 20px;
        }
    }
    </style>
</head>
<body>

    <!-- Header Kasir -->
    <header class="header-kasir">
        <div class="judul-kasir">Buku Kasir</div>
        <div style="display: flex; align-items: center; gap: 24px;">
            <div>
                <span class="teks-redup" style="font-size: 13px; margin-right: 8px;">Pembeli:</span>
                <select id="pilih-instansi" style="padding: 4px 8px;">
                    <option value="">Umum (Ritel)</option>
                    @foreach($instansis as $ins)
                        <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                    @endforeach
                </select>
            </div>
            <nav class="nav-links">
                <a href="/admin">Dashboard</a>
                <a href="/laporan">Laporan</a>
            </nav>
        </div>
    </header>

    <main class="area-utama">
        <!-- KIRI: Keranjang Belanja -->
        <div class="kolom-keranjang">
            <div class="tabel-keranjang-wadah">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Barang</th>
                            <th class="rata-kanan" style="width: 120px;">Harga</th>
                            <th style="width: 80px; text-align: center;">Qty</th>
                            <th class="rata-kanan" style="width: 120px;">Subtotal</th>
                            <th style="width: 60px; text-align: center;">Batal</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-keranjang">
                        <!-- Digambar oleh JS -->
                    </tbody>
                </table>
            </div>

            <div class="panel-bawah">
                <div>
                    <span class="label-kecil">Total Tagihan</span>
                    <div id="label-total" class="angka-total angka">Rp 0</div>
                </div>
                
                <div class="grup-bayar">
                    <div>
                        <label class="label-kecil">Metode</label>
                        <select id="pilih-metode" onchange="ubahMetodeBayar()">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Bon / Tempo">Bon Instansi</option>
                        </select>
                    </div>
                    <div>
                        <label id="judul-input-uang" class="label-kecil">Uang Diterima (Rp)</label>
                        <input type="number" id="uang-tunai" oninput="hitungKembalian()" class="angka rata-kanan" style="width: 140px; font-weight: 500;" placeholder="0">
                    </div>
                    <div>
                        <label class="label-kecil">Status / Kembali</label>
                        <div id="label-kembalian" class="angka-kembali angka">Rp 0</div>
                    </div>
                    <button onclick="prosesBayar()" class="btn-utama">Catat & Cetak</button>
                </div>
            </div>
        </div>

        <!-- KANAN: Katalog / Pencarian -->
        <div class="kolom-stok">
            <div class="header-stok" style="position: relative;">
                <input type="text" id="input-barcode" class="input-cari" placeholder="Cari nama barang atau barcode..." autocomplete="off" autofocus>
                <div id="hasil-pencarian" class="hasil-pencarian-box"></div>
            </div>
            
            <!-- List Katalog Gudang -->
            <ul class="list-stok">
                <li style="padding-top: 16px; padding-bottom: 8px; font-size: 13px; color: var(--teks-redup); border-bottom: 1px solid var(--garis);">
                    Katalog Gudang (Klik untuk tambah)
                </li>
                
                @forelse($products as $prod)
                <li class="item-stok" onclick="tambahDariKlik(@js($prod))">
                    <div>
                        <div style="font-weight: 500;">{{ $prod->nama_item }}</div>
                        <div class="teks-redup" style="font-size: 13px;">
                            Stok: <span class="angka">{{ $prod->stok }}</span> {{ $prod->satuan }}
                        </div>
                    </div>
                    <div class="angka rata-kanan" style="font-weight: 500;">
                        Rp {{ number_format($prod->harga_jual_ecer, 0, ',', '.') }}
                    </div>
                </li>
                @empty
                <li class="state-kosong">Tidak ada stok barang yang tersedia.</li>
                @endforelse
            </ul>
        </div>
    </main>

    <!-- Form Tersembunyi Asli -->
    <form id="form-bayar" action="/kasir/cetak" method="POST" target="_blank" style="display: none;">
        @csrf
        <input type="hidden" name="cart_data" id="input-cart-data">
        <input type="hidden" name="total_bayar" id="input-total-bayar">
        <input type="hidden" name="uang_tunai" id="input-uang-tunai">
        <input type="hidden" name="kembalian" id="input-kembalian">
        <input type="hidden" name="metode_pembayaran" id="input-metode-pembayaran">
        <input type="hidden" name="instansi_id" id="input-instansi-id">
    </form>

    <!-- Helper Format & Mesin Kasir -->
    <script>
        const formatUang = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        
        let keranjang = [];
        let totalTagihanSaatIni = 0;
        let hasilPencarianTerakhir = [];

        const inputBarcode = document.getElementById('input-barcode');
        const kotakHasil = document.getElementById('hasil-pencarian');
        const tabelKeranjang = document.getElementById('tabel-keranjang');
        const labelTotal = document.getElementById('label-total');

        function tambahDariKlik(barang) {
            tambahKeKeranjang(barang);
            inputBarcode.value = '';
            inputBarcode.focus();
        }

        // Live Search API (Logika asli dipertahankan)
        inputBarcode.addEventListener('input', function() {
            let kataKunci = this.value.trim();
            if (kataKunci.length < 1) {
                kotakHasil.style.display = 'none';
                kotakHasil.innerHTML = '';
                return;
            }

            fetch(`/api/search-product?q=${encodeURIComponent(kataKunci)}`)
                .then(response => response.json())
                .then(data => {
                    let daftarBarang = Array.isArray(data) ? data : (data && data.id ? [data] : []);
                    hasilPencarianTerakhir = daftarBarang;

                    if (daftarBarang.length === 0) {
                        kotakHasil.innerHTML = `<div style="padding: 16px; color: var(--teks-redup); font-size: 13px;">Tidak ditemukan...</div>`;
                        kotakHasil.style.display = 'block';
                        return;
                    }

                    kotakHasil.innerHTML = '';
                    daftarBarang.forEach(item => {
                        let infoStok = item.tipe === 'Barang' ? `Stok: ${item.stok}` : `Jasa`;
                        let div = document.createElement('div');
                        div.style.cssText = 'padding: 16px 24px; border-bottom: 1px solid var(--garis); cursor: pointer; display: flex; justify-content: space-between; align-items: baseline;';
                        div.innerHTML = `
                            <div>
                                <div style="font-weight: 500;">${item.nama_item}</div>
                                <div style="font-size: 13px; color: var(--teks-redup);">${item.barcode ?? '-'}</div>
                            </div>
                            <div class="rata-kanan">
                                <div class="angka">${formatUang.format(item.harga_jual_ecer)}</div>
                                <div style="font-size: 13px; color: var(--teks-redup);" class="angka">${infoStok}</div>
                            </div>
                        `;
                        div.onmouseenter = () => div.style.backgroundColor = 'var(--permukaan)';
                        div.onmouseleave = () => div.style.backgroundColor = 'transparent';
                        div.onclick = function() {
                            tambahKeKeranjang(item);
                            inputBarcode.value = '';
                            kotakHasil.style.display = 'none';
                            inputBarcode.focus();
                        };
                        kotakHasil.appendChild(div);
                    });
                    kotakHasil.style.display = 'block';
                })
                .catch(err => console.error('Error pencarian:', err));
        });

        inputBarcode.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (hasilPencarianTerakhir.length > 0) {
                    tambahKeKeranjang(hasilPencarianTerakhir[0]);
                    inputBarcode.value = '';
                    kotakHasil.style.display = 'none';
                }
            }
        });

        document.addEventListener('click', function(e) {
            if (!inputBarcode.contains(e.target) && !kotakHasil.contains(e.target)) {
                kotakHasil.style.display = 'none';
            }
        });

        function tambahKeKeranjang(barang) {
            if (barang.tipe === 'Barang' && barang.stok <= 0) return alert(`Stok habis!`);
            let itemAda = keranjang.find(item => item.id === barang.id);
            if (itemAda) {
                if (barang.tipe === 'Barang' && (itemAda.qty + 1) > barang.stok) return alert(`Sisa stok hanya ${barang.stok}.`);
                itemAda.qty += 1;
            } else {
                let barangBaru = Object.assign({}, barang);
                barangBaru.qty = 1;
                keranjang.push(barangBaru);
            }
            gambarUlangTabel();
        }

        function ubahQty(id, qtyBaru) {
            let item = keranjang.find(item => item.id === id);
            if (item) {
                let angkaBaru = parseInt(qtyBaru);
                if (item.tipe === 'Barang' && angkaBaru > item.stok) {
                    alert(`Sisa stok hanya ${item.stok}.`);
                    item.qty = item.stok;
                } else if (angkaBaru <= 0 || isNaN(angkaBaru)) {
                    keranjang = keranjang.filter(i => i.id !== id);
                } else {
                    item.qty = angkaBaru;
                }
            }
            gambarUlangTabel();
        }

        function gambarUlangTabel() {
            tabelKeranjang.innerHTML = '';
            totalTagihanSaatIni = 0;

            if (keranjang.length === 0) {
                tabelKeranjang.innerHTML = `<tr><td colspan="6"><div class="state-kosong">Buku nota masih kosong.</div></td></tr>`;
                labelTotal.innerText = formatUang.format(0);
                hitungKembalian();
                return;
            }

            keranjang.forEach((item, index) => {
                let hargaDipakai = Number(item.harga_jual_ecer);
                if (item.syarat_grosir !== null && item.harga_jual_grosir !== null && item.qty >= item.syarat_grosir) {
                    hargaDipakai = Number(item.harga_jual_grosir);
                }
                let subtotal = hargaDipakai * item.qty;
                totalTagihanSaatIni += subtotal;

                let baris = `
                    <tr>
                        <td class="angka teks-redup">${index + 1}</td>
                        <td style="font-weight: 500;">${item.nama_item}</td>
                        <td class="rata-kanan angka">${formatUang.format(hargaDipakai)}</td>
                        <td style="text-align: center;">
                            <input type="number" value="${item.qty}" min="1" onchange="ubahQty(${item.id}, this.value)" style="width: 56px; text-align: center; padding: 4px;">
                        </td>
                        <td class="rata-kanan angka" style="font-weight: 500;">${formatUang.format(subtotal)}</td>
                        <td style="text-align: center;">
                            <button onclick="ubahQty(${item.id}, 0)" class="btn-hapus">Hapus</button>
                        </td>
                    </tr>
                `;
                tabelKeranjang.innerHTML += baris;
            });
            labelTotal.innerText = formatUang.format(totalTagihanSaatIni);
            hitungKembalian();
        }

        function ubahMetodeBayar() {
            let metode = document.getElementById('pilih-metode').value;
            let inputUang = document.getElementById('uang-tunai');
            let judulUang = document.getElementById('judul-input-uang');

            if (metode === 'Tunai') {
                judulUang.innerText = 'Uang Tunai (Rp)';
                inputUang.readOnly = false;
                inputUang.value = '';
                inputUang.style.backgroundColor = 'transparent';
            } else if (metode === 'Bon / Tempo') {
                judulUang.innerText = 'Dicatat Bon';
                inputUang.value = 0;
                inputUang.readOnly = true;
                inputUang.style.backgroundColor = 'var(--permukaan)';
            } else {
                judulUang.innerText = 'Nominal Pas';
                inputUang.value = totalTagihanSaatIni;
                inputUang.readOnly = true;
                inputUang.style.backgroundColor = 'var(--permukaan)';
            }
            hitungKembalian();
        }

        function hitungKembalian() {
            let metode = document.getElementById('pilih-metode').value;
            let inputUang = document.getElementById('uang-tunai');

            if (metode === 'QRIS' || metode === 'Transfer') inputUang.value = totalTagihanSaatIni;
            else if (metode === 'Bon / Tempo') inputUang.value = 0;

            let uangTunai = parseInt(inputUang.value) || 0;
            let kembalian = uangTunai - totalTagihanSaatIni;
            let labelKembali = document.getElementById('label-kembalian');

            if (metode === 'Bon / Tempo') {
                labelKembali.innerText = 'Utang';
                labelKembali.style.color = 'var(--teks)';
            } else if (metode !== 'Tunai') {
                labelKembali.innerText = 'Lunas';
                labelKembali.style.color = 'var(--teks)';
            } else if (uangTunai === 0) {
                labelKembali.innerText = formatUang.format(0);
                labelKembali.style.color = 'var(--teks)';
            } else if (kembalian < 0) {
                labelKembali.innerText = 'Kurang ' + formatUang.format(Math.abs(kembalian));
                labelKembali.style.color = 'var(--teks-redup)';
            } else {
                labelKembali.innerText = formatUang.format(kembalian);
                labelKembali.style.color = 'var(--teks)';
            }
        }

        function prosesBayar() {
            if (keranjang.length === 0) return alert('Buku nota kosong.');
            let metode = document.getElementById('pilih-metode').value;
            let instansiId = document.getElementById('pilih-instansi').value;
            let uangTunai = parseInt(document.getElementById('uang-tunai').value) || 0;

            if (metode === 'Bon / Tempo' && !instansiId) return alert('Pilih nama instansi terlebih dahulu.');
            if (metode === 'Tunai' && uangTunai < totalTagihanSaatIni) return alert('Uang tunai kurang.');

            if (metode !== 'Tunai' && metode !== 'Bon / Tempo') uangTunai = totalTagihanSaatIni;
            let kembalian = (metode === 'Bon / Tempo') ? 0 : (uangTunai - totalTagihanSaatIni);

            let dataBersih = keranjang.map(item => {
                let hargaDipakai = (item.syarat_grosir !== null && item.harga_jual_grosir !== null && item.qty >= item.syarat_grosir) ? Number(item.harga_jual_grosir) : Number(item.harga_jual_ecer);
                return { id: item.id, nama_item: item.nama_item, qty: item.qty, harga: hargaDipakai, subtotal: hargaDipakai * item.qty };
            });

            document.getElementById('input-cart-data').value = JSON.stringify(dataBersih);
            document.getElementById('input-total-bayar').value = totalTagihanSaatIni;
            document.getElementById('input-uang-tunai').value = uangTunai;
            document.getElementById('input-kembalian').value = kembalian;
            document.getElementById('input-metode-pembayaran').value = metode;
            document.getElementById('input-instansi-id').value = instansiId;

            document.getElementById('form-bayar').submit();

            keranjang = [];
            document.getElementById('pilih-metode').value = 'Tunai';
            document.getElementById('pilih-instansi').value = '';
            ubahMetodeBayar();
            gambarUlangTabel();
            inputBarcode.focus();
        }

        gambarUlangTabel();
    </script>
</body>
</html>