<?php

namespace App\Http\Controllers\User;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\User\UserModel;
use App\Utils\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class Login extends Controller
{

    use AuthenticatesUsers;

    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function index(Request $request){
        $info=[
            "role" => NULL,
            "role_id" => NULL,
        ];
        $page = NULL;
        $role = $request->role;
        if($role=="dealer"){
            $page = "dealer";
            $info['role_id'] = $this->DEALER_ROLE_ID;
        }else if($role=="retailer"){
            $page = "retailer";
            $info['role_id'] = $this->RETAILER_ROLE_ID;
        }else if($role=="customer"){
            $page = "customer";
            $info['role_id'] = $this->CUSTOMER_ROLE_ID;
        }else if($role=="admin"){
            $page = "Admin";
            $info['role_id'] = $this->ADMIN_ROLE_ID;
        }else{
            return back()->with('warning', "Invalid role selected.");
        }
        if(!empty($page)){
            $info['role'] = $page;
            $page = "default";
            
        }
        // dd($info);

        return view($this->frontend_theme.".login.$page",$info);
    }
    
    public function verify(Request $request)
    {
        $rules=[
            'username' => 'required',
            'password' => 'required',
        ];
        $msg=[
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        // dd($validate->errors());
        if ($validate->fails()) {
            $request->session()->flash("error", "Validation error.");
            return redirect()->route('backend.login')
                        ->withErrors($validate)
                        ->withInput();
        }else{
            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
                'role_id' => $request->role_id,
            ];
            $auth = auth()->attempt($credentials);
            // dd($auth, $credentials);
            $info = [
                'success' => FALSE
            ];
            if($auth){
                $info = $this->common->getTokenOnLogin($credentials);
                // dd($info);
                $info['success'] = (isset($info['access_token']))? TRUE : FALSE;
                $info['user'] = [];
                if($info['success']){
                    $user = UserModel::where(['username' => $credentials['username']])->first();

                    if($user->is_deleted==1){
                        $request->session()->flash('error', "Your account has been blocked kindly contact administrator.");
                        return redirect(route('frontend.home'));            
                    }


                    if(Hash::check($credentials['password'], $user->password)){
                        $info['user'] = $user;
                    }else{
                        $info['success'] =  FALSE;
                    }
                }
                
            }else{
                $info['success'] = FALSE;
            }
            
        }

        // dd($info);
        if($info['success']){
            session([$this->session_name => $info]);
            return redirect(route('user.profile.edit'));
        }else{
            $request->session()->flash('error', "Invalid username and password");
            return redirect(route('frontend.home'));
        }
    }

    public function username()
    {
        return 'username';
    }

    public function logout(Request $request){
        $request->session()->forget($this->session_name);
        return redirect(route('frontend.home'))->with('info', 'Successfully Logged-Out');
    }

    public function forgotPassword(Request $request) {
        $info = [];
        return view($this->frontend_theme.'.login.forgot_password', $info);
    }

    public function mailResetPasswordLink(Request $request) {
        // dd($_POST);
        $messages = [
            'email.required' => 'This field is required.',
        ];

        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                        ], $messages);

        if ($validator->fails()) {
            return redirect(route('forgot-password'))
                            ->withErrors($validator)
                            ->withInput();
        } else {
            $mailHelper = new MailHelper();
            $user = UserModel::where(['email' => $request->email])->first();
            // dd($user);

            if (!empty($user)) {
                $query = [
                    'to' => $user->email,
                    'subject' => "Password reset link",
                    'user' => $user,
                    'reset_link' => route('reset-password', ["user_id" => $user->id]),
                    'view_path' => $this->frontend_theme."mails.send_reset_password_link",
                ];
                // dd($query);
                $result = $mailHelper->send($query);
                // dd($result);
                if ($result) {
                    $request->session()->flash('success', "Reset link is sent to your email.");
                    return redirect(route('frontend.home'));
                } else {
                    $request->session()->flash('error', "Error occured while trying to send reset link.");
                    return redirect(route('forgot-password', ["user_id" => $user->id]));
                }
                
            }else{
                $request->session()->flash('error', "Email is not found in our database.");
                return redirect(route('forgot-password', []));
            }
        }
    }

    public function resetPasswordForm(Request $request) {

        $user = UserModel::find($request->user_id);
        if (!empty($user)) {
            $info = [
                'user' => $user,
            ];
            return view($this->frontend_theme.'login.reset_password', $info);
        } else {
            $request->session()->flash('error', "Invalid link. Please try again.");
            return redirect(route('frontend.home'));
        }
    }

    public function saveResetPasswordForm(Request $request) {
    //    dd($_POST);
        $messages = [
            'user_id.required' => 'This field is required.',
            'password.required' => 'This field is required.',
            'confirm_password.required' => 'This field is required.',
            'confirm_password.same' => 'Password did not match.',
        ];

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'password' => 'required',
                    'confirm_password' => 'required|same:password',
                        ], $messages);

        if ($validator->fails()) {
            $request->session()->flash('error', "Please fill marked fields.");
            return back()->withErrors($validator)->withInput();
        } else {
            $user = UserModel::find($request->user_id);
            // dd($user);
            DB::beginTransaction();
            try {
                $user->password = bcrypt($request->password);
                $user->save();
                DB::commit();
                $request->session()->flash('success', "Password reset successful.");
                return redirect(route('frontend.home'));
            } catch (\Exception $e) {
                // dd($e);
                DB::rollback();
                $request->session()->flash('error', "Invalid link. Please try again by going to forgot password section.");
                return redirect(route('forgot-password', []));
            }
        }
    }
}
