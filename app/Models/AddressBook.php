<?php
/**
* AddressBook Model
*
* description : 
*
*@package AddressBook
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  09/09/19
*@version 
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * createOrUpdate method
     *
     * @param integer $userId
     * @param array $data
     * @return void
     */
    public static function createOrUpdate($userId = null, array $data)
    {
        $response = ['status' => 401, 'message' => __('Oops... Something went wrong.')];
        $shippingAddress = AddressBook::where('user_id', $userId)->first();
        
        if (!empty($shippingAddress)) {
            $message = __('Successfully updated');
        } else {
            $shippingAddress = new AddressBook();
            $message = __('Successfully saved');
        }

        $shippingAddress->user_id = $userId;
        $shippingAddress->description = isset($data['description']) ? $data['description'] : null;
        $shippingAddress->address_line_1 = isset($data['address_line_1']) ? $data['address_line_1'] : null;
        $shippingAddress->address_line_2 = isset($data['address_line_2']) ? $data['address_line_2'] : null;
        $shippingAddress->city = isset($data['city']) ? $data['city'] : null;
        $shippingAddress->state = isset($data['state']) ? $data['state'] : null;
        $shippingAddress->zip = isset($data['zip']) ? $data['zip'] : null;
        $shippingAddress->country = isset($data['country']) ? $data['country'] : null;
        $shippingAddress->email = isset($data['email']) ? $data['email'] : null;
        $shippingAddress->phone = isset($data['phone']) ? $data['phone'] : null;
        $shippingAddress->fax = isset($data['fax;']) ? $data['fax;'] : null;
        $shippingAddress->website = isset($data['website']) ? $data['website'] : null;

        if ($shippingAddress->save()) {
            $response['status'] = 200;
            $response['message'] = $message;
        }

        return $response;
    }
}
