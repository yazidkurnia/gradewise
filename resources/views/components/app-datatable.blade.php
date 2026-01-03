{{-- Search and Filter Controls --}}
<div id="{{ $tableConfig['tableId'] }}_controls"></div>

{{-- DataTable --}}
<div class="table-responsive">
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
</div>

{{-- Pagination Controls --}}
<div id="{{ $tableConfig['tableId'] }}_pagination" class="mt-3"></div>
