<?php
/**
* Product Controller 
*
* Description : Admin can Create Product for user and others things to do with Product(View, Update, Delete)
*
*@package Product Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  07/09/19
*@version 
*/

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ProductsDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Intervention\Image\Facades\Image;
use Validator;

class ProductController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    /**
     * Product listing function
     * 
     * Description: All prouduct information List in a datatable
     *
     * @param ProductsDataTable $dataTable
     * @return void
     */
    public function index(ProductsDataTable $dataTable)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'product_list';
        return $dataTable->render('admin.shop.product.index', $data);
    }

    /**
     * Product Add function
     *
     * Desscription: Product adding form and create Product functionalities
     * 
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        if ($_POST) {

            $rules = array (
                'title'             => 'required',
                'product_category'  => 'required',
                'store_name'        => 'required',
                'product_code'      => 'required|max:10|unique:products,product_code',
                'currency'          => 'required',
                'price'             => 'required|numeric',
                'stock'             => 'required|numeric',
                'photo'             => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
            );

            $fieldNames = array (
                'title'             => 'Title',
                'product_category'  => 'Product Category',
                'store_name'        => 'Store Name',
                'product_code'      => 'Product Code',
                'currency'          => 'Currency',
                'price'             => 'Price',
                'stock'             => 'Stock',
                'photo'             => 'Photo',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $product                       = new Product();
            $product->store_id             = $request->store_name;
            $product->product_category_id  = $request->product_category;
            $product->currency_id          = $request->currency;
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
            $product->save();

            $this->helper->one_time_message('success', 'Product Added Successfully');
            return redirect('admin/products');
        }

        $data['menu']                = 'shop';
        $data['sub_menu']            = 'product_list';
        $data['stores']              = Store::select(['id', 'name'])->where(['status' => 'Active'])->get();
        $data['currencies']          = Currency::where(['status' => 'Active'])->select(['id', 'code'])->get();
        $data['product_categories']  = ProductCategory::where(['store_id' => NULL, 'status' => 'Active'])->select(['id', 'name'])->get();
       
        return view('admin.shop.product.add', $data);
    }

    /**
     * Product Update function
     * 
     * Description: Product Update form and create Product functionalities
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        if ($_POST) {
            
            $rules = array (
                'title'             => 'required',
                'product_category'  => 'required',
                'store_name'        => 'required',
                'product_code'      => 'required|max:10|unique:products,product_code,'.$id,
                'currency'          => 'required',
                'price'             => 'required|numeric',
                'stock'             => 'required|numeric',
                'photo'             => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
            );

            $fieldNames = array (
                'title'             => 'Title',
                'product_category'  => 'Product Category',
                'product_code'      => 'Product Code',
                'currency'          => 'Currency',
                'price'             => 'Price',
                'stock'             => 'Stock',
                'store_name'        => 'Store Name',
                'photo'             => 'Photo',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
           

            $product                       = Product::findOrFail($id);
            $product->store_id             = $request->store_name;
            $product->product_category_id  = $request->product_category;
            $product->currency_id          = $request->currency;
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
                        Image::make($photo)->save($location);
                        
                        // Old file assigned to a variable
                        $oldfilename = $product->photo;
                        if (!(is_null($oldfilename))) {
                            $oldfile = public_path('images/shop/product/' . $product->photo) ;
                            unlink($oldfile);
                        }
                        
                        // Update the database
                        $product->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $product->save();

            $this->helper->one_time_message('success', 'Product Updated Successfully');
            return redirect('admin/products');
        }

        $data['result']              = $product = Product::find($id);
        $data['menu']                = 'shop';
        $data['sub_menu']            = 'product_list';
        $data['stores']              = Store::select(['id', 'name'])->where(['status' => 'Active'])->get();
        $data['currencies']          = Currency::select(['id', 'code'])->get();
        $data['product_categories']  = $productCategory = ProductCategory::where(['store_id' => $data['result']->store_id, 'status' => 'Active'])->orWhere(['store_id' => NULL])->select(['id', 'name'])->get();
        // dd($product); //'store_id' => $data['result']->store_id, 
        return view('admin.shop.product.edit', $data);
    }

    /**
     * Product Delete function
     *
     * Description: Delete specific($id) Product 
     * 
     * @param [int] $id
     * @return void
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product) {
            $filename = $product->photo;
            if (!(is_null($filename))) {
                $photo = public_path('images/shop/product/' . $product->photo) ;
                unlink($photo);
            }
            $product->delete();
        }

        $this->helper->one_time_message('success', 'Product Deleted Successfully');
        return redirect('admin/products');
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
    public function updateProductCodeCheck(Request $request)
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

    public function getCategoriesByStore(Request $request) 
    {
        return ProductCategory::getProductCategoriesByStore(['store_id' => $request->store_id]);
    }

    /**
     * Product photo delete function
     * 
     * Description: Delete directly Product Photo on update by Clicking on "X" button
     *
     * @param Request $request
     * @return void
     */
    public function deleteProductPhoto(Request $request)
    {
        $photo = $_POST['photo'];

        if (isset($photo)) {
            $product = Product::where(['id'=>$request->product_id, 'photo'=>$request->photo])->first();
            if ($product) {
                Product::where(['id'=>$request->product_id, 'photo'=>$request->photo])->update(['photo'=>null]);
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
}
