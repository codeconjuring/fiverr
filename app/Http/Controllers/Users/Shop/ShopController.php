<?php

namespace App\Http\Controllers\Users\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Input;

class ShopController extends Controller
{
    public function index() 
    {
        $data['menu']              = 'shop';
        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        
        $data['products']          = $products = Product::select('products.id', 'products.store_id', 'products.product_category_id',                                      'products.currency_id', 'products.product_code', 'products.title', 'products.description',                                        'products.photo', 'products.stock', 'products.price', 'products.created_at')
                                        ->leftjoin('stores as s', 's.id', '=', 'products.store_id')
                                        ->leftjoin('product_categories as pc', 'pc.id', '=', 'products.product_category_id')
                                        ->where(['pc.status' => 'Active', 's.status' => 'Active'])
                                        ->orderby('created_at', 'desc')
                                        ->paginate(12);

        return view('frontend.shop.pages.home', $data);
    }

    public function defaultSearch(Request $request)
    {
        $q = $request->q;

        if (empty($q)) {
            return redirect('/shop');
        }

        $data['menu']              = 'shop';

        $data['productCategories'] = $productCategories = ProductCategory::select(['name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        $data['products']  = $products = Product::select('products.id', 'products.store_id', 'products.product_category_id',                                      'products.currency_id', 'products.product_code', 'products.title', 'products.description',                                        'products.photo', 'products.stock', 'products.price', 'products.created_at')
                                ->leftjoin('stores as s', 's.id', '=', 'products.store_id')
                                ->leftjoin('product_categories as pc', 'pc.id', '=', 'products.product_category_id')
                                ->where(['pc.status' => 'Active', 's.status' => 'Active'])
                                ->where('products.title', 'LIKE', "%{$q}%") 
                                ->orWhere('products.description', 'LIKE', "%{$q}%") 
                                ->orWhere('products.price', 'LIKE', "%{$q}%") 
                                ->paginate(12);

        $data['products']->appends ( array ('q' => Input::get ( 'q' )));

        return view('frontend.shop.pages.search', $data)->withQuery($q);
    }

    public function defaultCategoryProducts($id)
    {
        $data['menu']        = 'shop';

        $data['category_id'] = $id;

        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        $data['products'] = $products = Product::select(['id', 'product_category_id', 'title', 'photo', 'price', 'currency_id'])->where(['product_category_id' => $id])->paginate(12);

        return view('frontend.shop.pages.home-category-product', $data);
    }
}
