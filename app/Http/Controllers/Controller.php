<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use App\Jobs\DoClosing;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    
    public function daily_closing()
    {
        DoClosing::dispatch();
        return true;
    }
}
