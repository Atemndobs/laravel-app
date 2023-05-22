<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_apis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique('custom_apis_slug_unique');
            $table->longText('selected_content_type')->nullable();
            $table->longText('structure')->nullable();
            $table->timestamps(, 6);
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'custom_apis_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'custom_apis_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_apis');
    }
}
