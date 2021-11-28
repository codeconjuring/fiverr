<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AddressBook;
use App\Http\Helpers\Common;

class ShippingAddressController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function addShippingAddress(Request $request)
    {

        $data['menu']            = 'shop';
        $data['shippingAddress'] = AddressBook::where('user_id', auth()->user()->id)->first();
       

        if ($_POST) {
            $id = base64_decode($request->shipping_address_id);
        
            $shippingAddress = AddressBook::find($id);

            if (!empty($shippingAddress)) {
                $shippingAddress->user_id        = auth()->user()->id;
                $shippingAddress->description    = $request->description;
                $shippingAddress->address_line_1 = $request->address_line_1;
                $shippingAddress->address_line_2 = $request->address_line_2;
                $shippingAddress->city           = $request->city;
                $shippingAddress->state          = $request->state;
                $shippingAddress->zip            = $request->zip;
                $shippingAddress->country        = $request->country;
                $shippingAddress->email          = $request->email;
                $shippingAddress->phone          = $request->phone;
                $shippingAddress->fax            = $request->fax;
                $shippingAddress->website        = $request->website;
            } else {
                $shippingAddress                 = new AddressBook();
                $shippingAddress->user_id        = auth()->user()->id;
                $shippingAddress->description    = $request->description;
                $shippingAddress->address_line_1 = $request->address_line_1;
                $shippingAddress->address_line_2 = $request->address_line_2;
                $shippingAddress->city           = $request->city;
                $shippingAddress->state          = $request->state;
                $shippingAddress->zip            = $request->zip;
                $shippingAddress->country        = $request->country;
                $shippingAddress->email          = $request->email;
                $shippingAddress->phone          = $request->phone;
                $shippingAddress->fax            = $request->fax;
                $shippingAddress->website        = $request->website;
            }
            
            if ($shippingAddress->save()) {
                $this->helper->one_time_message('success', __('Shipping Address Created Successfully!'));
                return redirect('shipping-address');
            } else {
                $this->helper->one_time_message('error', __('Shipping Address Not Updated!'));
                return redirect('shipping-address');
            }
        }
        
        return view('user_dashboard.shop.shipping_address.add', $data);
    }


    public function customerShippingAddress($id)
    {
        $data['menu']            = 'shop';
        $data['customerAddress'] = $customerAddress = AddressBook::where(['user_id' => $id])->first();
        
        return view('user_dashboard.shop.shipping_address.customer_address', $data);
    }
}
