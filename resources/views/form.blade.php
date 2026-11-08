<!DOCTYPE html>
<html>

<head>
    <title>Form Input Mahasiswa</title>
</head>

<body>
    <h2>Input Data Mahasiswa</h2>
    <form action="/simpan" method="POST">
        @csrf
        <label>Nama: </label><br>
        <input type="text" name="nama"><br><br>
        <label>Email:</label><br>
        <input type="text" name="email"><br><br>
        <label>Jurusan: </label><br>
        <input type="text" name="jurusan"><br><br>
        <label>Umur: </label><br>
        <input type="text" name="umur"><br><br>
        <button type="submit">Simpan</button>
    </form>
</body>

</html>