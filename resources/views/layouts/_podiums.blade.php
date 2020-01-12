{{--
Variables:
 - None
--}}

@push('css')

<style type="text/css">

.podium-box {
    margin: 0 auto;
    display: flex;
}

.podium-box .podium-number {
    font-family: DaggerSquare;
    font-weight: bold;
    font-size: 2em;
    color: white;
    line-height: 1.2;
}

.podium-box .step-container {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.podium-box .step-container > div:first-child {
    margin-top: auto;
    text-align: center;
}

.podium-box .step {
    text-align: center;
}

.podium-box .first-step {
    height: 100px;
}

.podium-box .second-step {
    height: 65px;
}

.podium-box .third-step {
    height: 40px;
}

</style>

@endpush

<div class="mb-3">
    <h3 class="text-center">Channels par Posts</h3>
    <div id="podium-box-posts" class="row podium-box" style="zheight: 100px">
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="second-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format second-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number second-step">2</div>
        </div>
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="first-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format first-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number first-step">1</div>
        </div>
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="third-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format third-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number third-step">3</div>
        </div>
    </div>
</div>

<div>
    <h3 class="text-center">Channels par Abonnés</h3>
    <div id="podium-box-members" class="row podium-box" style="zheight: 100px">
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="second-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format second-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number second-step">2</div>
        </div>
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="first-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format first-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number first-step">1</div>
        </div>
        <div class="col-md-4 step-container m-0 p-0">
            <div>
            	<span class="third-text">...</span>
            	<span class="badge badge-pill badge-secondary number-format third-score"></span>
            </div>
            <div class="bg-info step centerBoth podium-number third-step">3</div>
        </div>
    </div>
</div>

@push('js')

<script type="text/javascript">
"use strict";

var channelsDataUrl =  "{!! route('tables.channels.data') !!}";

$(function()
{
	$.getJSON( channelsDataUrl, function( dataTable )
	{
		//console.log( dataTable );
		var stats = [] ;
		for( var data in dataTable.data )
		{
			//console.log( dataTable.data[data].stats[0].posts_count );
			stats.push({
				l: dataTable.data[data].display_name,
				p: dataTable.data[data].stats[0].posts_count,
				m: dataTable.data[data].stats[0].members_count
			});
		}

		var extratPodium = function( attr )
		{
			var podium = [];
			var j = 0 ;
			for( var i=0; i<3; i++ )
			{
				podium[i] = {
					s: stats[j][attr],
					l: stats[j].l
				};
				j ++ ;
				while( stats[j][attr] == podium[i].s )
				{
					podium[i].l += (', '+ stats[j].l) ;
					j ++ ;
				};
			}
			return podium ;
		};

		stats.sort(function(a, b){
			return b.p - a.p ;
		});
		var podium = extratPodium( 'p' );
		var $podium = $('#podium-box-posts');
    	$('.first-text', $podium ).html( podium[0].l );
    	$('.first-score', $podium ).html( podium[0].s );
    	$('.second-text', $podium ).html( podium[1].l );
    	$('.second-score', $podium ).html( podium[1].s );
    	$('.third-text', $podium ).html( podium[2].l );
    	$('.third-score', $podium ).html( podium[2].s );

		stats.sort(function(a, b){
			return b.m - a.m ;
		});
		var podium = extratPodium( 'm' );
		var $podium = $('#podium-box-members');
    	$('.first-text', $podium ).html( podium[0].l );
    	$('.first-score', $podium ).html( podium[0].s );
    	$('.second-text', $podium ).html( podium[1].l );
    	$('.second-score', $podium ).html( podium[1].s );
    	$('.third-text', $podium ).html( podium[2].l );
    	$('.third-score', $podium ).html( podium[2].s );

    	//window.setTimeout( formatNumbers, 500 );
    	formatNumbers();
	});

});

</script>

@endpush
