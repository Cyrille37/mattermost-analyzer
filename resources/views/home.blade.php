@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

					@auth
                    <p>You are logged in!</p>
					@endauth

					<ul>
						<li><a href="{!! route('tables.channels') !!}">Channels <span class="badge badge-info number-format">{!! \App\Models\MatterMost\Channel::count() !!}</span></a></li>
						<li> Channels stats <span class="badge badge-secondary number-format">{!! \App\Models\MatterMost\ChannelStat::count() !!}</span>
						<li><a href="{!! route('tables.members') !!}">Members <span class="badge badge-info number-format">{!! \App\Models\MatterMost\Member::count() !!}</span></a></li>
						<li> Members subscriptions <span class="badge badge-secondary number-format">{!! \App\Models\MatterMost\ChannelHasMember::count() !!}</span>
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
