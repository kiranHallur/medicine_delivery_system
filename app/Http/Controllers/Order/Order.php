<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\ProductModel;
use App\Models\Order\OrderModel;
use App\Models\Order\OrderItemModel;
use App\Models\Stock\StockItemModel;
use App\Models\Stock\StockModel;
use App\Models\User\UserModel;
use App\Utils\Common;
use Illuminate\Support\Facades\Log; 
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Utils\FileManager;

class Order extends Controller
{ 
    public function __construct()
    {
        parent::__construct();
        $this->common = new Common();
    }

    public function index(Request $request)
    {
        $query = [
            'is_deleted' => 0,
        ];
        // dd($request->user()->role_id, $this->ADMIN_ROLE_ID);
        if($request->user()->role_id != $this->ADMIN_ROLE_ID){
            $query["ordered_by_user_id"] = $request->user()->id;
        }
        
        // dd($query);

        $info=[
            'orders' => OrderModel::where($query)->orderBy('updated_at', 'DESC')->get(),
        ];
        // return $info;

        return view($this->frontend_theme.'order.list', $info); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $query=[
            "role_id" => $this->DEALER_ROLE_ID,
        ];

        if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
            $query['role_id'] = $this->RETAILER_ROLE_ID;
        }

        // dd($query);

        $info=[
            'users' => UserModel::where($query)->get(),
            'order' => [],
            'products' => [],
            'role_id' => $request->user()->role_id,
        ];

        // return $info['users'];

