<!doctype html>
<html lang="{!! str_replace('_', '-', app()->getLocale()) !!}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{!! csrf_token() !!}">

    <title>{{ config('app.name', 'Mattermost-Analyzer') }}</title>

	<link rel="icon" href="{!! asset('favicon.ico') !!}" />

    <!-- Scripts -->
    <script src="{!! asset('/lib/jquery-3.4.1.min.js') !!}"></script>
    <script src="{!! asset('/lib/bootstrap-4.4.1/js/bootstrap.min.js') !!}" defer></script>
    <script src="{!! asset('/lib/Numeral-js-2.0.6/min/numeral.min.js') !!}" defer></script>
    <script src="{!! asset('/lib/Numeral-js-2.0.6/min/locales/'.app()->getLocale().'.min.js') !!}" defer></script>
    <script src="{!! asset('/lib/moment-with-locales-2.24.min.js') !!}" defer></script>
    <script src="{!! asset('/lib/Chart.js-2.9.3/Chart.min.js') !!}" defer></script>

    <!-- Styles -->
    <link href="{!! asset('/lib/bootstrap-4.4.1/css/bootstrap.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('/lib/fontawesome-free-5.12.0-web/css/all.min.css') !!}" rel="stylesheet" />
    <link href="{!! asset('/lib/Chart.js-2.9.3/Chart.min.css') !!}" rel="stylesheet" />

@if( env('APP_DEBUG') )
    <style type="text/css">
    /* debug bootstrap screen size https://getbootstrap.com/docs/3.3/css/ */
    #debug-bt {text-transform: uppercase; color: green; font-size: 80%; position: fixed; top: 5px; left: 5px;}
    #debug-bt .bt-sm, #debug-bt .bt-md, #debug-bt .bt-lg {display: none}
    /* Extra small devices (phones, less than 768px) */
    /* No media query since this is the default in Bootstrap */                    
    /* Small devices (tablets, 768px and up) */
    @media (min-width: 768px) { #debug-bt .bt-sm {display:inline;} #debug-bt .bt-xs {display:none;} }
    /* Medium devices (desktops, 992px and up) */
    @media (min-width: 992px) { #debug-bt .bt-md {display:inline;} #debug-bt .bt-sm {display:none;} }
    /* Large devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) { #debug-bt .bt-lg {display:inline;} #debug-bt .bt-md {display:none;} }
    </style>
@endif

	@stack('css')

	@stack('js_defer')

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        	@if( env('APP_DEBUG') )
        	<span id="debug-bt"><span class="bt-xs">xs</span><span class="bt-sm">sm</span><span class="bt-md">md</span><span class="bt-lg">lg</span></span>
        	@endif
            <div class="container">            
                <a class="navbar-brand" href="{!! url('/') !!}">
                    <i class="fas fa-chart-bar"></i> {!! config('app.name', 'Laravel') !!}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        <li class="nav-item">
	                        <a class="nav-link" href="{{ url('/home') }}">Home</a>
                        </li>
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('login') !!}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('register') !!}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{!! route('logout') !!}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{!! route('logout') !!}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main id="app-content" class="py-4">
            @yield('content')
        </main>
    </div>

<script type="text/javascript">
"use strict";

$(function()
{
	moment.locale('{!! app()->getLocale() !!}');
	numeral.locale('{!! app()->getLocale() !!}');

	formatNumbers();
});

function formatNumbers()
{
	// format numbers
	$('#app-content .number-format').each( function( i, el )
	{
		var $el = $(el);
		var n = $el.html().trim();
		$el.html( numeral(n).format() );
	});

}
</script>

	@stack('js')

</body>
</html>
