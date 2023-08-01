<?php

namespace App\Models\Order;

use App\Models\Stock\StockItemModel;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class OrderItemDeductionModel extends Model
{
    use SoftDeletes, ModelTrait;
 
    protected $table="order_item_deductions";
    protected $primaryKey = 'order_item_deduction_id'; 
    protected $fillable = ['order_id', 'order_item_id', 'stock_id', 'stock_item_id', 'product_id', 'deduction_qty', 'is_deleted'];
    protected $with = [];
    protected $appends = [];
 
    public function stockItem()
    {
        return $this->belongsTo(StockItemModel::class, 'stock_item_id', 'stock_item_id');
    }
}
