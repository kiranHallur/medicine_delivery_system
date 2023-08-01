<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->session_name=config('constants.session_name');
        $this->backend_theme=config('constants.backend_views');
        $this->frontend_theme=config('constants.frontend_views');
        $this->ADMIN_ROLE_ID = config('constants.ADMIN_ROLE_ID');
        $this->DEALER_ROLE_ID = config('constants.DEALER_ROLE_ID');
        $this->CUSTOMER_ROLE_ID = config('constants.CUSTOMER_ROLE_ID');
        $this->RETAILER_ROLE_ID = config('constants.RETAILER_ROLE_ID');
    }
}
