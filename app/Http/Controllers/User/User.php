<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\UserModel;
use App\Models\User\UserProfileModel;
use App\Utils\Common;
use Illuminate\Support\Facades\Log; 
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Utils\FileManager;
use Illuminate\Validation\Rule;

class User extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function index(Request $request)
    {
        
        $info=[
            'users' => UserModel::all(),
        ];

        // return $info;
        return view($this->frontend_theme.'user.list', $info); 
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $info = [
            "user" => []
        ];
        try {
            $info["user"] = UserModel::find($request->pk);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return view($this->frontend_theme.'user.show', $info); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function statusStore(Request $request){
        $key_name = "user";
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
            $key_name => [],
        ];

        DB::beginTransaction(); 
        try {
            // dd($query, $id);
            $info[$key_name] = UserModel::find($request->id);
            if($info[$key_name]->is_deleted==0){
                $info[$key_name]->is_deleted=1;
            }else{
                $info[$key_name]->is_deleted=0;
            }
            $info[$key_name]->save();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "User status changed successfully.";
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            $info['success'] = FALSE;
            $info['msg'] = "Failed to change user status.";
        }
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
