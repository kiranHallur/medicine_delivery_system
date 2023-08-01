<?php

namespace App\Models\Stock;

use App\Models\User\UserModel;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class StockModel extends Model
{
    use SoftDeletes, ModelTrait;
 
    protected $table="stock";
    protected $primaryKey = 'stock_id';
    protected $fillable = ['added_by_user_id', 'supplier_name', 'supplier_invoice_date', 'is_closed', 'is_deleted'];
    protected $with = [];

    public function stockItems(){
        return $this->hasMany(StockItemModel::class, 'stock_id', 'stock_id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(UserModel::class, 'added_by_user_id', 'id');
    }
}
