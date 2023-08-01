<?php

namespace App\Http\Controllers\Supply;

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
use App\Models\Order\OrderItemDeductionModel;

class Supply extends Controller
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
            $query["ordered_to_user_id"] = $request->user()->id;
        }
        
        // dd($query);

        $info=[
            'orders' => OrderModel::with('orderedByUser')->where($query)->orderBy('updated_at', 'DESC')->get(),
            'role_id' => $request->user()->role_id,
        ];
        // return $info;
        return view($this->frontend_theme.'supply.list', $info); 
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
                    "$product_tbl.product_id",
                    "$product_tbl.name",
                ]);
            }])->where(['order_id' => $request->pk])->first();

        if(!empty($order->orderItems)){
            foreach ($order->orderItems as $key => $value) {
                $query=[
                    'product_id' => $value['product_id'],
                    'price_per' => $value['price_per'],
                    'is_closed' => 0,
                    'is_deleted' => 0,
                ];
                // dd($query);
                $stock_qty = StockItemModel::where($query)->sum('stock_qty');
                // dd($stock_qty);
                $value["stock_qty"] = $stock_qty;
            }
        }

        $query=[
            "role_id" => $this->DEALER_ROLE_ID,
        ];

        if($order->orderedByUser->role_id==$this->CUSTOMER_ROLE_ID){
            $query["role_id"]= $this->RETAILER_ROLE_ID;
        }

        $info=[
            'users' => UserModel::where($query)->get(),
            'order' => $order,
            'status_choices' => OrderModel::getClassProperty('status_choices'),
            'products' => [],
            "role_id" => $request->user()->role_id,
        ];

        // return $info;
        if($request->user()->role_id==$this->RETAILER_ROLE_ID){
            return view($this->frontend_theme.'order.customer_order_form', $info);     
        }
 
        return view($this->frontend_theme.'supply.create', $info); 
    }

    public function itemDeduct(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = OrderItemModel::where(['order_item_id' => $request->order_item_id])->first();

            // check if item is already deducted
            $query = [
                'product_id' => $obj['product_id'],
                'price_per' => $obj['price_per'],
                'is_closed' => 0,
                'is_deleted' => 0,
            ];
            $stock_items = StockItemModel::where($query)->get();
            $order_qty = (int)$obj->qty;
            // dd($order_qty);
            $deduct_stock_objs_list = [];
            if(!empty($stock_items)){
                foreach($stock_items as $k => $v){
                    $v->stock_qty = (int)$v->stock_qty;
                    if($order_qty > 0){
                        if($v->stock_qty >= $order_qty){
                            $difference_qty = ($v->stock_qty-$order_qty);
                            
                            $v->deduction_qty = $order_qty;
                            $order_qty = 0;
                            $v->stock_qty = $difference_qty;
                            $v->is_closed = ($v->stock_qty<=0)? 1 : 0;
                        }else{
                            $v->deduction_qty = $v->stock_qty;
                            $order_qty -= $v->stock_qty;
                            $v->stock_qty = 0;
                            $v->is_closed = 1;
                        }
                        array_push($deduct_stock_objs_list, $v);
                    }
                }
            }
            
            
            $info["order_item_deductions"] = [];
            if($order_qty==0){
                foreach($deduct_stock_objs_list as $k => $v){
                    $query = [
                        "order_id" => $obj['order_id'],
                        "order_item_id" => $obj['order_item_id'],
                        "stock_id" => $v['stock_id'],
                        "stock_item_id" => $v['stock_item_id'],
                        "product_id" => $v['product_id'],
                        "deduction_qty" => $v['deduction_qty'],
                    ];
                    unset($v['deduction_qty']);
                    $item_deduction = OrderItemDeductionModel::create($query);
                    $v->save();
                    array_push($info["order_item_deductions"], $item_deduction);
                }

                $obj->save();

                DB::commit();
                $info['success'] = TRUE;
                $info['msg'] = "Stock deducted successfully.";
            }else{
                $info['success'] = FALSE;
                $info['msg'] = "Inusufficient Stock. Unable to deduct....";
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

    public function orderItemRollBack(Request $request){
        $info = [
            "success" => FALSE,
            "msg" => "Something went wrong",
        ];

        DB::beginTransaction(); 
        try {
            $obj = OrderItemModel::where(['order_item_id' => $request->order_item_id])->first();

            if($obj->status=="RETURN_REQUESTED_BY_CUSTOMER"){
                $obj->status="RETURN_REQUEST_ACCEPTED_BY_VENDOR";
            }

            $query=[
                'order_id' => $obj['order_id'],
                'order_item_id' => $obj['order_item_id'],
                "is_deleted" => 0,
            ];
            $order_deduction_items = OrderItemDeductionModel::where($query)->get();

            // return [$order_deduction_items, $query];

            foreach($order_deduction_items as $k => $v){
                $query = [
                    'stock_id' => $v['stock_id'],
                    'stock_item_id' => $v['stock_item_id'],
                    'product_id' => $v['product_id'],
                ];
                // return $query;
                $stock_item = $v->stockItem()->where($query)->first();
                // return $stock_item;
                $stock_item->stock_qty += (int)$v->deduction_qty;
                $stock_item->is_closed = 0;
                $stock_item->save();

                $v->is_deleted=1;
                $v->save();
            }
    
            $obj->save();

            DB::commit();
            $info['success'] = TRUE;
            $info['msg'] = "Order item restored successfully.";
        
        } catch (\Exception $e) {
            DB::rollback();
            $info['success'] = FALSE;
        }

        return $info;
    }

} 
