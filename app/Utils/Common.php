<?php

namespace App\Utils;

use App\Http\Controllers\Controller;
use App\Models\User\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class Common extends Controller
{
    public function __construct()
    {
        parent::__construct();        
    }

    public function validationResponse($validator)
    {
        return response()->json([
            "validation_error" => $validator->errors()
        ]);
    }

    public function getTokenOnLogin($data)
    {
        $client = $this->getOauthClient(2);
      
        $guzzle = new Client;
        $form = [
            'grant_type' => 'password',
            'client_id' => (string)$client->id,
            'client_secret' => (string)$client->secret,
            'username' => $data['username'],
            'password' => $data['password'],
            'scope' => '',
        ];

        // dd($form);
        $response = $guzzle->post(url('/oauth/token'), [
            'form_params' => $form,
        ]);

        // dd($response);

        return json_decode((string) $response->getBody(), true);
    }

    public function getOauthClient($id)
    {
        return $this->client = DB::table('oauth_clients')->where('id', $id)->first();
    }

    public function postClient($form, $route, $multipart_files=[],$method="POST"){
        $guzzle = new Client;
        // dd($form, $route,$multipart_files,$method);
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => $this->getAccessToken(),
        ];
        $end_point = config('api_constants.api_url').$route;

        $post_key_name = ($multipart_files)? 'multipart' : 'form_params';

        $pay_load = [
            $post_key_name => ($multipart_files)? $multipart_files : $form,
            'headers' => $headers,
        ];

        if($multipart_files){
            $pay_load['query'] = $form;
        }
        // dd($pay_load);
        $response = $guzzle->request($method,$end_point, $pay_load);
        // $response = $guzzle->post($end_point, $pay_load);

        // dd(json_decode((string) $response->getBody(), true));
        return json_decode((string) $response->getBody(), true);
    }

    public function getClient($route,$query_string=[]){
        $guzzle = new Client;
        // dd($route,$query_string);
        $headers = [
            'Accept' => 'application/json',
        ];
        $headers['Authorization'] = $this->getAccessToken();
        $end_point = config('api_constants.api_url').$route;
        // dd($headers,$end_point);
        
        $response = $guzzle->request('GET', $end_point, [
            "headers" => $headers,
            "query" => $query_string,
        ]);

        // dd($response);

        return json_decode((string) $response->getBody(), true);
    }

    public function getAccessToken($append_bearer=TRUE){
        $session = session($this->session_name);
        $token="";
        if($append_bearer){
            $token = "Bearer ";   
        }
        $token .= $session['access_token'] ?? NULL;
        return $token;
    }

    public function prepareMultipartFiles($files=[]){
        $multipart_files=[];
        if(isset($files[0])){
            foreach($files as $k=>$v){
                $data = [
                    'name'     => $v["key_name"],
                    'contents' => fopen($v["contents"]->getrealPath(), 'r'),
                ];
                if($v["Content-type"]){
                    $data['Content-type'] = $v["Content-type"];
                }
                array_push($multipart_files,$data);
            }
        }else{
            $data = [
                'name'     => $files["key_name"],
                'contents' => fopen($files["contents"]->getrealPath(), 'r'),
            ];
            if($files["Content-type"]){
                $data['Content-type'] = $files["Content-type"];
            }
            array_push($multipart_files,$data);
        }
        // dd($multipart_files);
        return $multipart_files;
    }

    public function getMimeType($file){
        return explode('/',$obj->getMimeType());
    }

    public function currentUser(Request $request){
        // dd($request->user());
        return $request->user();
    }
    
}
