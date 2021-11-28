<?php
/**
* Product Model 
*
* @package Product
* @author tehcvillage <support@techvill.org>
* @contributor Ahammed Imtiaze <imtiaze.techvill@gmail.com>
* @contributor Md. Nobeul Islam <[nobeul.techvill@gmail.com]>
* @created 09-07-2019
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * getAll function
     *
     * @return void
     */
    public static function getAll($category=null, $search=null)
    {       

        $allProducts = Product::with('currency:id,symbol')->whereHas('store', function($query) {
            $query->where('status', 'active');
        })
        ->whereHas('product_category', function($q) {
            $q->where('status', 'active');
        })
        ->orderby('created_at', 'desc');

        if ($category != '') {

            $allProducts = $allProducts->where('product_category_id', $category);
        }

        if ($search != '') {

            $allProducts = $allProducts->where('title', 'like', '%'.$search.'%');
        } 

        return $allProducts->paginate(6);

        

    }

    /**
     * view function
     *
     * @param integer $id
     * @return void
     */
    public static function view($id)
    {
        $product = Product::where(['products.id' => $id])->with(['store:id,name', 'currency:id,name,code,symbol', 'product_category:id,name'])->select('products.id', 'products.product_code', 'products.title', 'products.description', 'products.photo', 'products.stock', 'products.price', 'store_id', 'currency_id', 'product_category_id' )->first();

        return $product;
    }

    /**
     * updateStock function
     *
     * @param integer $id
     * @param integer $stock
     * @return boolean
     */
    public static function updateStock($id, $stock = 0)
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json(['status' => 401, 'message' => __('Product not found')]);
        }

        $product->stock = $stock;
        return $product->save();
    }

}
