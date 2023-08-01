<?php

namespace App\Models\User;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class UserModel extends Model
{

    use ModelTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $primaryKey = 'id'; 
    protected $fillable = [
        'username', 'name', 'email', 'password','role_id', 'is_deleted'
    ];
    protected $with = ['role', 'profile'];
    protected $appends = ['shop_name'];

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo(RoleModel::class, 'role_id', 'role_id');
    }

    public function profile() 
    {
        return $this->hasOne(UserProfileModel::class, 'user_id', 'id');
    }

    public function getShopNameAttribute(){
        $shop_name = $this->profile->shop_name ?? "Not Available";
        return $shop_name;
    }
}
