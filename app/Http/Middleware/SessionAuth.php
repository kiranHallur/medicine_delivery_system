<?php

namespace App\Http\Middleware;

use Redirect;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class SessionAuth {

    public function handle($request, Closure $next) {
        $session_name = Config('constants.session_name');
        if (!$request->session()->exists($session_name)) {
            $request->session()->flash('error', "User not logged-in.");
            return redirect(route('frontend.home'));
        }
        return $next($request);
    }

}