<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\User\UserModel;
use App\Models\User\RoleModel;
use App\Models\User\UserProfileModel;
use App\User;
use App\Utils\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;

class Register extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function index(Request $request){
        $info=[
            "role" => NULL
        ];
        $page = NULL;
        $role = $request->role;
        $role_id = NULL;
        if($role=="dealer"){
            $page = "dealer";
            $role_id = $this->DEALER_ROLE_ID;

        }else if($role=="retailer"){
            $page = "retailer";
            $role_id = $this->RETAILER_ROLE_ID;
        }else if($role=="customer"){
            $page = "customer";
            $role_id = $this->CUSTOMER_ROLE_ID;
        }else{
            return back()->with('warning', "Invalid role selected.");
        }
        if(!empty($page)){
            $info['role'] = $page;
            $info['role_id'] = $role_id;
            $page = "default";
        }
        // dd($info);

        return view($this->frontend_theme.".register.$page",$info);
    }
    
    public function store(Request $request)
    {
        
        $rules=[
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ];

        $other_rules = [
            'shop_name' => 'required',
            'shop_location' => 'required',
            'home_location' => 'required',
        ];
        
        $role_id = $request->role_id;
        if($role_id==$this->DEALER_ROLE_ID){
            array_merge($rules, $other_rules);
        }else if($role_id==$this->RETAILER_ROLE_ID){
            array_merge($rules, $other_rules);
        }else if($role_id==$this->CUSTOMER_ROLE_ID){
            array_merge($rules, ['home_location' => 'required']);
        }

        $msg=[
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid Email',
            'confirm_password.required' => 'Confirm Password is required.',
            'confirm_password.same' => 'Confirm Password doesnot match with password.',
            'shop_name.required' => 'Shop Name is required.',
            'shop_location.required' => 'Shop Location is required.',
            'home_location.required' => 'Location is required.',
        ];

        $role = RoleModel::find($request->role_id);
        // dd($_POST, $role);

        $validate = Validator::make($request->all(),$rules,$msg);
        // dd($validate->errors());
        if ($validate->fails()) {
            return redirect('register?role='.strtolower($role->name))
                        ->withErrors($validate)
                        ->withInput();
        }else{
            // dd("entrer");
            $info = [
                'success' => FALSE
            ];

            DB::beginTransaction(); 
            try {
                $query = [
                    'username' => $request->username,
                    'name' => normalize_str($request->name),
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role_id' => $request->role_id,
                ];
                // dd($query);
                $auth =  UserModel::create($query);

                //create profile
                $query = [
                    'home_address' => normalize_str($request->home_address),
                    'home_location' => normalize_str($request->home_location),
                    'shop_name' => normalize_str($request->shop_name),
                    'shop_address' => normalize_str($request->shop_address),
                    'gst_no' => normalize_str($request->gst_no),
                    'shop_location' => normalize_str($request->shop_location),
                    'contact_no' => normalize_str($request->contact_no),
                ];

                $auth->profile()->create($query);
                // dd($auth);
                DB::commit();
                $info['success'] = TRUE;
            } catch (\Exception $e) {
                dd($e);
                Log::info($e);
                DB::rollback();
                $info['success'] = FALSE;
            }

            if($info['success']){
                return redirect(route('frontend.home'))->with("success", "You have registered successfully.");
            }else{
                return back()->with('danger', "Something went wrong try again...");
            }            
        }        
    }
}
