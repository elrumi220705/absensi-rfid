@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Daftar Jadwal</h1>
      <a href="{{ route('jadwal.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Tambah Jadwal</a>
    </div>

    <div class="space-y-4">
      @forelse($jadwals as $j)
        <div class="bg-white rounded-lg shadow border p-4 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold">{{ $j->nama_jadwal }}</h2>
            <p class="text-sm text-gray-500">Pelajaran: {{ $j->details_count }}</p>
          </div>
          <div class="flex gap-3">
            <a href="{{ route('jadwal.show',$j) }}" class="text-blue-600 hover:underline">Lihat</a>
            <a href="{{ route('jadwal.edit',$j) }}" class="text-yellow-600 hover:underline">Edit</a>
            <form action="{{ route('jadwal.destroy',$j) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">Hapus</button>
            </form>
          </div>
        </div>
      @empty
        <p class="text-gray-600">Belum ada jadwal.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
