<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStrapiApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('strapi_api_tokens', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'strapi_api_tokens_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'strapi_api_tokens_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('strapi_api_tokens', function (Blueprint $table) {
            $table->dropForeign('strapi_api_tokens_created_by_id_fk');
            $table->dropForeign('strapi_api_tokens_updated_by_id_fk');
        });
    }
}
