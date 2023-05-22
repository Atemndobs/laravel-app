<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkableLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markable_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('markable_type');
            $table->unsignedBigInteger('markable_id');
            $table->string('value')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['markable_type', 'markable_id'], 'markable_likes_markable_type_markable_id_index');
            $table->foreign('user_id', 'markable_likes_user_id_foreign')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('markable_likes');
    }
}
