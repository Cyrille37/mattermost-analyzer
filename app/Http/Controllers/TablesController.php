<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MatterMost\Channel;
use App\Models\MatterMost\Member;
use Yajra\DataTables\Facades\DataTables;

class TablesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function channels( Request $request )
    {
        if( ! $request->ajax() )
        {
            return view('tables.channels');
        }

        return dataTables::of(
                Channel::query()->LastStats()
            )
            // Done on client-side.
            /*->editColumn('header', function( Channel $channel ) {
                return Str::limit( $channel->header, 20 );
            })*/
            /*->editColumn('purpose', function( Channel $channel ) {
                return Str::limit( $channel->purpose, 20 );
            })*/
            ->make(true);
    }

    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function members( Request $request )
    {
        if( ! $request->ajax() )
        {
            return view('tables.members');
        }

        return dataTables::of(
            Member::query()->Members()
            )
            ->make(true);
    }
}
