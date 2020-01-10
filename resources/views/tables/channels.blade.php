@extends('layouts.app')

@push('css')
    <link href="/lib/DataTables/datatables.min.css" rel="stylesheet">
@endpush

@push('js_defer')
    <script src="/lib/DataTables/datatables.min.js" defer></script>
@endpush

@section('content')
<div class="container">

<table id="channels-table" class="table table-bordered table-striped table-condensed dataTable">
    <thead>
        <tr>
            <th>Display name</th>
            <th>Header</th>
            <th>Purpose</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

</div>

<script type="text/javascript">

"use strict";

var dataTableUrl =  "{!! route('tables.channels') !!}";

$(function()
{

    $('#channels-table')
	.DataTable({
        processing: true,
        serverSide: false,
        pageLength: 25,
        ajax: dataTableUrl,
        columns: [
            { data: 'display_name' },
            { data: 'header' },
            { data: 'purpose' }
        ]
	});

});
</script>

@endsection
