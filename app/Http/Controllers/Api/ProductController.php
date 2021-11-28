<?php
/**
 * @package ProductController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md. Nobeul Islam <[nobeul.techvill@gmail.com]>
 * @created 31-07-2021
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\{Product,
    Wallet,
    Store,
    Order,
    ProductCategory,
    Transaction
};

class ProductController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 401;
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    /**
     * index function
     *
     * @return void
     */
    public function index()
    {
        $category = request()->categoryId;
        $search   = request()->eventSearch;
        $products = Product::getAll($category, $search);


        $categories = ProductCategory::select(['id', 'name', 'photo'])->where(['status' => 'Active', 'store_id' => NULL])->orderby('created_at', 'desc')->get();

        if (count($products) == 0) {
            return response()->json(['status' => 201, 'categories'=> $categories, 'message' => __('No product has been added yet.')]);
        } else {
            return response()->json(['status' => 200, 'products' => $products, 'categories'=> $categories ]);
        }
    }

    /**
     * show function
     *
     * @param Request $request
     * @return void
     */
    public function show()
    {
        $id = (int)request()->product;
        
        $product = Product::view($id);

        if (empty($product)) {
            return response()->json(['status' => 200, 'message' => __('Product not found.')]);
        } else {
           $product['productPrice'] = moneyFormat($product->currency->code, formatNumber($product->price));
            return response()->json(['status' => 200, 'details' => $product]);
        }
    }

    /**
     * buy function
     *
     * @param Request $request
     * @return void
     */
    public function buy()
    {
        $productId = (int) request()->product;
        $userId = (int) request()->user_id;
        $product = Product::view($productId);

        if (empty($product)) {
            return response()->json(['status' => 201, 'message' => __('Product not found!')]);
        }

        if ($product->stock < 1) {
            return response()->json(['status' => 201, 'message' => __('Stock not available!')]);
        }

        // check if its user own product
        $product_store_id = $product->store_id;
        $stores = Store::where(['user_id' => $userId])->get(['id']);

        foreach ($stores as $user_store_id) {
            if ($user_store_id->id == $product_store_id) {
                return response()->json(['status' => 201, 'message' => __('You can not buy your own Product!')]);
            }
        }

        $productPrice = $product->price;
        $currencyId   = $product->currency->id;
        $currencyCode = $product->currency->code;

        $walletParams['user_id'] = $userId;
        $walletParams['currency_id'] = $currencyId;

        $walletCheck = Wallet::where(['user_id' => $userId, 'currency_id' => $currencyId])->exists();

        if (!$walletCheck) {
            return response()->json(['status' => 201, 'message' => __('You do not have ') . $currencyCode . __(' wallet')]);
        }

        $userWallet = Wallet::viewOrUpdate($walletParams, 'view');

        if ($userWallet->balance < $productPrice) {
            return response()->json(['status' => 201, 'message' => __('You do not have enough ') . $currencyCode . __(' balance!')]);
        }

        try {

            \DB::beginTransaction();

            $userWalletUpdate          = Wallet::find($userWallet->id);
            $userWalletUpdate->balance = $userWallet->balance - $productPrice;
            $userWalletUpdate->save();

            // Added to the user wallet balance who owner of the store
            $store = Product::view($productId);
            $user = Store::view($store->store_id);
            
            $walletParams['user_id'] = $user->user_id;
            $walletParams['currency_id'] = $currencyId;
            
            $ownerWallet = Wallet::viewOrUpdate($walletParams, 'view');

            if (!empty($ownerWallet)) {
                $ownerWalletId      = $ownerWallet->id;
                $ownerWalletBalance = $ownerWallet->balance;

                $ownerWalletUpdate             = Wallet::find($ownerWalletId);
                $ownerWalletUpdate->balance    = $ownerWalletBalance + $productPrice;
                $ownerWalletUpdate->save();
            } else {
                $ownerWallet              = new Wallet();
                $ownerWallet->user_id     = $user->user_id;
                $ownerWallet->currency_id = $currencyId;
                $ownerWallet->balance     = $productPrice;
                $ownerWallet->is_default  = 'No';
                $ownerWallet->save();
            }


            
            // Product
            $product->updateStock($product->id, $product->stock - 1);
            
            // Order table
            $orderId = substr((md5(time())), 0, 10);
            
            $order = Order::create($store->store_id, $productId, $userId, $currencyId, $orderId, date('Y-m-d'), $productPrice, 'Complete');
            
            //Transaction
            $uuid = unique_code();
            
            Transaction::create($userId, $user->user_id, $currencyId, $order->id, Order_Product, $uuid, '-' . ($productPrice), $productPrice, 'Success');

            Transaction::create($user->user_id, $userId, $currencyId, $order->id, Order_Received, $uuid, $productPrice, $productPrice, 'Success');

            \DB::commit();

            return response()->json(['status' => 200, 'message' => __('The product has been purchased successfully.')]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
