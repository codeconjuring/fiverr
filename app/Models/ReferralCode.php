<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $table = 'referral_codes';

    protected $fillable = ['user_id', 'code', 'status', 'valid_from', 'valid_to'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referral_award()
    {
        return $this->hasOne(ReferralAward::class, 'referral_code_id');
    }
}
