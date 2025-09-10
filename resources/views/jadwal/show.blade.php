@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-6xl mx-auto px-4">

    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg grid place-items-center text-white">
          <i class="ri-calendar-event-line text-xl"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Jadwal: {{ $jadwal->nama_jadwal }}</h1>
          <p class="text-gray-600 text-sm">Total pelajaran: {{ $jadwal->details->count() }}</p>
        </div>
      </div>
      {{-- Tombol kembali --}}
      <a href="{{ route('jadwal.index') }}"
         class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
        <i class="ri-arrow-left-line mr-1"></i> Kembali
      </a>
    </div>

    {{-- Body --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6">
      @php
        $ordered = $jadwal->details
          ->sortBy(fn($x) => [$x->hari, $x->jam_mulai])
          ->groupBy('hari');

        $hariUrut = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
      @endphp

      @if($ordered->isEmpty())
        <div class="text-center py-12">
          <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3">
            <i class="ri-inbox-line text-2xl text-gray-400"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-800">Belum ada pelajaran</h3>
          <p class="text-gray-600 text-sm mt-1">Jadwal ini masih kosong.</p>
        </div>
      @else
        <div class="space-y-6">
          @foreach($hariUrut as $h)
            @if($ordered->has($h))
              <div class="rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 font-semibold">
                      {{ Str::upper(Str::substr($h,0,2)) }}
                    </span>
                    <h2 class="text-sm font-semibold text-gray-800">{{ $h }}</h2>
                  </div>
                  <span class="text-xs text-gray-500"> {{ $ordered[$h]->count() }} pelajaran</span>
                </div>

                <ul class="divide-y divide-gray-200">
                  @foreach($ordered[$h] as $d)
                    <li class="px-4 py-3 flex items-center justify-between">
                      <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $d->mapel }}</p>
                      </div>
                      <div class="text-right">
                        <span class="inline-flex items-center rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium px-2.5 py-1 border border-emerald-200">
                          {{ \Carbon\Carbon::parse($d->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($d->jam_selesai)->format('H:i') }}
                        </span>
                      </div>
                    </li>
                  @endforeach
                </ul>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </div>

  </div>
</div>
@endsection
