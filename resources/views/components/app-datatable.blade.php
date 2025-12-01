<table class="table table-hover table-fluid" id="{{ $tableConfig['tableId'] }}">
    <thead class="w-100">
        <tr>
            @foreach ($tableConfig['tableHead'] as $head)
                <th>{{ $head }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody id="{{ $tableConfig['tableId'] . '_body' }}">

    </tbody>
</table>
