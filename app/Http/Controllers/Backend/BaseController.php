<?php

namespace App\Http\Controllers\Backend;

use App\Traits\HasResourceActions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,HasResourceActions;
    protected $guard_name = 'admin';
}
