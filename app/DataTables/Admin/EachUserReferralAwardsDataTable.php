<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\ReferralAward;
use Yajra\DataTables\Services\DataTable;

class EachUserReferralAwardsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
        ->eloquent($this->query())
        ->editColumn('created_at', function ($referralAward)
        {
            return dateFormat($referralAward->created_at);
        })
        ->addColumn('currency_id', function ($referralAward)
        {
            return $referralAward->referral_level->currency->code;
        })
        ->addColumn('level', function ($referralAward)
        {
            return $referralAward->referral_level->level;
        })
        ->addColumn('referred_to', function ($referralAward)
        {
            $referredToUser        = isset($referralAward->referredTo) ? $referralAward->referredTo->first_name . ' ' . $referralAward->referredTo->last_name : "-";
            $referredToUserWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url('admin/users/edit/' . $referralAward->referredTo->id) . '">' . $referredToUser . '</a>' : $referredToUser;
            return $referredToUserWithLink;
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
        ->rawColumns(['awarded_amount','referred_to'])
        ->make(true);
    }

    public function query()
    {
        if (isset($_GET['btn']))
        {
            $currency = $_GET['currency'];
            $user     = $_GET['user_id']; //passed from Controller
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
            $user     = $this->user_id; //passed from controller to query() in dataTable
            $query    = (new ReferralAward())->getReferralAwardsList($from, $to, $currency, $user);
        }
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'referral_awards.id', 'title' => 'ID', 'searchable' => false, 'visible' => false]) //hidden
            ->addColumn(['data' => 'referred_to', 'name' => 'referredTo.last_name', 'title' => 'Referred To', 'visible' => false]) //hidden
            ->addColumn(['data' => 'created_at', 'name' => 'referral_awards.created_at', 'title' => 'Date'])
            ->addColumn(['data' => 'currency_id', 'name' => 'referral_level.currency.code', 'title' => 'Currency']) //custom
            ->addColumn(['data' => 'level', 'name' => 'referral_level.level', 'title' => 'Referral Level'])         //custom
            ->addColumn(['data' => 'referred_to', 'name' => 'referredTo.first_name', 'title' => 'Referred To']) //new - custom
            ->addColumn(['data' => 'awarded_amount', 'name' => 'referral_awards.awarded_amount', 'title' => 'Awarded Amount'])
            // ->addColumn(['data' => 'code', 'name' => 'referral_code.code', 'title' => 'Referral Code'])
            ->parameters($this->getBuilderParameters());
    }
}
