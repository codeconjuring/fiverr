<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\ReferralCode;
use App\Models\ReferralLevel;
use App\Models\Setting;
use App\Models\User;

class ReferralApiController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 401;

    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function referFriendApi()
    {
        // dd(request()->all());

        $referralLevel = ReferralLevel::with('currency:id,symbol')->where(['status' => 'Active'])->orderBy('priority', 'asc')->first(['currency_id', 'amount']);
        $user          = User::where(['id' => request('user_id'), 'status' => 'Active'])->first();
        // dd($user);
        if (!empty($referralLevel) && !empty($user->referral_code->code))
        {
            $success['referralLevel'] = $referralLevel;
            $success['referralCode']  = $user->referral_code->code;
        }
        else
        {
            //referral code - starts
            $userReferralCode             = $this->saveReferralCode(request('user_id'));
            //referral code - ends
            $success['referralLevel'] = $referralLevel;
            $success['referralCode']  = $userReferralCode;
        }
        $success['company_name'] = getCompanyName();

        $referralPreferences                   = Setting::where(['type' => 'referral'])->whereIn('name', ['referral_currency', 'min_referral_amount'])->get(['value', 'name'])->toArray();
        $referralPreferences                   = $this->helper->key_value('name', 'value', $referralPreferences);
        $success['min_referral_amount']        = isset($referralPreferences['min_referral_amount']) ? $referralPreferences['min_referral_amount'] : '';
        $success['referralPreferenceCurrency'] = $referralPreferenceCurrency = Currency::where(['id' => $referralPreferences['referral_currency'], 'status' => 'Active'])->first(['symbol']);

        return response()->json([
            'status'  => $this->successStatus,
            'success' => $success,
        ]);
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
