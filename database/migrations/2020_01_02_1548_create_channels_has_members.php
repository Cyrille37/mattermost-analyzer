<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\ChannelHasMember;
use App\Models\MatterMost\Member;
use App\Models\MatterMost\Channel;

class CreateChannelsHasMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( ChannelHasMember::TABLE_NAME, function (Blueprint $table)
        {
            /*
             */
            $table->bigIncrements('id')->unsigned();
            $table->string('channel_id');
            $table->string('member_id');
            $table->string('roles');
            $table->boolean('is_member');
            $table->integer('msg_count')->default(0);

            $table->timestamps();

            $table->foreign('channel_id')
                ->references('id')->on(Channel::TABLE_NAME )
                ->onDelete('cascade');
            $table->foreign('member_id')
                ->references('id')->on(Member::TABLE_NAME )
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( ChannelHasMember::TABLE_NAME );
    }
}
