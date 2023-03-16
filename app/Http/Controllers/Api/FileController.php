<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use Illuminate\Http\Request;
use Orion\Concerns\DisableAuthorization;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;

class FileController extends Controller
{
    use DisableAuthorization;
    use DisablePagination;

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected $model = File::class;
}
