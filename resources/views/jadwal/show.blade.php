@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow border p-5 mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">Jadwal: {{ $jadwal->nama_jadwal }}</h1>
        <p class="text-sm text-gray-600">Total pelajaran: {{ $jadwal->details->count() }}</p>
      </div>
      <a href="{{ route('jadwal.edit',$jadwal) }}" class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>
    </div>

    <div class="bg-white rounded-xl shadow border p-6">
      <div class="space-y-3">
        @forelse($jadwal->details as $d)
          <div class="flex justify-between border-b pb-2">
            <div class="font-medium">{{ $d->hari }} — {{ $d->mapel }}</div>
            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($d->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($d->jam_selesai)->format('H:i') }}</div>
          </div>
        @empty
          <p class="text-gray-600">Belum ada pelajaran.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
