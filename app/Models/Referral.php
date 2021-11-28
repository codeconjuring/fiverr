<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referrals';

    protected $fillable = ['referred_by', 'referred_to'];

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referredTo()
    {
        return $this->belongsTo(User::class, 'referred_to');
    }

    //new
    public function referral_awards()
    {
        return $this->hasMany(ReferralAward::class, 'referral_id');
    }
}
