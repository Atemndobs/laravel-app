<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('method', 10)->default('POST');
            $table->string('url');
            $table->string('status', 10)->default('active');
            $table->boolean('data')->default(1);
            $table->string('actions', 100);
            $table->string('collections');
            $table->longText('headers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_webhooks');
    }
}
