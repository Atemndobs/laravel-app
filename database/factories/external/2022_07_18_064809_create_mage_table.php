<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->string('subject')->nullable();
            $table->json('properties')->nullable();
            $table->json('conditions')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('admin_permissions_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('admin_permissions_updated_by_id_fk');
        });

        Schema::create('admin_permissions_role_links', function (Blueprint $table) {
            $table->unsignedInteger('permission_id')->nullable()->index('admin_permissions_role_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('admin_permissions_role_links_inv_fk');
        });

        Schema::create('admin_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('admin_roles_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('admin_roles_updated_by_id_fk');
        });

        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->string('registration_token')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('blocked')->nullable();
            $table->string('prefered_language')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('admin_users_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('admin_users_updated_by_id_fk');
        });

        Schema::create('admin_users_roles_links', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->index('admin_users_roles_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('admin_users_roles_links_inv_fk');
        });

        Schema::create('audience', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('key')->nullable()->unique();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('audience_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('audience_updated_by_id_fk');
        });

        Schema::create('catalogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name')->nullable();
            $table->string('item_category')->nullable();
            $table->string('description')->nullable();
            $table->string('features_list')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->dateTime('published_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('catalogs_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('catalogs_updated_by_id_fk');
        });

        Schema::create('checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('host_id')->index('checks_host_id_foreign');
            $table->string('type');
            $table->string('status')->nullable();
            $table->boolean('enabled')->default(true);
            $table->text('last_run_message')->nullable();
            $table->json('last_run_output')->nullable();
            $table->timestamp('last_ran_at')->nullable();
            $table->integer('next_run_in_minutes')->nullable();
            $table->timestamp('started_throttling_failing_notifications_at')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
        });

        Schema::create('comments_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('content')->nullable();
            $table->boolean('blocked')->nullable();
            $table->boolean('blocked_thread')->nullable();
            $table->string('block_reason')->nullable();
            $table->string('author_id')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_avatar')->nullable();
            $table->boolean('removed')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('related')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('comments_comment_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('comments_comment_updated_by_id_fk');
        });

        Schema::create('comments_comment-report', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('content')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('resolved')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('comments_comment-report_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('comments_comment-report_updated_by_id_fk');
        });

        Schema::create('comments_comment_author_user_links', function (Blueprint $table) {
            $table->unsignedInteger('comment_id')->nullable()->index('comments_comment_author_user_links_fk');
            $table->unsignedInteger('user_id')->nullable()->index('comments_comment_author_user_links_inv_fk');
        });

        Schema::create('comments_comment_report_related_links', function (Blueprint $table) {
            $table->unsignedInteger('comment_report_id')->nullable()->index('comments_comment_report_related_links_fk');
            $table->unsignedInteger('comment_id')->nullable()->index('comments_comment_report_related_links_inv_fk');
        });

        Schema::create('comments_comment_thread_of_links', function (Blueprint $table) {
            $table->unsignedInteger('comment_id')->nullable()->index('comments_comment_thread_of_links_fk');
            $table->unsignedInteger('inv_comment_id')->nullable()->index('comments_comment_thread_of_links_inv_fk');
        });

        Schema::create('custom_apis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->json('selected_content_type')->nullable();
            $table->json('structure')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('custom_apis_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('custom_apis_updated_by_id_fk');
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('tenant_id')->index('domains_tenant_id_foreign');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('content')->nullable();
            $table->string('payload')->nullable();
            $table->string('surce')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->dateTime('published_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('feeds_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('feeds_updated_by_id_fk');
        });

        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('alternative_text')->nullable();
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('formats')->nullable();
            $table->string('hash')->nullable();
            $table->string('ext')->nullable();
            $table->string('mime')->nullable();
            $table->decimal('size', 10)->nullable();
            $table->string('url')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('provider')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('files_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('files_updated_by_id_fk');
        });

        Schema::create('files_related_morphs', function (Blueprint $table) {
            $table->unsignedInteger('file_id')->nullable()->index('files_related_morphs_fk');
            $table->unsignedInteger('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('field')->nullable();
            $table->unsignedInteger('order')->nullable();
        });

        Schema::create('frequency_parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('frequency_id');
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('health_check_result_history_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('check_name');
            $table->string('check_label');
            $table->string('status');
            $table->text('notification_message')->nullable();
            $table->string('short_summary')->nullable();
            $table->json('meta');
            $table->timestamp('ended_at');
            $table->char('batch', 36)->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('hosts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('ssh_user')->nullable();
            $table->integer('port')->nullable();
            $table->string('ip')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
        });

        Schema::create('i18n_locale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('i18n_locale_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('i18n_locale_updated_by_id_fk');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->text('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('navigations', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->boolean('visible')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('navigations_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('navigations_updated_by_id_fk');
        });

        Schema::create('navigations_items', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('title')->nullable();
            $table->string('type')->nullable();
            $table->longText('path')->nullable();
            $table->longText('external_path')->nullable();
            $table->string('ui_router_key')->nullable();
            $table->boolean('menu_attached')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('collapsed')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('navigations_items_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('navigations_items_updated_by_id_fk');
        });

        Schema::create('navigations_items_audience_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_audience_links_fk');
            $table->unsignedInteger('audience_id')->nullable()->index('navigations_items_audience_links_inv_fk');
        });

        Schema::create('navigations_items_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_id')->nullable()->index('navigations_items_links_fk');
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_links_inv_fk');
        });

        Schema::create('navigations_items_master_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_master_links_fk');
            $table->unsignedInteger('navigation_id')->nullable()->index('navigations_items_master_links_inv_fk');
        });

        Schema::create('navigations_items_parent_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_parent_links_fk');
            $table->unsignedInteger('inv_navigation_item_id')->nullable()->index('navigations_items_parent_links_inv_fk');
        });

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

        Schema::create('navigations_items_related_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_related_links_fk');
            $table->unsignedInteger('navigations_items_related_id')->nullable()->index('navigations_items_related_links_inv_fk');
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tokenable_type');
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['tokenable_type', 'tokenable_id']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id')->index('role_has_permissions_role_id_foreign');

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->text('value')->nullable();
            $table->text('field');
            $table->tinyInteger('active');
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('link')->nullable();
            $table->string('source')->nullable();
            $table->string('key')->nullable();
            $table->string('scale')->nullable();
            $table->double('bpm')->nullable();
            $table->double('duration')->nullable();
            $table->double('danceability')->nullable();
            $table->double('happy')->nullable();
            $table->double('sad')->nullable();
            $table->double('relaxed')->nullable();
            $table->double('aggressiveness')->nullable();
            $table->double('energy')->nullable();
            $table->string('comment')->nullable();
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('status')->nullable();
            $table->boolean('analyzed')->nullable();
            $table->string('related_songs')->nullable();
            $table->json('genre')->nullable();
            $table->string('image')->nullable();
            $table->boolean('played')->nullable();
            $table->string('slug')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('songs_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('songs_updated_by_id_fk');
            $table->json('classification_properties')->nullable();
        });

        Schema::create('strapi_api_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->string('access_key')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('strapi_api_tokens_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('strapi_api_tokens_updated_by_id_fk');
        });

        Schema::create('strapi_core_store_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable();
            $table->longText('value')->nullable();
            $table->string('type')->nullable();
            $table->string('environment')->nullable();
            $table->string('tag')->nullable();
        });

        Schema::create('strapi_database_schema', function (Blueprint $table) {
            $table->increments('id');
            $table->json('schema')->nullable();
            $table->dateTime('time')->nullable();
            $table->string('hash')->nullable();
        });

        Schema::create('strapi_migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->dateTime('time')->nullable();
        });

        Schema::create('strapi_webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->longText('url')->nullable();
            $table->json('headers')->nullable();
            $table->json('events')->nullable();
            $table->boolean('enabled')->nullable();
        });

        Schema::create('task_frequencies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->index('task_frequencies_task_id_idx');
            $table->string('label');
            $table->string('interval');
            $table->timestamps();
        });

        Schema::create('task_results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->index('task_results_task_id_idx');
            $table->timestamp('ran_at')->useCurrent()->index('task_results_ran_at_idx');
            $table->longText('result');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
            $table->decimal('duration', 24, 14)->default(0);
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('command');
            $table->string('parameters')->nullable();
            $table->string('expression')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true)->index('tasks_is_active_idx');
            $table->boolean('dont_overlap')->default(false)->index('tasks_dont_overlap_idx');
            $table->boolean('run_in_maintenance')->default(false)->index('tasks_run_in_maintenance_idx');
            $table->string('notification_email_address')->nullable();
            $table->string('notification_phone_number')->nullable();
            $table->string('notification_slack_webhook')->nullable();
            $table->timestamps();
            $table->integer('auto_cleanup_num')->default(0)->index('tasks_auto_cleanup_num_idx');
            $table->string('auto_cleanup_type', 20)->nullable()->index('tasks_auto_cleanup_type_idx');
            $table->boolean('run_on_one_server')->default(false)->index('tasks_run_on_one_server_idx');
            $table->boolean('run_in_background')->default(false);
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamps();
            $table->json('data')->nullable();
        });

        Schema::create('up_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('up_permissions_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('up_permissions_updated_by_id_fk');
        });

        Schema::create('up_permissions_role_links', function (Blueprint $table) {
            $table->unsignedInteger('permission_id')->nullable()->index('up_permissions_role_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('up_permissions_role_links_inv_fk');
        });

        Schema::create('up_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('up_roles_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('up_roles_updated_by_id_fk');
        });

        Schema::create('up_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('provider')->nullable();
            $table->string('password')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->string('confirmation_token')->nullable();
            $table->boolean('confirmed')->nullable();
            $table->boolean('blocked')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('up_users_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('up_users_updated_by_id_fk');
        });

        Schema::create('up_users_role_links', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->index('up_users_role_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('up_users_role_links_inv_fk');
        });

        Schema::create('usages', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('has_played')->nullable();
            $table->integer('play_count')->nullable();
            $table->boolean('like')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->dateTime('published_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('usages_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('usages_updated_by_id_fk');
        });

        Schema::create('usages_songs_links', function (Blueprint $table) {
            $table->unsignedInteger('usage_id')->nullable()->index('usages_songs_links_fk');
            $table->unsignedInteger('song_id')->nullable()->index('usages_songs_links_inv_fk');
        });

        Schema::create('usages_users_permissions_users_links', function (Blueprint $table) {
            $table->unsignedInteger('usage_id')->nullable()->index('usages_users_permissions_users_links_fk');
            $table->unsignedInteger('user_id')->nullable()->index('usages_users_permissions_users_links_inv_fk');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('websockets_statistics_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->integer('peak_connection_count');
            $table->integer('websocket_message_count');
            $table->integer('api_message_count');
            $table->timestamps();
        });

        Schema::table('admin_permissions', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'admin_permissions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'admin_permissions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('admin_permissions_role_links', function (Blueprint $table) {
            $table->foreign(['permission_id'], 'admin_permissions_role_links_fk')->references(['id'])->on('admin_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'admin_permissions_role_links_inv_fk')->references(['id'])->on('admin_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('admin_roles', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'admin_roles_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'admin_roles_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'admin_users_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'admin_users_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('admin_users_roles_links', function (Blueprint $table) {
            $table->foreign(['user_id'], 'admin_users_roles_links_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'admin_users_roles_links_inv_fk')->references(['id'])->on('admin_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('audience', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'audience_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'audience_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('catalogs', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'catalogs_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'catalogs_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('checks', function (Blueprint $table) {
            $table->foreign(['host_id'])->references(['id'])->on('hosts')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('comments_comment', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'comments_comment_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'comments_comment_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('comments_comment-report', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'comments_comment-report_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'comments_comment-report_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('comments_comment_author_user_links', function (Blueprint $table) {
            $table->foreign(['comment_id'], 'comments_comment_author_user_links_fk')->references(['id'])->on('comments_comment')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'comments_comment_author_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('comments_comment_report_related_links', function (Blueprint $table) {
            $table->foreign(['comment_report_id'], 'comments_comment_report_related_links_fk')->references(['id'])->on('comments_comment-report')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['comment_id'], 'comments_comment_report_related_links_inv_fk')->references(['id'])->on('comments_comment')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('comments_comment_thread_of_links', function (Blueprint $table) {
            $table->foreign(['comment_id'], 'comments_comment_thread_of_links_fk')->references(['id'])->on('comments_comment')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['inv_comment_id'], 'comments_comment_thread_of_links_inv_fk')->references(['id'])->on('comments_comment')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('custom_apis', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'custom_apis_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'custom_apis_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->foreign(['tenant_id'])->references(['id'])->on('tenants')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'feeds_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'feeds_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'files_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'files_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('files_related_morphs', function (Blueprint $table) {
            $table->foreign(['file_id'], 'files_related_morphs_fk')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('i18n_locale', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'i18n_locale_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'i18n_locale_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('navigations', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'navigations_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'navigations_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('navigations_items', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'navigations_items_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'navigations_items_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('navigations_items_audience_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_audience_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['audience_id'], 'navigations_items_audience_links_inv_fk')->references(['id'])->on('audience')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('navigations_items_links', function (Blueprint $table) {
            $table->foreign(['navigation_id'], 'navigations_items_links_fk')->references(['id'])->on('navigations')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['navigation_item_id'], 'navigations_items_links_inv_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('navigations_items_master_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_master_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['navigation_id'], 'navigations_items_master_links_inv_fk')->references(['id'])->on('navigations')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('navigations_items_parent_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_parent_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['inv_navigation_item_id'], 'navigations_items_parent_links_inv_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('navigations_items_related', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'navigations_items_related_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'navigations_items_related_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('navigations_items_related_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_related_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['navigations_items_related_id'], 'navigations_items_related_links_inv_fk')->references(['id'])->on('navigations_items_related')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'songs_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'songs_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('strapi_api_tokens', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'strapi_api_tokens_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'strapi_api_tokens_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('task_frequencies', function (Blueprint $table) {
            $table->foreign(['task_id'], 'task_frequencies_task_id_fk')->references(['id'])->on('tasks')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('task_results', function (Blueprint $table) {
            $table->foreign(['task_id'], 'task_id_fk')->references(['id'])->on('tasks')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('up_permissions', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'up_permissions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'up_permissions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('up_permissions_role_links', function (Blueprint $table) {
            $table->foreign(['permission_id'], 'up_permissions_role_links_fk')->references(['id'])->on('up_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'up_permissions_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('up_roles', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'up_roles_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'up_roles_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('up_users', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'up_users_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'up_users_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('up_users_role_links', function (Blueprint $table) {
            $table->foreign(['user_id'], 'up_users_role_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'up_users_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('usages', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'usages_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'usages_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::table('usages_songs_links', function (Blueprint $table) {
            $table->foreign(['usage_id'], 'usages_songs_links_fk')->references(['id'])->on('usages')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['song_id'], 'usages_songs_links_inv_fk')->references(['id'])->on('songs')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        Schema::table('usages_users_permissions_users_links', function (Blueprint $table) {
            $table->foreign(['usage_id'], 'usages_users_permissions_users_links_fk')->references(['id'])->on('usages')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'usages_users_permissions_users_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usages_users_permissions_users_links', function (Blueprint $table) {
            $table->dropForeign('usages_users_permissions_users_links_fk');
            $table->dropForeign('usages_users_permissions_users_links_inv_fk');
        });

        Schema::table('usages_songs_links', function (Blueprint $table) {
            $table->dropForeign('usages_songs_links_fk');
            $table->dropForeign('usages_songs_links_inv_fk');
        });

        Schema::table('usages', function (Blueprint $table) {
            $table->dropForeign('usages_created_by_id_fk');
            $table->dropForeign('usages_updated_by_id_fk');
        });

        Schema::table('up_users_role_links', function (Blueprint $table) {
            $table->dropForeign('up_users_role_links_fk');
            $table->dropForeign('up_users_role_links_inv_fk');
        });

        Schema::table('up_users', function (Blueprint $table) {
            $table->dropForeign('up_users_created_by_id_fk');
            $table->dropForeign('up_users_updated_by_id_fk');
        });

        Schema::table('up_roles', function (Blueprint $table) {
            $table->dropForeign('up_roles_created_by_id_fk');
            $table->dropForeign('up_roles_updated_by_id_fk');
        });

        Schema::table('up_permissions_role_links', function (Blueprint $table) {
            $table->dropForeign('up_permissions_role_links_fk');
            $table->dropForeign('up_permissions_role_links_inv_fk');
        });

        Schema::table('up_permissions', function (Blueprint $table) {
            $table->dropForeign('up_permissions_created_by_id_fk');
            $table->dropForeign('up_permissions_updated_by_id_fk');
        });

        Schema::table('task_results', function (Blueprint $table) {
            $table->dropForeign('task_id_fk');
        });

        Schema::table('task_frequencies', function (Blueprint $table) {
            $table->dropForeign('task_frequencies_task_id_fk');
        });

        Schema::table('strapi_api_tokens', function (Blueprint $table) {
            $table->dropForeign('strapi_api_tokens_created_by_id_fk');
            $table->dropForeign('strapi_api_tokens_updated_by_id_fk');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign('songs_created_by_id_fk');
            $table->dropForeign('songs_updated_by_id_fk');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_permission_id_foreign');
            $table->dropForeign('role_has_permissions_role_id_foreign');
        });

        Schema::table('navigations_items_related_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_related_links_fk');
            $table->dropForeign('navigations_items_related_links_inv_fk');
        });

        Schema::table('navigations_items_related', function (Blueprint $table) {
            $table->dropForeign('navigations_items_related_created_by_id_fk');
            $table->dropForeign('navigations_items_related_updated_by_id_fk');
        });

        Schema::table('navigations_items_parent_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_parent_links_fk');
            $table->dropForeign('navigations_items_parent_links_inv_fk');
        });

        Schema::table('navigations_items_master_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_master_links_fk');
            $table->dropForeign('navigations_items_master_links_inv_fk');
        });

        Schema::table('navigations_items_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_links_fk');
            $table->dropForeign('navigations_items_links_inv_fk');
        });

        Schema::table('navigations_items_audience_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_audience_links_fk');
            $table->dropForeign('navigations_items_audience_links_inv_fk');
        });

        Schema::table('navigations_items', function (Blueprint $table) {
            $table->dropForeign('navigations_items_created_by_id_fk');
            $table->dropForeign('navigations_items_updated_by_id_fk');
        });

        Schema::table('navigations', function (Blueprint $table) {
            $table->dropForeign('navigations_created_by_id_fk');
            $table->dropForeign('navigations_updated_by_id_fk');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_role_id_foreign');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign('model_has_permissions_permission_id_foreign');
        });

        Schema::table('i18n_locale', function (Blueprint $table) {
            $table->dropForeign('i18n_locale_created_by_id_fk');
            $table->dropForeign('i18n_locale_updated_by_id_fk');
        });

        Schema::table('files_related_morphs', function (Blueprint $table) {
            $table->dropForeign('files_related_morphs_fk');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign('files_created_by_id_fk');
            $table->dropForeign('files_updated_by_id_fk');
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->dropForeign('feeds_created_by_id_fk');
            $table->dropForeign('feeds_updated_by_id_fk');
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->dropForeign('domains_tenant_id_foreign');
        });

        Schema::table('custom_apis', function (Blueprint $table) {
            $table->dropForeign('custom_apis_created_by_id_fk');
            $table->dropForeign('custom_apis_updated_by_id_fk');
        });

        Schema::table('comments_comment_thread_of_links', function (Blueprint $table) {
            $table->dropForeign('comments_comment_thread_of_links_fk');
            $table->dropForeign('comments_comment_thread_of_links_inv_fk');
        });

        Schema::table('comments_comment_report_related_links', function (Blueprint $table) {
            $table->dropForeign('comments_comment_report_related_links_fk');
            $table->dropForeign('comments_comment_report_related_links_inv_fk');
        });

        Schema::table('comments_comment_author_user_links', function (Blueprint $table) {
            $table->dropForeign('comments_comment_author_user_links_fk');
            $table->dropForeign('comments_comment_author_user_links_inv_fk');
        });

        Schema::table('comments_comment-report', function (Blueprint $table) {
            $table->dropForeign('comments_comment-report_created_by_id_fk');
            $table->dropForeign('comments_comment-report_updated_by_id_fk');
        });

        Schema::table('comments_comment', function (Blueprint $table) {
            $table->dropForeign('comments_comment_created_by_id_fk');
            $table->dropForeign('comments_comment_updated_by_id_fk');
        });

        Schema::table('checks', function (Blueprint $table) {
            $table->dropForeign('checks_host_id_foreign');
        });

        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropForeign('catalogs_created_by_id_fk');
            $table->dropForeign('catalogs_updated_by_id_fk');
        });

        Schema::table('audience', function (Blueprint $table) {
            $table->dropForeign('audience_created_by_id_fk');
            $table->dropForeign('audience_updated_by_id_fk');
        });

        Schema::table('admin_users_roles_links', function (Blueprint $table) {
            $table->dropForeign('admin_users_roles_links_fk');
            $table->dropForeign('admin_users_roles_links_inv_fk');
        });

        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropForeign('admin_users_created_by_id_fk');
            $table->dropForeign('admin_users_updated_by_id_fk');
        });

        Schema::table('admin_roles', function (Blueprint $table) {
            $table->dropForeign('admin_roles_created_by_id_fk');
            $table->dropForeign('admin_roles_updated_by_id_fk');
        });

        Schema::table('admin_permissions_role_links', function (Blueprint $table) {
            $table->dropForeign('admin_permissions_role_links_fk');
            $table->dropForeign('admin_permissions_role_links_inv_fk');
        });

        Schema::table('admin_permissions', function (Blueprint $table) {
            $table->dropForeign('admin_permissions_created_by_id_fk');
            $table->dropForeign('admin_permissions_updated_by_id_fk');
        });

        Schema::dropIfExists('websockets_statistics_entries');

        Schema::dropIfExists('users');

        Schema::dropIfExists('usages_users_permissions_users_links');

        Schema::dropIfExists('usages_songs_links');

        Schema::dropIfExists('usages');

        Schema::dropIfExists('up_users_role_links');

        Schema::dropIfExists('up_users');

        Schema::dropIfExists('up_roles');

        Schema::dropIfExists('up_permissions_role_links');

        Schema::dropIfExists('up_permissions');

        Schema::dropIfExists('tenants');

        Schema::dropIfExists('tasks');

        Schema::dropIfExists('task_results');

        Schema::dropIfExists('task_frequencies');

        Schema::dropIfExists('strapi_webhooks');

        Schema::dropIfExists('strapi_migrations');

        Schema::dropIfExists('strapi_database_schema');

        Schema::dropIfExists('strapi_core_store_settings');

        Schema::dropIfExists('strapi_api_tokens');

        Schema::dropIfExists('songs');

        Schema::dropIfExists('settings');

        Schema::dropIfExists('roles');

        Schema::dropIfExists('role_has_permissions');

        Schema::dropIfExists('personal_access_tokens');

        Schema::dropIfExists('permissions');

        Schema::dropIfExists('password_resets');

        Schema::dropIfExists('navigations_items_related_links');

        Schema::dropIfExists('navigations_items_related');

        Schema::dropIfExists('navigations_items_parent_links');

        Schema::dropIfExists('navigations_items_master_links');

        Schema::dropIfExists('navigations_items_links');

        Schema::dropIfExists('navigations_items_audience_links');

        Schema::dropIfExists('navigations_items');

        Schema::dropIfExists('navigations');

        Schema::dropIfExists('model_has_roles');

        Schema::dropIfExists('model_has_permissions');

        Schema::dropIfExists('jobs');

        Schema::dropIfExists('job_batches');

        Schema::dropIfExists('i18n_locale');

        Schema::dropIfExists('hosts');

        Schema::dropIfExists('health_check_result_history_items');

        Schema::dropIfExists('frequency_parameters');

        Schema::dropIfExists('files_related_morphs');

        Schema::dropIfExists('files');

        Schema::dropIfExists('feeds');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('domains');

        Schema::dropIfExists('custom_apis');

        Schema::dropIfExists('comments_comment_thread_of_links');

        Schema::dropIfExists('comments_comment_report_related_links');

        Schema::dropIfExists('comments_comment_author_user_links');

        Schema::dropIfExists('comments_comment-report');

        Schema::dropIfExists('comments_comment');

        Schema::dropIfExists('checks');

        Schema::dropIfExists('catalogs');

        Schema::dropIfExists('audience');

        Schema::dropIfExists('admin_users_roles_links');

        Schema::dropIfExists('admin_users');

        Schema::dropIfExists('admin_roles');

        Schema::dropIfExists('admin_permissions_role_links');

        Schema::dropIfExists('admin_permissions');
    }
}
