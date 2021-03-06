<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mattermost Analyzer</title>

		<link rel="icon" href="{!! asset('favicon.ico') !!}" />

        <!-- Styles -->
    	<link href="{!! asset('/lib/fontawesome-free-5.12.0-web/css/all.min.css') !!}" rel="stylesheet" />

        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/home') }}">Home</a>
                        <a href="{{ route('login') }}">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Plop
                </div>
                <p style="margin-bottom: 3rem; font-size: 2rem;">
                    <i class="far fa-hand-point-right"></i>
                    <a href="{!! route('home') !!}">c'est pa<del>s</del>r là</a>
                </p>

                <div class="links">
                    <a href="https://laravel.com">Laravel</a>
                    <a href="https://mattermost.org">Mattermost</a>
                    <a href="https://framateam.org">Framateam</a>
                    <a href="https://github.com/Cyrille37/mattermost-analyzer">GitHub</a>
                </div>
            </div>
        </div>
    </body>
</html>
