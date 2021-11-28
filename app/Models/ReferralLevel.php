<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralLevel extends Model
{
    protected $table = 'referral_levels';

    protected $fillable = ['currency_id', 'level', 'amount', 'priority', 'status'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function referral_award()
    {
        return $this->hasOne(ReferralAward::class, 'referral_level_id');
    }
}
