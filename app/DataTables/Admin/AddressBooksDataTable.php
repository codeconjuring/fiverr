<?php

/**
* AddressBook Datatable
*
* description : Use yajra datatable to view all addressbook data
*
*@package AddressBook Module
*@author Ahammed Imtiaze <imtiaze.techvill@gmail.com>,  09/09/19
*@version
*/

namespace App\DataTables\Admin;

use App\Models\AddressBook;
use App\User;
use App\Http\Helpers\Common;
use Yajra\DataTables\Services\DataTable;

class AddressBooksDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn (
                'user_id', function ($address_book) {
                    return $address_book->user->first_name.' '.$address_book->user->last_name;
                }
            )
            ->editColumn (
                'phone', function ($address_book) {
                    return $address_book->phone;
                }
            )
            ->editColumn (
                'email', function ($address_book) {
                    return $address_book->email;
                }
            )
            ->editColumn (
                'fax', function ($address_book) {
                    return $address_book->fax;
                }
            )
            ->editColumn (
                'website', function ($address_book) {
                    return $address_book->website;
                }
            )
            ->editColumn (
                'address_line_1', function ($address_book) {
                    return $address_book->address_line_1;
                }
            )
            ->editColumn (
                'address_line_2', function ($address_book) {
                    return $address_book->address_line_2;
                }
            )
            ->editColumn (
                'city', function ($address_book) {
                    return $address_book->city;
                }
            )
            ->editColumn (
                'state', function ($address_book) {
                    return $address_book->state;
                }
            )
            ->editColumn (
                'zip', function ($address_book) {
                    return $address_book->zip;
                }
            )
            ->editColumn (
                'country', function ($address_book) {
                    return $address_book->country;
                }
            )
            ->editColumn (
                'photo', function ($address_book) {
                    if ($address_book->photo) {
                        $photo = '<td><img src="'. url('public/images/shop/address_book/' . $address_book->photo).'" width="70" height="50" class="img-responsive"></td>';
                    } else {
                        $photo = '<td><img src="'. url('public/dist/img/shop/address_book.png').'" width="70" height="50" class="img-responsive"></td>';
                    }
                    return $photo;
                }
            )
            ->editColumn (
                'status', function ($address_book) {
                    if ($address_book->status == 'Active') {
                        $status = '<span class="label label-success">Active</span>';
                    } elseif ($address_book->status == 'Inactive') {
                        $status = '<span class="label label-danger">Inactive</span>';
                    }
                    return $status;
                }
            )
            ->addColumn ('action', function ($address_book) {
                $edit = '';
                // $edit = $delete = '';

                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_address_book')) ? '<a href="' . url('admin/address-books/edit/'.$address_book->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                // $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_address_book')) ? '<a href="' . url('admin/address-books/delete/'.$address_book->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;' : '';

                // return $edit . $delete;
                return $edit;
            })
            ->rawColumns(['photo','status','action'])
            ->make(true);
    }

    public function query()
    {
        $query = AddressBook::select('address_books.*');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'address_books.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'user_id', 'name' => 'address_book.user_id', 'title' => 'User'])
            ->addColumn(['data' => 'phone', 'name' => 'address_book.phone', 'title' => 'Phone'])
            ->addColumn(['data' => 'email', 'name' => 'address_book.email', 'title' => 'Email'])
            ->addColumn(['data' => 'website', 'name' => 'address_book.website', 'title' => 'Website'])
            ->addColumn(['data' => 'fax', 'name' => 'address_book.fax', 'title' => 'Fax'])
            ->addColumn(['data' => 'address_line_1', 'name' => 'address_book.address_line_1', 'title' => 'Address 1'])
            ->addColumn(['data' => 'address_line_2', 'name' => 'address_book.address_line_2', 'title' => 'Address 2'])
            ->addColumn(['data' => 'city', 'name' => 'address_book.city', 'title' => 'City'])
            ->addColumn(['data' => 'state', 'name' => 'address_book.state', 'title' => 'State'])
            ->addColumn(['data' => 'zip', 'name' => 'address_book.zip', 'title' => 'Zip'])
            ->addColumn(['data' => 'country', 'name' => 'address_book.country', 'title' => 'Country'])
            ->addColumn(['data' => 'photo', 'name' => 'address_book.photo', 'title' => 'Photo'])
            ->addColumn(['data' => 'status', 'name' => 'address_book.status', 'title' => 'Status'])
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
