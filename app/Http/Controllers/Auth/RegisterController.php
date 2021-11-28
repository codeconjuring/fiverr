<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;

use Illuminate\Support\Facades\{DB,
    Validator, 
    Auth
};
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{RoleUser,
    VerifyUser,
    Setting,
    User,
    Role,
    QrCode,
    Referral,
    ReferralCode
};
use Exception;
use Session;
use Cache;

class RegisterController extends Controller
{
    protected $helper;
    protected $email;
    protected $user;
    protected $referralIdentifier;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        $this->user   = new User();
        $this->referralIdentifier = md5(getBrowser($_SERVER['HTTP_USER_AGENT'])['platform'] . $_SERVER['REMOTE_ADDR']);
    }

    public function create()
    {
        $data['title'] = 'Register';

        if (Auth::check())
        {
            return redirect('/dashboard');
        }
        $data['checkMerchantRole'] = $checkMerchantRole = Role::where(['user_type' => 'User', 'customer_type' => 'merchant', 'is_default' => 'Yes'])->first(['id']);
        $data['checkUserRole']     = $checkUserRole     = Role::where(['user_type' => 'User', 'customer_type' => 'user', 'is_default' => 'Yes'])->first(['id']);
        return view('frontend.auth.register', $data);
    }

    public function checkReferralLink($code)
    {
        $referralData = [];
        $referralCode = ReferralCode::where(['code' => $code])->first(['user_id']);
        if (!empty($referralCode))
        {
            $referralData['referred-by']   = $referralCode->user->id;
            $referralData['referral-code'] = $code;

            Cache::put('referralData-' . $this->referralIdentifier, $referralData, 60 * 24 * 7); 

            Cache::put('referralFlag', true, 60 * 24 * 7);
        }

        return redirect('/register');
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $rules = array(
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|email|unique:users,email',
                'phone'                 => 'required|unique:users,phone',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required',
            );

            $fieldNames = array(
                'first_name'            => 'First Name',
                'last_name'             => 'Last Name',
                'email'                 => 'Email',
                'phone'                 => 'Phone',
                'password'              => 'Password',
                'password_confirmation' => 'Confirm Password',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);

                try
                {
                    DB::beginTransaction();

                    // Create user
                    $user = $this->user->createNewUser($request, 'user');

                    // Assign user type and role to new user
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                    // Create user detail
                    $this->user->createUserDetail($user->id);

                    // Create user's default wallet
                    $this->user->createUserDefaultWallet($user->id, $default_currency->value);
                    // Entry for User's QrCode Generation - starts
                    $this->saveUserQrCode($user);
                    //Entry for User's QrCode Generation - ends
                    //Entry for User's QrCode Generation - starts
                    $this->saveuserQrCode($user);
                    //Entry for User's QrCode Generation - ends
                    
                    // Save referral code for new User
                    $this->saveReferralCode($user->id);

                    // Check Cache & Save to Referrals - starts
                    $this->saveReferralWithCacheCheck($user->id);

                    // Create user's crypto wallet/wallets address
                    $generateUserCryptoWalletAddress = $this->user->generateUserCryptoWalletAddress($user);
                    if ($generateUserCryptoWalletAddress['status'] == 401)
                    {
                        DB::rollBack();
                        $this->helper->one_time_message('error', $generateUserCryptoWalletAddress['message']);
                        return redirect('/login');
                    }

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    // Process Registered User Transfers
                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Process Registered User Request Payments
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Email verification
                    if (!$user->user_detail->email_verification)
                    {
                        if (checkVerificationMailStatus() == "Enabled")
                        {
                            if (checkAppMailEnvironment())
                            {
                                $emainVerificationArr = $this->user->processUserEmailVerification($user);

                                try
                                {
                                    $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);

                                    if (checkOtpVerification() == 'Enabled') {
                                        if (checkAppSmsEnvironment()) {
                                            $this->sendOtpCode($user);
                                        }
                                    }

                                    DB::commit();
                                    $this->helper->one_time_message('success', __('We sent you an activation code. Check your email and click on the link to verify.'));
                                    return redirect('/login');
                                }
                                catch (Exception $e)
                                {
                                    DB::rollBack();
                                    $this->helper->one_time_message('error', $e->getMessage());
                                    return redirect('/login');
                                }
                            }
                        }
                    }

                    //email_verification - ends


                    if (checkOtpVerification() == 'Enabled') {
                        if (checkAppSmsEnvironment()) {
                           try {
                                $this->sendOtpCode($user);
                                $this->helper->one_time_message('success', __('OTP code sent to your Phone.'));
                                DB::commit();
                                return redirect('otp-verification');

                           } catch (Exception $e) {

                               DB::rollBack();
                               $this->helper->one_time_message('error', $e->getMessage());
                               return redirect('/login');
                           }
                        }
                    }

                    DB::commit();

                    $this->helper->one_time_message('success', __('Registration Successful!'));
                    return redirect('/login');
                }
                catch (Exception $e)
                {
                    DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('/login');
                }
            }
        }
    }

    protected function sendOtpCode($user)
    {
        $otpCode = $user->otp_code;
        //sending otp verification code to mobile and email
        $otpMeaasge="Your verification code is: ".$otpCode;
        if(!empty($user->formattedPhone)){
            $otpVerified=sendSMS($user->formattedPhone,$otpMeaasge);
        }

        Session::put('registerUserId',$user->id);
        //Session::put('registerUserEmail',$user->email);
        Session::put('registerUserFormattedPhone',$user->formattedPhone);
    }


    protected function saveUserQrCode($user) {
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode)) {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone)) {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . str_random(6));
            } else {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . str_random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }
    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser))
        {
            if (!$verifyUser->user->user_detail->email_verification)
            {
                $verifyUser->user->user_detail->email_verification = 1;
                $verifyUser->user->user_detail->save();
                $status = __("Your account is verified. You can now login.");
            }
            else
            {
                $status = __("Your account is already verified. You can now login.");
            }
        }
        else
        {
            return redirect('/login')->with('warning', __("Sorry your email cannot be identified."));
        }
        return redirect('/login')->with('status', $status);
    }

    public function checkUserRegistrationEmail(Request $request)
    {
        $email = User::where(['email' => $request->email])->exists();
        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function registerDuplicatePhoneNumberCheck(Request $request)
    {
        if (isset($request->carrierCode))
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->first(['phone', 'carrierCode']);
        }
        else
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone)])->first(['phone', 'carrierCode']);
        }

        if (!empty($user->phone) && !empty($user->carrierCode))
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The phone number is Available!";
        }
        return json_encode($data);
    }

    protected function saveReferralCode($user_id)
    {
        $referralCode          = new ReferralCode();
        $referralCode->user_id = $user_id;
        $referralCode->code    = str_random(30);
        $referralCode->status  = 'Active';
        $referralCode->save();
    }

    protected function saveReferralWithCacheCheck($user_id)
    {
        if (Cache::has('referralData-' . $this->referralIdentifier))
        {
            $referralData = Cache::get('referralData-' . $this->referralIdentifier);
            $checkReferral = Referral::where(['referred_by' => $referralData['referred-by'], 'referred_to' => $user_id])->first(['id', 'referred_by', 'referred_to']);

            if (empty($checkReferral) && Cache::has('referralFlag'))
            {
                $referral              = new Referral();
                $referral->referred_by = $referralData['referred-by'];
                $referral->referred_to = $user_id;
                $referral->save();

                Cache::forget('referralFlag');
            }
        }
    }
}
