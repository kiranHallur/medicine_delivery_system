<?php

namespace App\Models\Order;

use App\Models\User\UserModel;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class OrderModel extends Model
{
    
    use SoftDeletes, ModelTrait;
 
    protected $table="orders";
    protected $primaryKey = 'order_id';
    protected $fillable = ['ordered_by_user_id', 'ordered_to_user_id', 'description', 'prescription_path', 'allow_vendor_to_modify', 'status', 'status_desc', 'reason_to_return', 'is_deleted'];
    protected $with = [];
    protected $appends = ['ordered_to_user_shop_name', 'ordered_by_user_shop_name', 'prescription_path_url']; 
    public $status_choices = [
        ["id" => "PENDING","title" => "Pending"],
        ["id" => "PROCESSING","title" => "Processing"],
        ["id" => "DELIVERED","title" => "Delivered"],
        ["id" => "CANCELLED","title" => "Cancelled"],
        ["id" => "RETURN_REQUESTED_BY_CUSTOMER","title" => "Return Requested by Customer"],
        ["id" => "RETURN_REQUEST_ACCEPTED_BY_VENDOR","title" => "Return Request accepted by vendor"],
    ];

    


    public $file_directory = "orders/";

    public function getPrescriptionPathUrlAttribute() {
        return $this->resolveFileUrl($this->attributes['prescription_path'] ?? NULL);
    }
    
    public function getOrderedToUserShopNameAttribute(){
        return $this->orderedToUser->profile->shop_name ?? "Not Available";
    }

    public function getOrderedByUserShopNameAttribute(){
        return $this->orderedByUser->profile->shop_name ?? "Not Available";
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id', 'order_id');
    }

    public function orderedToUser()
    {
        return $this->belongsTo(UserModel::class, 'ordered_to_user_id', 'id');
    }


    public function orderedByUser()
    {
        return $this->belongsTo(UserModel::class, 'ordered_by_user_id', 'id');
    }
}
