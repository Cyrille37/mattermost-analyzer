<?php

namespace App\Models\MatterMost ;

/**
 * @property int $id
 * @property string $channel_id
 * @property string $member_id
 * @property string $roles
 * @property bool $is_member
 * @property int $msg_count
 * @property int $mention_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Member $member
 * @property Channel $channel
 *
 */
class ChannelHasMember extends MattermostModel
{
    const TABLE_NAME = 'channels_has_members';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE_NAME ;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function channel()
    {
        return $this->belongsTo( Channel::class );
    }
    
    public function member()
    {
        return $this->belongsTo( Member::class );
    }

}
