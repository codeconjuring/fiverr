<?php
/**
* Store Model
*
* description :
*
*@package Store
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  04/09/19
*@version 
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function product_categories()
    {
        // return $this->hasMany(ProductCategory::class);
        return $this->hasMany(ProductCategory::class)->orderBy('id','desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * StoreCode exist check function
     * 
     * Description: Checking in Update form if the store_code available or not
     *
     * @param $request
     * @return void
     */
    public static function checkCode($request)
    {
        $data = array('status' => false, 'type' => 'danger', 'message' => __('The store code has already been taken.'));

        $conditions = ['store_code' => $request->store_code];
        
        $store = self::where($conditions)->first();

        if (!$store || ($store->id && isset($request->store_id) && $request->store_id == $store->id)) {
            $data['status']  = true;
            $data['type']    = 'success';
            $data['message'] = __('Store code is available.');
        }

        return json_encode($data);
    }

    /**
     * view function
     *
     * @param [type] $id
     * @return object
     */
    public static function view($id)
    {
        $store = Store::find($id);

        if (empty($store)) {
            return response()->json(['status' => 401, 'message' => __('Store not found')]);
        }

        return $store;
    } 

}
