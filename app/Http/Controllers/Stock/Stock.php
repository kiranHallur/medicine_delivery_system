<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductModel;
use App\Utils\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Validator;
use App\Models\Stock\StockModel;
use App\Models\Stock\StockItemModel;
use App\Models\User\UserModel;
use DB;

class Stock extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function index(Request $request)
    {

        $stock_tbl = StockModel::getClassProperty('table');

        $query = [
            $stock_tbl.'.is_deleted' => 0,
        ];
        if($request->user()->role_id != $this->ADMIN_ROLE_ID){
            $query["added_by_user_id"] = $request->user()->id;
        }
        
        $stocks = StockModel::join(
            UserModel::tblName(), StockModel::tblName().'.added_by_user_id',  UserModel::tblName().'.id'
        )->where($query)->orderBy($stock_tbl.'.updated_at', 'DESC')->get();
        // dd($stocks);

        $info = [
            "stocks" => $stocks,
        ]; 

        if($request->user()->role_id == $this->ADMIN_ROLE_ID){
            $info["stocks"]->load('addedByUser');
        }

        // return $info;

        return view($this->frontend_theme.'stock.list', $info);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $info = [
            'products' => ProductModel::where(['is_deleted' => 0, "added_by_user_id" => $request->user()->id])->get(),
            'stocks' => [],
        ];
        // dd($info);
        return view($this->frontend_theme.'stock.create', $info);
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
            'supplier_name' => 'required',
        ];
        $msg=[
            'supplier_name.required' => 'Supplier name is required.',
        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        if ($validate->fails()) {
            return redirect()->route('stock.create')
                        ->withErrors($validate)
                        ->withInput();
        }else{
            $info = $this->process($request);
            if($info['success']){
                $request->session()->flash('success', 'Stock added successfully.');
                return redirect(route('stock.edit',['pk' => $info['stock_id']]));
            }else{
                $request->session()->flash('error', 'Something went wrong...');
                return redirect(route('stock.create'))->withInput();
            }
        }
    }

    public function process(Request $request)
    {
        // dd($_POST);
        $id = $request->stock_id ?? NULL;
        $key_name = "stock";
        $info = [
            "success" => FALSE,
            $key_name => [],
            "items" => [],
        ];

        DB::beginTransaction(); 
        try {
            $query = [
                'supplier_name' => normalize_str($request->supplier_name),
                'supplier_invoice_date' => (!empty($request->supplier_invoice_date))? date('Y-m-d', strtotime($request->supplier_invoice_date)) : NULL,
                // 'added_by_user_id' => $this->common->currentUser($request)->id,
                'is_closed' => ($request->is_closed!=null)? $request->is_closed : 0,
            ];

            if($request->user()->role_id != $this->ADMIN_ROLE_ID){
                $query["added_by_user_id"] = $request->user()->id;
            }

            // dd($query, $id);

            $info[$key_name] = StockModel::updateOrCreate(['stock_id' => $id],$query);
            $info['stock_id'] = $info[$key_name]->getKey();

            if(!empty($info[$key_name])){
                $items = (!empty($request->items))? $request->items : [];
                // dd($items);
                foreach ($items as $k => $v) {
                    $cond = [
                        'stock_id' => $info['stock_id'],
                        'stock_item_id' => (!empty($v['stock_item_id']))? $v['stock_item_id'] : NULL,
                    ];
                    $query = [
                        'stock_id' => $info['stock_id'],
                        'product_id' => $v['product_id'],
                        'barcode' => (!empty($v['barcode']))? $v['barcode'] : NULL,
                        'stock_qty' => $v['stock_qty'],
                        'price_per' => $v['price_per'],
                        'initial_qty' => $v['initial_qty'],
                        'is_closed' => (!empty($v['is_closed']))? $v['is_closed'] : 0,
                    ];
                    $item = StockItemModel::updateOrCreate($cond,$query);
                    array_push($info["items"], $item);
                }
            }
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
    
    public function edit(Request $request, $pk)
    {
        $product_tbl = ProductModel::tblName();
        $product_pk = ProductModel::getClassProperty('primaryKey');
        
        $stock_item_tbl = StockItemModel::tblName();

        $stock = StockModel::with(['stockItems' => function ($query) use ($product_tbl, $product_pk, $stock_item_tbl) {
            $query->join($product_tbl, $product_tbl.".".$product_pk, '=', $stock_item_tbl.".".$product_pk)
            ->where([
                $stock_item_tbl.'.is_deleted' => 0,
            ])
            ->select([
                "$stock_item_tbl.stock_item_id",
                "$stock_item_tbl.stock_id", 
                "$stock_item_tbl.product_id", 
                "$stock_item_tbl.barcode", 
                "$stock_item_tbl.price_per", 
                "$stock_item_tbl.stock_qty", 
                "$stock_item_tbl.initial_qty", 
                "$stock_item_tbl.is_closed", 
                "$product_tbl.name"
            ]);
        }])->where([
            'stock_id' => $pk,
        ])->first();

        $info = [
            "stock" => $stock,
            'products' => ProductModel::where(['is_deleted' => 0, "added_by_user_id" => $request->user()->id])->get(),
        ];



        // return $info['stock'];

        return view($this->frontend_theme.'stock.create', $info);
    }


    public function removeItem(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = StockItemModel::where(['stock_item_id' => $request->pk])->first();
            // dd($obj);
            $obj->is_deleted=1;
            $obj->save();
            $obj->delete();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Deleted successfully.";
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

    public function updateItem(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = StockItemModel::where(['stock_item_id' => $request->pk])->first();
            $obj->product_id = $request->product_id;
            $obj->barcode = (!empty($request->barcode))? $request->barcode : NULL;
            $obj->initial_qty = $request->initial_qty;
            $obj->stock_qty = $request->stock_qty;
            $obj->price_per = $request->price_per;
            $obj->is_closed = (!empty($request->is_closed))? $request->is_closed : 0;
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Update successful.";
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules=[
            'supplier_name' => 'required',
        ];
        $msg=[
            'supplier_name.required' => 'Supplier name is required.',
        ];
        $validate = Validator::make($request->all(),$rules,$msg);
        if ($validate->fails()) {
            return redirect()->route('stock.edit', ['pk' => $request->stock_id])
                        ->withErrors($validate)
                        ->withInput();
        }else{
            $info = $this->process($request);
            if($info['success']){
                $request->session()->flash('success', 'Stock updated successfully.');
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }
            return redirect(route('stock.edit',['pk' => $info['stock_id']]));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyStock(Request $request)
    {
        $error_msg = "Somthing went wrong.";
        $info=[
            "success" => FALSE,
            "msg" => $error_msg,
        ];
        // return $info;
        $stock = StockModel::find($request->pk);
        DB::beginTransaction(); 
        try {     
            $stock->is_deleted=1;
            $stock->stockItems()->update([
                "is_deleted" => 1
            ]);
            $stock->save();       
            $stock->delete();
            $stock->stockItems()->delete();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Stock removed successful.";
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $info['success'] = FALSE;
        }
        return $info;
    }
}
