<?php

namespace App\Models\MatterMost;

use Illuminate\Support\Facades\DB;
use \Pnz\MattermostClient\Model\Channel\Channel as ChannelApi;

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
class Channel extends MattermostModel
{
    const TABLE_NAME = 'channels';
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = self::TABLE_NAME;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'create_at',
        'update_at',
        'delete_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @param \Pnz\MattermostClient\Model\Channel\Channel $apiChannel
     * @return \App\Models\MatterMost\Channel
     */
    public static function firstOrCreateFromApi(ChannelApi $apiChannel)
    {
        return Channel::firstOrCreate(
            [
                'id' => $apiChannel->getId()
            ],
            [
                'id' => $apiChannel->getId(),
                'name' => $apiChannel->getName(),
                'display_name' => $apiChannel->getDisplayName(),
                'header' => $apiChannel->getHeader(),
                'purpose' => $apiChannel->getPurpose(),
                'create_at' => MattermostModel::mmDateToCarbon($apiChannel->getCreateAt()),
                'delete_at' => MattermostModel::mmDateToCarbon($apiChannel->getDeleteAt()),
                'creator_id' => $apiChannel->getCreatorId(),
            ]
        );
    }

    public function stats()
    {
        //return $this->hasMany( ChannelStat::class, 'channel_id', 'id' );
        return $this->hasMany(ChannelStat::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastStats($query)
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

        return $query->with(['stats' => function ($q) {
            $q->join(
                DB::raw('
            	   (
            		select channel_id, MAX(created_at) maxDate from channels_stats
            		group by channel_id
            	   ) CS2
                '),
                function ($join) {
                    $join
                        ->on('CS2.channel_id', '=', 'channels_stats.channel_id')
                        ->on('channels_stats.created_at', '=', 'CS2.maxDate');
                }
            );
        }]);
    }

    public static function getNamesDictionnary()
    {
        $keyed = DB::table(self::TABLE_NAME)->select('id', 'display_name')->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->display_name];
            });
        return $keyed->toArray();
    }

    /**
     * Retrieve current memberships on this channel
     * .
     * @return \Illuminate\Support\Collection
     */
    public function getMemberships()
    {
        return DB::table(ChannelHasMember::TABLE_NAME . ' as CHM')
            ->where('CHM.channel_id', $this->id)
            ->where('CHM.is_member', 1)
            ->join(
                DB::raw(
                    '( select member_id, MAX(created_at) maxDate from channels_has_members'
                        . ' where channel_id="' . $this->id . '"'
                        . ' group by member_id'
                        . ' ) CHM2'
                ),
                function ($join) {
                    $join
                        ->on('CHM2.member_id', '=', 'CHM.member_id')
                        ->on('CHM.created_at', '=', 'CHM2.maxDate');
                }
            )
            ->get();
    }
}