        if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
            return view($this->frontend_theme.'order.customer_order_form', $info);     
        }
        return view($this->frontend_theme.'order.create', $info); 
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
            'ordered_to_user_id' => 'required',
        ];
        $msg=[
            'ordered_to_user_id.required' => 'Select vendor from whom you want to order.',
        ];

        if(count((Array)$request->items)==0 && $request->user()->role_id!=$this->CUSTOMER_ROLE_ID){
            return back()->with("error", "You have no items added to your basket.");
        }

        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            // dd('s');
            $info = $this->process($request); 
            // dd($info);
            if($info['success']){

                // dd(($request->redirect_route == "SUPPLY_VIEW"), $_POST);

                if($request->redirect_route == "SUPPLY_VIEW"){
                    $request->session()->flash('success', 'Successfully.');
                    
                    return redirect(route("order.edit", ["pk" => $info['order_id']]));
                }

                $request->session()->flash('success', 'Order created successfully.');
                if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
                    $request->session()->flash('success', 'Order booked successfully.');
                    return redirect(route('orders'));
                }
                return redirect(route('order.edit',['pk' => $info['order_id']]));
            }else{
                $request->session()->flash('error', 'Something went wrong...');
                return redirect(route('order.create'))->withInput();
            }
            
        } 
    }

    public function process(Request $request)
    {
        // dd($request->file('prescription_path'));
        // dd($_POST);
        $id = $request->order_id ?? NULL;
        $key_name = "order";
        $info = [
            "success" => FALSE,
            $key_name => [],
            "items" => [],
            "msg" => "Somethng went wrong",
        ];

        DB::beginTransaction(); 
        try {

            $order = OrderModel::find($id);
            // dd($_POST, $order);

            $query = [
                'ordered_to_user_id' => $request->ordered_to_user_id,
                'description' => NULL,
                'prescription_path' => NULL,
                'allow_vendor_to_modify' => (!empty($request->allow_vendor_to_modify))? $request->allow_vendor_to_modify : 0,
            ];

            if(empty($order)){
                $query['ordered_by_user_id'] = $request->user()->id;
            }

            // dd($order, $query);
            
            $role_id = $request->user()->role_id;
            if($this->DEALER_ROLE_ID == $role_id || $this->RETAILER_ROLE_ID == $role_id){
                $query["status"] = (!empty($request->status))? $request->status : "PENDING";
                $query["status_desc"] = (!empty($request->status_desc))? normalize_str($request->status_desc) : NULL;
            }

            $prescription_path = $request->file('prescription_path') ?? NULL;
            // dd($order, $prescription_path);
            if(!empty($order) && !empty($order->prescription_path)){
                $query['prescription_path'] = $order->prescription_path;
            }
            
            if($prescription_path!=NULL){
                // dd($prescription_path);
                $result = (new FileManager())->fileUpload($prescription_path, [
                    'path' => 'orders'
                ]); 
                if($result['success']){
                    $query['prescription_path'] = $result['file_name'];
                }
            }

            // dd($query, $prescription_path);

            // dd($query, $id);

            $info[$key_name] = OrderModel::updateOrCreate(['order_id' => $id],$query);
            $info['order_id'] = $info[$key_name]->getKey();

            if(!empty($info[$key_name])){
                $items = (!empty($request->items))? $request->items : [];
                // dd($items);
                foreach ($items as $k => $v) {

                    $stock_item = StockItemModel::where([
                        'stock_item_id' => $v['stock_item_id'],
                        'is_closed' => 0,
                        'is_deleted' => 0,
                    ])->first();

                    // dd($stock_item);

                    $price_per = (float)$stock_item->price_per;
                    $stock_qty = (int)$stock_item->stock_qty;
                    $purchase_qty = (int)$v['purchase_qty'];

                    $cond = [
                        'order_id' => $info['order_id'],
                        'order_item_id' => (!empty($v['order_item_id']))? $v['order_item_id'] : NULL,
                    ];
                    $query = [
                        'order_id' => $info['order_id'],
                        'product_id' => $v['product_id'],
                        'stock_id' => $v['stock_id'],
                        'stock_item_id' => $stock_item->stock_item_id,
                        'qty' => $purchase_qty,
                        'description' => NULL,
                        'price_per' => $price_per,
                    ];
                    // dd($query);
                    $item = OrderItemModel::updateOrCreate($cond,$query);
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
    
    public function edit(Request $request)
    {

        $order_tbl = OrderModel::tblName();
        $order_pk = OrderModel::getClassProperty('primaryKey');
        $order_item_tbl = OrderItemModel::tblName();
        $product_tbl = ProductModel::tblName();
        $product_pk = ProductModel::getClassProperty('primaryKey');

        $order = OrderModel::with([
            'orderItems' => function ($query) use ($product_tbl, $product_pk, $order_item_tbl) {
                $query->join($product_tbl, "$product_tbl.$product_pk", '=', "$order_item_tbl.$product_pk")
                ->where([
                    $order_item_tbl.'.is_deleted' => 0,
                ])
                ->select([
                    "$order_item_tbl.order_id",
                    "$order_item_tbl.order_item_id",
                    "$order_item_tbl.stock_id",
                    "$order_item_tbl.stock_item_id",
                    "$order_item_tbl.qty as purchase_qty",
                    "$order_item_tbl.price_per",
                    "$order_item_tbl.description",
                    "$order_item_tbl.status",
                    "$order_item_tbl.status_desc",
                    "$order_item_tbl.reason_to_return",
                    "$product_tbl.name"
                ]);
            }])->where(['order_id' => $request->pk])->first();

        // return $order;

        $query=[
            "role_id" => $this->DEALER_ROLE_ID,
        ];
        
        if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
            $query["role_id"]= $this->RETAILER_ROLE_ID;
        }

        $info=[
            'users' => UserModel::where($query)->get(),
            'order' => $order,
            'role_id' => $request->user()->role_id,
            'products' => [],
        ];

        // return $info;

        if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
            // dd('s');
            return view($this->frontend_theme.'order.customer_order_form', $info);     
        }

        return view($this->frontend_theme.'order.create', $info); 
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
            'ordered_to_user_id' => 'required',
        ];
        $msg=[
            'ordered_to_user_id.required' => 'Select user from whom you want to order.',
        ];

        if(count((Array)$request->items)==0 && $request->user()->role_id!=$this->CUSTOMER_ROLE_ID){
            // return back()->with("error", "You have no items added to your basket.");
        } 

        $validate = Validator::make($request->all(),$rules,$msg);
        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }else{
            $info = $this->process($request); 
            
            if($info['success']){
                $request->session()->flash('success', 'Order updated successfully.');
            }else{
                $request->session()->flash('error', 'Something went wrong...');
            }

            if($request->user()->role_id==$this->CUSTOMER_ROLE_ID){
                $request->session()->flash('success', 'Order booked successfully.');
                return redirect(route('order.edit', ["pk" => $request->order_id]));
            }

            if($request->redirect_route == "SUPPLY_VIEW"){
                return redirect(route("supply.edit", ["pk" => $request->order_id]));
            }

            return redirect(route('order.edit',['pk' => $request->order_id]));
            
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = OrderModel::find($request->pk);
            $obj->is_deleted=1;
            $obj->orderItems()->update(['is_deleted' => 1]);
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Removed successfully.";
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

    public function destroyItem(Request $request)
    {
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = OrderItemModel::where(['order_item_id' => $request->pk])->first();
            $obj->is_deleted=1;
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Removed successfully.";
            
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

    public function loadUserProducts(Request $request){
        
        $product_tbl = ProductModel::getClassProperty('table');
        $stock_tbl = StockModel::getClassProperty('table');
        $stock_items_tbl = StockItemModel::getClassProperty('table');

        $query = [
            "$product_tbl.is_deleted" => 0,
            
            "$stock_tbl.is_closed" => 0,
            "$stock_tbl.is_deleted" => 0,
            "$stock_tbl.added_by_user_id" => $request->user_id,

            "$stock_items_tbl.is_closed" => 0,
            "$stock_items_tbl.is_deleted" => 0,
            
        ];

        $select = [
            "$stock_tbl.stock_id",
            "$stock_tbl.added_by_user_id",

            "$product_tbl.name",

            "$stock_items_tbl.stock_item_id",
            "$stock_items_tbl.product_id",
            "$stock_items_tbl.price_per",
            "$stock_items_tbl.barcode",
            "$stock_items_tbl.stock_qty",
            "$stock_items_tbl.initial_qty",
            "$stock_items_tbl.is_closed as stock_item_is_closed",
        ];

        $stocks = StockItemModel::select($select)->where($query)
                ->join($stock_tbl, function($query) use ($stock_items_tbl, $stock_tbl){
                    $query->on("$stock_items_tbl.stock_id", "=", "$stock_tbl.stock_id");
                })
                ->join($product_tbl, function($query) use ($product_tbl, $stock_items_tbl){
                    $query->on("$stock_items_tbl.product_id", "=", "$product_tbl.product_id");
                })
                ->get();
        
        return $stocks;
    }


    public function cancelOrder(Request $request)
    {
        $info = [
            "order" => OrderModel::with('orderItems.product')->find($request->order_id)
        ];
        // return $info;
        return view($this->frontend_theme.'order.cancel_form', $info); 
    }

    public function cancelOrderStore(Request $request)
    {
        // dd($_POST);

        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];
        DB::beginTransaction(); 
        try {
            $obj = OrderModel::find($request->order_id);
            $obj->status= "RETURN_REQUESTED_BY_CUSTOMER" ;
            $obj->reason_to_return= normalize_str($request->reason_to_return_or_cancel_order);
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Order status change to return or cancel.";
            $request->session()->flash('success', $info["msg"]);
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
            $request->session()->flash('danger', $info["msg"]);
        }

        return redirect(route('orders'));
    }

    public function itemReturns(Request $request){
        $info = [
            "returns" => OrderItemModel::with('order')->where([
                'is_deleted' => 0, 
                "status" => "RETURN_REQUESTED_BY_CUSTOMER",
            ])->get()
        ];
        // return $info;
        return view($this->frontend_theme.'order-returns.list', $info);
    }

    public function itemReturnForm(Request $request){
        $info = [
            "item" => OrderItemModel::find($request->order_item_id)
        ];
        return view($this->frontend_theme.'order-returns.confirm_item_return_form', $info);
    }

    public function itemReturnStore(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];
        DB::beginTransaction(); 
        try {
            $obj = OrderItemModel::where(['order_item_id' => $request->order_item_id])->first();
            $obj->status = "RETURN_REQUESTED_BY_CUSTOMER";
            $obj->reason_to_return = normalize_str($request->reason_to_return);
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $request->session()->flash('success', "Item return request sent to vendor");
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
            $request->session()->flash('success', "Something went wrong");
        }

        return redirect(route('order.edit', ["pk" => $obj->order_id]));
    }

    public function orderCancelOrReturn(Request $request){
        $query = [];
        $query["ordered_to_user_id"] = $request->user()->id;

        $info = [
            "orders" => OrderModel::where($query)->get(),
        ];

        // return $info;
        return view($this->frontend_theme.'order-returns.order-list', $info);
    }

    public function orderCancelOrReturnStore(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];
        DB::beginTransaction(); 
        try {
            $obj = OrderModel::find($request->order_id);
            $obj->status = "RETURN_REQUEST_ACCEPTED_BY_VENDOR";
            $obj->save();
            DB::commit();
            $info['success'] = TRUE;
            $request->session()->flash('success', "Status updated successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
            $request->session()->flash('success', "Something went wrong");
        }

        if($obj->orderedByUser->role_id==$this->CUSTOMER_ROLE_ID){
            return redirect(route('supply.edit', ["pk" => $obj->order_id]));    
        }

        return redirect(route('order.edit', ["pk" => $obj->order_id]));
    }
}
