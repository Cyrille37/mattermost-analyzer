<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MatterMost\Channel;
use Yajra\DataTables\Facades\DataTables;

class TablesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
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
}
