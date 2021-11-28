<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\ReferralLevel;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferralSettingController extends Controller
{

    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function referralPreferenceSettings(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'referral_settings';

            $data['activeCurrencies']    = $activeCurrencies = Currency::where(['status' => 'Active'])->get(['id', 'code']);

            // dd($activeCurrencies);

            $referralPreferences         = Setting::where(['type' => 'referral'])->whereIn('name', ['referral_currency', 'is_referral_enabled', 'min_referral_amount'])->get(['value', 'name'])->toArray();
            $referralPreferences         = $this->helper->key_value('name', 'value', $referralPreferences);
            $data['is_referral_enabled'] = isset($referralPreferences['is_referral_enabled']) ? $referralPreferences['is_referral_enabled'] : '';
            $data['referral_currency']   = isset($referralPreferences['referral_currency']) ? $referralPreferences['referral_currency'] : '';
            $data['min_referral_amount'] = isset($referralPreferences['min_referral_amount']) ? $referralPreferences['min_referral_amount'] : '';

            // dd($data);

            return view('admin.referral_settings.referral-preference', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());

            Setting::where(['name' => 'is_referral_enabled', 'type' => 'referral'])->update(['value' => $request->is_referral_enabled]);
            Setting::where(['name' => 'referral_currency', 'type' => 'referral'])->update(['value' => $request->referral_currency]);
            Setting::where(['name' => 'min_referral_amount', 'type' => 'referral'])->update(['value' => $request->min_referral_amount]);

            $this->helper->one_time_message('success', 'Referral Preferences Updated Successfully');
            return redirect('admin/settings/referral-preferences');
        }
    }

    public function referralSettings()//index
    {
        $data['menu']                     = 'referral_settings';
        $data['referralLevelsStatus']     = $referralLevelsStatus = ReferralLevel::groupBy('status')->get(['status']);
        // dd($referralLevelsStatus);
        $data['referralLevelsCurrencies'] = $referralLevelsStatus = ReferralLevel::with('currency:id,name')->groupBy('currency_id')->get(['currency_id']);
        // dd($referralLevelsStatus);

        //Status Filtering
        $data['status']   = $status   = isset($_GET['status']) ? $_GET['status'] : 'Active';   // dd($status);
        $data['currency'] = $currency = isset($_GET['currency']) ? $_GET['currency'] : 'all';   // dd($currency);

        $referralLevels   = ReferralLevel::with('currency:id,code,status,name');


        // dd($referralLevels[0]['code']);

        if ($status != 'all')
        {
            $referralLevels->where(['status' => $status]);
        }
        if ($currency != 'all')
        {
            $referralLevels->where(['currency_id' => $currency]);
        }
        $data['referralLevels'] = $referralLevels = $referralLevels->orderBy('priority', 'asc')->orderBy('status', 'asc')->get();
        //

        //To make latest currency as selected in - Mass Update
        $data['referralLevelCurrency'] = $referralLevelCurrency = ReferralLevel::with('currency:id,code,status,name')->latest()->first(['currency_id']);
        // dd($referralLevelCurrency);

        $data['activeCurrencies'] = Currency::where(['status' => 'Active'])->get(['id', 'name']);
        $data['defaultCurrency']  = Currency::where(['default' => '1'])->first(['id', 'name']);


        return view('admin.referral_settings.list', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST)
        {
            // dd(session()->all());
            $data['menu']             = 'referral_settings';
            $data['referralLevel']    = $referralLevel    = ReferralLevel::where(['status' => 'Active'])->orderBy('priority', 'desc')->first();

            //    dd($referralLevel); // priority = jar sobcheye basi and active

            $data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active'])->get(['id', 'code']);

            $data['defaultCurrency']  = $defaultCurrency  = Currency::where(['default' => '1'])->first(['id']);
            // dd($referralLevel);
            return view('admin.referral_settings.add', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());
            $rules = array(
                'level'  => 'required|unique:referral_levels,level', //TODO
                'level'  => 'required',
                'amount' => 'required|numeric',
            );

            $fieldNames = array(
                'level'  => 'Level',
                'amount' => 'Amount',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $referralLevel              = new ReferralLevel();
                $referralLevel->level       = $request->level;
                $referralLevel->amount      = $request->amount;
                $referralLevel->currency_id = $request->currency_id;
                $referralLevel->priority    = $request->priority;
                $referralLevel->status      = 'Active'; //Active
                $referralLevel->save();

                $this->helper->one_time_message('success', 'Referral Setting Added Successfully');
                return redirect('admin/settings/referral-settings');
            }
        }
        else
        {
            return redirect('admin/settings/referral-settings');
        }
    }

    public function updateReferralSetting(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']          = 'referral_settings';
            $data['referralLevel'] = $referralLevel = ReferralLevel::find($request->id);
            // dd($referralLevel);
            return view('admin.referral_settings.edit', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());

            //Backend Validation Check
            $request['referral_id'] = $request->referral_id;
            $request['level']       = $request->level;
            $checkDuplicateLevel    = $this->checkDuplicateLevel($request);
            $checkDuplicateLevel    = json_decode($checkDuplicateLevel, true);
            if ($checkDuplicateLevel['status'] == true)
            {
                $this->helper->one_time_message('error', $checkDuplicateLevel['message']);
                return redirect('admin/settings/referral-settings');
            }

            $rules = array(
                'level'  => 'required',
                'amount' => 'required|numeric',
            );

            $fieldNames = array(
                'level'  => 'Level',
                'amount' => 'Amount',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $findReferralLevel = ReferralLevel::find($request->referral_id);

                // dd($findReferralLevel);
                //$request->status == 'Active'
                if ($request->status == 'Active' && $findReferralLevel->status == 'Inactive')
                {
                    // dd('test');
                    //get active referral levels
                    $referralLevels = ReferralLevel::where(['status' => 'Active'])->get();
                    // dd($referralLevels);
                    foreach ($referralLevels as $refLevl)
                    {
                        //Referral Level Duplicate Check
                        if (trim($request->level) == trim($refLevl->level))
                        {
                            $this->helper->one_time_message('error', 'Referral Level Already Exists!');
                            return redirect('admin/settings/referral-settings');
                        }

                        //if destination currency priority > destination current priority
                        if ($refLevl->priority > $request->priority)
                        {
                            //Update/Increment active referral levels priority
                            $refLevl->priority = ++$refLevl->priority;
                            $refLevl->save();
                        }
                        //Current Level entry - unchanged
                    }

                    // fetch Active Referral Level and Insert new active currency referral level - from inactive request currency referral level
                    $fetchActiveReferralLevel       = ReferralLevel::where(['status' => 'Active'])->orderBy('priority', 'desc')->first(['currency_id','priority']);
                    if (!empty($fetchActiveReferralLevel))
                    {
                        $newRefLvlInstance              = new ReferralLevel();
                        $newRefLvlInstance->level       = $request->level;
                        $newRefLvlInstance->amount      = $request->amount;
                        $newRefLvlInstance->currency_id = $fetchActiveReferralLevel->currency_id; //destination referral level currency
                        $newRefLvlInstance->priority    = ++$request->priority;
                        $newRefLvlInstance->status      = 'Active'; //Active
                        $newRefLvlInstance->save();
                    }

                }
                else
                {
                    $referralLevels = ReferralLevel::where(['status' => 'Active'])->get(['id']);
                    if (count($referralLevels) <= 1) {
                        $this->helper->one_time_message('error', 'Atleast one referral level setting must be active!');
                        return redirect('admin/settings/referral-settings');
                    }

                    //$request->status == 'Inactive' or $request->status == unchanged(in active)
                    $findReferralLevel->level       = $request->level;
                    $findReferralLevel->amount      = $request->amount;
                    $findReferralLevel->currency_id = $request->currency_id;
                    $findReferralLevel->priority    = $request->priority;
                    $findReferralLevel->priority    = $request->priority;
                    $findReferralLevel->status      = $request->status; //Status can now be changed - not incremented
                    $findReferralLevel->save();
                }
                $this->helper->one_time_message('success', 'Referral Setting Updated Successfully');
                return redirect('admin/settings/referral-settings');
            }
        }
    }

    public function massUpdateReferralLevels(Request $request)
    {
        // dd($request->all());

        $selectedCurrencyReferralLevels = ReferralLevel::where(['currency_id' => $request->selectedCurrencyId, 'status' => 'Active'])->get(['id']);

        //Check Selected Currency levels has collection or not
        if ($selectedCurrencyReferralLevels->isEmpty())
        {
            //get Default Currency levels Collection
            $defaultCurrencyReferralLevels = ReferralLevel::where(['currency_id' => $request->defaultCurrencyId, 'status' => 'Active'])->orderBy('priority', 'ASC')->get();

            foreach ($defaultCurrencyReferralLevels as $refLevl)
            {
                //all defaul Currency level's status - set to Inactive
                $refLevl->status = 'Inactive';
                $refLevl->save();

                //Update all Currency level's status - except default Currency and selected Currency - to Inactive
                $getActiveStatusLevels = ReferralLevel::where(['status' => 'Active'])->where('currency_id', '!=', $request->defaultCurrencyId)->where('currency_id', '!=', $request->selectedCurrencyId)
                    ->get(['id', 'status']);
                foreach ($getActiveStatusLevels as $activeRefLevl)
                {
                    $activeRefLevl->status = 'Inactive';
                    $activeRefLevl->save();
                }

                //Mass Update - Replication/Creating Existing Values to New Selected Currency
                $newRefLvl              = new ReferralLevel();
                $newRefLvl->level       = $refLevl->level;
                $newRefLvl->amount      = $refLevl->amount;
                $newRefLvl->currency_id = $request->selectedCurrencyId; //Selected Currency ID
                $newRefLvl->priority    = $refLevl->priority;
                $newRefLvl->status      = 'Active';
                $newRefLvl->save();
            }
            return response()->json([
                'status' => true,
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function checkDuplicateLevel(Request $request)
    {
        // dd($request->all());
        $referral_id = $request->referral_id;
        if (isset($referral_id))
        {
            $referralLevels = ReferralLevel::where(['level' => $request->level, 'status' => 'Active'])
                ->where(function ($q) use ($referral_id)
            {
                    $q->where('id', '!=', $referral_id);
                })
                ->orderBy('priority', 'ASC')->get();
        }
        else
        {
            $referralLevels = ReferralLevel::where(['level' => $request->level, 'status' => 'Active'])->orderBy('priority', 'ASC')->get();
        }

        if ($referralLevels->isNotEmpty())
        {
            $data['status']  = true;
            $data['message'] = "The referral level already exists!";
        }
        else
        {
            $data['status']  = false;
            $data['message'] = "The referral level is Available!";
        }
        return json_encode($data);
    }
}
