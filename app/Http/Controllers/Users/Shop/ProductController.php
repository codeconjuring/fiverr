<?php

namespace App\Http\Controllers\Users\Shop;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\Wallet;
use Cache;

class ProductController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function show($id)
    {
        if (!empty(Cache::get('product-url'))) {
            Cache::forget('product-url');
        }

        $data['menu']              = 'shop';
        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        $data['product']           = $product = Product::find($id);

        if(empty($product)) {
            return redirect('shop');
        }
        return view('frontend.shop.pages.product', $data);
    }

    public function buy($id)
    {
        setActionSession();

        $data['menu']              = 'shop';

        $data['productCategories'] = $productCategories = ProductCategory::select(['id', 'name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        $data['product']           = $product = Product::find($id);

        // Check if user logged in or not
        if(\Auth::check()) {

            // check if its user own product
            $product_store_id = $product->store_id;
            $stores = Store::where(['user_id' => auth()->user()->id])->get(['id']);
            
            foreach ($stores as $user_store_id) {
                if ($user_store_id->id == $product_store_id) {
                    $this->helper->one_time_message('error', 'You can not buy your own product !');
                    return redirect()->back();
                }
            }

            // out of stock
            $stock = (int)$product->stock;
            if ($stock < 1) {
                $this->helper->one_time_message('error', 'Out of stock !');
                return redirect()->back();
            }

            return view('frontend.shop.pages.buy', $data);

        } else {
            Cache::put('product-url', url()->previous(), 600);
            $this->helper->one_time_message('error', 'Please login first !');
            return redirect('login');
        }

    }

    public function buyConfirm($id)
    {

        actionSessionCheck();

        $product                = Product::find($id);
        $data['productId']      = $id;
        $data['productPrice']   = $product_price = $product->price;
        $data['currencyId']     = $currency_id   = $product->currency->id;
        $currency_code          = $product->currency->code;
        $data['currencySymbol'] = $product->currency->symbol;

        $walletCheck = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $currency_id])->exists();

        if (!$walletCheck) {
            $this->helper->one_time_message('error', "You don't have  $currency_code  wallet !");
            return redirect()->back();
        }

        $customerWallet        = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $currency_id])->first(['id','currency_id', 'balance']);
        $CustomerWalletId      = $customerWallet->id;
        $customerWalletBalance = $customerWallet->balance;

        if ($customerWalletBalance < $product_price) {
            $this->helper->one_time_message('error', "You don't have enough $currency_code balance !");
            return redirect()->back();
        }

        try
        {

            \DB::beginTransaction();

            // Deduct balance from user wallet who buy the product
            $wallet          = Wallet::find($CustomerWalletId);
            $wallet->balance = $customerWalletBalance - $product_price;
            $wallet->save();

            // Added to the user wallet balance who owner of the store
            $store              = Product::where(['id' => $id])->first(['store_id']);
            $user               = Store::where(['id' => $store->store_id])->first(['user_id']);
            $ownerWallet        = Wallet::where(['user_id' => $user->user_id, 'currency_id' => $currency_id])->first(['id','currency_id', 'balance']);


            if (!empty($ownerWallet)) {

                $ownerWalletId      = $ownerWallet->id;
                $ownerWalletBalance = $ownerWallet->balance;

                $wallet             = Wallet::find($ownerWalletId);
                $wallet->balance    = $ownerWalletBalance + $product_price;
                $wallet->save();

            } else {

                $ownerWallet              = new Wallet();
                $ownerWallet->user_id     = $user->user_id;
                $ownerWallet->currency_id = $currency_id;
                $ownerWallet->balance     = $product_price;
                $ownerWallet->is_default  = 'No';
                $ownerWallet->save();
            }

            // Product
            $product->stock = $product->stock - 1;
            $product->save();

            // Order table
            $data['orderId']    = $orderId = substr((md5(time())), 0, 10);

            $order              = new Order();
            $order->store_id    = $store->store_id;
            $order->product_id  = $id;
            $order->user_id     = auth()->user()->id;
            $order->currency_id = $currency_id;
            $order->order_id    = $orderId;
            $order->order_date  = date('Y-m-d');
            $order->paid_amount = $product_price;
            $order->status      = 'Complete';
            $order->save();

            //Transaction
            $uuid                                    = unique_code();

            $transaction_o                           = new Transaction();
            $transaction_o->user_id                  = auth()->user()->id;
            $transaction_o->end_user_id              = $user->user_id;
            $transaction_o->currency_id              = $currency_id;
            $transaction_o->transaction_reference_id = $order->id;
            $transaction_o->transaction_type_id      = Order_Product;
            $transaction_o->uuid                     = $uuid;
            $transaction_o->total                    = '-' . ($product_price);
            $transaction_o->subtotal                 = $product_price;
            $transaction_o->status                   = 'Success';
            $transaction_o->save();

            $transaction_r                           = new Transaction();
            $transaction_r->user_id                  = $user->user_id;
            $transaction_r->end_user_id              = auth()->user()->id;
            $transaction_r->currency_id              = $currency_id;
            $transaction_r->transaction_reference_id = $order->id;
            $transaction_r->transaction_type_id      = Order_Received;
            $transaction_r->uuid                     = $uuid;
            $transaction_r->total                    = $product_price;
            $transaction_r->subtotal                 = $product_price;
            $transaction_r->status                   = 'Success';
            $transaction_r->save();

            \DB::commit();

            clearActionSession();

            $data['menu']   =  'shop';

            return view('frontend.shop.pages.confirm', $data);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect()->back();
        }

    }
}
