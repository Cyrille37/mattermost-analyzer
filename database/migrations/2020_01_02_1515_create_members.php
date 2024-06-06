<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MatterMost\Member ;

class CreateMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( Member::TABLE_NAME, function (Blueprint $table)
        {
            /*
              'team_id' => 'h4fwwegraidd9c6w9ecywbfj7c',
              'user_id' => '6g5cs8hx3fr6djfxkg4pi9s8th',
              'roles' => 'team_user',
              'create_at' => NULL,

                'id' => '5zb4rb7nxtre381dwjwjhi8ujy',
                'create_at' => 1487878978677,
                'update_at' => 1561452691804,
                'delete_at' => 0,
                'roles' => 'system_user',
                'allow_marketing' => NULL,
                'locale' => 'en',
                'username' => 'satiss',
                'nickname' => '',
                'auth_data' => '',
                'email' => '',
                'email_verified' => NULL,
                'notify_props' => NULL,
                'last_password_update' => NULL,
                'last_name' => '', <- pas dispo
                'first_name' => '', <- pas dispo
             */
            $table->string('id');
            $table->string('roles');
            $table->string('username');
            $table->string('nickname');
            $table->string('email')->nullable();
            $table->datetime('create_at');
            $table->datetime('update_at')->nullable();
            $table->datetime('delete_at')->nullable();

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
        Schema::dropIfExists( Member::TABLE_NAME );
    }
}
