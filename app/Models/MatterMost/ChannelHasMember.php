<?php

namespace App\Models\MatterMost ;

use Illuminate\Database\Eloquent\Model as EloquentModel ;

/**
 * @property int $id
 * @property string $channel_id
 * @property string $member_id
 * @property string $roles
 * @property bool $is_member
 * @property int $msg_count Still at zero, it's a shame...
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Member $member
 * @property Channel $channel
 *
 */
class ChannelHasMember extends EloquentModel
{
    const TABLE_NAME = 'channels_has_members';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE_NAME ;

    protected $fillable = [
        'channel_id',
        'member_id',
        'roles',
        'is_member',
        'msg_count'
    ];

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
