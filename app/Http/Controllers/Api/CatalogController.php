<?php

namespace App\Http\Controllers\Api;

use App\Models\Catalog;
use Illuminate\Http\Request;
use Orion\Concerns\DisableAuthorization;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;

class CatalogController extends Controller
{
    use DisableAuthorization;
   // use DisablePagination;

    /**
     * @var string $model
     */
    protected $model = Catalog::class;
}
