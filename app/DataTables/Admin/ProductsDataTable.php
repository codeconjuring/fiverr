<?php
/**
* Product Datatable 
*
* description : Use yajra datatable to view all data of products
*
*@package Product Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  07/09/19
*@version 
*/

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Product;
use App\User;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn (
                'title', function ($product) {
                    return  $product->title;
                }
            )
            ->editColumn (
                'product_category_id', function ($product) {
                    return  $product->product_category->name;
                }
            )
            ->editColumn (
                'product_code', function ($product) {
                    return  $product->product_code;
                }
            )
            ->editColumn (
                'store_id', function ($product) {
                    return  isset($product->store->name) ? $product->store->name : '---';
                }
            )
            ->editColumn (
                'price', function ($product) {
                    return  $product->price;
                }
            )
            ->editColumn (
                'stock', function ($product) {
                    return  $product->stock;
                }
            ) 
            ->editColumn (
                'currency_id', function ($product) {
                    return  $product->currency->code;
                }
            )
            ->editColumn (
                'photo', function ($product) {
                    if ($product->photo) {
                        $product = '<td><img src="' . url('public/images/shop/product/' . $product->photo) . '" width="70" height="50" class="img-responsive"></td>';
                    } else {
                        $product = '<td><img src="' . url('public/dist/img/shop/product.jpg') . '" width="70" height="50" class="img-responsive"></td>';
                    }
                    return $product;
                }
            )
            ->addColumn ('action', function ($products) {
                // $edit = $delete = '';
                $edit = '';
                
                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_product')) ? '<a href="' . url('admin/products/edit/'.$products->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                // $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_product')) ? '<a href="' . url('admin/products/delete/'.$products->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;' : '';

                // return $edit . $delete;
                return $edit;
            })
            ->rawColumns(['photo','action'])
            ->make(true);
    }

    public function query()
    {
        $query = Product::select('products.*'); 
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'products.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'title', 'name' => 'products.title', 'title' => 'Title'])
            ->addColumn(['data' => 'product_category_id', 'name' => 'products.product_category_id', 'title' => 'Category'])          
            ->addColumn(['data' => 'store_id', 'name' => 'products.store_id', 'title' => 'Store'])
            ->addColumn(['data' => 'product_code', 'name' => 'products.product_code', 'title' => 'Code'])
            ->addColumn(['data' => 'currency_id', 'name' => 'products.currency_id', 'title' => 'Currency'])  
            ->addColumn(['data' => 'price', 'name' => 'products.price', 'title' => 'Price'])
            ->addColumn(['data' => 'stock', 'name' => 'products.stock', 'title' => 'Stock'])        
            ->addColumn(['data' => 'photo', 'name' => 'products.photo', 'title' => 'Photo'])
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
