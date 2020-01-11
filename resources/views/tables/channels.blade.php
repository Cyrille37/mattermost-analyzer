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
        <table id="channels-table" class="table table-bordered table-striped table-condensed dataTable">
            <thead>
                <tr>
                    <th>Display name</th>
                    <th>Members</th>
                    <th>Posts</th>
                    <th>Created</th>
                    <th>Last post</th>
                    <th>Header</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

</div>

<script type="text/javascript">

"use strict";

var dataTableUrl =  "{!! route('tables.channels') !!}";

$(function()
{

    var t = $('#channels-table')
	.DataTable({
        processing: true,
        serverSide: false,
        ajax: dataTableUrl,
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
            { data: 'display_name',
                render: function( data, action, row ){
                    return '<span title="'+row.id+'">'+data+'</span>' ;
                }
            },
            { data: null, name: 'members',
                render: function( data, action, row ){
                    return row.stats[0].members_count ;
                }
            },
            { data: null, name: 'posts',
                render: function( data, action, row ){
                    return row.stats[0].posts_count ;
                }
            },
            { data: 'create_at',
                render: function( data, action, row ){
                    var d = moment( parseInt(data) );
                    return '<span class="d-none">'+data+' - </span>' /* ordering */
                    	+'<span title="'+ d.format('L LT') +'">'+d.fromNow()+'</span>' ;
                }
            },
            { data: null, name: 'last_post',
                render: function( data, action, row ){
                    //return row.stats[0].last_post_at ;
                	//var date = new Date( parseInt( row.stats[0].last_post_at ) );
                	//return date.toISOString().replace('T',' ');
                    var d = moment( parseInt(row.stats[0].last_post_at) );
                    return '<span class="d-none">'+row.stats[0].last_post_at+' - </span>' /* ordering */
                    	+ d.format('L') ;
                }
            },
            { data: 'header',
                render: function( data ){
                    if( data == '' )
                        return '';
                    return ''
	                	+ '<i class="text-unwrap fas fa-crosshairs" onclick="textWrapToggle(this)"></i> '
                    	+ '<span class="text-extract">'+data.substring(0,20)+'...</span>'
                    	+ '<span class="text-full">'+data+'</span>'
                    	;
                }
            },
            { data: 'purpose',
                render: function( data ){
                    if( data == '' )
                        return '';
                    return ''
	                	+ '<i class="text-unwrap fas fa-crosshairs" onclick="textWrapToggle(this)"></i> '
                    	+ '<span class="text-extract">'+data.substring(0,20)+'...</span>'
                    	+ '<span class="text-full">'+data+'</span>'
                    	;
                }
            }
        ]
	});

});

function textWrapToggle( el )
{
	$('.text-extract,.text-full', $(el).parent('td') ).toggle();
};

</script>

@endsection
