<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Mitra Anda Seponti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Edit Data: {{ $product->nama_item }}</h2>
            <a href="/admin" class="text-gray-500 hover:text-gray-700 font-medium">Batal & Kembali</a>
        </div>

        <!-- Perhatikan action mengarah ke ID barang dan ada method PUT -->
        <form action="/admin/{{ $product->id }}" method="POST" enctype="multipart/form-data">
            @csrf 
            @method('PUT') 
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Ganti Foto -->
             <div class="md:col-span-2 mb-2 p-4 bg-amber-50 border border-amber-100 rounded-lg">
                 <label class="block text-sm font-bold text-amber-800 mb-2">Ganti Foto Produk (Opsional)</label>

                 <!-- Menampilkan cuplikan foto lama jika ada -->
                 @if($product->foto_produk)
                     <div class="mb-3">
                         <p class="text-xs text-amber-700 mb-1 font-medium">Foto saat ini:</p>
                         <img src="{{ asset('storage/' . $product->foto_produk) }}" class="h-24 w-24 object-cover rounded border border-gray-300 shadow-sm">
                     </div>
                 @endif

                 <input type="file" name="foto_produk" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-500 file:text-white hover:file:bg-amber-600">
                 <p class="text-xs text-gray-500 mt-2">*Biarkan kosong jika Anda tidak ingin mengganti foto.</p>
             </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barcode</label>
                    <input type="text" name="barcode" value="{{ $product->barcode }}" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang / Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_item" value="{{ $product->nama_item }}" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                 <label class="block text-sm font-medium text-gray-700 mb-1">Milik Toko <span class="text-red-500">*</span></label>
                 <select name="pemilik" class="w-full border border-gray-300 rounded-md p-2 bg-yellow-50 font-bold text-slate-800">
                     <option value="Mitra Anda" {{ $product->pemilik == 'Mitra Anda' ? 'selected' : '' }}>🏢 Mitra Anda</option>
                     <option value="Kado Koeni" {{ $product->pemilik == 'Kado Koeni' ? 'selected' : '' }}>🎁 Kado Koeni</option>
                 </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Item <span class="text-red-500">*</span></label>
                    <select name="tipe" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Barang" {{ $product->tipe == 'Barang' ? 'selected' : '' }}>Fisik (Barang)</option>
                        <option value="Jasa" {{ $product->tipe == 'Jasa' ? 'selected' : '' }}>Non-Fisik (Jasa)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="kategori" value="{{ $product->kategori }}" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <input type="text" name="satuan" value="{{ $product->satuan }}" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Modal</label>
                    <input type="number" name="harga_modal" value="{{ $product->harga_modal }}" class="w-full border border-gray-300 rounded-md p-2 bg-red-50 text-red-700 font-bold outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Eceran <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_jual_ecer" value="{{ $product->harga_jual_ecer }}" required class="w-full border border-gray-300 rounded-md p-2 bg-blue-50 text-blue-700 font-bold outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Grosir</label>
                    <input type="number" name="harga_jual_grosir" value="{{ $product->harga_jual_grosir }}" class="w-full border border-gray-300 rounded-md p-2 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Syarat Grosir</label>
                    <input type="number" name="syarat_grosir" value="{{ $product->syarat_grosir }}" class="w-full border border-gray-300 rounded-md p-2 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Tersedia</label>
                    <input type="number" name="stok" value="{{ $product->stok }}" class="w-full border border-gray-300 rounded-md p-2 font-bold outline-none">
                </div>
            </div>

            <div class="mt-8 pt-4 border-t text-right">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-md shadow-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</body>
</html>