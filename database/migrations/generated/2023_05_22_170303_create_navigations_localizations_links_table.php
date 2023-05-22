<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationsLocalizationsLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations_localizations_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_id')->nullable();
            $table->unsignedInteger('inv_navigation_id')->nullable();
            
            $table->foreign('navigation_id', 'navigations_localizations_links_fk')->references('id')->on('navigations')->onDelete('cascade');
            $table->foreign('inv_navigation_id', 'navigations_localizations_links_inv_fk')->references('id')->on('navigations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigations_localizations_links');
    }
}
