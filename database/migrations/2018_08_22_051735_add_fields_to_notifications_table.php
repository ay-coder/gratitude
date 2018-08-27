<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_notifications', function (Blueprint $table) 
        {
            $table->integer('post_id')->after('is_read')->default(0)->nullable();
            $table->integer('comment_id')->after('post_id')->default(0)->nullable();
            $table->string('notification_type')->after('comment_id')->nullable();
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
