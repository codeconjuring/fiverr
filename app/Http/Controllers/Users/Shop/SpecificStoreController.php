<?php

namespace App\Http\Controllers\Users\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;

class SpecificStoreController extends Controller
{
    public function index($id, $name) 
    {
        $store = Store::find($id);
        
        if (empty($store)) {
            return redirect('shop');
        } 

        $data['menu']              = 'shop';
        $data['store']             = $store;
        $data['products']          = $products = Product::select(['id', 'title', 'photo', 'price', 'currency_id'])->where(['store_id' => $id])->orderBy('created_at', 'desc')->paginate(12);

        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo', 'store_id', 'created_at'])->where(['store_id' => $id])->orderBy('created_at', 'desc')->get();

        return view('frontend.shop.pages.store', $data);
    }

    public function storeCategoryProducts($name, $store_id, $category_id) 
    {
        $store                     = Store::find($store_id);
        $data['menu']              = 'shop';
        $data['category_id']       = $category_id;
        $data['store']             = $store;
        $data['products']          = $products = Product::select(['id', 'title', 'photo', 'price', 'currency_id'])->where(['product_category_id' => $category_id])->orderBy('created_at', 'desc')->paginate(12);

        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo', 'store_id', 'created_at'])->where(['store_id' => $store_id])->orderBy('created_at', 'desc')->get();

        return view('frontend.shop.pages.store-category-product', $data);
    }
}
