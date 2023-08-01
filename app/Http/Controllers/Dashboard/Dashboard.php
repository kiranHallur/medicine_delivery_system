<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Utils\Common;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }
    
    public function index(Request $request){
        $info=[];
        return view($this->frontend_theme.'dashboard.dashboard', $info); 
    }
}
