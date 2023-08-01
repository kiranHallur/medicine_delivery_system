<?php

namespace App\Models\Product;

use App\Models\Category\CategoryModel;
use App\Models\Image\ImageModel;
use App\Models\Manufacturer\ManufacturerModel;
use App\Models\Option\OptionModel;
use App\Models\Option\OptionWidgetModel;
use App\Models\Seo\SeoModel;
use App\Models\User\UserModel;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log; 

class ProductModel extends Model
{
    use SoftDeletes, ModelTrait; 
 
    protected $table="products"; 
    protected $primaryKey = 'product_id';
    protected $fillable = ['added_by_user_id', 'name', 'description', 'status', 'image', 'sort_order', 'is_deleted'];
    protected $with = [];
    

    public $choices = [
        'product_appearance'=> [
            ["key" => "PHYSICAL","value" => "Physical/Movable Product",],
            ["key" => "VIRTUAL","value" => "Virtual/Downloadable Product",],
        ],
        'dimension_class' => [
            ["key" => "CM","value" => "Centimeter"],
            ["key" => "MM","value" => "Millimeter"],
            ["key" => "INCH","value" => "Inch"],
        ],
        'weight_class' => [
            ["key" => "KG","value" => "Kilogram"],
            ["key" => "GRAM","value" => "Gram"],
        ]
    ];

    protected $appends = ['image_url', 'pk'];
    protected $file_directory = "products/";
    protected $morphClass = "product";

    public function getImageUrlAttribute() {
        return $this->resolveFileUrl($this->attributes['image'] ?? NULL);
    }
 
    public function manufacturer(){
        return $this->belongsTo(ManufacturerModel::class, 'manufacturer_id', 'manufacturer_id');
    }

    public function attributes(){
        $attributeModel = new AttributeModel();
        $attributeProductModel = new AttributeProductModel();
        $with_pivot_columns = $attributeProductModel->getFillable();
        array_push($with_pivot_columns, $attributeProductModel->pk);

        return $this->belongsToMany(AttributeModel::class, $attributeProductModel->getTable(), $this->primaryKey, $attributeModel->pk)
        ->withTimestamps()
        ->withPivot($with_pivot_columns)
        ->orderBy($attributeProductModel->getTable().'.sort_order', 'ASC')
        ->as($attributeProductModel->getTable());
    }


    public function categories(){
        $categoryModel = new CategoryModel();
        $categoryProductModel = new CategoryProductModel();
        $with_pivot_columns = $categoryProductModel->getFillable();
        array_push($with_pivot_columns, $categoryProductModel->pk);

        return $this->belongsToMany(CategoryModel::class, $categoryProductModel->getTable(), $this->primaryKey, $categoryModel->pk)
        ->withTimestamps()
        ->withPivot($with_pivot_columns)
        ->as($categoryProductModel->getTable());
    }

    public function options(){
        $optionModel = new OptionModel();
        $productOptionModel = new ProductOptionModel();
        $with_pivot_columns = $productOptionModel->getFillable();
        array_push($with_pivot_columns, $productOptionModel->pk);

        return $this->belongsToMany(OptionModel::class, $productOptionModel->getTable(), $this->primaryKey, $optionModel->pk)
        ->withTimestamps()
        ->withPivot($with_pivot_columns)
        ->orderBy($productOptionModel->getTable().'.sort_order', 'ASC')
        ->as($productOptionModel->getTable());
    }

    public function optionWidgets(){
        $optionWidgetModel = new OptionWidgetModel();
        $productOptionWidgetModel = new ProductOptionWidgetModel();
        $with_pivot_columns = $productOptionWidgetModel->getFillable();
        array_push($with_pivot_columns, $productOptionWidgetModel->pk);

        return $this->belongsToMany(OptionWidgetModel::class, $productOptionWidgetModel->getTable(), $this->primaryKey, $optionWidgetModel->pk)
        ->withTimestamps()
        ->withPivot($with_pivot_columns)
        ->orderBy($productOptionWidgetModel->getTable().'.sort_order', 'ASC')
        ->as($productOptionWidgetModel->getTable());
    }

    public function sliderImages(){
        $imageModel = new ImageModel();
        return $this->morphMany(ImageModel::class, 'image_morph')->orderBy($imageModel->getTable().'.sort_order', 'ASC');
    }

    public function createData($data) {
        $db_query = ProductModel::create($data);
        return $db_query;
    }

    public function updateData($data, $return_data = FALSE) {
        $db_query = ProductModel::where($data['condition'])->update($data['update_data']);
        if ($return_data == TRUE) {
            $db_query = ProductModel::find($data['condition'][$this->primaryKey]);
        }
        return $db_query;
    }

    public function fetchData($data, $type="OBJECT") {
    //    dd($data);
        $select_columns = (isset($data['select_columns'])) ? $data['select_columns'] : "*";
        $condition = (isset($data['condition'])) ? $data['condition'] : [];

        $db_query = ProductModel::select($select_columns)->where($condition);
        
        if (isset($data['search']) && !empty($data['search'])) {
            $db_query = $db_query->where(function ($query) use ($data) {});
        }
        
        if (isset($data['filters']) && !empty($data['filters'])) {
            
        }
        if (isset($data['limit']) && !empty($data['limit']) && $type != 'COUNT' && $data['limit'] != -1) {
            $db_query = $db_query->skip($data['offset']);
            $db_query = $db_query->take($data['limit']);
        }

        if (isset($data['order_by'])) {
            $db_query = $db_query->orderBy($data['order_by_field_name'], $data['order_by']);
        }

        if (isset($type)) {
            if ($type == 'OBJECT') {
                $db_query = $db_query->get();
                if (method_exists($db_query, 'count')) {
                    $count = $db_query->count();
                    if ($count == 0) {
                        $db_query = [];
                    }
                } 
            } else if ($type == 'OBJECT_FIRST') {
                $db_query = $db_query->first();
            } else if ($type == 'ARRAY') {
                $db_query = $db_query->get();
                if (!empty($db_query)) {
                    if (method_exists($db_query, 'toArray')) {
                        $db_query = $db_query->toArray();
                    } else {
                        $db_query = (Array) $db_query;
                    }
                }
            } else if ($type == 'ARRAY_FIRST') {
                $db_query = $db_query->first();
                if (!empty($db_query)) {
                    $db_query = $db_query->toArray();
                }
            } else if ($type == 'COUNT') {
                $db_query = $db_query->count($data['field_to_count']);
            } else if ($type == 'SUM') {
                $db_query = $db_query->sum($data['field_to_sum']);
            }
        }
        if (!empty($db_query) && isset($data['associate_relationships']) && $data['associate_relationships'] == TRUE) {
            if ($type == 'OBJECT' || $type == 'OBJECT_FIRST') {
                $result = $this->associateRelationship($db_query);
            }
        }

        return $db_query;
    }

    protected function associateRelationship($objects, $options = NULL) {

    }

    public function addedByUser()
    {
        return $this->belongsTo(UserModel::class, 'added_by_user_id', 'id');
    }
}
