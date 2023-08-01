<?php

namespace App\Providers;

use App\Models\Category\CategoryModel;
use App\Models\Manufacturer\ManufacturerModel;
use App\Models\Product\ProductModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
/*        View::composer(
                '*', 'App\Http\ViewComposers\Company'
        );*/
        
        View::composer(
                '*', 'App\Http\ViewComposers\SessionInfo'
        );
        
        View::composer(
                '*', 'App\Http\ViewComposers\AssetVersion'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'category' => CategoryModel::class,
            'manufacturer' => ManufacturerModel::class,
            'product' => ProductModel::class,
        ]);

        $theme_name = config('constants.frontend_views');
        $component_path = $theme_name."components.";

        $theme_component_arr = [
            ['file_name' => 'radio','alias' => 'radio_btn'],
        ];

        // Blade::component($component_path.'radio', 'radio_btn');

        foreach(($theme_component_arr ?? []) as $k => $v){
            // dd($component_path.$v["file_name"]);
            Blade::component($component_path.$v["file_name"], $v["alias"]);
        }
    }
}
