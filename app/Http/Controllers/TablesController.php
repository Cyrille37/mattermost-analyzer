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
        return view('tables.channels')
            ;
    }

    public function channels_data( Request $request )
    {
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
            ->make(true)
            ->withHeaders([
                'Cache-Control' => self::cacheHeader()
            ])
            ;
    }

    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function members( Request $request )
    {
        return view('tables.members')
            ;
    }

    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function members_data( Request $request )
    {        
        return dataTables::of(
            Member::query()->Memberships()
            )
            ->make(true)
            ->withHeaders([
                'Cache-Control' => self::cacheHeader()
            ])
            ;
    }

}
