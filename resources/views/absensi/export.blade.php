<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Absensi {{ $materi->nama }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        .footer { text-align: right; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <h2>Daftar Absensi</h2>
    <p><strong>Hari:</strong> {{ $materi->nama }}</p>
    <p><strong>Kelas (Komisi):</strong> {{ $materi->komisi ?? '—' }}</p>

    <table>
        <thead>
            <tr>
                <th style="width:60px">No</th>
                <th>Nama</th>
                <th style="width:200px">Kelas</th>
                <th style="width:160px">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $index => $p)
                @php
                    $a = $p->absensi->first();   // sudah di-eager load & difilter per materi
                    $status = $a?->status ? ucfirst($a->status) : 'Tidak Hadir';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->kelas ?? '—' }}</td>
                    <td>{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $currentDateTime }}
    </div>
</body>
</html>
