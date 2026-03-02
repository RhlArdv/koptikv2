@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('menu.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Tambah Menu</h2>
            <p class="text-sm text-gray-500 mt-0.5">Tambah produk baru ke daftar menu Kopi Titik</p>
        </div>
    </div>
@endsection

@section('content')

    <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ============ KOLOM KIRI: Gambar ============ --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Foto Menu</h3>

                    {{-- Preview gambar --}}
                    <div id="preview-container"
                         class="relative w-full aspect-square rounded-xl border-2 border-dashed border-gray-200
                                bg-gray-50 flex flex-col items-center justify-center overflow-hidden mb-3
                                hover:border-amber-400 transition-colors cursor-pointer"
                         onclick="document.getElementById('input-gambar').click()">

                        {{-- Placeholder --}}
                        <div id="placeholder-gambar" class="text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP maks. 2MB</p>
                        </div>

                        {{-- Preview --}}
                        <img id="preview-gambar"
                             src=""
                             alt="Preview"
                             class="hidden absolute inset-0 w-full h-full object-cover">
                    </div>

                    <input type="file"
                           id="input-gambar"
                           name="gambar"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="hidden"
                           onchange="previewGambar(this)">

                    @error('gambar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-gray-400 text-center">
                        Gambar opsional. Jika tidak diisi akan menggunakan foto default.
                    </p>
                </div>
            </div>

            {{-- ============ KOLOM KANAN: Detail Menu ============ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Info Dasar --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Menu</h3>

                    <div class="space-y-4">

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="kategori_id"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                           focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400
                                           @error('kategori_id') border-red-400 @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}"
                                            {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Menu --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Menu <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="nama"
                                   value="{{ old('nama') }}"
                                   placeholder="Contoh: Kopi Susu Titik"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                          focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400
                                          @error('nama') border-red-400 @enderror">
                            @error('nama')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Deskripsi
                                <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <textarea name="deskripsi"
                                      rows="3"
                                      placeholder="Deskripsi singkat menu..."
                                      maxlength="500"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                             focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400
                                             resize-none @error('deskripsi') border-red-400 @enderror">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Harga, Stok, Status --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Harga & Stok</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Harga --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">Rp</span>
                                <input type="number"
                                       name="harga"
                                       value="{{ old('harga') }}"
                                       min="0"
                                       step="500"
                                       placeholder="15000"
                                       class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                              focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400
                                              @error('harga') border-red-400 @enderror">
                            </div>
                            @error('harga')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stok Awal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Stok Awal <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="stok"
                                   value="{{ old('stok', 0) }}"
                                   min="0"
                                   placeholder="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                          focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400
                                          @error('stok') border-red-400 @enderror">
                            @error('stok')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Status Aktif --}}
                    <div class="mt-4 flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_aktif" value="0">
                            <input type="checkbox"
                                   name="is_aktif"
                                   value="1"
                                   {{ old('is_aktif', '1') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-amber-500"></div>
                        </label>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Menu Aktif</p>
                            <p class="text-xs text-gray-400">Menu nonaktif tidak tampil di halaman pemesanan</p>
                        </div>
                    </div>

                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('menu.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200
                              hover:bg-gray-50 rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-amber-500
                                   hover:bg-amber-600 rounded-lg transition-colors">
                        Simpan Menu
                    </button>
                </div>

            </div>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    function previewGambar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-gambar').src = e.target.result;
                document.getElementById('preview-gambar').classList.remove('hidden');
                document.getElementById('placeholder-gambar').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush