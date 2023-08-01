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


class Profile extends Controller
{ 
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function edit(Request $request)
    {
        
        $info=[
            'user' => UserModel::find($request->user()->id),
        ];

        // return $info;
        return view($this->frontend_theme.'profile.form', $info); 
    }


    public function update(Request $request)
    {

        $id = $request->user()->id;
        $rules=[
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
        ];
        $msg=[
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'This Email is already being taken.',

        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        // dd($validate->errors());
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            
            $key_name = "user";
            $info = [
                "success" => FALSE,
                $key_name => [],
            ];

            DB::beginTransaction(); 
            try {
                $query = [
                    'name' => normalize_str($request->name),
                    'email' => normalize_str($request->email),
                ];
                // dd($query, $id);
                $info[$key_name] = UserModel::find($id);
                $info[$key_name]->fill($query);
                $info[$key_name]->save();

                $query=[
                    'gst_no' => normalize_str($request->gst_no),
                    'home_location' => normalize_str($request->home_location),
                    'home_address' => normalize_str($request->home_address),
                ];
                
                $info[$key_name]->profile->fill($query);
                // dd($info[$key_name]->profile->save());
                $info[$key_name]->profile->save();
                DB::commit();
                $info['success'] = TRUE;
            } catch (\Exception $e) {
                dd($e);
                Log::info($e);
                DB::rollback();
                $info['success'] = FALSE;
            }
            // dd($info);
            if($info['success']){
                $request->session()->flash('success', 'Profile updated successfully.');
                
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }
            return redirect(route('user.profile.edit'));
        }
    }

    public function changePassword(Request $request)
    {

        $id = $request->user()->id;
        $rules=[
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ];
        $msg=[
            'old_password.required' => 'Old Password is required.',
            'new_password.required' => 'New Password is required.',
            'confirm_password.required' => 'Confirm Password is required.',
            'confirm_password.same' => 'Confirm Password must match with New Password.',

        ];

        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            
            $key_name = "user";
            $info = [
                "success" => FALSE,
                $key_name => [],
            ];

            DB::beginTransaction(); 
            try {
                // dd($query, $id);
                $info[$key_name] = UserModel::find($id);
                $info[$key_name]->password = bcrypt($request->new_password);
                // dd($info[$key_name]);
                $info[$key_name]->save();
                DB::commit();
                $info['success'] = TRUE;
            } catch (\Exception $e) {
                dd($e);
                Log::info($e);
                DB::rollback();
                $info['success'] = FALSE;
            }
            // dd($info);
            if($info['success']){
                $request->session()->flash('success', 'Password updated successfully.');
                
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }
            return redirect(route('user.profile.edit'));
        }
    }

    public function shopUpdate(Request $request)
    {

        $id = $request->user()->id;
        $rules=[
            'shop_name' => 'required',
            'shop_location' => 'required',
        ];
        $msg=[
            'shop_name.required' => 'Shop Name is required.',
            'shop_location.required' => 'Shop Location is required.',

        ];

        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            
            $key_name = "user";
            $info = [
                "success" => FALSE,
                $key_name => [],
            ];

            DB::beginTransaction(); 
            try {
                $info[$key_name] = UserProfileModel::where(["user_id" => $id])->first();
                $query = [
                    'shop_name' => normalize_str($request->shop_name),
                    'shop_address' => normalize_str($request->shop_address),
                    'shop_location' => normalize_str($request->shop_location),
                ];
                $info[$key_name]->fill($query);
                $info[$key_name]->save();
                DB::commit();
                $info['success'] = TRUE;
            } catch (\Exception $e) {
                dd($e);
                Log::info($e);
                DB::rollback();
                $info['success'] = FALSE;
            }
            // dd($info);
            if($info['success']){
                $request->session()->flash('success', 'Shop details updated successfully.');
                
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }
            return redirect(route('user.profile.edit'));
        }
    }
}
