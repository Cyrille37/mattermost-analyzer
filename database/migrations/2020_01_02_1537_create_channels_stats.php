<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\ChannelStat;

class CreateChannelsStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( ChannelStat::TABLE_NAME, function (Blueprint $table)
        {
            /*
             */
            $table->string('channel_id');
            $table->bigInteger('last_post_at')->unsigned()->comment('unix timestamp with milliseconds');
            $table->bigInteger('posts_count')->unsigned();
            $table->integer('members_count')->unsigned();

            $table->timestamps();

            $table->index('channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( ChannelStat::TABLE_NAME );
    }
}
