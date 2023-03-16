<?php

namespace App\Http\Controllers\Api;

use App\Models\Song;
use Illuminate\Database\Eloquent\Model;
use Orion\Concerns\DisableAuthorization;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class SongController extends Controller
{
    use DisableAuthorization;
   // use DisablePagination;

    /**
     * @var string
     */
    protected $model = Song::class;
}
