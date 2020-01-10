@extends('layouts.app')

@push('css')
    <link href="/lib/DataTables/datatables.min.css" rel="stylesheet">

<style type="text/css">
.text-extract {
}
.text-full {
    display: none;
}
.text-unwrap:hover {
    color: green;
}
</style>

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
            <th>Members</th>
            <th>Posts</th>
            <th>Last post</th>
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
            { data: null, name: 'last_post',
                render: function( data, action, row ){
                    //return row.stats[0].last_post_at ;
                	var date = new Date( parseInt( row.stats[0].last_post_at ) );
                	return date.toISOString().replace('T',' ');
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
