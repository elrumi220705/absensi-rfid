@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow border p-5 mb-6">
      <h1 class="text-xl font-bold">Tambah Jadwal Baru</h1>
    </div>

    <div class="bg-white rounded-xl shadow border p-6">
      <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
          <label class="block text-sm font-medium mb-2">Nama Jadwal / Kelas</label>
          <input type="text" name="nama_jadwal" required class="w-full border rounded-md px-3 py-2" placeholder="Contoh: X-IPA 1" value="{{ old('nama_jadwal') }}">
          @error('nama_jadwal') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div id="rows" class="space-y-4">
          @php $days = ['Senin','Selasa','Rabu','Kamis','Jumat']; @endphp
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
              <label class="block text-sm font-medium mb-1">Hari</label>
              <select name="hari[]" class="w-full border rounded-md px-2 py-2">
                @foreach($days as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Mata Pelajaran</label>
              <input name="mapel[]" class="w-full border rounded-md px-2 py-2" placeholder="Matematika">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Jam Mulai</label>
              <input type="time" name="jam_mulai[]" class="w-full border rounded-md px-2 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Jam Selesai</label>
              <input type="time" name="jam_selesai[]" class="w-full border rounded-md px-2 py-2">
            </div>
          </div>
        </div>

        <button type="button" id="addRow" class="px-3 py-2 bg-green-600 text-white rounded-md">+ Tambah Baris</button>

        <div class="flex justify-end gap-3 pt-4">
          <a href="{{ route('jadwal.index') }}" class="px-4 py-2 border rounded-md">Batal</a>
          <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('addRow').addEventListener('click', () => {
  const tpl = document.querySelector('#rows .grid').cloneNode(true);
  // kosongkan input
  tpl.querySelectorAll('input').forEach(i => i.value = '');
  document.getElementById('rows').appendChild(tpl);
});
</script>
@endsection
