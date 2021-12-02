<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\ReferralCode;
use App\Models\ReferralLevel;
use App\Models\Setting;

class ReferralController extends Controller
{
    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function referFriend()
    {

        $data['menu'] = 'refer';

        $referralLevel = ReferralLevel::with('currency:id,symbol')->where(['status' => 'Active'])->orderBy('priority', 'asc')->first(['currency_id', 'amount']);
        // dd($referralLevel);

        if (empty($referralLevel)) {
            return redirect('/dashboard');
        }
        // dd(auth()->user()->referral_code->code);
        if (!empty($referralLevel) && !empty(auth()->user()->referral_code)) {
            // dd('tes1t');
            $data['referralLevel'] = $referralLevel;
        } else {
            // dd('test2');
            //referral code - starts
            $referralCode = $this->saveReferralCode(auth()->user()->id);
            //referral code - ends
            $data['referralLevel'] = $referralLevel;
            $data['referralCode']  = $referralCode;
        }

        // dd($data['referralLevel']);
        $data['company_name'] = getCompanyName();

        $referralPreferences = Setting::where(['type' => 'referral'])->whereIn('name', ['referral_currency', 'min_referral_amount'])->get(['value', 'name'])->toArray();
        $referralPreferences = $this->helper->key_value('name', 'value', $referralPreferences);

        // dd($referralPreferences);

        $data['min_referral_amount']        = isset($referralPreferences['min_referral_amount']) ? $referralPreferences['min_referral_amount'] : '';
        $data['referralPreferenceCurrency'] = $referralPreferenceCurrency = Currency::where(['id' => $referralPreferences['referral_currency'], 'status' => 'Active'])->first(['symbol']);
        // dd($data);
        return view('user_dashboard.refer-friend.index', $data);
    }

    protected function saveReferralCode($user_id)
    {

        $referralCode          = new ReferralCode();
        $referralCode->user_id = $user_id;
        $referralCode->code    = str_random(30);
        $referralCode->status  = 'Active';
        $referralCode->save();

        return $referralCode->code;
    }
}
