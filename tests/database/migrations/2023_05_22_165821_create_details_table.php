<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->bigIncrements('idcount');
            $table->char('id', 64);
            $table->char('url', 255)->nullable()->index('details_url_index');
            $table->char('c_url', 255)->nullable()->index('details_c_url_index');
            $table->timestamp('timestamp')->default('current_timestamp()')->index('details_timestamp_index');
            $table->name`('server');
            $table->binary('perfdata')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->binary('cookie')->nullable();
            $table->binary('post')->nullable();
            $table->binary('get')->nullable();
            $table->integer('pmu')->nullable()->index('details_pmu_index');
            $table->integer('wt')->nullable()->index('details_wt_index');
            $table->integer('cpu')->nullable()->index('details_cpu_index');
            $table->char('server_id', 64)->nullable();
            $table->char('aggregateCalls_include', 255)->nullable();
            
            $table->index(['name_timestamp_index'], 'details_server');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('details');
    }
}
