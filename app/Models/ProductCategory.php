<?php
/**
* ProductCategory Model 
*
* description : 
*
*@package ProductCategory
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  05/09/19
*@version 
*/

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function getProductCategoriesByStore($options = []) 
    {

        $options    = array_merge($options, ['onlyActive' => true]);

        $data       = ['success' => false, 'message' => 'No Data found.'];

        $conditions = ['status' => 'Active'];
        if (is_array($options)) {
            if (!$options['onlyActive']) {
                unset($conditions['status']);
            }
            unset($options['onlyActive']);

            if (!empty($options['store_id'])) {
                $conditions["store_id"] = $options['store_id'];
                unset($options['store_id']);
            }

            $conditions = array_merge($conditions, $options);
        }

        $productCategories  = ProductCategory::where($conditions)->orWhere(['store_id' => NULL])->get(['id', 'name', 'status']);

        if (count($productCategories)) {
            $data ['success'] = true;
            $data ['message'] = count($productCategories).' category(s) found';
            $data ['data']    = $productCategories;
        }

        echo json_encode($data);
        exit();
    }

    /**
     * getAll function
     *
     * @return object
     */
    public static function getAll()
    {
        return ProductCategory::select('product_categories.id', 'product_categories.name', 'product_categories.photo', 'product_categories.store_id', 'product_categories.status', 'product_categories.created_at')
                                ->with('store:id,name')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
    }
}
