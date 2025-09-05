@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-6xl mx-auto px-4">

    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
          <i class="ri-calendar-check-line text-xl text-white"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Edit Jadwal</h1>
          <p class="text-gray-600 text-sm">Perbarui jadwal: <span class="font-semibold text-gray-800">{{ $jadwal->nama_jadwal }}</span></p>
        </div>
      </div>
    </div>

    {{-- Form --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6 mb-8">
      <form action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="space-y-6" autocomplete="off" id="jadwalForm">
        @csrf @method('PUT')

        {{-- Nama Jadwal --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nama Jadwal / Kelas</label>
          <div class="relative">
            <input
              type="text"
              name="nama_jadwal"
              value="{{ old('nama_jadwal', $jadwal->nama_jadwal) }}"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Contoh: X-IPA 1"
            >
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <i class="ri-book-2-line w-5 h-5 text-gray-400"></i>
            </div>
          </div>
          @error('nama_jadwal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
          <p class="mt-1 text-xs text-gray-500">Gunakan format konsisten, misal: X-IPA 1, X-IPS 2, XI RPL A, dll.</p>
        </div>

        {{-- Grid header --}}
        <div class="hidden md:grid md:grid-cols-12 text-[11px] font-semibold text-gray-600 tracking-wide">
          <div class="md:col-span-3">HARI</div>
          <div class="md:col-span-5">MATA PELAJARAN</div>
          <div class="md:col-span-2">JAM MULAI</div>
          <div class="md:col-span-2">JAM SELESAI</div>
        </div>

        {{-- Rows --}}
        <div id="rows" class="space-y-4">
          @php $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp

          @forelse($jadwal->details as $d)
            <div class="row-item bg-white/70 rounded-lg border border-gray-200 shadow-sm">
              <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                  {{-- Hari --}}
                  <div class="md:col-span-3">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Hari</label>
                    <div class="relative">
                      <select name="hari[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($days as $day)
                          <option value="{{ $day }}" @selected(old('hari.'.$loop->index, $d->hari) === $day)>{{ $day }}</option>
                        @endforeach
                      </select>
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-arrow-down-s-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  {{-- Mapel --}}
                  <div class="md:col-span-5">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <div class="relative">
                      <input name="mapel[]" value="{{ old('mapel.'.$loop->index, $d->mapel) }}" placeholder="Contoh: Matematika" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-graduation-cap-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  {{-- Jam Mulai --}}
                  <div class="md:col-span-2">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <div class="relative">
                      <input type="time" name="jam_mulai[]" value="{{ old('jam_mulai.'.$loop->index, $d->jam_mulai) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-time-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  {{-- Jam Selesai --}}
                  <div class="md:col-span-2">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <div class="relative">
                      <input type="time" name="jam_selesai[]" value="{{ old('jam_selesai.'.$loop->index, $d->jam_selesai) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-time-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Row action --}}
                <div class="mt-3 flex justify-end">
                  <button type="button" class="remove-row px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                    <i class="ri-close-line mr-1"></i> Hapus
                  </button>
                </div>
              </div>
            </div>
          @empty
            {{-- fallback 1 row kosong --}}
            <div class="row-item bg-white/70 rounded-lg border border-gray-200 shadow-sm">
              <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                  <div class="md:col-span-3">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Hari</label>
                    <div class="relative">
                      <select name="hari[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach($days as $day) <option value="{{ $day }}">{{ $day }}</option> @endforeach
                      </select>
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-arrow-down-s-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>
                  <div class="md:col-span-5">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <div class="relative">
                      <input name="mapel[]" placeholder="Contoh: Matematika" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-graduation-cap-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>
                  <div class="md:col-span-2">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <div class="relative">
                      <input type="time" name="jam_mulai[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-time-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>
                  <div class="md:col-span-2">
                    <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <div class="relative">
                      <input type="time" name="jam_selesai[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="ri-time-line w-5 h-5 text-gray-400"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-3 flex justify-end">
                  <button type="button" class="remove-row px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                    <i class="ri-close-line mr-1"></i> Hapus
                  </button>
                </div>
              </div>
            </div>
          @endforelse
        </div>

        {{-- Add Row --}}
        <div class="pt-2">
          <button type="button" id="addRow" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
            <i class="ri-add-line mr-1"></i> Tambah Baris
          </button>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3 pt-4">
          <a href="{{ route('jadwal.index') }}"
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
            <i class="ri-close-line mr-1"></i> Batal
          </a>
          <button type="submit"
             class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
            <i class="ri-save-line mr-1"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Template baris untuk cloning --}}
<template id="row-template">
  <div class="row-item bg-white/70 rounded-lg border border-gray-200 shadow-sm">
    <div class="p-4">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-3">
          <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Hari</label>
          <div class="relative">
            <select name="hari[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              @foreach($days as $day) <option value="{{ $day }}">{{ $day }}</option> @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <i class="ri-arrow-down-s-line w-5 h-5 text-gray-400"></i>
            </div>
          </div>
        </div>
        <div class="md:col-span-5">
          <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Mata Pelajaran</label>
          <div class="relative">
            <input name="mapel[]" placeholder="Contoh: Bahasa Indonesia" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <i class="ri-graduation-cap-line w-5 h-5 text-gray-400"></i>
            </div>
          </div>
        </div>
        <div class="md:col-span-2">
          <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Mulai</label>
          <div class="relative">
            <input type="time" name="jam_mulai[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <i class="ri-time-line w-5 h-5 text-gray-400"></i>
            </div>
          </div>
        </div>
        <div class="md:col-span-2">
          <label class="md:hidden block text-xs font-medium text-gray-700 mb-1">Jam Selesai</label>
          <div class="relative">
            <input type="time" name="jam_selesai[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <i class="ri-time-line w-5 h-5 text-gray-400"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-3 flex justify-end">
        <button type="button" class="remove-row px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
          <i class="ri-close-line mr-1"></i> Hapus
        </button>
      </div>
    </div>
  </div>
</template>

{{-- JS --}}
<script>
  const rows = document.getElementById('rows');
  const addBtn = document.getElementById('addRow');
  const tpl = document.getElementById('row-template');

  function bindRemove(scope=document){
    scope.querySelectorAll('.remove-row').forEach(btn=>{
      btn.onclick = () => {
        const item = btn.closest('.row-item');
        if (document.querySelectorAll('#rows .row-item').length > 1) {
          item.style.opacity = .0;
          item.style.transform = 'scale(.99)';
          setTimeout(() => item.remove(), 120);
        }
      };
    });
  }

  addBtn?.addEventListener('click', () => {
    const node = tpl.content.firstElementChild.cloneNode(true);
    node.querySelectorAll('input').forEach(i=>i.value='');
    rows.appendChild(node);
    bindRemove(node);
  });

  // validasi sederhana: jam mulai < jam selesai
  document.getElementById('jadwalForm').addEventListener('submit', (e) => {
    const items = document.querySelectorAll('#rows .row-item');
    for (const item of items) {
      const start = item.querySelector('input[name="jam_mulai[]"]').value;
      const end   = item.querySelector('input[name="jam_selesai[]"]').value;
      if (start && end && start >= end) {
        e.preventDefault();
        alert('Jam mulai harus lebih kecil dari jam selesai.');
        item.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
    }
  });

  // init
  bindRemove(document);
</script>
@endsection
