<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\OrdersDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Order;

class OrderController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index(OrdersDataTable $dataTable)
    {
        $data['menu']     = 'shop';
        $data['sub_menu'] = 'order_list';
        return $dataTable->render('admin.shop.order.index', $data);
    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);

        if ($order)
        {
            $order->delete();
        }

        $this->helper->one_time_message('success', 'Order Deleted Successfully');
        return redirect('admin/orders');
    }
}
