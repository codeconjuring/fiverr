<?php
/**
* ProductCategory Controller
*
* description : Admin can Create Product category for user and others things to do with Productcateogry(View, Update, Delete)
*
*@package ProductCategory Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  05/09/19
*@version
*/

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ProductCategoriesDatatable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
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
     * Index function
     *
     * Description: All product category information listion in Datatable
     *
     * @param ProductCategoryDatatable $dataTable
     * @return void
     */
    public function index(ProductCategoriesDatatable $dataTable)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'productcategory_list';
        return $dataTable->render('admin.shop.product_category.index', $data);
    }

    /**
     * Product Category Add function
     *
     * Description: Product category Add form and Adding functionalities
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        if ($_POST) {

            $rules = array(
                'name'          => 'required',
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
            $productCategory->slug          = str_slug($request->name);
            $productCategory->description   = $request->description;
            $productCategory->store_id      = !empty($request->store) ? $request->store : NULL;
            $productCategory->status        = $request->status;

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location  = public_path('images/shop/product_category/' . $filename);
                    if (file_exists($location)) {
                        unlink($location);
                    }
                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                        $productCategory->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $productCategory->save();

            $this->helper->one_time_message('success', 'Product Category Added Successfully');
            return redirect('admin/product-categories');
        }

        $data['menu']     = 'shop';
        $data['sub_menu'] = 'productcategory_list';
        $data['stores']   = Store::select(['id', 'name'])->where(['status' => 'Active'])->get();
        return view('admin.shop.product_category.add', $data);
    }

    /**
     * Product Category Update function
     *
     * Description: Product category Edit form and Updating functionalities
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        if (!$_POST) {

            $data['menu']     = 'shop';
            $data['sub_menu'] = 'productcategory_list';
            $data['result']   = ProductCategory::findOrFail($id);
            $data['stores']   = Store::select(['id', 'name'])->where(['status' => 'Active'])->get();
            return view('admin.shop.product_category.edit', $data);

        } else if ($_POST) {

            $rules = array(
                'name'          => 'required',
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

            $productCategory                = ProductCategory::findOrFail($id);
            $productCategory->name          = $request->name;
            $productCategory->slug          = str_slug($request->name);
            $productCategory->description   = $request->description;
            $productCategory->store_id      = !empty($request->store) ? $request->store : NULL;
            $productCategory->status        = $request->status;

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location = public_path('images/shop/product_category/'.$filename);
                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);

                        // Old file assigned to a variable
                        $oldfilename = $productCategory->photo;
                        if (!(is_null($oldfilename))) {
                            $oldfile = public_path('images/shop/product_category/' . $productCategory->photo) ;
                            unlink($oldfile);
                        }

                        // Update the database
                        $productCategory->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $productCategory->save();

            $this->helper->one_time_message('success', 'Product Category Updated Successfully');
            return redirect('admin/product-categories');

        }
    }

    /**
     * Delete Product Category Function
     *
     * Description: Delete a specific($id) product category
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function delete(Request $request, $id)
    {

        $productCategory = ProductCategory::findOrFail($id);

        if ($productCategory) {
            $filename = $productCategory->photo;
            if (!(is_null($filename))) {
                $photo = public_path('images/shop/product_category/' . $productCategory->photo) ;
                unlink($photo);
            }
            $productCategory->delete();
        }

        $this->helper->one_time_message('success', 'Product Category Deleted Successfully');
        return redirect('admin/product-categories');
    }

    /**
     * Delete Photo function
     *
     * Description: Delete directly Product Category Photo on Update by clicking on "X" button
     *
     * @param Request $request
     * @return void
     */
    public function deleteProductCategoryPhoto(Request $request)
    {
        $photo = $_POST['photo'];

        if (isset($photo)) {

            $productCategory = ProductCategory::where(['id' => $request->product_category_id, 'photo' => $request->photo])->first();

            if ($productCategory) {
                ProductCategory::where(['id' => $request->product_category_id, 'photo' => $request->photo])->update(['photo' => null]);
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
