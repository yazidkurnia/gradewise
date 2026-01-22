<h2>Edit Mahasiswa</h2>

<form action="{{ route('student.update', $student) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="nim" value="{{ $student->nim }}"><br><br>
    <input type="text" name="name" value="{{ $student->name }}"><br><br>
    <input type="text" name="faculty" value="{{ $student->faculty }}"><br><br>
    <input type="text" name="program" value="{{ $student->program }}"><br><br>
    <input type="int" name="entry_year" value="{{ $student->entry_year }}"><br><br>
    <input type="text" name="status" value="{{ $student->status }}"><br><br>

    <button type="submit">Update</button>
</form>
