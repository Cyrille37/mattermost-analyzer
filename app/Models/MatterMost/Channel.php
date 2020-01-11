<?php

namespace App\Models\MatterMost ;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as EloquentModel ;

/**
 * @property string $id
 * @property string $name
 * @property string $display_name
 * @property string $header
 * @property string $purpose
 * @property string $creator_id
 * @property int $create_at Unix timestamp with milliseconds. User "Carbon::createFromTimestampMs()"
 * @property int $delete_at Unix timestamp with milliseconds. User "Carbon::createFromTimestampMs()"
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ChannelStat[] $stats
 *
 */
class Channel extends EloquentModel
{
    const TABLE_NAME = 'channels';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE_NAME ;

    public $incrementing = false ;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'display_name', 'header', 'purpose', 'create_at', 'delete_at', 'creator_id'
    ];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        //'create_at',
        //'delete_at',
        'created_at',
        'updated_at',
    ];

    public function stats()
    {
        //return $this->hasMany( ChannelStat::class, 'channel_id', 'id' );
        return $this->hasMany( ChannelStat::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastStats( $query )
    {
        //return $query->with('stats');

        /*
        SELECT CS.channel_id, CS.last_post_at, CS.posts_count, CS.members_count, CS.created_at
        FROM channels C
        left join channels_stats CS 
            on C.id = CS.channel_id
        	inner join
        	(
        		select channel_id, MAX(created_at) maxDate from channels_stats
        		group by channel_id
        	) CS2
        		on CS2.channel_id = C.id
        		and CS.created_at = CS2.maxDate
         */

        return $query->with( ['stats'=> function($q)
        {
            $q->join(
                DB::raw('
            	   (
            		select channel_id, MAX(created_at) maxDate from channels_stats
            		group by channel_id
            	   ) CS2
                '), function($join)
                {
                    $join
                        ->on('CS2.channel_id', '=', 'channels_stats.channel_id')
                        ->on( 'channels_stats.created_at', '=', 'CS2.maxDate');
                }
            );
        }]);

    }

    public static function getNamesDictionnary()
    {
        $keyed = DB::table(self::TABLE_NAME)->select('id', 'display_name')->get()
            ->mapWithKeys(function ($item)
            {
                return [$item->id => $item->display_name];
            });
        return $keyed->toArray();
    }

}
