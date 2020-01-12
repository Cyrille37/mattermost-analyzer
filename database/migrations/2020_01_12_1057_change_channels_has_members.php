<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\ChannelHasMember;
use App\Models\MatterMost\Member;
use App\Models\MatterMost\Channel;

class ChangeChannelsHasMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( ChannelHasMember::TABLE_NAME, function (Blueprint $table)
        {
            $table->integer('mention_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( ChannelHasMember::TABLE_NAME, function (Blueprint $table)
        {
            $table->dropColumn('mention_count');
        });
        
    }
}
