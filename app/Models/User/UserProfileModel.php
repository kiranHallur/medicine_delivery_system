<?php

namespace App\Models\User;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class UserProfileModel extends Model
{
    use SoftDeletes, ModelTrait;

    protected $table = 'users_profile';
    protected $primaryKey = 'user_profile_id';  
    protected $fillable = ['user_id', 'home_address', 'home_location', 'shop_name', 'shop_address', 'gst_no', 'shop_location', 'contact_no'];
    protected $with = [];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
