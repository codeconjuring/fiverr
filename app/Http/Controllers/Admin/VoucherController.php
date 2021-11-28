<?php

namespace App\Http\Controllers\Admin;

use App\Models\Wallet;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\DataTables\Admin\VouchersDataTable;
use App\Http\Controllers\Users\EmailController;

class VoucherController extends Controller
{
    protected $helper;
    protected $email;
    protected $voucher;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->email   = new EmailController();
        $this->voucher = new Voucher();
    }

    public function index(VouchersDataTable $dataTable)
    {
        $data['menu'] = 'vouchers';

        $data['vouchers_status']   = $this->voucher->select('status')->groupBy('status')->get();
        $data['vouchers_currency'] = $this->voucher->select('currency_id')->groupBy('currency_id')->get();

        if (isset($_GET['btn'])) {
            $data['status']   = $_GET['status'];
            $data['currency'] = $_GET['currency'];
            $data['user']     = $user     = $_GET['user_id'];

            $data['getName'] = $this->voucher->getVouchersUserName($user);

            if (empty($_GET['from'])) {
                $data['from'] = null;
                $data['to']   = null;
            } else {
                $data['from'] = $_GET['from'];
                $data['to']   = $_GET['to'];
            }
        } else {
            $data['from'] = null;
            $data['to']   = null;

            $data['status']   = 'all';
            $data['currency'] = 'all';
            $data['user']     = null;
        }
        return $dataTable->render('admin.voucher.list', $data);
    }

    public function voucherCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['vouchers'] = $vouchers = $this->voucher->getVouchersListForCsvPdf($from, $to, $status, $currency, $user);

        $datas = [];
        if (!empty($vouchers)) {
            foreach ($vouchers as $key => $value) {
                $datas[$key]['Date'] = dateFormat($value->created_at);

                $datas[$key]['User'] = isset($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";

                $datas[$key]['Code'] = $value->code;

                $datas[$key]['Amount'] = formatNumber($value->amount);

                $datas[$key]['Currency'] = $value->currency->code;

                $datas[$key]['Redeemed'] = $value->redeemed;

                $datas[$key]['Status'] = ($value->status == 'Blocked') ? 'Cancelled' : $value->status;
            }
        } else {
            $datas[0]['Date']     = '';
            $datas[0]['User']     = '';
            $datas[0]['Amount']   = '';
            $datas[0]['Code']     = '';
            $datas[0]['Currency'] = '';
            $datas[0]['Redeemed'] = '';
            $datas[0]['Status']   = '';
        }

        return Excel::create('vouchers_list_' . time() . '', function ($excel) use ($datas) {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas) {
                $sheet->cells('A1:F1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function voucherPdf()
    {
        $data['company_logo'] = Session::get('company_logo');

        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $currency = isset($_GET['currency']) ? $_GET['currency'] : null;

        $user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        $data['vouchers'] = $this->voucher->getVouchersListForCsvPdf($from, $to, $status, $currency, $user);

        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else {
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

        $mpdf->WriteHTML(view('admin.voucher.vouchers_report_pdf', $data));

        $mpdf->Output('vouchers_report_' . time() . '.pdf', 'D');
    }

    public function vouchersUserSearch(Request $request)
    {
        $search = $request->search;
        $user   = $this->voucher->getVouchersUsersResponse($search);

        $res = [
            'status' => 'fail',
        ];
        if (count( $user ) > 0) {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }

    public function edit($id)
    {
        $data['menu']    = 'vouchers';
        $data['voucher'] = $voucher = Voucher::find($id);

        $data['transaction'] = $transaction = Transaction::select('transaction_type_id', 'status', 'transaction_reference_id', 'percentage', 'charge_percentage', 'charge_fixed', 'uuid')
            ->where(['transaction_reference_id' => $voucher->id, 'status' => $voucher->status])
            ->whereIn('transaction_type_id', [Voucher_Created, Voucher_Activated])
            ->orderBy('id', 'desc')
            ->first();

        return view('admin.voucher.edit', $data);
    }

    public function update(Request $request)
    {
        if ($request->transaction_type == 'Voucher_Created') {
            if ($request->status == 'Blocked') {
                if ($request->transaction_status == 'Success') {
                    $voucher         = Voucher::find($request->id);
                    $voucher->status = $request->status;
                    $voucher->save();

                    Transaction::where([
                        'user_id'                  => $voucher->user_id,
                        'currency_id'              => $request->currency_id,
                        'transaction_reference_id' => $request->transaction_reference_id,
                        'transaction_type_id'      => $request->transaction_type_id,
                    ])->update([
                        'status' => $request->status,
                    ]);

                    // creator_wallet entry update
                    $creator_wallet = Wallet::where([
                        'user_id'     => $voucher->user_id,
                        'currency_id' => $request->currency_id,
                    ])->select('balance')->first();

                    Wallet::where([
                        'user_id'     => $voucher->user_id,
                        'currency_id' => $request->currency_id,
                    ])->update([
                        'balance' => $creator_wallet->balance + trim($request->amount, '-'),
                    ]);
                    $this->helper->one_time_message('success', 'Voucher Updated Successfully!');
                }
            }
        }    
        return redirect('admin/vouchers');
    }
}
