<?php

namespace App\Models;

use App\Models\Currency;
use DB;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table    = 'wallets';
    protected $fillable = ['user_id', 'currency_id', 'balance', 'is_default'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function active_currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->where('status', 'Active');
    }

    public function currency_exchanges()
    {
        return $this->hasMany(CurrencyExchange::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function walletBalance()
    {
        $data = $this->leftJoin('currencies', 'currencies.id', '=', 'wallets.currency_id')
            ->select(DB::raw('SUM(wallets.balance) as amount,wallets.currency_id,currencies.type, currencies.code, currencies.symbol'))
            ->groupBy('wallets.currency_id')
            ->get();

        $array_data = [];
        foreach ($data as $row)
        {
            $array_data[$row->code] = $row->type != 'fiat' ? $row->amount : formatNumber($row->amount);
        }
        return $array_data;
    }

    //new
    public function cryptoapi_log()
    {
        return $this->hasOne(CryptoapiLog::class, 'object_id')->whereIn('object_type', ["wallet_address"]);
    }

    //Query for Mobile Application - starts
    public function getAvailableBalance($user_id)
    {
        $wallets = $this->with(['currency:id,type,code'])->where(['user_id' => $user_id])
            ->orderBy('balance', 'ASC')
            ->get(['currency_id', 'is_default', 'balance'])
            ->map(function ($wallet)
        {
                $arr['balance']    = $wallet->currency->type != 'fiat' ? $wallet->balance : formatNumber($wallet->balance);
                $arr['is_default'] = $wallet->is_default;
                $arr['curr_code']  = $wallet->currency->code;
                return $arr;
            });
        return $wallets;
    }

    //Create new wallet 
    public static function create($user_id = null, $currency_id = null, $balance = null, $is_default = 'No')
    {
        $wallet = new Wallet;

        $wallet->user_id = $user_id;
        $wallet->currency_id = $currency_id;
        $wallet->balance = $balance;
        $wallet->is_default = $is_default;

        $wallet->save();
    }
    
    //Query for Mobile Application - ends

    // View or update an existing wallet
    /**
     * viewOrUpdate function
     *
     * @param array $values
     * @param string $option
     * @return object
     */
    public static function viewOrUpdate(array $values, $option = null)
    {
        if (empty($option)) {
            return response()->json(['status' => 400, 'message' => __('Invalid params!')]);
        }

        if ($option == 'view') {
            if (array_key_exists('id', $values)) {
                $wallet = Wallet::where('id', $values['id'])->first();
            } else {
                $wallet = Wallet::where(['user_id' => $values['user_id'], 'currency_id' => $values['currency_id']])->first();
            }

            if (empty($wallet)) {
                return response()->json(['status' => 401, 'message' => __('Wallet not found.')]);
            }
            
            return $wallet;
        }
        
        if ($option == 'update') {
            $wallet = Wallet::find($values['id']);
            if (empty($wallet)) {
                exit();
            }
            $wallet->balance = $values['balance'];
            return $wallet->save();
        }

    }
}
