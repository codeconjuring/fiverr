<?php
namespace App\Http\Helpers;

use App\Http\Controllers\Users\EmailController;
use App\Models\CryptoapiLog;
use App\Models\CryptoCurrenciesSetting;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\FeesLimit;
use App\Models\NotificationSetting;
use App\Models\PayoutSetting;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Preference;
use App\Models\QrCode;
use App\Models\RoleUser;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Referral;
use App\Models\ReferralAward;
use App\Models\ReferralLevel;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Exception;

class Common
{
    public static $templateIds = ['deposit' => 23, 'payout' => 24, 'exchange' => 25, 'send' => 26, 'request' => 27, 'payment' => 28];
    public static $languages   = [1 => 'en', 2 => 'ar', 3 => 'fr', 4 => 'pt', 5 => 'ru', 6 => 'es', 7 => 'tr', 8 => 'ch'];
    protected $email;
    protected $cryptoCurrenciesSetting;
    protected $cryptoapiLog;

    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        $this->email                   = new EmailController();
        $this->cryptoCurrenciesSetting = new CryptoCurrenciesSetting();
        $this->cryptoapiLog            = new CryptoapiLog();
    }

    public static function one_time_message($class, $message)
    {
        if ($class == 'error')
        {
            $class = 'danger';
        }
        Session::flash('alert-class', 'alert-' . $class);
        Session::flash('message', $message);
    }

    // key_value [key,value, collection]
    public static function key_value($key, $value, $collection)
    {
        $return_value = [];
        foreach ($collection as $row)
        {
            $return_value[$row[$key]] = $row[$value];
        }
        return $return_value;
    }

    /*
     * @param $user_id
     * @param $permissions
     * @static has_permission
     */
    public static function has_permission($user_id, $permissions = '')
    {
        $permissions = explode('|', $permissions);

        $permission_id = [];
        $i             = 0;

        $prefix = str_replace('/', '', request()->route()->getPrefix());
        if ($prefix == 'admin')
        {
            $user_type = 'Admin';
        }
        else
        {
            $user_type = 'User';
        }

        $userPermissions = Permission::whereIn('name', $permissions)->get(['id']);
        foreach ($userPermissions as $value)
        {
            $permission_id[$i++] = $value->id;
        }
        $role = RoleUser::where(['user_id' => $user_id, 'user_type' => $user_type])->first(['role_id']);
        if (count($permission_id) && isset($role->role_id))
        {
            $has_permit = PermissionRole::where('role_id', $role->role_id)->whereIn('permission_id', $permission_id);
            return $has_permit->count();
        }
        else
        {
            return 0;
        }
    }

    /**
     * Undocumented function
     *
     * @param  [type] $host
     * @param  [type] $user
     * @param  [type] $pass
     * @param  [type] $name
     * @param  string $tables
     * @return void
     */
    public function backup_tables($host, $user, $pass, $name, $tables = '*')
    {
        try {
            $con = mysqli_connect($host, $user, $pass, $name);
        }
        catch (Exception $e)
        {
            print_r($e->getMessage());
        }

        if (mysqli_connect_errno())
        {
            $this->one_time_message('danger', "Failed to connect to MySQL: " . mysqli_connect_error());
            return 0;
        }

        if ($tables == '*')
        {
            $tables = array();
            $result = mysqli_query($con, 'SHOW TABLES');
            while ($row = mysqli_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        $return = '';
        foreach ($tables as $table)
        {
            $result     = mysqli_query($con, 'SELECT * FROM ' . $table);
            $num_fields = mysqli_num_fields($result);

            $row2 = mysqli_fetch_row(mysqli_query($con, 'SHOW CREATE TABLE ' . $table));
            $return .= "\n\n" . str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]) . ";\n\n";

            for ($i = 0; $i < $num_fields; $i++)
            {
                while ($row = mysqli_fetch_row($result))
                {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';
                    for ($j = 0; $j < $num_fields; $j++)
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                        if (isset($row[$j]))
                        {
                            $return .= '"' . $row[$j] . '"';
                        }
                        else
                        {
                            $return .= '""';
                        }
                        if ($j < ($num_fields - 1))
                        {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
            }

            $return .= "\n\n\n";
        }

        $backup_name = date('Y-m-d-His') . '.sql';

        $handle = fopen(storage_path("db-backups") . '/' . $backup_name, 'w+');
        fwrite($handle, $return);
        fclose($handle);

        return $backup_name;
    }

    //  Check user status
    public function getUserStatus($userStatus)
    {
        if ($userStatus == 'Suspended')
        {
            return 'Suspended';
        }
        elseif ($userStatus == 'Inactive')
        {
            return 'Inactive';
        }
    }

    public function checkWalletBalanceAgainstAmount($totalWithFee, $currency_id, $user_id)
    {
        //Backend Validation - Wallet Balance Again Amount Check - Starts here
        $wallet = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'balance']);
        if (!empty($wallet))
        {
            if (($totalWithFee > $wallet->balance) || ($wallet->balance < 0))
            {
                return true;
            }
        }
        //Backend Validation - Wallet Balance Again Amount Check - Ends here
    }

    /**
     * [Fetch email template]
     * param  integer $type     [temp_id]
     * param  string  $lang     [language]
     * return object  [subject, body]
     */
    public function getEmailOrSmsTemplate($tempId, $type, $lang = 'en')
    {
        $templateObject = EmailTemplate::where(['temp_id' => $tempId, 'type' => $type, 'lang' => $lang])->select('subject', 'body')->first();
        return $templateObject;
    }

    /**
     * [Get Current Date & Time - Carbon]
     * return [string] [Cardbon Date Time]
     */
    public function getCurrentDateTime()
    {
        return dateFormat(now());
    }

    public function clearSessionWithRedirect($sessionArr, $exception, $path)
    {
        Session::forget($sessionArr);
        clearActionSession();
        $this->one_time_message('error', $exception->getMessage());
        return redirect($path);
    }

    public function returnUnauthorizedResponse($unauthorisedStatus, $exception)
    {
        $success            = [];
        $success['status']  = $unauthorisedStatus;
        $success['message'] = $exception->getMessage();
        return response()->json(['success' => $success], $unauthorisedStatus);
    }

    public function getPrefProcessedBy()
    {
        $processedBy = Preference::where(['category' => 'preference', 'field' => 'processed_by'])->first(['value'])->value;
        return $processedBy;
    }

    public function validateEmailInput($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function validatePhoneInput($value)
    {
        return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i',
            $value);
    }

    public function getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, $emailOrPhone)
    {
        $selectOptions = ['id', 'first_name', 'last_name', 'email', 'carrierCode', 'phone'];
        if ($emailFilterValidate)
        {
            $userInfo = User::where(['email' => $emailOrPhone])->first($selectOptions);
        }
        elseif ($phoneRegex)
        {
            $userInfo = User::where(['formattedPhone' => $emailOrPhone])->first($selectOptions);
        }
        return $userInfo;
    }

    /**
     * fetch Deposit Active Fees Limit
     * param  int $transactionType
     * param  int $currencyId
     * param  int $paymentMethodId
     * param  array $options
     * return object
     */
    public function getFeesLimitObject($withOptions = [], $transactionType, $currencyId, $paymentMethodId, $hasTransaction, $options)
    {
        if (!empty($hasTransaction) && !empty($paymentMethodId))
        {
            $getFeesLimitObject = FeesLimit::with($withOptions)->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId, 'payment_method_id' => $paymentMethodId, 'has_transaction' => $hasTransaction])
                ->first($options);
        }
        elseif (!empty($hasTransaction) && empty($paymentMethodId))
        {
            $getFeesLimitObject = FeesLimit::with($withOptions)->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId, 'has_transaction' => $hasTransaction])
                ->first($options);
        }
        elseif (empty($hasTransaction) && !empty($paymentMethodId))
        {
            $getFeesLimitObject = FeesLimit::with($withOptions)->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId, 'payment_method_id' => $paymentMethodId])
                ->first($options);
        }
        else
        {
            $getFeesLimitObject = FeesLimit::with($withOptions)->where(['transaction_type_id' => $transactionType, 'currency_id' => $currencyId])->first($options);
        }
        return $getFeesLimitObject;
    }

    /**
     * Get Wallet Object
     * param  array  $withOptions   [eagar loaded relations]
     * param  array $constraints   [where or other conditions]
     * param  array $selectOptions [specific fields]
     * return object
     */
    public function getUserWallet($withOptions = [], $constraints, $selectOptions)
    {
        return Wallet::with($withOptions)->where($constraints)->first($selectOptions);
    }

    /**
     * Get All Wallets
     * param  array  $withOptions   [eagar loaded relations]
     * param  array $constraints   [where or other conditions]
     * param  array $selectOptions [specific fields]
     * return collection
     */
    public function getUserWallets($withOptions = [], $constraints, $selectOptions)
    {
        return Wallet::with($withOptions)->where($constraints)->get($selectOptions);
    }

    /**
     * Get Currency
     * @param  array    $constraints
     * @param  array    $selectOptions
     * @return Object
     */
    public function getCurrencyObject($constraints, $selectOptions)
    {
        return Currency::where($constraints)->first($selectOptions);
    }

    /**
     * Get Payout Setting
     * @param  array    $constraints
     * @param  array    $selectOptions
     * @return Object
     */
    public function getPayoutSettingObject($withOptions = [], $constraints, $selectOptions)
    {
        return PayoutSetting::with($withOptions)->where($constraints)->first($selectOptions);
    }

    public function sendTransactionSMSNotification($value = '')
    {
        //For SMS - in future
    }

    public function sendTransactionEmailNotification($type = null, $data = [])
    {
        //Only Email is done here; need similiar function for SMS too
        if (empty($type) || empty($data))
        {
            return false;
        }

        if (!checkAppMailEnvironment())
        {
            return false;
        }

        $emailSetting = NotificationSetting::getSettings(['nt.alias' => $type, 'notification_settings.recipient_type' => 'email', 'notification_settings.status' => 'Yes']);
        if ($emailSetting->isNotEmpty())
        {
            $recipient = $emailSetting[0]['recipient'];
            if (filter_var($recipient, FILTER_VALIDATE_EMAIL))
            {
                $lang       = !empty(getDefaultLanguage()) ? self::$languages[getDefaultLanguage()] : 'en';
                $senderInfo = self::getEmailOrSmsTemplate(self::$templateIds[$type], 'email', $lang);
                if (!empty($senderInfo->subject) && !empty($senderInfo->body))
                {
                    $subject = $senderInfo->subject;
                }
                else
                {
                    $senderInfo = self::getEmailOrSmsTemplate(self::$templateIds[$type], 'email', $lang);
                    $subject    = $senderInfo->subject;
                }
                $message = str_replace('{uuid}', $data->uuid, $senderInfo->body);
                $message = str_replace('{soft_name}', getCompanyName(), $message);

                if (in_array($type, ['deposit', 'payout']))
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);

                }
                else if ($type == 'exchange')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                    $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    $message = str_replace('{amount}', moneyFormat($data->fromWallet->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{from_wallet}', $data->fromWallet->currency->code, $message);
                    $message = str_replace('{to_wallet}', $data->toWallet->currency->code, $message);
                    $message = str_replace('{fee}', moneyFormat($data->fromWallet->currency->symbol, formatNumber($data->fee)), $message);
                }
                else if ($type == 'send')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->sender_id), $message);
                    $message = str_replace('{sender}', $data->sender->first_name . ' ' . $data->sender->last_name, $message);
                    if (!empty($data->receiver))
                    {
                        $message = str_replace('{receiver}', $data->receiver->first_name . ' ' . $data->receiver->last_name, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->email))
                    {
                        $message = str_replace('{receiver}', $data->email, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->phone))
                    {
                        $message = str_replace('{receiver}', $data->phone, $message);
                    }
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->fee)), $message);
                }
                else if ($type == 'request')
                {
                    $message = str_replace('{created_at}', dateFormat($data->created_at, $data->receiver_id), $message);
                    $message = str_replace('{creator}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    if (!empty($data->receiver))
                    {
                        $message = str_replace('{acceptor}', $data->receiver->first_name . ' ' . $data->receiver->last_name, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->email))
                    {
                        $message = str_replace('{acceptor}', $data->email, $message);
                    }
                    elseif (empty($data->receiver) && !empty($data->phone))
                    {
                        $message = str_replace('{acceptor}', $data->phone, $message);
                    }
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{request_amount}', moneyFormat($data->currency->symbol, formatNumber($data->amount)), $message);
                    $message = str_replace('{given_amount}', moneyFormat($data->currency->symbol, formatNumber($data->accept_amount)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);
                }
                else if ($type == 'payment')
                {
                    if ($data->payment_method_id == 1)
                    {
                        $message = str_replace('{created_at}', dateFormat($data->created_at, $data->user_id), $message);
                        $message = str_replace('{user}', $data->user->first_name . ' ' . $data->user->last_name, $message);
                    }
                    else
                    {
                        $message = str_replace('{created_at}', dateFormat($data->created_at), $message);
                        $message = str_replace('{user}', 'Unregistered User', $message);
                    }
                    $message = str_replace('{merchant}', $data->merchant->business_name, $message);
                    $message = str_replace('{code}', $data->currency->code, $message);
                    $message = str_replace('{amount}', moneyFormat($data->currency->symbol, formatNumber($data->total)), $message);
                    $message = str_replace('{fee}', moneyFormat($data->currency->symbol, formatNumber($data->charge_fixed + $data->charge_percentage)), $message);
                }
                try {
                    $this->email->sendEmail($recipient, $subject, $message);
                    return true;
                }
                catch (Exception $e)
                {
                    return $e;
                }
            }
        }
        return false;
    }

    /**
     * Send Transaction Notification
     *
     * @param  string $type
     * @param  array  $options
     * @return void
     */
    public function sendTransactionNotificationToAdmin($type = null, $options = [])
    {
        if (empty($type) || empty($options['data']))
        {
            return [
                'ex' => null,
            ];
        }

        $response = $this->sendTransactionEmailNotification($type, $options['data']);
        if ($response !== true)
        {
            return [
                'exFrom' => 'mailToAdmin',
                'ex'     => $response,
            ];
        }

        //if SMS - for future
        // $response = $this->sendTransactionSMSNotification($type, $data);
        // if ($response !== true)
        // {
        //     return [
        //         'exFrom' => 'mailToAdmin',
        //         'ex' => $response,
        //     ];
        // }

        return [
            'ex' => null,
        ];
    }

    public static function checkReferralAward($refAwardData)
    {
        //check min referral amount and referral currency from settings
        $referralPreferences = Setting::where(['type' => 'referral'])->whereIn('name', ['referral_currency', 'min_referral_amount', 'is_referral_enabled'])->get(['value', 'name'])->toArray();
        $common              = new Common();
        $referralPreferences = $common->key_value('name', 'value', $referralPreferences);
        $referralCurrencyId  = $referralPreferences['referral_currency'];
        $minReferralAmount   = $referralPreferences['min_referral_amount'];
        $isReferralEnabled   = $referralPreferences['is_referral_enabled'];

        //Get referral Preference Currency Exchange Rate
        $referralPreferenceCurrency = Currency::where(['id' => $referralCurrencyId, 'status' => 'Active'])->first(['exchange_from', 'code', 'rate', 'status']);
        // dd($referralPreferenceCurrency);

        //Get Referred By user id
        $referral = Referral::with(['referredBy:id,first_name,last_name,email,carrierCode,phone', 'referredBy.referral_code:id,user_id'])
            ->where(['referred_to' => $refAwardData['userId']])->first(['id', 'referred_by']);

        //if min_referral_amount exist and referral Preference Currency is Active
        if ($isReferralEnabled == 'yes' && isset($minReferralAmount) && !empty($referralPreferenceCurrency) && !empty($referral))
        {
            $initialReferralId = $referral->id; //new
            if ($refAwardData['currencyId'] != $referralCurrencyId)
            {
                // Example - if (USD and GBP) - different

                //Check referral Preference Currency rate is LOCAL or API
                if ($referralPreferenceCurrency->exchange_from == "local")
                {
                    $presentTransactionCurrency = Currency::where(['id' => $refAwardData['currencyId']])->first(['rate']);
                    $defaultCurrency            = Currency::where(['default' => 1])->first(['rate']);
                    $resultedCurrencyRate       = ($defaultCurrency->rate / $presentTransactionCurrency->rate) * $referralPreferenceCurrency->rate;
                }
                else
                {
                    $resultedCurrencyRate = getCurrencyRate($refAwardData['currencyCode'], $referralPreferenceCurrency->code);
                }
                //Conversion - Mulitplying referral Preference Currency rate with amount to get equivalent referral Award Check Amount amount
                $referralAwardCheckAmount = $refAwardData['presentAmount'] * $resultedCurrencyRate;
            }
            else
            {
                // Example - if (GBP and GBP) - same
                $referralAwardCheckAmount = $refAwardData['presentAmount'];
            }

            // check amount is equal to or greater than min referral amount
            if ($referralAwardCheckAmount >= $minReferralAmount)
            {
                //Get Priory By Asc Amount from referrel level
                $referralLevel = ReferralLevel::where(['status' => 'Active'])->orderBy('priority', 'asc')->get(['id', 'amount', 'currency_id'])->toArray();
                // dd($referralLevel);


                if (!empty($referralLevel))
                {
                    $referredTo = $refAwardData['userId'];
                    $i          = 0;
                    $count = count($referralLevel) - 1;

                    while ($i <= $count)
                    {
                        if ($i != 0)
                        {
                            $referral = Referral::with(['referredBy:id,first_name,last_name,email,carrierCode,phone', 'referredBy.referral_code:id,user_id'])
                                ->where(['referred_to' => $referredTo])->first(['referred_by']);
                        }
                        if (empty($referral))
                        {
                            break;
                        }

                        //Check Referral Award
                        $referralAwardConditions = [
                            'referral_id'       => $initialReferralId,
                            'referral_level_id' => $referralLevel[$i]['id'],
                            'referral_code_id'  => $referral->referredBy->referral_code->id,
                            'referred_to'       => $referredTo,
                        ];
                        $referralAward = ReferralAward::where($referralAwardConditions)->first(['id']);

                        // dd($referralAward);
                        if (empty($referralAward))
                        {
                            try
                            {
                                \DB::beginTransaction();

                                //Create Referral Award Entry
                                $referralAward                    = new ReferralAward();
                                $referralAward->referral_id       = $initialReferralId; //new
                                $referralAward->referral_level_id = $referralLevel[$i]['id'];
                                $referralAward->referral_code_id  = $referral->referredBy->referral_code->id;
                                $referralAward->awarded_user_id   = $referral->referred_by;
                                $referralAward->referred_to       = $referredTo;
                                $referralAward->awarded_amount    = $referralLevel[$i]['amount'];
                                $referralAward->save();

                                //Create Referral Transaction Entry
                                $transaction                           = new Transaction();
                                $transaction->user_id                  = $referral->referred_by; //fixed
                                $transaction->currency_id              = $refAwardData['currencyId'];
                                // $transaction->currency_id              = $referralLevel[$i]['currency_id'];
                                $transaction->payment_method_id        = $refAwardData['paymentMethodId'];
                                $transaction->transaction_reference_id = $referralAward->id;
                                $transaction->transaction_type_id      = Referral_Award; //DONE
                                $transaction->uuid                     = unique_code();
                                $transaction->subtotal                 = $referralLevel[$i]['amount'];
                                $transaction->percentage               = 0;
                                $transaction->charge_percentage        = 0;
                                $transaction->charge_fixed             = 0;
                                $transaction->total                    = $referralLevel[$i]['amount'];
                                $transaction->status                   = 'Success';
                                $transaction->save();

                                //Update/Create Referred By Wallet
                                $referredByUserWallet = Wallet::with('currency:id,symbol')
                                    ->where(['user_id' => $referral->referred_by, 'currency_id' => $refAwardData['currencyId']])->first(['id', 'balance', 'currency_id']);
                                if (!empty($referredByUserWallet))
                                {
                                    //Update Referred By Wallet Balance with referral Level amount
                                    $referredByUserWallet->balance = $referredByUserWallet->balance + $referralLevel[$i]['amount'];
                                    $referredByUserWallet->save();
                                }
                                else
                                {
                                    //Create Referred By Wallet and add referral Level amount
                                    $referredByUserWallet              = new Wallet();
                                    $referredByUserWallet->user_id     = $referral->referred_by;
                                    $referredByUserWallet->currency_id = $refAwardData['currencyId'];
                                    $referredByUserWallet->balance     = $referralLevel[$i]['amount'];
                                    $referredByUserWallet->is_default  = 'No';
                                    $referredByUserWallet->save();
                                }

                                \DB::commit();

                                //Send Notification(Mail and SMS) to referred by user about referral award

                                /**
                                 * Mail To Referred By User
                                 */
                                //if other language's subject and body not set, get en sub and body for mail
                                $englishSenderLanginfo  = EmailTemplate::where(['temp_id' => 30, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
                                $referralAwardEmailTemp = EmailTemplate::where(['temp_id' => 30, 'language_id' => getDefaultLanguage(), 'type' => 'email'])->select('subject', 'body')->first();
                                if (!empty($referralAwardEmailTemp->subject) && !empty($referralAwardEmailTemp->body))
                                {
                                    $referralAwardEmailTempSub     = $referralAwardEmailTemp->subject;
                                    $referralAwardEmailTempMessage = str_replace('{referred_by_user}', $referral->referredBy->first_name . ' ' . $referral->referredBy->last_name, $referralAwardEmailTemp->body);
                                }
                                else
                                {
                                    $referralAwardEmailTempSub     = $englishSenderLanginfo->subject;
                                    $referralAwardEmailTempMessage = str_replace('{referred_by_user}', $referral->referredBy->first_name . ' ' . $referral->referredBy->last_name, $englishSenderLanginfo->body);
                                }
                                $referralAwardEmailTempMessage = str_replace('{amount}', moneyFormat($referredByUserWallet->currency->symbol, formatNumber($referralAward->awarded_amount)),
                                    $referralAwardEmailTempMessage);
                                $referralAwardEmailTempMessage = str_replace('{soft_name}', getCompanyName(), $referralAwardEmailTempMessage);
                                if (checkAppMailEnvironment())
                                {
                                    try
                                    {
                                        $emailObject = new EmailController();
                                        $emailObject->sendEmail($referral->referredBy->email, $referralAwardEmailTempSub, $referralAwardEmailTempMessage);
                                    }
                                    catch (\Exception $e)
                                    {
                                        // \DB::rollBack();
                                        echo "<pre>";
                                        echo $e->getMessage();
                                        echo "<br>";
                                    }
                                }

                                /**
                                 * SMS To Referred By User
                                 */
                                //if other language's subject and body not set, get en sub and body for mail
                                $englishSenderLangSMSinfo = EmailTemplate::where(['temp_id' => 31, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
                                $referralAwardSmsTemp     = EmailTemplate::where(['temp_id' => 31, 'language_id' => getDefaultLanguage(), 'type' => 'sms'])->select('subject', 'body')->first();
                                if (!empty($referralAwardSmsTemp->subject) && !empty($referralAwardSmsTemp->body))
                                {
                                    $referralAwardSmsTempSub     = $referralAwardSmsTemp->subject;
                                    $referralAwardSmsTempMessage = str_replace('{referred_by_user}', $referral->referredBy->first_name . ' ' . $referral->referredBy->last_name, $referralAwardSmsTemp->body);
                                }
                                else
                                {
                                    $referralAwardSmsTempSub     = $englishSenderLanginfo->subject;
                                    $referralAwardSmsTempMessage = str_replace('{referred_by_user}', $referral->referredBy->first_name . ' ' . $referral->referredBy->last_name, $englishSenderLanginfo->body);
                                }
                                $referralAwardSmsTempMessage = str_replace('{amount}', moneyFormat($referredByUserWallet->currency->symbol, formatNumber($referralAward->awarded_amount)),
                                    $referralAwardSmsTempMessage);
                                $referralAwardSmsTempMessage = str_replace('{soft_name}', getCompanyName(), $referralAwardSmsTempMessage);

                                if (!empty($referral->referredBy->carrierCode) && !empty($referral->referredBy->phone))
                                {
                                    if (checkAppSmsEnvironment())
                                    {
                                        if (!empty($referral->referredBy->carrierCode) && !empty($referral->referredBy->phone)) {
                                            if (checkAppSmsEnvironment()) {
                                                try {
                                                    sendSMS($referral->referredBy->carrierCode . $referral->referredBy->phone, $referralAwardSmsTempMessage);
                                                } catch (\Exception $e) {
                                                    \DB::rollBack();
                                                    echo "<pre>";
                                                    echo $e->getMessage();
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                    }
                                }
                                
                            }
                            catch (\Exception $e)
                            {
                                \DB::rollBack();
                                echo "<pre>";
                                echo $e->getMessage();
                                echo "<br>";
                            }
                        }
                        $referredTo = $referral->referred_by;
                        $i++; //increment
                    }
                }
            }
        }
    }
    
     /**
     * [It will print QR code for express, standard merchant, customer profile]
     * @param  [type] $id             [Can be merchant ID or User ID]
     * @param  [type] $objectType     [standard_merchant, express_merchant]
     * @return [type] [description]
     */
    public function printQrCode($id, $objectType) {
        // dd([$id,$objectType]);

        $data['qrCode'] = $qrCode = QrCode::where(['object_id' => $id, 'object_type' => $objectType, 'status' => 'Active'])->first(['secret']);
        if (empty($qrCode)) {
            $this->one_time_message('error', "QR code not found");
            return redirect('merchants');
        }

        $data['QrCodeSecret'] = urlencode($qrCode->secret);
        $mpdf                 = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf                 = new \Mpdf\Mpdf([
            'mode'                 => 'utf-8',
            'format'               => 'A4',
            'orientation'          => 'P',
            'shrink_tables_to_fit' => 0,
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        if ($objectType == 'standard_merchant' || $objectType == 'express_merchant') {
            $mpdf->WriteHTML(view('user_dashboard.Merchant.qrCodePDF', $data));
            $mpdf->Output('MerchantQrCode' . time() . '.pdf', 'I');
        } else if ($objectType == "user") {
            $mpdf->WriteHTML(view('user_dashboard.users.qrCodePDF', $data));
            $mpdf->Output('CustomerQrCode' . time() . '.pdf', 'I');
        } else if ($objectType == "voucher") {
            $mpdf->WriteHTML(view('user_dashboard.voucher.qrCodePDF', $data));
            $mpdf->Output('CustomerQrCode' . time() . '.pdf', 'I');
        }
    }


     public function getOtpVerificationVia(){
        $otpVerificationvia= Preference::where(['category'=>'preference','field'=>'otp_verification_via'])->first()->value;
        return $otpVerificationvia;
    }

    public function otpVerification($user, $message, $otp_status){

       $otpVerificationvia = $this->getOtpVerificationVia();

       if( $otpVerificationvia=='phone'){

            if (!empty($user->formattedPhone)) {
                $otpVerified=sendSMS($user->formattedPhone,$message);
            }

            if($otp_status=='register'){
                $this->one_time_message('success', __('We have send a verfication code to your phone number!'));
            }elseif($otp_status=='login'){
                $this->one_time_message('error', __('You are not verified, check your phone for verfication code!'));
            }elseif($otp_status=='resend'){
                $this->one_time_message('success', __('We have send a new verfication code to your phone number'));
            }  
        }

        Session::put('registerUserId', $user->id);
        Session::put('registerUserFormattedPhone', $user->formattedPhone);
    }
        
    /**
     * Method otpVerificationApi
     *
     * @param $user $user [explicite description]
     * @param $message $message [explicite description]
     * @param $otp_status $otp_status [explicite description]
     *
     * @return void
     */
    public function otpVerificationApi($user, $message, $otp_status)
    {
        $otpVerificationvia = $this->getOtpVerificationVia();
        if ($otpVerificationvia == 'phone') {
            if (!empty($user->formattedPhone)) {
                $otpVerified = sendSMS($user->formattedPhone, $message);
            }
            if ($otp_status == 'register') {
                $result_message = 'We have send a verification code to your phone number!';
            } elseif ($otp_status == 'login') {
                $result_message = 'You are not verified, check your phone for verification code!';
            } elseif ($otp_status == 'resend') {
                $result_message = 'We have send a new verification code to your phone number!';
            }
        } elseif ($otpVerificationvia == 'email') {
            if ($otp_status == 'register') {
                $this->email->sendEmail($user->email, 'OTP Verification', $message);
                $result_message = 'We have send a verification code to your email!';
            } elseif ($otp_status == 'login') {
                $this->email->sendEmail($user->email, 'OTP Verification', $message);
                $result_message = 'You are not verified, check your email for verification code!';
            } elseif ($otp_status == 'resend') {
                $this->email->sendEmail($user->email, 'New OTP Verification', $message);
                $result_message = 'We have send a new verification code to your email!';
            }
        } else {
            if (!empty($user->formattedPhone)) {
                $otpVerified = sendSMS($user->formattedPhone, $message);
            }
            if ($otp_status == 'register') {
                $this->email->sendEmail($user->email, 'OTP Verification', $message);
                $result_message = 'We have send a verification code, check your email or phone!';
            } elseif ($otp_status == 'login') {
                $this->email->sendEmail($user->email, 'OTP Verification', $message);
                $result_message = 'You are not verified, check your email or phone for verification code!';
            } elseif ($otp_status == 'resend') {
                $this->email->sendEmail($user->email, 'New OTP Verification', $message);
                $result_message = 'We have send a new verification code, check your email or phone!';
            }
        }
        return $result_message;
    }
}
