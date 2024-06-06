<?php

namespace App\Models\MatterMost;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class MattermostModel extends EloquentModel
{
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * make all attributes mass assignable.
     * @var array
     */
    protected $guarded = [];

    /**
     * Convert MM milliseconds timestamp or 0
     */
    public static function mmDateToCarbon($value)
    {
        if (empty($value))
            return null;
        $date = Carbon::createFromTimestampMs($value);
        return $date->isValid() ? $date : null;
    }
}
