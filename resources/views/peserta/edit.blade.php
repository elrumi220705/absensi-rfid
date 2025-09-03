{{-- resources/views/peserta/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-6xl mx-auto px-4">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
          <i class="ri-edit-2-line text-xl text-white"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Edit Data Murid</h1>
          <p class="text-gray-600 text-sm">Perbarui informasi murid & kartu RFID</p>
        </div>
      </div>
    </div>

    {{-- Form --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6 mb-8">
      <form id="editPesertaForm"
            action="{{ route('peserta.update', $peserta->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            autocomplete="off">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- RFID --}}
          <div>
            <label for="id_rfid" class="block text-sm font-medium text-gray-700 mb-2">ID Kartu RFID</label>
            <div class="relative">
              <input
                type="text"
                id="id_rfid"
                name="id_rfid"
                value="{{ old('id_rfid', $peserta->id_rfid) }}"
                required
                inputmode="numeric"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Tap kartu RFID atau masukkan ID manual"
                onkeydown="return event.key !== 'Enter';"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="ri-sd-card-line w-5 h-5 text-gray-400"></i>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">Bisa di-scan atau diketik manual.</p>
            @error('id_rfid') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Nama --}}
          <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
            <input
              type="text"
              id="nama"
              name="nama"
              value="{{ old('nama', $peserta->nama) }}"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Masukkan nama lengkap"
            >
            @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Kelas (datalist seperti create) --}}
          <div>
            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
            <input
              type="text"
              id="kelas"
              name="kelas"
              list="kelasList"
              value="{{ old('kelas', $peserta->kelas) }}"
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
          @php
            $jkRaw  = old('jenis_kelamin', $peserta->jenis_kelamin);
            $jkCode = $jkRaw;
            if ($jkRaw === 'Laki-laki')  $jkCode = 'L';
            if ($jkRaw === 'Perempuan')  $jkCode = 'P';
            if (!in_array($jkCode, ['L','P'])) {
              $jkCode = 'L';
            }
          @endphp
          <div>
            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
            <select
              id="jenis_kelamin"
              name="jenis_kelamin"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="L" {{ $jkCode === 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ $jkCode === 'P' ? 'selected' : '' }}>Perempuan</option>
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
              value="{{ old('tanggal_daftar', optional($peserta->tanggal_daftar)->format('Y-m-d')) }}"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <p class="mt-1 text-xs text-gray-500">Biarkan seperti semula jika tidak ingin diubah.</p>
            @error('tanggal_daftar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          </div>

          {{-- Foto Profil --}}
          <div class="md:col-span-2">
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <input
              type="file"
              id="foto"
              name="foto"
              accept="image/*"
              class="w-full text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent p-1"
            >
            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah foto. Maks 2 MB.</p>
            @error('foto') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

            @if($peserta->foto)
              <div class="mt-3">
                <p class="text-xs text-gray-600 mb-1">Foto saat ini:</p>
                <img src="{{ asset('storage/'.$peserta->foto) }}" alt="Foto {{ $peserta->nama }}" class="w-24 h-24 rounded-md object-cover border">
              </div>
            @endif
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3 pt-4">
          <a href="{{ route('peserta.index') }}"
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
            <i class="ri-close-line w-4 h-4 inline mr-1"></i> Batal
          </a>
          <button type="submit"
             class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm transition-all">
            <i class="ri-save-line w-4 h-4 inline mr-1"></i> Simpan Perubahan
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

    rfidInput?.addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); return false; }
    });

    rfidInput?.addEventListener('input', function () {
      this.value = this.value.replace(/[\r\n]/g, '');
    });

    rfidInput?.addEventListener('change', function () {
      this.value = this.value.replace(/[\r\n]/g, '');
      document.getElementById('nama')?.focus();
    });
  });
</script>
@endsection
