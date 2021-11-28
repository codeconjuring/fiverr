<?php
/**
* AddressBook Model
*
* description : Admin can Create AddressBook for user and others things to do with AddressBook(Create, Update, Delete)
*
*@package AddressBook Module
*@author Ahammed Imtiaze <ahammedimtiaze78@gmail.com>,  09/09/19
*@version
*/

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AddressBooksDataTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\AddressBook;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AddressBookController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    /**
     * Index function
     *
     * Description: Listing all AddressBook information in datatable
     *
     * @param AddressBooksDatatable $dataTable
     * @return void
     */
    public function index(AddressBooksDataTable $dataTable)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'address_list';
        return $dataTable->render('admin.shop.address_book.index', $data);
    }


    /**
     * Address Book Add function
     *
     * Description: AddressBook adding form and create AddressBook functionalities
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        if (!$_POST) {

            $data['menu']     = 'shop';
            $data['sub_menu'] = 'address_list';
            $data['users']    = User::select(['id', 'first_name', 'last_name'])->where(['status' => 'Active'])->get();
            return view('admin.shop.address_book.add', $data);

        } else if ($_POST) {

            $rules = array(
                'user_id'       => 'required',
                'photo'         => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
            );

            $fieldNames = array(
                'user_id'       => 'User Id',
                'photo'         => 'Photo',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $addressBook                   = new AddressBook();
            $addressBook->user_id          = $request->user_id;
            $addressBook->phone            = $request->phone;
            $addressBook->email            = $request->email;
            $addressBook->website          = $request->website;
            $addressBook->fax              = $request->fax;
            $addressBook->address_line_1   = $request->address_line_1;
            $addressBook->address_line_2   = $request->address_line_2;
            $addressBook->city             = $request->city;
            $addressBook->state            = $request->state;
            $addressBook->zip              = $request->zip;
            $addressBook->country          = $request->country;
            $addressBook->description      = $request->description;
            $addressBook->status           = $request->status;

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location  = public_path('images/shop/address_book/' . $filename);
                    if (file_exists($location)) {
                        unlink($location);
                    }
                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);
                        $addressBook->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $addressBook->save();

            $this->helper->one_time_message('success', 'Address Book Added Successfully');
            return redirect('admin/address-books');
        }

    }

    /**
     * AddressBook Update function
     *
     * Description: AddressBook Edit form and update functionalities
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        if (!$_POST) {

            $data['menu'] = 'shop';
            $data['sub_menu'] = 'address_list';
            $data['result'] = AddressBook::findOrFail($id);
            $data['users'] = User::select(['id', 'first_name', 'last_name'])->where(['status' => 'Active'])->get();
            return view('admin.shop.address_book.edit', $data);

        }  else if ($_POST) {

            $rules = array(
                'user_id'       => 'required',
                'photo'         => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
            );

            $fieldNames = array(
                'user_id'       => 'User Id',
                'photo'         => 'Photo',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $addressBook                   = AddressBook::findOrFail($id);
            $addressBook->user_id          = $request->user_id;
            $addressBook->phone            = $request->phone;
            $addressBook->email            = $request->email;
            $addressBook->website          = $request->website;
            $addressBook->fax              = $request->fax;
            $addressBook->address_line_1   = $request->address_line_1;
            $addressBook->address_line_2   = $request->address_line_2;
            $addressBook->city             = $request->city;
            $addressBook->state            = $request->state;
            $addressBook->zip              = $request->zip;
            $addressBook->country          = $request->country;
            $addressBook->description      = $request->description;
            $addressBook->status           = $request->status;

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                if (isset($photo)) {
                    $filename  = time() . '.' . $photo->getClientOriginalExtension();
                    $extension = strtolower($photo->getClientOriginalExtension());
                    $location = public_path('images/shop/address_book/'.$filename);
                    if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp') {
                        Image::make($photo)->fit(120, 80, function ($constraint) { $constraint->aspectRatio(); })->save($location);

                        // Old file assigned to a variable
                        $oldfilename = $addressBook->photo;
                        if (!(is_null($oldfilename))) {
                            $oldfile = public_path('images/stores/address_book/' . $addressBook->photo) ;
                            unlink($oldfile);
                        }

                        // Update the database
                        $addressBook->photo = $filename;
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
            }
            $addressBook->save();

            $this->helper->one_time_message('success', 'Address Book Updated Successfully');
            return redirect('admin/address-books');
        }
    }

    /**
     * AddressBook Photo delete function
     *
     * Delete a specific($id) AddressBook
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function delete(Request $request, $id)
    {
        $addressBook = AddressBook::findOrFail($id);

        if ($addressBook) {
            $filename = $addressBook->photo;
            if (!(is_null($filename))) {
                $photo = public_path('images/stores/address_book/'.$addressBook->photo) ;
                unlink($photo);
            }
            $addressBook->delete();
        }
        $this->helper->one_time_message('success', 'Address Book Deleted Successfully');
        return redirect('admin/address-books');
    }

    /**
     * Delete Photo function
     *
     * Delete directly Address Book Photo on Update by clicking on "X" button
     *
     * @param Request $request
     * @return void
     */
    public function deleteAddressBookPhoto(Request $request)
    {
        $photo = $_POST['photo'];

        if (isset($photo)) {

            $addressBook = AddressBook::where(['id'=>$request->address_book_id, 'photo'=>$request->photo])->first();

            if ($addressBook) {
                AddressBook::where(['id'=>$request->address_book_id, 'photo'=>$request->photo])->update(['photo'=>null]);
                if ($photo != null) {
                    $dir = public_path('images/stores/address_book/' . $request->photo);
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
