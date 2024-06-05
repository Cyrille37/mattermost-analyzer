<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\Channel ;
use App\Models\MatterMost\Post;

class CreatePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( Post::TABLE_NAME, function (Blueprint $table)
        {
            /*
            'id' => '14ngkuned7yszx55bxhz1esr1o',
            'create_at' => 1708330613942,
            'update_at' => 1708330613942,
            'delete_at' => 0,
            'user_id' => '4cwmakt1qb8rze4u8bmu698fwe',
            'channel_id' => 'qohbcctxrb8cxr6hgt1ernc9xe',
            'is_pinned' => false,
            'root_id' => 'k3ay8xf6qbycuxicubj8ka7o5c',
            'parent_id' => NULL,
            'original_id' => '',
            'message' => '...'
            'type' => '',
            'hashtag' => NULL,

            'props' => array (),
            'filenames' => NULL,
            'file_ids' => NULL,
            'pending_post_id' => '',
             */

            $table->string('id');
            $table->bigInteger('create_at')->unsigned()->comment('unix timestamp with milliseconds');
            $table->bigInteger('update_at')->unsigned()->nullable()->comment('unix timestamp with milliseconds');
            $table->bigInteger('delete_at')->unsigned()->nullable()->comment('unix timestamp with milliseconds');
            $table->string('user_id');
            $table->string('channel_id');
            $table->boolean('pinned')->default(false);
            $table->string('root_id')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('original_id')->nullable();
            $table->longText('message');
            $table->string('type')->nullable();
            $table->string('hashtag')->nullable();

            $table->timestamps();
            
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( Post::TABLE_NAME );
    }
}
