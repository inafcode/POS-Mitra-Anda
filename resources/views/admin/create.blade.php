<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Formulir Tambah Barang/Jasa</h2>
            <a href="/admin" class="text-gray-500 hover:text-gray-700 font-medium">Batal & Kembali</a>
        </div>

        <!-- Form mengarah ke rute /admin dengan metode POST -->
        <form action="/admin" method="POST" enctype="multipart/form-data">
            @csrf <!-- Wajib ada di Laravel sebagai sistem keamanan -->
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload Foto -->
             <div class="md:col-span-2 mb-2 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                 <label class="block text-sm font-bold text-blue-800 mb-2">Foto Produk (Opsional)</label>
                 <input type="file" name="foto_produk" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
             </div>
                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barcode (Opsional)</label>
                    <input type="text" name="barcode" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Nama Item -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang / Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_item" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Tipe Item -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Item <span class="text-red-500">*</span></label>
                    <select name="tipe" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Barang">Fisik (Barang)</option>
                        <option value="Jasa">Non-Fisik (Jasa)</option>
                    </select>
                </div>

                <div>
                 <label class="block text-sm font-medium text-gray-700 mb-1">Milik Toko <span class="text-red-500">*</span></label>
                 <select name="pemilik" class="w-full border border-gray-300 rounded-md p-2 bg-yellow-50 font-bold text-slate-800">
                     <option value="Mitra Anda">🏢 Mitra Anda</option>
                     <option value="Kado Koeni">🎁 Kado Koeni</option>
                 </select>
             </div>

                <!-- Kategori & Satuan -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="kategori" placeholder="Cth: Alat Tulis" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <input type="text" name="satuan" placeholder="Cth: Pcs, Lembar" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <!-- Harga Modal & Jual Ecer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Modal (HPP)</label>
                    <input type="number" name="harga_modal" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none bg-red-50 text-red-700 font-bold">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Eceran <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_jual_ecer" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none bg-blue-50 text-blue-700 font-bold">
                </div>

                <!-- Harga Grosir & Syarat Grosir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Grosir (Opsional)</label>
                    <input type="number" name="harga_jual_grosir" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min. Beli Grosir (Syarat)</label>
                    <input type="number" name="syarat_grosir" placeholder="Cth: 12" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                    <input type="number" name="stok" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                </div>
            </div>

            <div class="mt-8 pt-4 border-t text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition">
                    Simpan Barang ke Gudang
                </button>
            </div>
        </form>
    </div>

</body>
</html>