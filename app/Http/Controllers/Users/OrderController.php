<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Transaction;

class OrderController extends Controller
{
    public function ownerOrders() 
    {
        $data['menu']         = 'shop';
        $data['ownerOrders']  = $ownerorders = Order::where(['user_id' => auth()->user()->id ])->orderBy('created_at','desc')->paginate(10);

        return view('user_dashboard.shop.order.owner_order', $data);
    }
    
    public function customersOrders()
    {
        $data['menu']            = 'shop';

        $data['customersOrders'] = $customersOrders = Order::select('orders.*')
                                ->leftjoin('stores as s', 's.id', '=', 'orders.store_id')
                                ->where(['s.user_id' => auth()->user()->id])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('user_dashboard.shop.order.customer_order', $data);
    }

    //Send Money - Generate pdf for print
    public function orderProductPrintPdf($trans_id)
    {
        $data['companyInfo']        = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transactionDetails'] = Transaction::with(['end_user:id,first_name,last_name', 'currency:id,symbol,code'])
            ->where(['id' => $trans_id])
            ->first(['transaction_type_id', 'end_user_id', 'currency_id', 'uuid', 'created_at', 'status', 'subtotal', 'charge_percentage', 'charge_fixed', 'total', 'note']);
        // dd($data['transactionDetails']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'                 => 'utf-8',
            'format'               => 'A3',
            'orientation'          => 'P',
            'shrink_tables_to_fit' => 0,
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.shop.order.orderProductPaymentPdf', $data));
        $mpdf->Output('OrderProduct_' . time() . '.pdf', 'I'); // this will output data
    }

    public function orderReceivedPrintPdf($trans_id)
    {
        $data['companyInfo']        = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transactionDetails'] = Transaction::with(['end_user:id,first_name,last_name', 'currency:id,symbol,code'])
            ->where(['id' => $trans_id])
            ->first(['transaction_type_id', 'end_user_id', 'currency_id', 'uuid', 'created_at', 'status', 'subtotal', 'charge_percentage', 'charge_fixed', 'total', 'note']);
        // dd($data['transactionDetails']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'                 => 'utf-8',
            'format'               => 'A3',
            'orientation'          => 'P',
            'shrink_tables_to_fit' => 0,
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.shop.order.orderReceivedPaymentPdf', $data));
        $mpdf->Output('Order_Receive' . time() . '.pdf', 'I'); // this will output data
    }
}
