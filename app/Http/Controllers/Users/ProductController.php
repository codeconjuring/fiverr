<?php
/**
* Product Controller 
*
* Description : Admin can Create Product for user and others things to do with Product(View, Update, Delete)
*
*@package Product Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  26/09/19
*@version 
*/

namespace App\Http\Controllers\Users;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['menu']     = 'shop';

        $data['products'] = $products = Product::select('products.id', 'products.store_id', 'products.product_category_id', 'products.currency_id', 'products.product_code', 'products.title', 'products.description', 'products.photo', 'products.stock', 'products.price', 'products.created_at')
                                        ->leftjoin('stores as s', 's.id', '=', 'products.store_id')
                                        ->where(['s.user_id' => auth()->user()->id])
                                        ->orderby('created_at', 'desc')
                                        ->paginate(10);

        return view('user_dashboard.shop.product.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $data['menu']              = 'shop';
        $data['currencies']        = $currency         = Currency::where(['status' => 'Active'])->select(['id', 'code'])->get();

        $data['stores']            = $stores           = Store::select(['id', 'name'])->where(['user_id' => auth()->user()->id, 'status' => 'Active'])->get();
    
        // $data['productCategories'] = $productCategories = ProductCategory::select('product_categories.id',  'product_categories.name')
        //                                 ->leftjoin('stores as s', 's.id', '=', 'product_categories.store_id')
        //                                 ->where(['s.user_id' => auth()->user()->id])
        //                                 ->orWhere(['store_id' => NULL])
        //                                 ->get();
                                        //dd($productCategories);

        $data['productCategories'] = $productCategory = ProductCategory::where(['store_id' => NULL, 'status' => 'Active'])->select(['id', 'name'])->get();

        return view('user_dashboard.shop.product.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array (
            'title'                => 'required',
            'product_category_id'  => 'required',
            'product_code'         => 'required|max:10|min:4|unique:products,product_code',
            'store_id'             => 'required',
            'currency_id'          => 'required',
            'price'                => 'required|numeric',
            'stock'                => 'required|numeric',
            'photo'                => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array (
            'title'                => 'Name',
            'product_category_id'  => 'Product Category',
            'product_code_id'      => 'Product Code',
            'currency_id'          => 'Currency',
            'price'                => 'Price',
            'stock'                => 'Stock',
            'store_id'             => 'Store Name',
            'photo'                => 'Photo',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $product                       = new Product();
        $product->store_id             = $request->store_id;
        $product->product_category_id  = $request->product_category_id;
        $product->currency_id          = $request->currency_id;
        $product->product_code         = $request->product_code;
        $product->title                = $request->title;
        $product->description          = $request->description;
        $product->stock                = $request->stock;
        $product->price                = $request->price;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            if (isset($photo)) {
                $filename  = time() . '.' . $photo->getClientOriginalExtension();
                $extension = strtolower($photo->getClientOriginalExtension());
                $location  = public_path('images/shop/product/' . $filename);
                if (file_exists($location)) {
                    unlink($location);
                }
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                    // Image::make($photo)->fit(420, 512, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    Image::make($photo)->save($location);

                    $product->photo = $filename;
                } else {
                    $this->helper->one_time_message('error', 'Invalid Image Format!');
                }
            }
        }
        if ($product->save()) {
            $this->helper->one_time_message('success', 'Product added successfully!');
            return redirect('products');
        } 
        
        $this->helper->one_time_message('error', 'Something went wrong!');
        return redirect('products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param [int], $id
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['menu']        = 'shop';
        $data['product']     = $products           = Product::find($id);
        $data['currencies']  = $currency           = Currency::select(['id', 'code'])->get();
        $data['stores']      = $stores             = Store::select(['id', 'name'])->where(['user_id' => auth()->user()->id, 'status' => 'Active'])->get();
        
        $data['productCategories'] = $productCategories = ProductCategory::select('product_categories.id',  'product_categories.name')
                                        ->leftjoin('stores as s', 's.id', '=', 'product_categories.store_id')
                                        ->where(['s.user_id' => auth()->user()->id])
                                        ->where(['s.id' => $products->store_id])
                                        ->orWhere(['store_id' => NULL])
                                        ->get();
        
        if (empty($products)) {
            $this->helper->one_time_message('error', 'Product not found!');
            return redirect('products');
        }
        return view('user_dashboard.shop.product.edit', $data);   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id      = base64_decode($request->product_id);
        $product = Product::find($id);
        
        if (empty($product)) {
            $this->helper->one_time_message('error', 'Product not found!');
            return redirect('products');
        }

        $rules = array (
            'title'                => 'required',
            'product_category_id'  => 'required',
            'product_code'         => 'required|max:10|min:4|unique:products,product_code,'.$id,
            'store_id'             => 'required',
            'currency_id'          => 'required',
            'price'                => 'required|numeric',
            'stock'                => 'required|numeric',
            'photo'                => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array (
            'title'                => 'Name',
            'product_category_id'  => 'Product Category',
            'product_code_id'      => 'Product Code',
            'currency_id'          => 'Currency',
            'price'                => 'Price',
            'stock'                => 'Stock',
            'store_id'             => 'Store Name',
            'photo'                => 'Photo',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $product->store_id             = $request->store_id;
        $product->product_category_id  = $request->product_category_id;
        $product->currency_id          = $request->currency_id;
        $product->product_code         = $request->product_code;
        $product->title                = $request->title;
        $product->description          = $request->description;
        $product->stock                = $request->stock;
        $product->price                = $request->price;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            if (isset($photo)) {
                $filename  = time() . '.' . $photo->getClientOriginalExtension();
                $extension = strtolower($photo->getClientOriginalExtension());
                $location = public_path('images/shop/product/' . $filename);

                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {       
                    // Image::make($photo)->fit(420, 512, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    Image::make($photo)->save($location);

                    $oldfilename = $product->photo;
                    if (!(is_null($oldfilename))) {
                        $oldfile = public_path('images/shop/product/' . $product->photo) ;
                        unlink($oldfile);
                    }
                    $product->photo = $filename;
                } else {
                    $this->helper->one_time_message('error', 'Invalid image format!');
                }
            }
        }

        if ($product->save()) {
            $this->helper->one_time_message('success', 'Product updated successfully!');
            return redirect('products');
        }

        $this->helper->one_time_message('error', 'Something went wrong!');
        return redirect('products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $product = Product::find($request->id);
    
        if (empty($product)) {
            $this->helper->one_time_message('error', __('Product not found!'));
            return redirect('products');
        }

        $filename = $product->photo;
        if ($product->delete()) {

            if (!empty($filename) && file_exists(public_path('images/shop/product/' . $filename))) {
                @unlink(public_path('images/shop/product/' . $filename));
            }

            $this->helper->one_time_message('success', __('Product deleted successfully.'));
            return redirect('products');
        }

        $this->helper->one_time_message('error', __('Something went wrong, please try again.'));
        return redirect('products');
    }

    /**
     * Product unique Code function
     * 
     * Description: Checking in product adding form if the product_code available or not 
     *
     * @param Request $request
     * @return void
     */
    public function checkProductCode(Request $request)
    {
        $product = Product::where(['product_code' => $request->product_code])->exists();

        if ($product) {
            $data['status']  = false;
            $data['fail']    = "Product code has already been taken.";
        } else {
            $data['status']  = true;
            $data['success'] = "Product code is available.";
        }
        return json_encode($data);
    }

    /**
     * Product unique Code Check function
     * 
     * Description: Checking in product Update form if the product_code is available or not
     *
     * @param Request $request
     * @return void
     */
    public function checkProductCodeUpdate(Request $request)
    {
        $req_id = $request->product_id;

        $product  = Product::where(['product_code' => $request->product_code])->where(function ($query) use ($req_id) {
            $query->where('id', '!=', $req_id);
        })->exists();

        if ($product) {
            $data['status']  = false;
            $data['fail']    = "Product code has already been taken.";
        } else {
            $data['status']  = true;
            $data['success'] = "Product code is available.";
        }
        return json_encode($data);
    }

    /**
     * Delete Photo function
     * 
     * Delete directly product Photo on product update by clicking on 'X' button
     *
     * @param Request $request
     * @return void
     */
    public function deleteProductPhoto(Request $request)
    {
        $photo = $_POST['photo'];
        $product_id = base64_decode($request->product_id);

        if (isset($photo)) {
            $product = Product::where(['id' => $product_id, 'photo' => $request->photo])->first();
            if ($product) {
                Product::where(['id' => $product_id, 'photo' => $request->photo])->update(['photo' => null]);
                if ($photo != null) {
                    $dir = public_path('images/shop/product/' . $request->photo);
                    if (file_exists($dir)) {
                        unlink($dir);
                    }
                }
                $data['success'] = 1;
                $data['message'] = 'Photo has been successfully deleted!';
            } else {
                $data['success'] = 0;
                $data['message'] = "No Record Found!";
            }
        }
        echo json_encode($data);
        exit();
    }

    public function getCategoriesByStore(Request $request) 
    {
        return ProductCategory::getProductCategoriesByStore(['store_id' => $request->store_id]);
    }
}
