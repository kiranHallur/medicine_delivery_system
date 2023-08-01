<?php

namespace App\Models\Order;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 
use App\Models\Order\OrderItemDeductionModel;
use App\Models\Product\ProductModel;

class OrderItemModel extends Model
{
    use SoftDeletes, ModelTrait;
 
    protected $table="order_items";
    protected $primaryKey = 'order_item_id';  
    protected $fillable = ['order_id', 'stock_id', 'stock_item_id', 'product_id', 'qty', 'price_per', 'description', 'status', 'status_desc', 'reason_to_return', 'is_deleted'];
    protected $with = [];
    protected $appends = ['is_order_items_deducted', 'gross_price']; 
    public $status_choices = [
        ["id" => "PENDING","title" => "Pending"],
        ["id" => "PROCESSING","title" => "Processing"],
        ["id" => "DELIVERED","title" => "Delivered"],
        ["id" => "CANCELLED","title" => "Cancelled"],
        ["id" => "RETURN_REQUESTED_BY_CUSTOMER","title" => "Return Requested by Customer"],
        ["id" => "RETURN_REQUEST_ACCEPTED_BY_VENDOR","title" => "Return Request accepted by vendor"],
    ];

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }

    public function getGrossPriceAttribute(){
        return num_fmt((int)$this->qty*(float)$this->price_per);
    }

    public function getIsOrderItemsDeductedAttribute(){
        $count = count_objects($this->deductedItems);
        $is_order_deducted = FALSE;
        if($count>0){
            $is_order_deducted = TRUE;
        }
        return $is_order_deducted;
    }

    public function getPriceAttribute(){
        $price_per = (float)$this->price_per;
        $qty = (int)$this->qty;
        $price = number_format(($price_per*$qty),2);
        return $price;
    }

    public function deductedItems()
    {
        return $this->hasMany(OrderItemDeductionModel::class, 'order_item_id', 'order_item_id')->where(['is_deleted' => 0]);
    }
}
