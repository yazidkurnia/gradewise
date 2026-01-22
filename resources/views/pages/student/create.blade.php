<h2>Tambah Mahasiswa</h2>

<form action="{{ route('student.store') }}" method="POST">
    @csrf

    <input type="text" name="nim" placeholder="NIM"><br><br>
    <input type="text" name="name" placeholder="Nama"><br><br>
    <input type="text" name="faculty" placeholder="Fakultas"><br><br>
    <input type="text" name="program" placeholder="Jurusan"><br><br>
    <input type="int" name="entry_year" placeholder="Tahun Masuk"><br><br>
    <input type="text" name="status" placeholder="Status"><br><br>

    <button type="submit">Simpan</button>
</form>
