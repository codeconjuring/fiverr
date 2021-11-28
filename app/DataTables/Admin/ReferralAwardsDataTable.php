<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\ReferralAward;
use Yajra\DataTables\Services\DataTable;

class ReferralAwardsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($referralAward)
            {
                return dateFormat($referralAward->created_at);
            })
            ->addColumn('awarded_user', function ($referralAward)
            {
                $awarded_user        = isset($referralAward->awarded_user) ? $referralAward->awarded_user->first_name . ' ' . $referralAward->awarded_user->last_name : "-";
                $awardedUserWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url('admin/users/edit/' . $referralAward->awarded_user->id) . '">' . $awarded_user . '</a>' : $awarded_user;
                return $awardedUserWithLink;
            })
            ->addColumn('referred_to', function ($referralAward)
            {
                $referredToUser        = isset($referralAward->referredTo) ? $referralAward->referredTo->first_name . ' ' . $referralAward->referredTo->last_name : "-";
                $referredToUserWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url('admin/users/edit/' . $referralAward->referredTo->id) . '">' . $referredToUser . '</a>' : $referredToUser;
                return $referredToUserWithLink;
            })
            ->addColumn('currency_id', function ($referralAward)
            {
                return $referralAward->referral_level->currency->code;
            })
            ->addColumn('level', function ($referralAward)
            {
                return $referralAward->referral_level->level;
            })
            ->editColumn('awarded_amount', function ($referralAward)
            {
                if ($referralAward->awarded_amount > 0)
                {
                    $awarded_amount = '<td><span class="text-green">+' . formatNumber($referralAward->awarded_amount) . '</span></td>';
                }
                return $awarded_amount;
            })
            // ->addColumn('code', function ($referralAward)
            // {
            //     return $referralAward->referral_code->code;
            // })
            ->rawColumns(['awarded_user','awarded_amount','referred_to'])
            ->make(true);
    }

    public function query()
    {
        if (isset($_GET['btn']))
        {
            // $currency = $_GET['currency'];
            $currency = isset($_GET['currency']) ? $_GET['currency'] : null; //
            $user     = $_GET['user_id'];
            if (empty($_GET['from']))
            {
                $from  = null;
                $to    = null;
                $query = (new ReferralAward())->getReferralAwardsList($from, $to, $currency, $user);
            }
            else
            {
                $from  = setDateForDb($_GET['from']);
                $to    = setDateForDb($_GET['to']);
                $query = (new ReferralAward())->getReferralAwardsList($from, $to, $currency, $user);
            }
        }
        else
        {
            $from     = null;
            $to       = null;
            $currency = 'all';
            $user     = null;
            $query    = (new ReferralAward())->getReferralAwardsList($from, $to, $currency, $user);
            // dd($query);
        }
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'referral_awards.id', 'title' => 'ID', 'searchable' => false, 'visible' => false]) //hidden
            ->addColumn(['data' => 'awarded_user', 'name' => 'awarded_user.last_name', 'title' => 'Last Name', 'visible' => false])   //hidden
            ->addColumn(['data' => 'referred_to', 'name' => 'referredTo.last_name', 'title' => 'Referred To', 'visible' => false]) //hidden

            ->addColumn(['data' => 'created_at', 'name' => 'referral_awards.created_at', 'title' => 'Date'])
            ->addColumn(['data' => 'currency_id', 'name' => 'referral_level.currency.code', 'title' => 'Currency']) //custom
            ->addColumn(['data' => 'level', 'name' => 'referral_level.level', 'title' => 'Referral Level'])         //custom
            // ->addColumn(['data' => 'code', 'name' => 'referral_code.code', 'title' => 'Referral Code'])
            ->addColumn(['data' => 'awarded_user', 'name' => 'awarded_user.first_name', 'title' => 'Awarded User']) //custom

            ->addColumn(['data' => 'referred_to', 'name' => 'referredTo.first_name', 'title' => 'Referred To']) //new - custom

            ->addColumn(['data' => 'awarded_amount', 'name' => 'referral_awards.awarded_amount', 'title' => 'Awarded Amount'])
            ->parameters($this->getBuilderParameters());
    }
}
