<?php

namespace App\Models\MatterMost ;

use Illuminate\Database\Eloquent\Model as EloquentModel ;
use Carbon\Carbon;

/**
 * @property string $channel_id
 * @property int $last_post_at
 * @property int $posts_count
 * @property int $members_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChannelStat extends EloquentModel
{
    const TABLE_NAME = 'channels_stats';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE_NAME ;

    public $incrementing = false ;

    protected $fillable = [
        'channel_id',
        'last_post_at',
        'posts_count',
        'members_count',
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

}
