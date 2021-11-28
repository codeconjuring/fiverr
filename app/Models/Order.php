<?php
/**
 * @package OrderModel
 * @author tehcvillage <support@techvill.org>
 * @contributor Md. Nobeul Islam <[nobeul.techvill@gmail.com]>
 * @created 31-07-2021
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'transaction_reference_id', 'id');
    }


    /**
     * create function
     *
     * @param integer $storeId
     * @param integer $productId
     * @param integer $userId
     * @param integer $currencyId
     * @param integer $orderId
     * @param string $orderDate
     * @param integer $amount
     * @param string $status
     * @return object
     */
    public static function create($storeId = null, $productId = null, $userId = null, $currencyId = null, $orderId = null, $orderDate = null, $amount = 0, $status = null)
    {
        $order = new Order;

        $order->store_id = $storeId;
        $order->product_id = $productId;
        $order->user_id = $userId;
        $order->currency_id = $currencyId;
        $order->order_id = $orderId;
        $order->order_date = $orderDate;
        $order->paid_amount = $amount;
        $order->status = $status;

        $order->save();
        
        return $order;
    }

    /**
     * myOrders function
     *
     * @param integer $userId
     * @return object
     */
    public static function myOrders($userId)
    {
        return Order::where(['user_id' => $userId])->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * customerOrders function
     *
     * @param integer $userId
     * @return object
     */
    public static function customerOrders($userId)
    {
        return Order::with('store:id,name', 'product:id,title', 'currency:id,symbol', 'transaction:id,transaction_reference_id')->where(['user_id' => $userId])
                                ->orderBy('created_at', 'desc')
                                ->get();
    }
}
