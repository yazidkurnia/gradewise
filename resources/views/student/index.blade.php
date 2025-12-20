<h2>Data Mahasiswa</h2>

<a href="{{ route('student.create') }}">Tambah Mahasiswa</a>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="10">
    <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Fakultas</th>
        <th>Jurusan</th>
        <th>Tahun Masuk</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    @foreach ($students as $std)
        <tr>
            <td>{{ $std->nim }}</td>
            <td>{{ $std->name }}</td>
            <td>{{ $std->faculty }}</td>
            <td>{{ $std->program }}</td>
            <td>{{ $std->entry_year }}</td>
            <td>{{ $std->status }}</td>
            <td>
                <a href="{{ route('student.edit', $std) }}">Edit</a>

                <form action="{{ route('student.destroy', $std) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
