<?php

namespace App\Models\MatterMost;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Pnz\MattermostClient\Model\User\User as UserApi;

/**
 * @property string $id
 * @property string $roles
 * @property string $username
 * @property string $nickname
 * @property int $create_at Unix timestamp with milliseconds. User "Carbon::createFromTimestampMs()"
 * @property int $delete_at Unix timestamp with milliseconds. User "Carbon::createFromTimestampMs()"
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Member extends MattermostModel
{
    const TABLE_NAME = 'members';

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
     * @param UserApi $apiMember
     * @return \App\Models\MatterMost\Member
     */
    public static function firstOrCreateFromApi(UserApi $apiMember)
    {
        return Member::firstOrCreate(
            [
                'id' => $apiMember->getId()
            ],
            [
                'id' => $apiMember->getId(),
                'roles' => $apiMember->getRoles(),
                'username' => $apiMember->getUsername(),
                'nickname' => $apiMember->getNickname(),
                'email' => $apiMember->getEmail(),
                'create_at' => MattermostModel::mmDateToCarbon($apiMember->getCreateAt()),
                'update_at' => MattermostModel::mmDateToCarbon($apiMember->getUpdateAt()),
                'delete_at' => MattermostModel::mmDateToCarbon($apiMember->getDeleteAt()),
            ]
        );
    }

    public function channels()
    {
        //return $this->hasMany( ChannelStat::class, 'channel_id', 'id' );
        return $this->hasMany(ChannelHasMember::class, 'member_id', 'id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $onlyMember default: true
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMemberships($query, $onlyMember = true)
    {
        /*
        SELECT C.display_name, M.username, CHM.is_member
        FROM members M
        left join channels_has_members CHM on CHM.member_id = M.id
        	inner join
        	(
        		select channel_id, MAX(created_at) maxDate from channels_has_members
                where is_member=1
        		group by channel_id
        	) CHM2
        		on CHM2.channel_id = CHM.id
        		and CHM.created_at = CHM2.maxDate
        left join channels C on C.id = CHM.channel_id
        order by username
        */

        return $query->with(['channels' => function ($q) use ($onlyMember) {

            $q->join(
                DB::raw('
            	   (
            		select channel_id, MAX(created_at) maxDate from channels_has_members'
                    . ($onlyMember ? ' where is_member=1' : '')
                    . ' group by channel_id
            	   ) CHM2
                '),
                function ($join) {
                    $join
                        ->on('CHM2.channel_id', '=', 'channels_has_members.channel_id')
                        ->on('channels_has_members.created_at', '=', 'CHM2.maxDate');
                }
            );
            //->with('channel');
        }]);
    }
}
