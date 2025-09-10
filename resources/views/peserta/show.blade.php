{{-- resources/views/peserta/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br py-4">
  <div class="max-w-5xl mx-auto px-4">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
            <i class="ri-user-3-line text-xl text-white"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Detail Murid</h1>
            <p class="text-gray-600 text-sm">Informasi & Akun Login Siswa</p>
          </div>
        </div>
        <a href="{{ route('peserta.index') }}"
           class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50">
          Kembali
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      {{-- Kartu Biodata --}}
      <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6">
        <div class="flex items-start gap-4">
          <div>
            @if($peserta->foto)
              <img src="{{ asset('storage/'.$peserta->foto) }}" class="w-28 h-28 rounded-xl object-cover border" alt="Foto {{ $peserta->nama }}">
            @else
              <div class="w-28 h-28 rounded-xl bg-gray-200 flex items-center justify-center text-gray-400">
                <i class="ri-user-line text-3xl"></i>
              </div>
            @endif
          </div>
          <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900">{{ $peserta->nama }}</h2>
            <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
              <div>
                <div class="text-gray-500">ID RFID</div>
                <div class="font-mono text-gray-900">{{ $peserta->id_rfid }}</div>
              </div>
              <div>
                <div class="text-gray-500">Kelas</div>
                <div class="text-gray-900">{{ $peserta->kelas ?? '—' }}</div>
              </div>
              <div>
                <div class="text-gray-500">Jenis Kelamin</div>
                <div class="text-gray-900">{{ $peserta->jenis_kelamin_label }}</div>
              </div>
              <div>
                <div class="text-gray-500">Tanggal Daftar</div>
                <div class="text-gray-900">{{ optional($peserta->tanggal_daftar)->format('d M Y') ?? '—' }}</div>
              </div>
            </div>
            <div class="mt-4">
              <a href="{{ route('peserta.edit', $peserta->id) }}"
                 class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                <i class="ri-edit-line"></i> Edit Data
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Kartu Akun Siswa --}}
      <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Akun Login Siswa</h3>

        @if($peserta->account)
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-500">Status</span>
              <span class="px-2 py-1 rounded-full text-xs {{ $peserta->account->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $peserta->account->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-500">Login ID</span>
              <span class="font-mono text-gray-900">{{ $peserta->account->login_id }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-500">Last Login</span>
              <span class="text-gray-900">{{ optional($peserta->account->last_login_at)->format('d M Y H:i') ?? '—' }}</span>
            </div>
          </div>

          {{-- (Opsional) Form reset password cepat --}}
          <div class="mt-4 border-t pt-4">
            <form action="{{ route('siswa.account.store', $peserta->id) }}" method="POST" class="space-y-3">
              @csrf
              <input type="hidden" name="login_id" value="{{ $peserta->account->login_id }}">
              <div>
                <label class="block text-sm text-gray-700 mb-1">Password Baru</label>
                <input type="text" name="password"
                       class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
                       placeholder="mis. 123456">
              </div>
              <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                Simpan (Setel Ulang Password)
              </button>
              <p class="text-xs text-gray-500 mt-1">Tombol ini menimpa password dengan nilai baru di atas.</p>
            </form>
          </div>
        @else
          <p class="text-sm text-gray-600 mb-3">Murid ini belum memiliki akun untuk login di aplikasi mobile.</p>
          <form action="{{ route('siswa.account.store', $peserta->id) }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-sm text-gray-700 mb-1">Login ID</label>
              <input type="text" name="login_id" value="{{ $peserta->id_rfid }}"
                     class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500" required>
              <p class="text-xs text-gray-500 mt-1">Default gunakan ID RFID agar mudah diingat.</p>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Password Awal</label>
              <input type="text" name="password" value="123456"
                     class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
              Buat Akun Login
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
