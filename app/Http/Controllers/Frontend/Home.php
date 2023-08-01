<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product\AttributeModel;
use App\Utils\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Validator;

class Home extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }
    
    public function index(Request $request)
    {
        $info=[];
        return view($this->frontend_theme.'default', $info);
    }
}
