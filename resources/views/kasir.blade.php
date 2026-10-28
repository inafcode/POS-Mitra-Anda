<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex flex-col justify-between overflow-hidden">

    <!-- Header & Kotak Pencarian -->
    <header class="bg-slate-900 text-white p-4 shadow-md flex justify-between items-center relative z-20">
        <div>
            <h1 class="text-xl font-bold text-amber-400">POS Mitra Anda Seponti</h1>
            <p class="text-xs text-slate-400">Shift: Pagi | Kasir: Admin</p>
        </div>

        <!-- Area Input Barcode & Live Search Dropdown -->
        <div class="w-1/2 relative">
            <input type="text" id="input-barcode" autofocus autocomplete="off"
                placeholder="Scan Barcode atau Ketik Nama Barang (misal: tinta)..." 
                class="w-full p-3 rounded-lg text-slate-900 font-bold focus:outline-none focus:ring-4 focus:ring-amber-400 shadow-inner">
            
            <!-- Kotak Daftar Pilihan Barang (Muncul Otomatis Saat Mengetik) -->
            <div id="hasil-pencarian" class="absolute left-0 right-0 mt-1 bg-white text-slate-800 rounded-lg shadow-2xl border border-gray-300 max-h-72 overflow-y-auto hidden z-50">
            </div>
        </div>

        <div class="flex gap-2">
            <a href="/admin" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded text-sm font-medium transition">Ke Gudang</a>
            <a href="/" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded text-sm font-medium transition">Lihat Pintu Depan</a>
        </div>
    </header>

    <!-- Area Tabel Keranjang Belanja -->
    <main class="flex-1 p-6 overflow-y-auto">
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-200 text-slate-700 text-sm">
                        <th class="p-3 border-b w-12 text-center">No</th>
                        <th class="p-3 border-b">Nama Barang / Jasa</th>
                        <th class="p-3 border-b w-36 text-right">Harga (Rp)</th>
                        <th class="p-3 border-b w-28 text-center">Qty</th>
                        <th class="p-3 border-b w-36 text-right">Subtotal</th>
                        <th class="p-3 border-b w-20 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-keranjang">
                    <!-- Baris belanjaan akan digambar di sini oleh Javascript -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Form Tersembunyi untuk Mengirim Data ke Struk -->
    <form id="form-bayar" action="/kasir/cetak" method="POST" target="_blank" class="hidden">
        @csrf
        <input type="hidden" name="cart_data" id="input-cart-data">
        <input type="hidden" name="total_bayar" id="input-total-bayar">
        <input type="hidden" name="uang_tunai" id="input-uang-tunai">
        <input type="hidden" name="kembalian" id="input-kembalian">
        <input type="hidden" name="metode_pembayaran" id="input-metode-pembayaran">
    </form>

    <!-- Panel Pembayaran Bawah (Footer) -->
    <footer class="bg-slate-900 text-white p-4 shadow-inner">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-4">
            
            <!-- Info Total Tagihan -->
            <div>
                <span class="text-slate-400 text-xs uppercase tracking-wider block">Total Tagihan</span>
                <span id="label-total" class="text-3xl font-black text-emerald-400">Rp 0</span>
            </div>

            <!-- Input Uang Tunai & Kembalian -->
            <!-- Pilihan Metode Pembayaran, Uang Tunai & Kembalian -->
            <div class="flex items-center gap-5 bg-slate-800 px-5 py-2 rounded-lg border border-slate-700">
                <div>
                    <label class="text-amber-400 text-xs uppercase font-bold block mb-1">Metode Bayar</label>
                    <select id="pilih-metode" onchange="ubahMetodeBayar()" class="bg-slate-900 text-white border border-slate-600 font-bold text-base px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="Tunai">💵 TUNAI (Laci)</option>
                        <option value="QRIS">📱 QRIS (Rekening)</option>
                        <option value="Transfer">🏦 TRANSFER (Rekening)</option>
                    </select>
                </div>
                <div class="border-l border-slate-600 pl-5">
                    <label id="judul-input-uang" class="text-slate-300 text-xs uppercase font-bold block mb-1">Uang Tunai (Rp)</label>
                    <input type="number" id="uang-tunai" oninput="hitungKembalian()" placeholder="0" class="w-36 bg-white text-slate-900 font-extrabold text-xl px-3 py-1 rounded outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="border-l border-slate-600 pl-5">
                    <span class="text-slate-400 text-xs uppercase font-bold block mb-1">Kembalian</span>
                    <span id="label-kembalian" class="text-2xl font-black text-amber-400">Rp 0</span>
                </div>
            </div>

            <!-- Tombol Bayar -->
            <div>
                <button onclick="prosesBayar()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-xl font-black shadow-lg transition">
                    BAYAR & CETAK
                </button>
            </div>

        </div>
    </footer>

    <!-- Mesin Javascript Kasir -->
    <script>
        let keranjang = [];
        let totalTagihanSaatIni = 0;
        let hasilPencarianTerakhir = [];

        const inputBarcode = document.getElementById('input-barcode');
        const kotakHasil = document.getElementById('hasil-pencarian');
        const tabelKeranjang = document.getElementById('tabel-keranjang');
        const labelTotal = document.getElementById('label-total');

        // 1A. Mencari Otomatis Saat Kasir Mengetik (Live Search)
        inputBarcode.addEventListener('input', function() {
            let kataKunci = this.value.trim();

            if (kataKunci.length < 1) {
                kotakHasil.classList.add('hidden');
                kotakHasil.innerHTML = '';
                return;
            }

            fetch(`/api/search-product?q=${encodeURIComponent(kataKunci)}`)
                .then(response => response.json())
                .then(data => {
                    // Antisipasi jika response berupa 1 objek atau array
                    let daftarBarang = Array.isArray(data) ? data : (data && data.id ? [data] : []);
                    hasilPencarianTerakhir = daftarBarang;

                    if (daftarBarang.length === 0) {
                        kotakHasil.innerHTML = `<div class="p-3 text-sm text-red-500 font-medium">Barang "${kataKunci}" tidak ditemukan...</div>`;
                        kotakHasil.classList.remove('hidden');
                        return;
                    }

                    // Tampilkan daftar pilihan barang di bawah kotak pencarian
                    kotakHasil.innerHTML = '';
                    daftarBarang.forEach(item => {
                        let badgeStok = item.tipe === 'Barang' 
                            ? `<span class="text-xs px-2 py-0.5 rounded ${item.stok <= 0 ? 'bg-red-100 text-red-700 font-bold' : 'bg-gray-100 text-gray-600'}">Stok: ${item.stok}</span>`
                            : `<span class="text-xs px-2 py-0.5 rounded bg-purple-100 text-purple-700">Jasa</span>`;

                        let div = document.createElement('div');
                        div.className = 'p-3 border-b hover:bg-amber-50 cursor-pointer flex justify-between items-center transition';
                        div.innerHTML = `
                            <div>
                                <div class="font-bold text-slate-800">${item.nama_item}</div>
                                <div class="text-xs text-gray-400 font-mono">${item.barcode ?? '-'}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">Rp${Number(item.harga_jual_ecer).toLocaleString('id-ID')}</div>
                                ${badgeStok}
                            </div>
                        `;
                        // Jika salah satu barang di daftar diklik, langsung masuk ke keranjang!
                        div.onclick = function() {
                            tambahKeKeranjang(item);
                            inputBarcode.value = '';
                            kotakHasil.classList.add('hidden');
                            inputBarcode.focus();
                        };
                        kotakHasil.appendChild(div);
                    });

                    kotakHasil.classList.remove('hidden');
                })
                .catch(err => console.error('Error pencarian:', err));
        });

        // 1B. Jika Kasir Menekan Tombol ENTER (langsung pilih barang pertama di daftar)
        inputBarcode.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (hasilPencarianTerakhir.length > 0) {
                    tambahKeKeranjang(hasilPencarianTerakhir[0]);
                    inputBarcode.value = '';
                    kotakHasil.classList.add('hidden');
                } else {
                    alert('Barang tidak ditemukan di gudang!');
                }
            }
        });

        // Sembunyikan dropdown jika klik di luar kotak
        document.addEventListener('click', function(e) {
            if (!inputBarcode.contains(e.target) && !kotakHasil.contains(e.target)) {
                kotakHasil.classList.add('hidden');
            }
        });

        // 2. Fungsi Menambah Barang ke Keranjang Belanja
        function tambahKeKeranjang(barang) {
            if (barang.tipe === 'Barang' && barang.stok <= 0) {
                alert(`Stok "${barang.nama_item}" sudah HABIS di gudang!`);
                return;
            }

            let itemAda = keranjang.find(item => item.id === barang.id);
            
            if (itemAda) {
                if (barang.tipe === 'Barang' && (itemAda.qty + 1) > barang.stok) {
                    alert(`Tidak bisa menambah lagi! Sisa stok "${barang.nama_item}" hanya tinggal ${barang.stok} ${barang.satuan ?? ''}.`);
                    return;
                }
                itemAda.qty += 1;
            } else {
                // Salin objek barang agar aman
                let barangBaru = Object.assign({}, barang);
                barangBaru.qty = 1;
                keranjang.push(barangBaru);
            }
            gambarUlangTabel();
        }

        // 3. Fungsi Mengubah Jumlah Barang Secara Manual
        function ubahQty(id, qtyBaru) {
            let item = keranjang.find(item => item.id === id);
            if (item) {
                let angkaBaru = parseInt(qtyBaru);
                
                if (item.tipe === 'Barang' && angkaBaru > item.stok) {
                    alert(`Stok tidak cukup! Sisa stok "${item.nama_item}" hanya ${item.stok}.`);
                    item.qty = item.stok;
                } else if (angkaBaru <= 0 || isNaN(angkaBaru)) {
                    keranjang = keranjang.filter(i => i.id !== id);
                } else {
                    item.qty = angkaBaru;
                }
            }
            gambarUlangTabel();
        }

        // 4. Fungsi Menggambar Ulang Tabel & Menghitung Total
        function gambarUlangTabel() {
            tabelKeranjang.innerHTML = '';
            totalTagihanSaatIni = 0;

            keranjang.forEach((item, index) => {
                let hargaDipakai = Number(item.harga_jual_ecer);
                if (item.syarat_grosir !== null && item.harga_jual_grosir !== null && item.qty >= item.syarat_grosir) {
                    hargaDipakai = Number(item.harga_jual_grosir);
                }

                let subtotal = hargaDipakai * item.qty;
                totalTagihanSaatIni += subtotal;

                let warnaBaris = item.tipe === 'Jasa' ? 'bg-purple-50' : '';
                let namaTampil = item.tipe === 'Jasa' ? `[JASA] ${item.nama_item}` : item.nama_item;

                let baris = `
                    <tr class="border-b hover:bg-gray-50 ${warnaBaris}">
                        <td class="p-3 text-center">${index + 1}</td>
                        <td class="p-3 font-semibold ${item.tipe === 'Jasa' ? 'text-purple-800' : 'text-gray-800'}">
                            ${namaTampil}
                        </td>
                        <td class="p-3 text-right">${hargaDipakai.toLocaleString('id-ID')}</td>
                        <td class="p-3 text-center">
                            <input type="number" value="${item.qty}" min="1" max="${item.tipe === 'Barang' ? item.stok : ''}" onchange="ubahQty(${item.id}, this.value)" class="w-16 border border-gray-400 rounded p-1 text-center font-bold focus:ring-2 focus:ring-blue-500">
                        </td>
                        <td class="p-3 text-right font-bold text-slate-700">${subtotal.toLocaleString('id-ID')}</td>
                        <td class="p-3 text-center">
                            <button onclick="ubahQty(${item.id}, 0)" class="text-red-500 hover:text-red-700 font-bold bg-red-100 px-3 py-1 rounded">X</button>
                        </td>
                    </tr>
                `;
                tabelKeranjang.innerHTML += baris;
            });

            labelTotal.innerText = 'Rp ' + totalTagihanSaatIni.toLocaleString('id-ID');
            hitungKembalian();
        }

        // 5A. Fungsi Saat Kasir Mengubah Metode Pembayaran (Tunai / QRIS / Transfer)
        function ubahMetodeBayar() {
            let metode = document.getElementById('pilih-metode').value;
            let inputUang = document.getElementById('uang-tunai');
            let judulUang = document.getElementById('judul-input-uang');

            if (metode === 'Tunai') {
                judulUang.innerText = 'Uang Tunai (Rp)';
                inputUang.readOnly = false;
                inputUang.classList.remove('bg-slate-300');
                inputUang.classList.add('bg-white');
                inputUang.value = '';
            } else {
                // Jika QRIS atau Transfer, otomatis isi nominal pas & kunci input
                judulUang.innerText = 'Nominal ' + metode + ' (Pas)';
                inputUang.value = totalTagihanSaatIni;
                inputUang.readOnly = true;
                inputUang.classList.remove('bg-white');
                inputUang.classList.add('bg-slate-300');
            }
            hitungKembalian();
        }

        // 5B. Fungsi Menghitung Kembalian Secara Real-Time
        function hitungKembalian() {
            let metode = document.getElementById('pilih-metode').value;
            let inputUang = document.getElementById('uang-tunai');

            // Jika sedang mode QRIS/Transfer dan keranjang berubah, samakan terus angkanya
            if (metode !== 'Tunai') {
                inputUang.value = totalTagihanSaatIni;
            }

            let uangTunai = parseInt(inputUang.value) || 0;
            let kembalian = uangTunai - totalTagihanSaatIni;
            let labelKembali = document.getElementById('label-kembalian');

            if (metode !== 'Tunai') {
                labelKembali.innerText = 'Non-Tunai (Pas)';
                labelKembali.className = 'text-lg font-extrabold text-cyan-400';
            } else if (uangTunai === 0) {
                labelKembali.innerText = 'Rp 0';
                labelKembali.className = 'text-2xl font-black text-amber-400';
            } else if (kembalian < 0) {
                labelKembali.innerText = 'Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
                labelKembali.className = 'text-xl font-bold text-red-400';
            } else {
                labelKembali.innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
                labelKembali.className = 'text-2xl font-black text-amber-400';
            }
        }

        // 6. Fungsi Tombol Bayar (Cetak Struk)
        function prosesBayar() {
            if (keranjang.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }

            let metode = document.getElementById('pilih-metode').value;
            let uangTunai = parseInt(document.getElementById('uang-tunai').value) || 0;

            if (metode === 'Tunai' && uangTunai < totalTagihanSaatIni) {
                alert('Uang tunai kurang dari total tagihan! Silakan masukkan nominal uang yang sesuai.');
                document.getElementById('uang-tunai').focus();
                return;
            }

            if (metode !== 'Tunai') {
                uangTunai = totalTagihanSaatIni;
            }

            let kembalian = uangTunai - totalTagihanSaatIni;

            let dataBersih = keranjang.map(item => {
                let hargaDipakai = (item.syarat_grosir !== null && item.harga_jual_grosir !== null && item.qty >= item.syarat_grosir) 
                    ? Number(item.harga_jual_grosir) 
                    : Number(item.harga_jual_ecer);
                return {
                    id: item.id,
                    nama_item: item.nama_item,
                    qty: item.qty,
                    harga: hargaDipakai,
                    subtotal: hargaDipakai * item.qty
                };
            });

            document.getElementById('input-cart-data').value = JSON.stringify(dataBersih);
            document.getElementById('input-total-bayar').value = totalTagihanSaatIni;
            document.getElementById('input-uang-tunai').value = uangTunai;
            document.getElementById('input-kembalian').value = kembalian;
            document.getElementById('input-metode-pembayaran').value = metode;

            document.getElementById('form-bayar').submit();

            // Reset meja kasir kembali ke mode Tunai untuk pembeli berikutnya
            keranjang = [];
            document.getElementById('pilih-metode').value = 'Tunai';
            ubahMetodeBayar();
            gambarUlangTabel();
            inputBarcode.focus();
        }
    </script>
</body>
</html>