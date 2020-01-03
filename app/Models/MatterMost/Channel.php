<?php

namespace App\Models\MatterMost ;

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
}
