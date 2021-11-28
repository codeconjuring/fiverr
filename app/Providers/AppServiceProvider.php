<?php

namespace App\Providers;

use App\Models\Fee;
use App\Models\Merchant;
use App\Models\Voucher;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //To hide PHP version from attackers
        header('x-powered-by:');

        if (!defined('Deposit')) define('Deposit', 1);

        if (!defined('Withdrawal')) define('Withdrawal', 2);

        if (!defined('Transferred')) define('Transferred', 3);

        if (!defined('Received')) define('Received', 4);

        if (!defined('Exchange_From')) define('Exchange_From', 5);

        if (!defined('Exchange_To')) define('Exchange_To', 6);

        if (!defined('Request_From')) define('Request_From', 9);

        if (!defined('Request_To')) define('Request_To', 10);

        if (!defined('Payment_Sent')) define('Payment_Sent', 11);

        if (!defined('Payment_Received')) define('Payment_Received', 12);

        if (!defined('Crypto_Sent')) define('Crypto_Sent', 13);

        if (!defined('Crypto_Received')) define('Crypto_Received', 14);

        if (!defined('Order_Product')) define('Order_Product', 15);

        if (!defined('Order_Received')) define('Order_Received', 16);

        if (!defined('Referral_Award')) define('Referral_Award', 17);

        if (!defined('Voucher_Created')) define('Voucher_Created', 18);

        if (!defined('Voucher_Activated')) define('Voucher_Activated', 19);

        if (!defined('BLOCKIO_API_VERSION')) define('BLOCKIO_API_VERSION', 2);

        Schema::defaultStringLength(191);

        //custom validation rule - alpha_spaces
        Validator::extend('alpha_spaces', function ($attribute, $value)
        {
            return preg_match('/^[\pL\s-]+$/u', $value);// This will only accept alpha, hyphens and spaces.
        });

        // unique_transaction_type validation
        Validator::extend('unique_transaction_type', function ($attribute, $value, $parameters, $validator)
        {
            if (!empty($value))
            {
                $request = app(\Illuminate\Http\Request::class);
                $fees    = Fee::where(['transaction_type' => $request->transaction_type, 'payment_method_id' => $value])->first();
                if (!empty($fees))
                {
                    return false;
                }
                return true;
            }
            return false;
        });

        Validator::extend('default_wallet_balance', function ($attribute, $value, $parameters, $validator)
        {
            if (!empty($value))
            {
                $request = app(\Illuminate\Http\Request::class);
                $wallet  = Wallet::where(['user_id' => Auth::user()->id, 'is_default' => 'Yes'])->first();

                // if ($wallet->balance < $request->amount) // shahin vai - old logic
                if (($wallet->balance + $request->fee) > $request->amount) //fixed by parvez
                {
                    return false;
                }
                return true;
            }
            return false;
        });

        Validator::extend('check_wallet_balance', function ($attribute, $value, $parameters, $validator)
        {
            if (!empty($value))
            {
                $request = app(\Illuminate\Http\Request::class);
                $wallet  = Wallet::where(['user_id' => Auth::user()->id, 'currency_id' => $request->currency_id])->first();

                if (empty($wallet))
                {
                    $walletObj              = new Wallet();
                    $walletObj->user_id     = Auth::user()->id;
                    $walletObj->currency_id = $request->currency_id;
                    $walletObj->balance     = 0;
                    $walletObj->save();

                    return false;
                }
                if ($wallet->balance < $request->amount)
                {
                    return false;
                }
                return true;
            }
            return false;
        });

        Validator::extend('check_voucher_code', function ($attribute, $value, $parameters, $validator) {
            if (!empty($value)) {
                $request = app(\Illuminate\Http\Request::class);
                $voucher = Voucher::where(['code' => $request->code, 'status' => 'Success'])->first();
                if (empty($voucher)) {
                    return false;
                }
                return true;
            }
            return false;
        });

        Validator::extend('unique_merchant_business_name', function ($attribute, $value, $parameters, $validator)
        {
            if (!empty($value))
            {
                $request = app(\Illuminate\Http\Request::class);
                $merchant = Merchant::where(['business_name' => $request->business_name, 'user_id' => auth()->user()->id])
                    ->first();
                if (empty($merchant))
                {
                    return true;
                }
                return false;
            }
            return false;
        });

    }

    /**
     * Register any application services.
     *
     */
    public function register()
    {
    }
}
