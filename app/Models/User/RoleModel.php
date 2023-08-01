<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    protected $fillable = ['name', 'is_deleted'];

    // public function user(){
    //     return $this->belongsTo(UserModel::class);
    // }
}
 