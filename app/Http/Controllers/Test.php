<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Utils\FileManager;

class Test extends Controller
{
    public function testSave(Request $request){

        $file_obj = $request->file('file');

        // $x = $file_obj->store("sample.png");
        // dd($x);


        $x = $file_obj->storeAs("test_order", "09-03-2020__01-32-00__PM.png", "public");
        dd($x);

    }

}
