<?php
/**
 * @package OrderController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md. Nobeul Islam <[nobeul.techvill@gmail.com]>
 * @created 31-07-2021
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{
    AddressBook,
    Order,
    User
};
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * ownerOrders function
     *
     * @param Request $request
     * @return void
     */
    public function ownerOrders(Request $request)
    {
        $user = null;
        if (isset($request->user_id) && !empty($request->user_id)) {
            $ownerId = $request->user_id;
            $user = User::find($ownerId);
        } 
        if (empty($user)) {
            return response()->json(['status' => 401, 'message' => __('User not found.')]);
        }

        $orders = Order::myOrders($ownerId);
        if (empty($orders)) {
            return response()->json(['status' => 200, 'message' => __('There is no order available.')]);
        }
        return response()->json(['status' => 200, 'orders' => $orders]);
    }

    /**
     * customersOrders function
     *
     * @param Request $request
     * @return void
     */
    public function customersOrders(Request $request)
    {
        $user = null;
        if (isset($request->user_id) && !empty($request->user_id)) {
            $userId = $request->user_id;
            $user = User::find($userId);
        } 
        if (empty($user)) {
            return response()->json(['status' => 401, 'message' => __('User not found.')]);
        }

        $orders = Order::customerOrders($userId);
                                
        return response()->json(['status' => 200, 'orders' => $orders]);
    }

    /**
     * addShippingAddress function
     *
     * @param Request $request
     * @return void
     */
    public function addShippingAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_line_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => $validator->errors()]);
        }

        $user = null;
        if (isset($request->user_id) && !empty($request->user_id)) {
            $userId = $request->user_id;
            $user = User::find($userId);
        } 
        if (empty($user)) {
            return response()->json(['status' => 401, 'message' => __('User not found.')]);
        }

        $data = $request->all();

        $shippingAddress = AddressBook::createOrUpdate($userId, $data);

        return response()->json(['status' => $shippingAddress['status'], 'message' => $shippingAddress['message']]);
    }
}
