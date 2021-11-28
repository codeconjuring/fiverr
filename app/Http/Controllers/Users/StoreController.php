<?php
/**
* Store controller - frontend
*
* Description : User can make store and others functionalities to do with store (View, Update, Delete)
*
*@package Store
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  24/09/19
*@version 
*/

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Store;
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
     * List all the stores in details that user created 
     *
     * @return void
     */
    public function index()
    {
        $data['menu']   = 'shop';
        $data['stores'] = $stores = Store::where(['user_id' => auth()->user()->id])->select(['id', 'name', 'slug', 'store_code', 'phone','email', 'website', 'photo', 'status'])->orderBy('created_at', 'desc')->paginate(10);
        // dd($stores);
        return view('user_dashboard.shop.store.index', $data);
    }

    public function add()
    {
        $data['menu'] = 'shop'; 
        return view('user_dashboard.shop.store.add', $data);
    }

    /**
     * Store Add function
     * 
     * Store adding form and create store functionalities
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $rules = array (
            'store_name'    => 'required',
            'store_code'    => 'required|max:10|unique:stores,store_code',
            'email'         => 'nullable|email',
            'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array (
            'store_name'    => 'Name',
            'store_code'    => 'Store Code',
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
        $store->name             = $request->store_name;
        $store->slug             = str_slug($request->store_name);
        $store->store_code       = $request->store_code;
        $store->user_id          = auth()->user()->id;
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

        //dd($request->file('photo'));
        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');
            if (isset($photo)) {
                $filename  = time() . '.' . $photo->getClientOriginalExtension();
                $extension = strtolower($photo->getClientOriginalExtension());
                $location  = public_path('images/shop/store/' . $filename);
                //echo $extension;exit();
                if (file_exists($location)) {
                    unlink($location);
                }
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                    Image::make($photo)->fit(831, 412, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    // Image::make($photo)->save($location);
                    $store->photo = $filename;
                } else {
                    $this->helper->one_time_message('error', 'Invalid Image Format!');
                    return redirect('stores/add');
                }
            }
        }
        $store->save();
  
        $this->helper->one_time_message('success', __('Store Created Successfully!'));
        return redirect('stores');
    }

    /**
     * Edit funtion
     *
     * @param [int] $id
     * @return void
     */
    public function edit($id) 
    {
        $data['menu']  = 'shop';
        $data['store'] = $store = Store::find($id);

        if (empty($store)) {
            $this->helper->one_time_message('error', __('Store not found!'));
            return redirect('stores');  
        }
        return view('user_dashboard.shop.store.edit', $data);
    }

    /**
     * Update Function
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function update(Request $request, $id)
    {   
        $rules = array (
            'store_name'    => 'required',
            'store_code'    => 'required|max:10|unique:stores,store_code,'.$id,
            'email'         => 'email|nullable',
            'photo'         => 'max:15000|mimes:png,jpg,jpeg,gif,bmp',
        );
        
        $fieldNames = array (
            'store_name'    => 'Store Name',
            'store_code'    => 'Store Code',
            'email'         => 'Email',
            'photo'         => 'Photo',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $store                   = Store::findOrFail($id);
        $store->name             = $request->store_name;
        $store->slug             = str_slug($request->store_name);
        $store->store_code       = $request->store_code;
        $store->user_id          = auth()->user()->id;
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
                    
                    Image::make($photo)->fit(831, 412, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                    // Image::make($photo)->save($location);
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
        return redirect('stores');
    }
    /**
     * Delete function
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $store = Store::find($request->id);
        // dd($store->orders->count() > 0);


        // if ($store->orders->) 
        if ($store->orders->count() > 0) 
        {
            $this->helper->one_time_message('error','Store cannot be deleted! Order exists!');
            return redirect('stores');
        }

        if (empty($store)) {
            $this->helper->one_time_message('error', __('Store not found!'));
            return redirect('stores');
        }

        $filename = $store->photo;
        if ($store->delete()) {

            if (!empty($filename) && file_exists(public_path('images/shop/store/' . $filename))) {
                @unlink(public_path('images/shop/store/' . $filename));
            }

            $this->helper->one_time_message('success', __('Store deleted successfully.'));
            return redirect('stores');
        }

        $this->helper->one_time_message('error', __('Something went wrong, please try again.'));
        return redirect('stores');
    }

    /**
     * StoreCode exist check function
     * 
     * Description : Checking in Adding form if the store_code available or not
     *
     * @param Request $request
     * @return void
     */
    public function checkStoreCode(Request $request)
    {
        $store = Store::where(['store_code' => $request->store_code])->exists();
           
        if ($store) {
            $data['status']  = false;
            $data['fail']    = "Store code has already been taken.";
        } else {
            $data['status']  = true;
            $data['success'] = "Store code is available.";
        }
        return json_encode($data);
    }

    /**
     * StoreCode exist check function
     * 
     * Description: Checking in Update form if the store_code available or not
     *
     * @param Request $request
     * @return void
     */
    public function updateStoreCodeCheck(Request $request)
    {
        $req_id = $request->store_id;

        $store  = Store::where(['store_code' => $request->store_code])->where(function ($query) use ($req_id)
        {
            $query->where('id', '!=', $req_id);
        })->exists();

        if ($store) {
            $data['status']  = false;
            $data['fail']    = "Store code has already been taken.";
        } else {
            $data['status']  = true;
            $data['success'] = "Store code is available.";
        }
        return json_encode($data);
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
                    $dir = public_path('images/shop/store/' . $request->photo);
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
