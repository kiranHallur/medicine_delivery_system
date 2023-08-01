<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductModel;
use App\Utils\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Validator;
use DB;

class Product extends Controller
{
 
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $query = [
            'is_deleted' => 0,
        ];
        // dd($request->user()->role_id, $this->ADMIN_ROLE_ID);
        if($request->user()->role_id != $this->ADMIN_ROLE_ID){
            $query["added_by_user_id"] = $request->user()->id;
        }
        
        // dd($query);

        $info=[
            'products' => ProductModel::where($query)->orderBy('updated_at', 'DESC')->get(),
        ];

        if($request->user()->role_id == $this->ADMIN_ROLE_ID){
            $info["products"]->load('addedByUser');
        }

        // return $info;

        return view($this->frontend_theme.'product.list', $info); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $info = [
            'product' => []
        ];
        return view($this->frontend_theme.'product.create', $info);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $rules=[
            'name' => 'required',
        ];
        $msg=[
            'name.required' => 'Product Name is required.',

        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            $info = $this->process($request);
            // dd($info);
            if($info['success']){
                $request->session()->flash('success', 'Product created successfully.');
                return redirect(route('product.edit',['pk' => $info['product_id']]));
            }else{
                $request->session()->flash('error', 'Something went wrong...');
                return redirect(route('product.create'))->withInput();
            }
            
        } 
    }

    public function process(Request $request)
    {
        $id = $request->product_id ?? NULL;
        $key_name = "product";
        $info = [
            "success" => FALSE,
            $key_name => [],
        ];

        DB::beginTransaction(); 
        try {
            $query = [
                'name' => normalize_str($request->name),
                'added_by_user_id' => $this->common->currentUser($request)->id,
             ];

            // dd($query, $id);

            $info[$key_name] = ProductModel::updateOrCreate(['product_id' => $id],$query);
            $info['product_id']= $info[$key_name]->getKey();
            // dd($info);
            DB::commit();
            $info['success'] = TRUE;
        } catch (\Exception $e) {
            dd($e);
            Log::info($e);
            DB::rollback();
            $info['success'] = FALSE;
        }
        // dd($info);
        return $info;
    }

    public function edit(Request $request)
    {
        $info=[
            'product' => ProductModel::where(['is_deleted' => 0, 'product_id' => $request->pk])->first(),
        ];
        // return $info;
        return view($this->frontend_theme.'product.create', $info);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $rules=[
            'name' => 'required',
        ];
        $msg=[
            'name.required' => 'Product Name is required.',

        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            $info= $this->process($request);

            if($info['success']){
                $request->session()->flash('success', 'Product updated successfully.');
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }
            return redirect(route('product.edit',['pk' => $request->product_id]));
            
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error_msg = "Somthing went wrong.";
        $info=[
            "success" => FALSE,
            "msg" => $error_msg,
        ];
        // return $info;
        $product = ProductModel::find($id);
        // dd($product);
        try {            
            $product->delete();
            $info["success"] = TRUE;
            $info["msg"] = "Product removed successfully.";
        } catch (\Throwable $th) {
            Log::info($th);
            $info["success"] = FALSE;
            $info["msg"] = $error_msg;
        }
        return $info;
    }
}
