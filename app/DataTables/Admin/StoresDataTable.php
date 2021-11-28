<?php
/**
* Store Datatable
*
* description : Use yajra datatable to view all store information
*
*@package Store Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  04/09/19
*@version 
*/

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Store;
use Yajra\DataTables\Services\DataTable;

class StoresDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn (
                'name', function ($store) {
                    return  $store->name;
                }
            )
            ->editColumn (
                'user_id', function ($store) {
                    return $store->user->first_name.' '.$store->user->last_name;
                }
            )
            ->editColumn (
                'store_code', function( $store ){
                    return $store->store_code;
                }
            )
            ->editColumn (
                'photo', function ($store) {
                    if ($store->photo) {
                        $photo = '<td><img src="'. url('public/images/shop/store/' . $store->photo).'" width="70" height="50" class="img-responsive"></td>';
                    } else {
                        $photo = '<td><img src="' . url('public/dist/img/shop/store.jpg') . '" width="70" height="50" class="img-responsive"></td>';
                    }
                    return $photo;
                }
            )
            ->editColumn (
                'phone', function ($store) {
                    return  $store->phone;
                }
            )
            ->editColumn(
                'email', function ($store) {
                    return  $store->email;
                }
            ) 
            ->editColumn(
                'website', function ($store) {
                    return  $store->website;
                }
            )
            ->editColumn(
                'status', function ($store) {
                    if ($store->status == 'Active') {
                        $status = '<span class="label label-success">Active</span>';
                    } elseif ($store->status == 'Inactive') {
                        $status = '<span class="label label-danger">Inactive</span>';
                    }
                    return $status;
                }
            )
            ->addColumn('action', function($store){
                // $edit = $delete = '';
                $edit = '';
    
                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_store')) ? '<a href="' . url('admin/stores/edit/'.$store->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                // $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_store')) ? '<a href="' . url('admin/stores/delete/'.$store->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;' : '';

                // return $edit . $delete;
                return $edit;
            })
            ->rawColumns(['photo','status','action'])
            ->make(true);
    }

    public function query()
    {
        $query = Store::select('stores.*'); 
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'stores.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'stores.name', 'title' => 'Store Name'])
            ->addColumn(['data' => 'store_code', 'name' => 'stores.store_code', 'title' => 'Code'])
            ->addColumn(['data' => 'user_id', 'name' => 'stores.user_id', 'title' => 'Owner'])
            ->addColumn(['data' => 'phone', 'name' => 'stores.phone', 'title' => 'Phone'])
            ->addColumn(['data' => 'email', 'name' => 'stores.email', 'title' => 'Email'])
            ->addColumn(['data' => 'website', 'name' => 'stores.website', 'title' => 'Website'])
            ->addColumn(['data' => 'photo', 'name' => 'stores.photo', 'title' => 'Photo'])
            ->addColumn(['data' => 'status', 'name' => 'stores.status', 'title' => 'Status'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])
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
