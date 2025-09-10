@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br py-4">
        <div class="max-w-6xl mx-auto px-4">
            {{-- Header --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="ri-calendar-2-line text-xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Daftar Jadwal</h1>
                            <p class="text-gray-600 text-sm">Kelola jadwal pelajaran untuk setiap kelas</p>
                        </div>
                    </div>

                    <a href="{{ route('jadwal.create') }}"
                       class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl px-5 py-3 font-semibold flex items-center justify-center gap-2 transition-all duration-300 shadow-md">
                        <i class="ri-add-line text-lg"></i>
                        <span>Tambah Jadwal</span>
                    </a>
                </div>
            </div>

            {{-- Kartu daftar --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200/50 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <h3 class="text-lg font-medium text-gray-900">Jadwal Kelas</h3>

                        {{-- (Opsional) tempat search/filter nama_jadwal kalau nanti mau ditambah di controller --}}
                        {{-- <form method="GET" action="{{ route('jadwal.index') }}" class="flex items-center gap-2">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jadwal…"
                                   class="px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500">
                            <button class="px-3 py-2 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700">Cari</button>
                            @if (request()->filled('q'))
                                <a href="{{ route('jadwal.index') }}" class="px-3 py-2 text-sm rounded-md border hover:bg-gray-50">Reset</a>
                            @endif
                        </form> --}}
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pelajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/60 divide-y divide-gray-200/50">
                            @forelse ($jadwals as $index => $j)
                                <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $j->nama_jadwal }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 text-gray-700 px-2.5 py-1">
                                            <i class="ri-book-2-line text-sm"></i> {{ $j->details_count ?? $j->details->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('jadwal.show', $j->id) }}"
                                               class="px-3 py-1.5 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50">
                                                <i class="ri-eye-line inline-block mr-1"></i>
                                            </a>
                                            <a href="{{ route('jadwal.edit', $j->id) }}"
                                               class="px-3 py-1.5 rounded-md border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100">
                                                <i class="ri-edit-2-line inline-block mr-1"></i>
                                            </a>
                                            <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus jadwal {{ $j->nama_jadwal }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-md border border-red-200 text-red-700 bg-red-50 hover:bg-red-100">
                                                    <i class="ri-delete-bin-line inline-block mr-1"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        Belum ada jadwal. Klik <strong>Tambah Jadwal</strong> untuk membuat baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Jika nanti pakai paginate(), aktifkan ini --}}
                {{-- @if ($jadwals instanceof \Illuminate\Pagination\AbstractPaginator && $jadwals->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200/50 bg-gray-50/80">
                        {{ $jadwals->links() }}
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
@endsection
