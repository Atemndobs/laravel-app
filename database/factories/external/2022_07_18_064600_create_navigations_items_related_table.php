<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationsItemsRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations_items_related', function (Blueprint $table) {
            $table->increments('id');
            $table->string('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('field')->nullable();
            $table->integer('order')->nullable();
            $table->string('master')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('navigations_items_related_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('navigations_items_related_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigations_items_related');
    }
}
