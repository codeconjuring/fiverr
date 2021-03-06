<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Repositories\CryptoCurrencyRepository;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTransactionController extends Controller
{
    /**
     * The CryptoCurrency repository instance.
     *
     * @var CryptoCurrencyRepository
     */
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->cryptoCurrency = new CryptoCurrencyRepository();
    }

    public function index()
    {

        $transaction      = new Transaction();
        $data['menu']     = 'transactions';
        $data['sub_menu'] = 'transactions';

        $data['from']   = $from   = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']     = $to     = isset(request()->to) ? setDateForDb(request()->to) : null;
        $data['status'] = $status = isset(request()->status) ? request()->status : 'all';
        $data['type']   = $type   = isset(request()->type) ? request()->type : 'all';
        $data['wallet'] = $wallet = isset(request()->wallet) ? request()->wallet : 'all';

        $data['transactions'] = $transaction->getTransactions($from, $to, $type, $wallet, $status);

        $data['wallets'] = Wallet::with(['currency:id,code'])->where(['user_id' => Auth::user()->id])->get(['currency_id']);
        if ($type == Deposit || $type == Withdrawal || $type == 'all') {
            $data['type'] = $type;
        } else {
            switch ($type) {
                case 'sent':
                    $data['type'] = 'sent';
                    break;

                case 'request':
                    $data['type'] = 'request';
                    break;

                case 'received':
                    $data['type'] = 'received';
                    break;

                case 'exchange':
                    $data['type'] = 'exchange';
                    break;

                case 'voucher':
                    $data['type'] = 'voucher';
                    break;

                case 'crypto_sent':
                    $data['type'] = 'crypto_sent';
                    break;

                case 'crypto_received':
                    $data['type'] = 'crypto_received';
                    break;

                case 'referral_award':
                    $data['type'] = 'referral_award';
                    break;
            }
        }
        return view('user_dashboard.transactions.new_index', $data);
    }

    public function getTransaction(Request $request)
    {

        $data['status'] = 0;

        $transaction = Transaction::with([
            'payment_method:id,name',
            'transaction_type:id,name',
            'currency:id,code,symbol',
            'transfer:id,sender_id,receiver_id',
            'transfer.sender:id,first_name,last_name',
            'end_user:id,first_name,last_name,email,formattedPhone',
            'merchant:id,business_name',
            'cryptoapi_log:id,object_id,payload,confirmations',
        ])->find($request->id);

        // Get crypto api log details for Crypto_Sent & Crypto_Received (via custom relationship)
        if (!empty($transaction->cryptoapi_log)) {
            $getCryptoDetails = $this->cryptoCurrency->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0) {
                // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                if (isset($getCryptoDetails['senderAddress'])) {
                    $senderAddress = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress'])) {
                    $receiverAddress = $getCryptoDetails['receiverAddress'];
                }
                $confirmations = $getCryptoDetails['confirmations'];
            }
        }

        if ($transaction->count() > 0) {
            switch ($transaction->transaction_type_id) {
                case Deposit:
                    if ($transaction->payment_method->name == 'Mts') {
                        $pm = getCompanyName();
                    } else {
                        $pm = $transaction->payment_method->name;
                    }
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.deposit.deposited-to') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->currency->code . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.deposit.payment-method') . "</p>" .
                    "<div  class='text-black font-w500'>" . $pm . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.deposit.deposited-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</div>" . //r2
                    "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total - $transaction->subtotal)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('deposit-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('deposit-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Withdrawal:
                    if ($transaction->payment_method->name == 'Mts') {
                        $pm = getCompanyName();
                    } else {
                        $pm = $transaction->payment_method->name;
                    }
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.withdrawal.withdrawan-with') . "</p>" .
                    "<div  class='text-black font-w500'>" . $pm . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.withdrawal.withdrawan-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('withdrawal-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('withdrawal-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Transferred:

                    $receiverEmailorPhone = '';
                    $receiverName         = '';
                    if (isset($transaction->email) && ($transaction->user_type == 'registered')) {
                        $receiverEmailorPhone = $transaction->end_user->email;
                        $receiverName         = $transaction->transfer->receiver->first_name . ' ' . $transaction->transfer->receiver->last_name;
                    } else if (isset($transaction->phone) && ($transaction->user_type == 'registered')) {
                        $receiverEmailorPhone = $transaction->end_user->formattedPhone;
                        $receiverName         = $transaction->transfer->receiver->first_name . ' ' . $transaction->transfer->receiver->last_name;

                    }

                    if ($transaction->user_type == 'unregistered') {
                        if (!empty($transaction->email)) {
                            $unregisteredEmailOrPhone = "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.transferred-to') . "</p>" .
                            "<div>" . $transaction->email . "</div><br>";
                        } else {
                            $unregisteredEmailOrPhone = "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.transferred-to') . "</p>" .
                            "<div>" . $transaction->phone . "</div><br>";
                        }
                    } else {
                        $unregisteredEmailOrPhone = "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.transferred-to') . "</p>" .
                            "<div class='text-black font-w500'>" . $receiverName . " <strong>(" . $receiverEmailorPhone . ")</strong>" . "</div>" .
                            "</div>";
                    }

                    $data['html'] = "<div class='form-group trans_details'>" .
                    $unregisteredEmailOrPhone .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left'>" . __('message.dashboard.left-table.transferred.transferred-amount') . "</div>" .
                    "<div class='right'>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left'>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right'>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left'><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                        "<div  class='act-detail-font'>" . $transaction->note . "</div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('moneytransfer/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left'><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                        "<div  class='act-detail-font'>" . $transaction->note . "</div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('moneytransfer/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Received:

                    $senderEmailOrPhone = '';
                    if (isset($transaction->email)) {
                        $senderEmailOrPhone = $transaction->end_user->email;
                    } else if (isset($transaction->phone)) {
                        $senderEmailOrPhone = $transaction->end_user->formattedPhone;
                    }

                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.received.received-from') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->transfer->sender->first_name . ' ' . $transaction->transfer->sender->last_name . " <strong>(" . $senderEmailOrPhone . ")</strong>" . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.received.received-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                    "<div class='act-detail-font'>" . $transaction->note . "</div>" .
                    "</div>" .

                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('moneytransfer/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Exchange_From:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.exchange-from.from-wallet') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->currency->code . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.exchange-from.exchange-from-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('transactions/exchangeTransactionPrintPdf/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('transactions/exchangeTransactionPrintPdf/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Exchange_To:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.exchange-to.to-wallet') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->currency->code . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.exchange-from.exchange-from-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('transactions/exchangeTransactionPrintPdf/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Voucher_Created:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.voucher-created.voucher-code') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->voucher->code . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.voucher-created.voucher-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('voucher/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('voucher/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Voucher_Activated:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.voucher-created.voucher-code') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->voucher->code . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . "Voucher Created By" . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.voucher-created.voucher-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('voucher/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Request_From:
                    $conditionForRequestToPhoneAndEMail = !empty($transaction->email) ? $transaction->email : $transaction->phone;
                    $cancel_btn                         = '';
                    if ($transaction->status == 'Pending') {
                        $cancel_btn = "<button class='btn btn-secondary btn-sm trxnreqfrom' data-notificationType='{$conditionForRequestToPhoneAndEMail}' data='{$transaction->id}' data-type='{$transaction->transaction_type_id}' id='btn_{$transaction->id}'>" . __('message.form.cancel') . "</button>";
                    }
                    if ($transaction->user_type == 'registered') {
                        $data['html'] = "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.form.request_to') . "</p>" .
                        "<div class='text-black font-w500'>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . '<strong> (' . (!empty($transaction->email) ? $transaction->email : $transaction->phone) . ')</strong>' . "</div>" .
                            "</div>";
                    } else {
                        if (!empty($transaction->email)) {
                            $data['html'] = "<div class='form-group trans_details'>" .
                            "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.form.request_to') . "</p>" .
                            "<div class='text-black font-w500'>" . $transaction->email . "</div>" .
                                "</div>";
                        } else {
                            $data['html'] = "<div class='form-group trans_details'>" .
                            "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.form.request_to') . "</p>" .
                            "<div class='text-black font-w500'>" . $transaction->phone . "</div>" .
                                "</div>";
                        }

                    }
                    $data['html'] .= "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.send-request.request.confirmation.requested-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                    "<div  class='act-detail-font'>" . $transaction->note . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('request-payment/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" . $cancel_btn .
                        "</div>";
                    break;

                case Request_To:
                    $conditionForRequestToPhoneAndEMail = !empty($transaction->email) ? $transaction->email : $transaction->phone;
                    $twoButtons                         = '';
                    //
                    if ($transaction->status == 'Pending') {
                        $twoButtons = "<button class='btn btn-secondary btn-sm trxn' data-notificationType='{$conditionForRequestToPhoneAndEMail}' data='{$transaction->id}' data-type='{$transaction->transaction_type_id}'
                        id='btn_{$transaction->id}'>" . __('message.form.cancel') . "</button>";

                        $twoButtons .= " <button class='btn btn-secondary btn-sm trxn_accept' data-rel='" . $transaction->transaction_reference_id . "' data='" . $transaction->id . "' id='acceptbtn_" . $transaction->id . "'> " . __('message.dashboard.left-table.request-to.accept') . " </button>";
                    }
                    //
                    if ($transaction->user_type == 'registered') {
                        $data['html'] = "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.form.request_from') . "</p>" .
                        "<div class='text-black font-w500'>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . ' <strong>(' . (!empty($transaction->email) ? $transaction->end_user->email : $transaction->end_user->formattedPhone) . ')</strong>' . "</div>" .
                            "</div>";
                    } else {
                        $data['html'] = "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.email') . "</p>" .
                        "<div class='text-black font-w500'>" . $transaction->email . "</div>";
                    }
                    $data['html'] .= "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.send-request.request.confirmation.requested-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                        "<div class='act-detail-font'>" . $transaction->note . "</div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('request-payment/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" . $twoButtons .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transferred.note') . "</p>" .
                        "<div class='act-detail-font'>" . $transaction->note . "</div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('request-payment/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" . $twoButtons .
                            "</div>";
                    }
                    break;

                case Payment_Sent:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.merchant.payment.merchant') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->merchant->business_name . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.payment-Sent.payment-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group'>" .
                    "<a href='" . url('transactions/merchant-payment-print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Payment_Received:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.payment-Sent.payment-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                        "<div class='clearfix'></div>";
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0) {
                        $data['html'] .= "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                        "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</div>" .
                        "<div class='clearfix'></div>" .
                        "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('transactions/merchant-payment-print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    } else {
                        $data['html'] .= "<hr/>" .
                        "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                        "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                        "<div class='clearfix'></div>" .
                        "</div>" .
                        "<div class='form-group trans_details'>" .
                        "<a href='" . url('transactions/merchant-payment-print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                            "</div>";
                    }
                    break;

                case Crypto_Sent:
                    $data['html'] = "";

                    if (isset($receiverAddress)) {
                        $data['html'] .= "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.crypto.transactions.receiver-address') . "</p>" .
                            "<div>" . $receiverAddress . "</div>" .
                            "</div>" .
                            "<div class='form-group trans_details'>";
                    }

                    if (isset($confirmations)) {
                        $data['html'] .= "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.crypto.transactions.confirmations') . "</p>" .
                            "<div>" . $confirmations . "</div>" .
                            "</div>" .
                            "<div class='form-group trans_details'>";
                    }

                    $data['html'] .= "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.crypto.send.confirm.sent-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, $transaction->subtotal) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.crypto.send.confirm.network-fee') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, $transaction->charge_fixed) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, $transaction->total) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group'>" .
                    "<a href='" . url('transactions/crypto-sent-received-print/' . encrypt($transaction->id)) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Crypto_Received:
                    $data['html'] = "";

                    // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                    if (isset($senderAddress)) {
                        $data['html'] .= "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.crypto.transactions.sender-address') . "</p>" .
                            "<div>" . $senderAddress . "</div>" .
                            "</div>" .
                            "<div class='form-group trans_details'>";
                    }

                    if (isset($confirmations)) {
                        $data['html'] .= "<div class='form-group trans_details'>" .
                        "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.crypto.transactions.confirmations') . "</p>" .
                            "<div>" . $confirmations . "</div>" .
                            "</div>" .
                            "<div class='form-group trans_details'>";
                    }

                    $data['html'] .= "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.received.received-amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, $transaction->subtotal) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, $transaction->subtotal) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group'>" .
                    "<a href='" . url('transactions/crypto-sent-received-print/' . encrypt($transaction->id)) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Order_Product:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('Paid To') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .

                    "<div class='left '>" . 'Order Payment Amount' . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('order-product/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Order_Received:
                    $data['html'] = "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('Paid By') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('Order Received Amount') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . __('message.dashboard.left-table.fee') . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('order-received/print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                case Referral_Award:
                    $data['html'] = "<div class='form-group trans_details'>" .

                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.transaction-id') . "</p>" .
                    "<div class='text-black font-w500'>" . $transaction->uuid . "</div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<p class='exampleInputEmail1 fs-12 mb-2'>" . __('message.dashboard.left-table.details') . "</p>" .
                    "<div class='clearfix'></div>" .
                    "<div class='left '>" . 'Referral Award Amount' . "</div>" .
                    "<div class='right '>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</div>" .
                    "<div class='clearfix'></div>" .
                    "<hr/>" .
                    "<div class='left '><strong>" . __('message.dashboard.left-table.total') . "</strong></div>" .
                    "<div class='right '><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></div>" .
                    "<div class='clearfix'></div>" .
                    "</div>" .
                    "<div class='form-group trans_details'>" .
                    "<a href='" . url('transactions/referral-award-print/' . $transaction->id) . "' target='_blank' class='btn btn-secondary btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                        "</div>";
                    break;

                default:
                    $data['html'] = '';
                    break;
            }
        }
        return response()->json($data);
        // return json_encode($data);
    }

    /**
     * Generate pdf print for exchangeTransaction entries
     */
    public function exchangeTransactionPrintPdf($id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        // $data['transaction'] = $transaction = Transaction::where(['id' => $id])->first();
        $data['transaction'] = $transaction = Transaction::with([
            'currency:id,code,symbol',
        ])->where(['id' => $id])->first();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.exchangeTransactionPrintPdf', $data));
        $mpdf->Output('exchange_' . time() . '.pdf', 'I'); // this will output data
    }

    /**
     * Generate pdf print for merchant payment entries
     */
    public function merchantPaymentTransactionPrintPdf($id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transaction'] = $transaction = Transaction::with([
            'merchant:id,business_name',
            'currency:id,symbol',
        ])->where(['id' => $id])->first();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.merchantPaymentTransactionPrintPdf', $data));
        $mpdf->Output('merchant-payment_' . time() . '.pdf', 'I'); // this will output data
    }

    /**
     * Generate pdf print for crypto sent & received
     */
    public function cryptoSentReceivedTransactionPrintPdf($id)
    {
        $id                  = decrypt($id);
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transaction'] = $transaction = Transaction::with(['currency:id,symbol', 'cryptoapi_log:id,object_id,payload,confirmations'])->where(['id' => $id])->first();

        // Get crypto api log details for Crypto_Sent & Crypto_Received (via custom relationship)
        if (!empty($transaction->cryptoapi_log)) {
            $getCryptoDetails = $this->cryptoCurrency->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0) {
                // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                if (isset($getCryptoDetails['senderAddress'])) {
                    $data['senderAddress'] = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress'])) {
                    $data['receiverAddress'] = $getCryptoDetails['receiverAddress'];
                }
                $data['confirmations'] = $getCryptoDetails['confirmations'];
            }
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
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.crypto_sent_received', $data));
        $mpdf->Output('crypto-sent-received_' . time() . '.pdf', 'I'); // this will output data
    }

    public function referralAwardTransactionPrintPdf($id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transaction'] = $transaction = Transaction::with(['currency:id,symbol'])
            ->where(['id' => $id])->first(['uuid', 'created_at', 'status', 'currency_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);
        // dd($transaction);

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.referralAwrdPrintPdf', $data));
        $mpdf->Output('referral-award_' . time() . '.pdf', 'I'); // this will output data
    }
}
