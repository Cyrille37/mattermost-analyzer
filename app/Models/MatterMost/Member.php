<?php

namespace App\Models\MatterMost ;

use Illuminate\Database\Eloquent\Model as EloquentModel ;

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
class Member extends EloquentModel
{
    const TABLE_NAME = 'members';

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = self::TABLE_NAME ;

    public $incrementing = false ;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'roles', 'username', 'nickname', 'create_at', 'delete_at'
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
