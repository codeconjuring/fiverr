<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Order;
use Yajra\DataTables\Services\DataTable;

class OrdersDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn (
                'product_id', function ($order) {
                    return  $order->product->title;
                }
            )
            ->editColumn (
                'product_category_id', function ($order) {
                    return  $order->product->product_category->name;
                }
            )
            ->editColumn (
                'paid_amount', function ($order) {
                    return  $order->paid_amount;
                }
            )
            ->editColumn (
                'currency_id', function ($order) {
                    // return  isset($order->store->name) ? $order->store->name : '---';
                    return $order->currency->code;
                }
            )
            ->editColumn (
                'order_id', function ($order) {
                    return  $order->order_id;
                }
            )
            ->editColumn (
                'order_date', function ($order) {
                    return  $order->order_date;
                }
            ) 
            ->editColumn (
                'user_id', function ($order) {
                    return  $order->user->first_name . ' ' . $order->user->last_name;
                }
            )
            ->editColumn (
                'store_user_id', function ($order) {
                    return  $order->store->user->first_name. ' ' . $order->store->user->last_name;
                }
            )
            ->editColumn (
                'store_id', function ($order) {
                    return  $order->store->name;
                }
            )
            ->editColumn (
                'status', function ($order) {
                    // return  $order->status;

                    $status = '<td><span class="label label-success">' . $order->status . '</span></td>';
                    return  $status;
                }
            )
            // ->editColumn (
            //     'photo', function ($order) {
            //         if ($order->photo) {
            //             $order = '<td><img src="'. url('public/images/stores/order/' . $order->photo).'" width="70" height="50" class="img-responsive"></td>';
            //         } else {
            //             $order = '<td><img src="'. url('public/dist/img/order.jpg').'" width="70" height="50" class="img-responsive"></td>';
            //         }
            //         return $order;
            //     }
            // )
            // ->addColumn ('action', function ($order) {
            //     $delete = '';
            //     // (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_order'))
            //     // $edit = true ? '<a href="' . url('admin/orders/edit/'.$order->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

            //     $delete = true ? '<a href="' . url('admin/orders/delete/'.$order->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;' : '';

            //     return $delete;
            // })
            // ->rawColumns(['action'])
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = Order::select('orders.*'); 
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'orders.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'product_id', 'name' => 'orders.product_id', 'title' => 'Product Name'])          
            ->addColumn(['data' => 'product_category_id', 'name' => 'orders.product_id', 'title' => 'Category Name'])          
            ->addColumn(['data' => 'paid_amount', 'name' => 'orders.paid_amount', 'title' => 'Paid Amount'])        
            ->addColumn(['data' => 'currency_id', 'name' => 'orders.currency_id', 'title' => 'Currency'])
            ->addColumn(['data' => 'order_id', 'name' => 'orders.order_id', 'title' => 'Order Id'])  
            ->addColumn(['data' => 'order_date', 'name' => 'orders.order_date', 'title' => 'Order Date'])
            ->addColumn(['data' => 'user_id', 'name' => 'orders.user_id', 'title' => 'Customer Name'])
            ->addColumn(['data' => 'store_user_id', 'name' => 'orders.store_id', 'title' => 'Store Owner'])
            ->addColumn(['data' => 'store_id', 'name' => 'orders.store_id', 'title' => 'Store Name'])
            ->addColumn(['data' => 'status', 'name' => 'orders.status', 'title' => 'Status'])        
            // ->addColumn(['data' => 'photo', 'name' => 'orders.photo', 'title' => 'Photo'])
            // ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])
            ->parameters([
                'order'      => [[0, 'desc']],
                //centering all texts in columns
                "columnDefs" => [
                    [
                        "className" => "dt-center",
                        "targets" => "_all"
                    ]
                ],
                'pageLength' => \Session::get('row_per_page'),
                'language'   => \Session::get('language'),
            ]);
    }
}
