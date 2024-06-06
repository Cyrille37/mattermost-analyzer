<?php

namespace App\Models\MatterMost;

use Illuminate\Support\Facades\DB;

class Post extends MattermostModel
{
    const TABLE_NAME = 'posts';
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
     * @param \Pnz\MattermostClient\Model\Post\Post $apiPost
     * @return \App\Models\MatterMost\Post
     */
    public static function firstOrCreateFromApi($apiPost)
    {
        return Post::firstOrCreate(
            [
                'id' => $apiPost->getId()
            ],
            [
                'id' => $apiPost->getId(),
                'create_at' => MattermostModel::mmDateToCarbon($apiPost->getCreateAt()),
                'update_at' => MattermostModel::mmDateToCarbon($apiPost->getUpdateAt()),
                'delete_at' => MattermostModel::mmDateToCarbon($apiPost->getDeleteAt()),
                'user_id' => $apiPost->getUserId(),
                'channel_id' => $apiPost->getChannelId(),
                'pinned' => $apiPost->getIsPinned(),
                'root_id' => $apiPost->getRootId(),
                'parent_id' => $apiPost->getParentId(),
                'original_id' => $apiPost->getOriginalId(),
                'message' => $apiPost->getMessage(),
                'type' => $apiPost->getType(),
                'hashtag' => $apiPost->getHashtag(),
            ]
        );
    }
}
