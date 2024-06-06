<?php

namespace App\Models\MatterMost ;

use Illuminate\Database\Eloquent\Model as EloquentModel ;
use Carbon\Carbon;

/**
 * @property string $channel_id
 * @property Carbon $last_post_at
 * @property int $posts_count
 * @property int $members_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChannelStat extends MattermostModel
{
    const TABLE_NAME = 'channels_stats';

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
        'last_post_at',
        'created_at',
        'updated_at',
    ];

}
