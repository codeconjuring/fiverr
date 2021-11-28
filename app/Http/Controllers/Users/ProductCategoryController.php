<?php
/**
* Product Category Controller 
*
* Description : Admin can Create ProductCategory for user and others things to do with ProductCategory(View, Update, Delete)
*
*@package ProductCategory Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  26/09/19
*@version 
*/
namespace App\Http\Controllers\Users;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductCategoryController extends Controller
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
        $data['menu']               = 'shop';                 
        $data['product_categories'] = $productCategories = ProductCategory::select('product_categories.id', 'product_categories.name',                                  'product_categories.photo', 'product_categories.store_id', 'product_categories.status',                                           'product_categories.created_at')
                                        ->leftJoin('stores as s', 's.id', '=', 'product_categories.store_id')
                                        ->where(['s.user_id' => auth()->user()->id])
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
     
        return view('user_dashboard.shop.product_category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $data['menu']  = 'shop';
        $data['stores'] = $store = Store::where(['user_id' => auth()->user()->id, 'status' => 'Active'])->get(['id', 'name']);
        // dd($store);
        return view('user_dashboard.shop.product_category.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name'          => 'required',
            'store_id'      => 'required',
            'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'name'          => 'Name',
            'photo'         => 'Photo',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $productCategory                = new ProductCategory();
        $productCategory->name          = $request->name;
        $productCategory->store_id      = $request->store_id;
        $productCategory->slug          = str_slug($request->name);
        $productCategory->description   = $request->description;
        $productCategory->status        = 'Active';
        
        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            if (isset($photo)) {
                $filename  = time() . '.' . $photo->getClientOriginalExtension();
                $extension = strtolower($photo->getClientOriginalExtension());
                $location = public_path('images/shop/product_category/' . $filename);

                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {       
                    // Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    Image::make($photo)->save($location);
                    $oldfilename = $productCategory->photo;
                    if (!(is_null($oldfilename))) {
                        $oldfile = public_path('images/shop/store/' . $productCategory->photo) ;
                        unlink($oldfile);
                    }
                    $productCategory->photo = $filename;
                } else {
                    $this->helper->one_time_message('error', 'Invalid image format!');
                }
            }
        }
        if ($productCategory->save()) {
            $this->helper->one_time_message('success', 'Product category added successfully!');
            return redirect('product-categories');
        }

        $this->helper->one_time_message('error', 'Something went wrong!');
        return redirect('product-categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['menu']            = 'shop';
        $data['productCategory'] = $productCategory = ProductCategory::find($id);
        $data['stores']          = $store           = Store::where(['user_id' => auth()->user()->id, 'status' => 'Active'])->get(['id', 'name']);
        
        if(empty($productCategory)) {
            $this->helper->one_time_message('error', 'Product category not found!');
            return redirect('product-categories');
        }
    
        return view('user_dashboard.shop.product_category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = base64_decode($request->id);

        $rules = array(
            'name'          => 'required',
            'store_id'      => 'required',
            'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'name'          => 'Name',
            'photo'         => 'Photo',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $productCategory = ProductCategory::find($id);
        
        if (empty($productCategory)) {
            $this->helper->one_time_message('error', 'Product category not found!');
            return redirect('product-categories');
        }

        $productCategory->name        = $request->name;
        $productCategory->description = $request->description;
        $productCategory->store_id    = $request->store_id;
        $productCategory->status      = $request->status;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            if (isset($photo)) {
                $filename  = time() . '.' . $photo->getClientOriginalExtension();
                $extension = strtolower($photo->getClientOriginalExtension());
                $location = public_path('images/shop/product_category/' . $filename);

                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {       
                    // Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    Image::make($photo)->save($location);

                    $oldfilename = $productCategory->photo;
                    if (!(is_null($oldfilename))) {
                        $oldfile = public_path('images/shop/product_category/' . $productCategory->photo) ;
                        unlink($oldfile);
                    }
                    $productCategory->photo = $filename;
                } else {
                    $this->helper->one_time_message('error', 'Invalid image format!');
                }
            }
        }

        if ( $productCategory->save()) {
            $this->helper->one_time_message('success', 'Product category updated successfully!');
            return redirect('product-categories'); 
        }

        $this->helper->one_time_message('error', 'Something went wrong!');
        return redirect('product-categories'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $productCategory = ProductCategory::find($request->id);

        if (empty($productCategory)) {
            $this->helper->one_time_message('error', __('Product category not found!'));
            return redirect('product-categories');
        }

        $filename = $productCategory->photo;
        if ($productCategory->delete()) {

            if (!empty($filename) && file_exists(public_path('images/shop/product_category/' . $filename))) {
                @unlink(public_path('images/shop/product_category/' . $filename));
            }

            $this->helper->one_time_message('success', __('Product category deleted successfully.'));
            return redirect('product-categories');
        }

        $this->helper->one_time_message('error', __('Something went wrong, please try again.'));
        return redirect('product-categories');
    }

    /**
     * Delete Photo function
     * 
     * Delete directly product category Photo on product category update by clicking on 'X' button
     *
     * @param Request $request
     * @return void
     */
    public function deleteProductCategoryPhoto(Request $request)
    {
        $photo = $_POST['photo'];
        $product_category_id = base64_decode($request->product_category_id);

        if (isset($photo)) {
            $productCategory = ProductCategory::where(['id' => $product_category_id, 'photo' => $request->photo])->first();
            if ($productCategory) {
                ProductCategory::where(['id' => $product_category_id, 'photo' => $request->photo])->update(['photo' => null]);
                if ($photo != null) {
                    $dir = public_path('images/shop/product_category/' . $request->photo);
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
}
