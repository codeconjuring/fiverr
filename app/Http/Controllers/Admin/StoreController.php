<?php
/**
* Store controller
*
* Description : Admin can make store for user and others things to do with store (View, Update, Delete)
*
*@package Store
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  04/09/19
*@version 
*/

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\StoresDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Store;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Validator;

class StoreController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    
    /**
     * Index function
     * 
     * description: Listing all store information in datatable
     *
     * @param StoreDataTable $datatable
     * @return void
     */
    public function index(StoresDataTable $datatable)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'store_list';
        return $datatable->render('admin.shop.store.index', $data);
    }

    /**
     * Store Add function
     * 
     * Store adding form and create store functionalities
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'store_list';
        $data['users']      = $user = User::select(['id', 'first_name', 'last_name'])->where(['status' => 'Active'])->get();
        
        if ($_POST) {

            $rules = array (
                'name'          => 'required',
                'store_code'    => 'required|max:10|unique:stores,store_code',
                'user_id'       => 'required',
                'email'         => 'email|nullable',
                'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
            );

            $fieldNames = array (
                'name'          => 'Name',
                'store_code'    => 'Store Code',
                'user_id'       => 'User Id',
                'email'         => 'Email',
                'phone'         => 'Phone',
                'photo'         => 'Photo',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            $store                   = new Store();
            $store->name             = $request->name;
            $store->slug             = str_slug($request->name);
            $store->store_code       = $request->store_code;
            $store->user_id          = $request->user_id;
            $store->description      = $request->description;
            $store->address_line_1   = $request->address_line_1;
            $store->address_line_2   = $request->address_line_2;
            $store->city             = $request->city;
            $store->state            = $request->state;
            $store->zip              = $request->zip;
            $store->country          = $request->country;
            $store->email            = $request->email;
            $store->phone            = $request->phone;
            $store->website          = $request->website;
            $store->status           = 'Active';

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');
                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location  = public_path('images/shop/store/' . $filename);

                    if (file_exists($location)) {
                        unlink($location);
                    }

                    // Image Extension that only accept: png, jpg, jpeg, gif, bmp
                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        Image::make($photo)->fit(831, 412, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                        // Image::make($photo)->save($location);

                        $store->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $store->save();

            $this->helper->one_time_message('success', 'Store Added Successfully');
            return redirect('admin/stores');
        }

        return view('admin.shop.store.add', $data);
    }

    /**
     * Update function
     * 
     * Description: Update a specific store information 
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        
        $data['result'] = $store = Store::find($id);
        
        if (empty($store)) {
            $this->helper->one_time_message('error', __('Store not found!'));
            return redirect('admin/stores');
        }
        
        if ($_POST) {
            $rules = array (
                'name'          => 'required',
                'store_code'    => 'required|max:10|unique:stores,store_code,'.$id,
                'user_id'       => 'required',
                'email'         => 'email|nullable',
                'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
            );

            $fieldNames = array (
                'name'          => 'Name',
                'store_code'    => 'Store Code',
                'user_id'       => 'User Id',
                'email'         => 'Email',
                'photo'         => 'Photo',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            $store->name             = $request->name;
            $store->slug             = str_slug($request->name);
            $store->store_code       = $request->store_code;
            $store->user_id          = $request->user_id;
            $store->description      = $request->description;
            $store->address_line_1   = $request->address_line_1;
            $store->address_line_2   = $request->address_line_2;
            $store->city             = $request->city;
            $store->state            = $request->state;
            $store->zip              = $request->zip;
            $store->country          = $request->country;
            $store->email            = $request->email;
            $store->phone            = $request->phone;
            $store->website          = $request->website;
            $store->status           = $request->status;

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location = public_path('images/shop/store/' . $filename);

                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        // Image Extension that only accept: png, jpg, jpeg, gif, bmp
                        Image::make($photo)->fit(831, 412, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                        $oldfilename = $store->photo;
                        if (!(is_null($oldfilename))) {
                            $oldfile = public_path('images/shop/store/' . $store->photo) ;
                            unlink($oldfile);
                        }
                        $store->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $store->save();

            $this->helper->one_time_message('success', 'Store Updated Successfully');
            return redirect('admin/stores');
        }
        
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'store_list';
        $data['users']    = User::select(['id', 'first_name', 'last_name'])->where(['status' => 'Active'])->get();
           
        return view('admin.shop.store.edit', $data);
    }

    /**
     * Delete function
     *
     * Description: Delete a specific store information
     * 
     * @param [int] $id
     * @return void
     */
    public function delete($id)
    {
        $store = Store::find($id);
        if (empty($store)) {
            $this->helper->one_time_message('error', __('Store not found!'));
            return redirect('admin/stores');
        }

        // if ($store->orders->) 
        if ($store->orders->count() > 0) 
        {
            $this->helper->one_time_message('error','Store cannot be deleted! Order exists!');
            return redirect('admin/stores');
        }


        $filename = $store->photo;
        if ($store->delete()) {

            if (!empty($filename) && file_exists(public_path('images/shop/store/' . $filename))) {
                @unlink(public_path('images/shop/store/' . $filename));
            }

            $this->helper->one_time_message('success', __('Store deleted successfully.'));
            return redirect('admin/stores');
        }

        $this->helper->one_time_message('error', __('Something went wrong, please try again.'));
        return redirect('admin/stores');
    }

    /**
     * StoreCode exist check function
     * 
     * Description: Checking in Update form if the store_code available or not
     *
     * @param Request $request
     * @return void
     */
    public function checkStoreCode(Request $request)
    {
        echo Store::checkCode($request);
    }

    /**
     * Delete Photo function
     * 
     * Description: Delete directly Store Photo on store update by clicking on 'X' button
     *
     * @param Request $request
     * @return void
     */
    public function deleteStorePhoto(Request $request)
    {
        $photo = $_POST['photo'];

        if (isset($photo)) {
            $store = Store::where(['id' => $request->store_id, 'photo' => $request->photo])->first();
            if ($store) {
                Store::where(['id' => $request->store_id, 'photo' => $request->photo])->update(['photo' => null]);
                if ($photo != null) {
                    $dir = public_path('images/shop/store/'.$request->photo);
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
