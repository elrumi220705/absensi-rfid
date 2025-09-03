@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-6xl mx-auto px-4">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
          <i class="ri-user-add-line text-xl text-white"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Tambah Data Murid</h1>
          <p class="text-gray-600 text-sm">Daftarkan murid baru dengan kartu RFID</p>
        </div>
      </div>
    </div>

    {{-- Form --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6 mb-8">
      <form id="tambahPesertaForm"
            action="{{ route('peserta.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            autocomplete="off">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- ID Kartu RFID --}}
          <div>
            <label for="id_rfid" class="block text-sm font-medium text-gray-700 mb-2">ID Kartu RFID</label>
            <div class="relative">
              <input
                type="text"
                id="id_rfid"
                name="id_rfid"
                value="{{ old('id_rfid') }}"
                required
                inputmode="numeric"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Tap kartu RFID atau masukkan ID manual"
                onkeydown="return event.key !== 'Enter';"
                autofocus
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="ri-sd-card-line w-5 h-5 text-gray-400"></i>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">Tempelkan kartu ke reader, atau ketik manual bila perlu.</p>
            @error('id_rfid') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Nama --}}
          <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
            <input
              type="text"
              id="nama"
              name="nama"
              value="{{ old('nama') }}"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Masukkan nama lengkap"
            >
            @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Kelas (datalist) --}}
          <div>
            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
            <input
              type="text"
              id="kelas"
              name="kelas"
              list="kelasList"
              value="{{ old('kelas') }}"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Contoh: VII-A, X IPA 1, dll"
            >
            <datalist id="kelasList">
              @foreach(($daftarKelas ?? []) as $kelas)
                <option value="{{ $kelas }}"></option>
              @endforeach
            </datalist>
            @error('kelas') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Jenis Kelamin --}}
          <div>
            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select
              id="jenis_kelamin"
              name="jenis_kelamin"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Pilih Jenis Kelamin</option>
              <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Tanggal Daftar --}}
          <div>
            <label for="tanggal_daftar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Daftar</label>
            <input
              type="date"
              id="tanggal_daftar"
              name="tanggal_daftar"
              value="{{ old('tanggal_daftar', \Carbon\Carbon::today()->format('Y-m-d')) }}"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk otomatis hari ini.</p>
            @error('tanggal_daftar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Foto Profil --}}
          <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <input
              type="file"
              id="foto"
              name="foto"
              accept="image/*"
              class="w-full text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent p-1"
            >
            <p class="mt-1 text-xs text-gray-500">Opsional. Format: JPG/PNG. Maks 2 MB.</p>
            @error('foto') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3 pt-4">
          <a href="{{ route('peserta.index') }}"
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
            <i class="ri-close-line w-4 h-4 inline mr-1"></i> Batal
          </a>
          <button type="submit" id="submitButton"
             class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm transition-all">
            <i class="ri-save-line w-4 h-4 inline mr-1"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- UX kecil untuk input RFID --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const rfidInput = document.getElementById('id_rfid');

    rfidInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); return false; }
    });

    rfidInput.addEventListener('input', function() {
      this.value = this.value.replace(/[\r\n]/g, '');
    });

    rfidInput.addEventListener('change', function() {
      this.value = this.value.replace(/[\r\n]/g, '');
      document.getElementById('nama')?.focus();
    });
  });
</script>
@endsection
