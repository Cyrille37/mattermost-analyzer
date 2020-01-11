<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const CLIENT_CACHE_SECONDS = 60 ;

    protected static function cacheHeader()
    {
        return 'public, proxy-revalidate, max-age='.self::CLIENT_CACHE_SECONDS ;
    }

}
