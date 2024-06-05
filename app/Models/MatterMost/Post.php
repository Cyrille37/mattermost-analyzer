<?php

namespace App\Models\MatterMost ;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as EloquentModel ;
class Post extends EloquentModel
{
    const TABLE_NAME = 'posts';
    protected $table = self::TABLE_NAME ;

    public $incrementing = false ;
    protected $keyType = 'string';

    /**
     * make all attributes mass assignable.
     * @var array
     */
    protected $guarded = [];

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
