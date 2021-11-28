<?php

namespace App\Models;

use App\Traits\Excludable;
use Illuminate\Database\Eloquent\Model;

class ReferralAward extends Model
{
    use Excludable;

    protected $table = 'referral_awards';

    protected $fillable = ['referral_id', 'referral_level_id', 'referral_code_id', 'awarded_user_id', 'referred_to', 'awarded_amount'];

    public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function referral_level()
    {
        return $this->belongsTo(ReferralLevel::class, 'referral_level_id');
    }

    public function referral_code()
    {
        return $this->belongsTo(ReferralCode::class, 'referral_code_id');
    }

    public function awarded_user()
    {
        return $this->belongsTo(User::class, 'awarded_user_id');
    }

    public function referredTo()
    {
        return $this->belongsTo(User::class, 'referred_to');
    }

    /**
     * [get users firstname and lastname for filtering]
     * @param  [integer] $user      [id]
     * @return [string]  [firstname and lastname]
     */
    public function getAwardedUserName($user)
    {
        return $this->leftJoin('users', 'users.id', '=', 'referral_awards.awarded_user_id')
            ->where(['referral_awards.awarded_user_id' => $user])
            ->select('users.id', 'users.first_name', 'users.last_name')
            ->first();
    }

    /**
     * [ajax response for search results]
     * @param  [string] $search   [query string]
     * @return [string] [distinct firstname and lastname]
     */
    public function getReferralAwardsUsersResponse($search)
    {
        return $this->leftJoin('users', 'users.id', '=', 'referral_awards.awarded_user_id')
            ->where('users.first_name', 'LIKE', '%' . $search . '%')
            ->orWhere('users.last_name', 'LIKE', '%' . $search . '%')
            ->distinct('users.first_name')
            ->select('users.first_name', 'users.last_name', 'referral_awards.awarded_user_id')
            ->get();
    }

    /**
     * [Referral Awards Filtering Results]
     * @param  [null/date] $from     [start date]
     * @param  [null/date] $to       [end date]
     * @param  [string]    $currency [currency]
     * @param  [null/id]   $user     [User ID]
     * @return [void]      [All Query Results]
     */
    public function getReferralAwardsList($from, $to, $currency, $user)
    {

        // dd([$from, $to, $currency, $user]);

        $referralAwards = ReferralAward::with([
            'referral_level:id,currency_id,level',
            'referral_level.currency:id,code,symbol',
            'referral_code:id,code',
            'awarded_user:id,first_name,last_name',
            'referredTo:id,first_name,last_name',
        ]);

        //currency
        if (!empty($currency) && $currency != 'all')
        {
            $referralAwards->whereHas('referral_level', function ($q) use ($currency)
            {
                $q->where('currency_id', $currency);
            });
        }

        //user
        if (!empty($user))
        {
            $referralAwards->where(['awarded_user_id' => $user]);
        }

        //date_range
        if (empty($from) || empty($to))
        {
            $date_range = null;
        }
        else if (empty($from))
        {
            $date_range = null;
        }
        else if (empty($to))
        {
            $date_range = null;
        }
        else
        {
            $date_range = 'Available';
        }
        if (!empty($date_range))
        {
            $referralAwards->where(function ($q) use ($from, $to)
            {
                $q->whereDate('referral_awards.created_at', '>=', $from)->whereDate('referral_awards.created_at', '<=', $to);
            });
        }
        // $referralAwards->exclude(['referred_to']); //used - Excludable Trait

        // ->orderBy('referral_awards.id', 'desc')
        // ->get();

        return $referralAwards;
    }
}
