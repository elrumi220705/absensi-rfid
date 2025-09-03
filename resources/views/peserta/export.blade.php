<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Peserta</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        .footer { text-align: right; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <h2>Daftar Peserta</h2>

    <table>
        <thead>
            <tr>
                <th style="width:60px">No</th>
                <th>Nama</th>
                <th style="width:220px">Kelas</th>
                <th style="width:180px">Jenis Kelamin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->kelas ?? '—' }}</td>
                    <td>{{ $p->jenis_kelamin_label ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $currentDateTime }}
    </div>
</body>
</html>
