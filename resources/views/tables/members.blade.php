@extends('layouts.app')

@push('css')
    <link href="{!! asset('/lib/DataTables/datatables.min.css') !!}" rel="stylesheet">

<style type="text/css">

.text-extract {
}
.text-full {
    display: none;
}
.text-unwrap:hover {
    color: green;
}

#channels-table_info{
    float: left;
}

.dt-buttons {
    float: right;
}

</style>

@endpush

@push('js_defer')
    <script src="{!! asset('/lib/DataTables/datatables.min.js') !!}" defer></script>
@endpush

@section('content')

<div class="container">

    <div class="table-responsive">
        <table id="members-table" class="table table-bordered table-striped table-condensed dataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Roles</th>
                    <th>Created</th>
                    <th>Subscriptions</th>
                    <th>Channels</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

</div>

<script type="text/javascript">

"use strict";

var dataTableUrl =  "{!! route('tables.members.data') !!}";

var channelsNames = {!! json_encode( \App\Models\MatterMost\Channel::getNamesDictionnary() ) !!} ;

$(function()
{

    var t = $('#members-table')
	.DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: dataTableUrl,
            cache: true,
        },
        language: {
            url: "{!! asset('/lib/DataTables/lang.'. app()->getLocale() .'.json') !!}"
        },
        buttons: {
        	buttons: [
                { className: 'btn-sm', extend: 'copy' },
                { className: 'btn-sm', extend: 'csv' },
                { className: 'btn-sm', extend: 'print' },
        	]
        },
        pageLength: 50,
        pagingType: 'full_numbers',
        dom: 'lifBrstip',
        fixedHeader: true,
        responsive: false,
        columns: [
            { data: 'username',
                render: function( data, action, row )
                {
                    return '<span>'+data+'</span>'
                    	+ (row.nickname ? ' / '+row.nickname : '')
                    	// to search by user.id
                    	+ '<span class="d-none">'+row.id+' - </span>'
                    ;
                }
            },
            { data: 'roles',
                render: function( data, action, row )
                {
                    return data ;
                }
            },
            { data: 'create_at',
                render: function( data, action, row )
                {
                    var d = moment( parseInt(data) );
                    return '<span class="d-none">'+data+' - </span>' /* ordering */
                    	+'<span title="'+ d.format('L LT') +'">'+d.fromNow()+'</span>' ;
                }
            },
            { data: 'channels', name: 'subscriptions',
                render: function( data, action, row )
                {
                    return data.length ;
                }
            },
            { data: 'channels',
                render: function( data, action, row )
                {
                    var channels = [];
                    for( var c in data )
                    {
                    	channels.push( channelsNames[data[c].channel_id] );
                    }
                    return channels.join(', ');
                }
            }
        ]
	});

});

</script>

@endsection
