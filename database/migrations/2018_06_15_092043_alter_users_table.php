<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table)
        {
            $table->string('username')->after('email')->unique();
            $table->string('phone')->after('password')->unique();
            $table->string('otp')->after('phone')->unique();
            $table->string('gender')->after('otp')->nullable();
            $table->string('profile_pic')->after('gender')->default('default.png');
            $table->date('dob')->after('profile_pic')->nullable();
            $table->string('device_token')->after('dob')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
