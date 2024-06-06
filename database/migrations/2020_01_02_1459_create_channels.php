<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\Channel ;

class CreateChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( Channel::TABLE_NAME, function (Blueprint $table)
        {
            /*
            'id',
            'create_at',
            'update_at',
            'delete_at',
            'team_id',
            'type',
            'display_name',
            'name',
            'header',
            'purpose',
            'last_post_at', <- ChannelStat
            'total_msg_count', <- ChannelStat
            'extra_update_at',
            'creator_id',
             */

            $table->string('id');
            $table->string('name');
            $table->string('display_name');
            $table->longText('header');
            $table->longText('purpose');
            $table->datetime('create_at');
            $table->datetime('update_at')->nullable();
            $table->datetime('delete_at')->nullable();
            $table->string('creator_id');

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
        Schema::dropIfExists( Channel::TABLE_NAME );
    }
}
