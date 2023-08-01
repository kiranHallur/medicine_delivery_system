<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ModelTrait {


    public static function tblName() {
        $class = get_called_class();
        $obj = new $class();
        return $obj->table;;
    }

    // public function tblName() {
    //     return $this->table;
    // }

    function __get($key){
        if($key=="pk"){
            return $this->pk();
        }else if($key=="file_directory"){
            return $this->file_directory ?? NULL;
        }
        return $this->getAttribute($key);
    }

    public static function getClassProperty($key){
        $class = get_called_class();
        $obj = new $class();
        return $obj->$key;
    } 

    function pk(){
        // dd($this);
        $pk = (isset($this[$this->primaryKey]))? $this[$this->primaryKey] : NULL;
        return $pk;
    }

    public function getPkAttribute(){
        // dd($this);
        $pk = $this->pk();
        return $pk;
    }

    public function resolveFilePath($file) {
        $directory = $this->file_directory ?? "";
        $path = public_path(config('constants.storage_path')).$directory.$file;
        return $path;
    }

    public function resolveFileUrl($file) {
        // dd($file);
        $directory = $this->file_directory ?? "";
        $path = url(config('constants.storage_path').$directory.$file);        
        return $path;
    }

    public function unlinkFile(){
        $is_unlinked=FALSE;
        try {            
            $directory = $this->file_directory ?? "";
            $file = $this->attributes["image"] ?? NULL;
            $path = public_path(str_replace("public/", "", config('constants.storage_path')).$directory.$file); 
            if(is_file($path) && unlink($path)){
                $is_unlinked=TRUE;
            }
        } catch (\Throwable $th) {
            // dd($th);
            $is_unlinked=FALSE;
        }
        return $is_unlinked;
    }
    
    public function syncPivot($related_model_path, $cond, $query){

        $related_model = new $related_model_path();

        $result = $related_model::withTrashed()->where($cond)->first();

        if($result){
            //update
            $result->fill($query);
            $result->deleted_at= null;
            $result->save();
            $new_object = $result;
        }else{
            //create
            $new_object = $related_model_path::create($query);
        }

        // dd($new_object);
        return $new_object;
    }

    public static function getStatusId($title, $choice_field_name){
        $class = get_called_class();
        $obj = new $class();
        $list = $obj->$choice_field_name ?? [];

        $context = NULL;
        foreach($list as $k => $v){
            if($v["title"]==$title){
                $context = $v["id"];
            }
        }
        dd($context);
        return $context;
    }
}
