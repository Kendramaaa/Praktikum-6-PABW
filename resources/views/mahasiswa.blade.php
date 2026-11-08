<!DOCTYPE html>
<html>

<head>
    <title>Daftar Mahasiswa</title>
</head>

<body>
    <h2>Daftar Mahasiswa</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Jurusan</th>
            <th>Umur</th>
        </tr>
        @foreach ($mahasiswa as $mhs)
            <tr>
                <td>{{ $mhs->id }}</td>
                <td>{{ $mhs->nama }}</td>
                <td>{{ $mhs->email }}</td>
                <td>{{ $mhs->jurusan }}</td>
                <td>{{ $mhs->umur }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>