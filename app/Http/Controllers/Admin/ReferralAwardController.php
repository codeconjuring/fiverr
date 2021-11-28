<?php

namespace App\Http\Controllers\Admin;

use App;
use App\DataTables\Admin\ReferralAwardsDataTable;
use App\Http\Controllers\Controller;
use App\Models\ReferralAward;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReferralAwardController extends Controller
{
    protected $helper;
    protected $email;
    protected $transfer;

    public function __construct()
    {
        $this->referralAward = new ReferralAward();
    }

    public function index(ReferralAwardsDataTable $dataTable)
    {
        $data['menu'] = 'referral-awards';

        $referralAwardsLevels = $this->referralAward->with(['referral_level:id,currency_id'])->groupBy('referral_level_id')->get(['referral_level_id']);
        if ($referralAwardsLevels->count() > 0)
        {
            foreach ($referralAwardsLevels as $referralAwardsLevel)
            {
                $data['referralAwardsLevels'] = $referralAwardsLevel->referral_level()->groupBy('currency_id')->get(['currency_id']);
            }
        }

        if (isset($_GET['btn']))
        {
            // $data['currency']           = $_GET['currency'];
            $data['currency']           = isset($_GET['currency']) ? $_GET['currency'] : null; //
            $data['user']               = $user               = $_GET['user_id'];
            $data['getAwardedUserName'] = $getAwardedUserName = $this->referralAward->getAwardedUserName($user);
            if (empty($_GET['from']))
            {
                $data['from'] = null;
                $data['to']   = null;
            }
            else
            {
                $data['from'] = $_GET['from'];
                $data['to']   = $_GET['to'];
            }
        }
        else
        {
            $data['from']     = null;
            $data['to']       = null;
            $data['currency'] = 'all';
            $data['user']     = null;
        }
        return $dataTable->render('admin.referral_awards.list', $data);
    }

    public function referralAwardCsv()
    {
        $from     = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to       = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;
        $user     = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['referralAwards'] = $referralAwards = $this->referralAward->getReferralAwardsList($from, $to, $currency, $user)->get();
        $datas = [];
        if (!empty($referralAwards))
        {
            foreach ($referralAwards as $key => $value)
            {
                $datas[$key]['Date']           = dateFormat($value->created_at);
                $datas[$key]['Awarded User']   = $value->awarded_user->first_name . ' ' . $value->awarded_user->last_name;
                $datas[$key]['Currency']       = $value->referral_level->currency->code;
                $datas[$key]['Referral Level'] = $value->referral_level->level;
                $datas[$key]['Awarded Amount'] = formatNumber($value->awarded_amount);
                $datas[$key]['Referral Code']  = $value->referral_code->code;
            }
        }
        else
        {
            $datas[0]['Date']           = '';
            $datas[0]['Awarded User']   = '';
            $datas[0]['Currency']       = '';
            $datas[0]['Referral Level'] = '';
            $datas[0]['Awarded Amount'] = '';
            $datas[0]['Referral Code']  = '';
        }
        return Excel::create('referral_awards_list_' . time() . '', function ($excel) use ($datas)
        {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas)
            {
                $sheet->cells('A1:F1', function ($cells)
                {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function referralAwardPdf()
    {
        $data['company_logo'] = getCompanyLogoWithoutSession();
        $from                 = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to                   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $currency             = isset($_GET['currency']) ? $_GET['currency'] : null;
        $user                 = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['referralAwards'] = $referralAwards = $this->referralAward->getReferralAwardsList($from, $to, $currency, $user)->get();
        if (isset($from) && isset($to))
        {
            $data['date_range'] = $from . ' To ' . $to;
        }
        else
        {
            $data['date_range'] = 'N/A';
        }
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->WriteHTML(view('admin.referral_awards.referral_awards_report_pdf', $data));
        $mpdf->Output('referral_awards_report_' . time() . '.pdf', 'D');
    }

    public function referralAwardUserSearch(Request $request)
    {
        $search = $request->search;
        $user   = $this->referralAward->getReferralAwardsUsersResponse($search);

        $res = [
            'status' => 'fail',
        ];
        if (count($user) > 0)
        {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }
}

