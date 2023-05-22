<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('timestamp')->nullable()->default('current_timestamp()');
            $table->string('status')->default('inbox');
            $table->uuid('recipient');
            $table->uuid('sender')->nullable();
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('collection', 64)->nullable();
            $table->string('item')->nullable();
            
            $table->foreign('recipient', 'directus_notifications_recipient_foreign')->references('id')->on('directus_users')->onDelete('cascade');
            $table->foreign('sender', 'directus_notifications_sender_foreign')->references('id')->on('directus_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_notifications');
    }
}
