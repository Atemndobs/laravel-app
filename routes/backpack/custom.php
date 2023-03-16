<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('song', 'SongCrudController');
    Route::crud('file', 'FileCrudController');
    Route::crud('admin-user', 'AdminUserCrudController');
    Route::crud('catalog', 'CatalogCrudController');
   # Route::crud('feed', 'FeedCrudController');
    Route::crud('up-user', 'UpUserCrudController');
    Route::crud('usage', 'UsageCrudController');
}); // this should be the absolute last line of this file
