<?php
/**
* ProductCategory Datatable
*
* description : Use yajra Datatables to view all data of product category
*
*@package ProductCategory Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  05/09/19
*@version
*/

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\ProductCategory;
use Yajra\DataTables\Services\DataTable;

class ProductCategoriesDatatable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn (
                'name', function ($productCategory) {
                    return  $productCategory->name;
                }
            )
            ->editColumn (
                'photo', function ($productCategory) {
                    if ($productCategory->photo) {
                        $photo = '<td><img src="'. url('public/images/shop/product_category/' . $productCategory->photo).'" width="70" height="50" class="img-responsive"></td>';
                    } else {
                        $photo = '<td><img src="'. url('public/dist/img/shop/product_category.png').'" width="70" height="50" class="img-responsive"></td>';
                    }
                    return $photo;
                }
            )
            ->editColumn (
                'store_id', function ($productCategory) {
                    return  isset($productCategory->store->name) ? $productCategory->store->name : '--';
                }
            )
            ->editColumn (
                'description', function ($productCategory) {
                    return  $productCategory->description;
                }
            )
            ->editColumn (
                'status', function ($productCategory) {
                    if ($productCategory->status == 'Active') {
                        $status = '<span class="label label-success">Active</span>';
                    } elseif ($productCategory->status == 'Inactive') {
                        $status = '<span class="label label-danger">Inactive</span>';
                    }
                    return $status;
                }
            )
            ->addColumn('action', function ($productCategory) {
                // $edit = $delete = '';
                $edit = '';

                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_product_category')) ? '<a href="' . url('admin/product-categories/edit/'.$productCategory->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                // $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_product_category')) ? '<a href="' . url('admin/product-categories/delete/'.$productCategory->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;' : '';

                // return $edit . $delete;
                return $edit;
            })
            ->rawColumns(['photo','status','action'])
            ->make(true);
    }

    public function query()
    {
        $query = ProductCategory::with('store')->select('product_categories.*');

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'product_categories.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'product_categories.name', 'title' => 'Name'])
            ->addColumn(['data' => 'photo', 'name' => 'product_categories.photo', 'title' => 'Photo'])
            ->addColumn(['data' => 'store_id', 'name' => 'product_categories.store_id', 'title' => 'Store'])
            ->addColumn(['data' => 'description', 'name' => 'product_categories.description', 'title' => 'Description'])
            ->addColumn(['data' => 'status', 'name' => 'product_categories.status', 'title' => 'Status'])
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
