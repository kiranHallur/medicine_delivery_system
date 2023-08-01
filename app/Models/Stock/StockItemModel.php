<?php

namespace App\Models\Stock;


use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class StockItemModel extends Model
{
    use SoftDeletes, ModelTrait;
 
    protected $table="stock_items";
    protected $primaryKey = 'stock_item_id'; 
    protected $fillable = ['stock_id', 'product_id', 'barcode', 'price_per', 'stock_qty', 'initial_qty', 'is_closed', 'is_deleted'];
    protected $with = [];

    public function stock(){
        return $this->belongsTo(StockModel::class, 'stock_id', 'stock_id');
    }
} 
