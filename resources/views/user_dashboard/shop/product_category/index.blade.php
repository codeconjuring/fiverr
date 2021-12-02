@extends('user_dashboard.layouts.new_app')

@section('css')
    <style>
        .product-category {
            color: #5B7DCA;;
        }
        .product-category:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12 mb20 marginTopPlus">
                    @include('user_dashboard.layouts.common.alert')

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li>
                                        <a href="{{ url('stores') }}">Stores</a>
                                    </li>
                                    <li class="active">
                                        <a href="{{ url('product-categories') }}">Product Categories</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('products') }}">Products</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('owner-orders') }}">My Orders</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('customer-orders') }}">Customer Orders</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('shipping-address') }}">My Shipping Address</a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <a href="{{url('/shop') }}" target="_blank" class="pull-right product-category">Go to Shop</a>
                            </div>

                        </div>

                        <div class="right mb10" style="padding:10px;">
                            <a href="{{ url('product-categories/add') }}" class="btn btn-cust ticket-btn pull-right"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i>&nbsp; Add Product Category</a>
                        </div>
                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            @if($product_categories->count() > 0)

                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td class="" width="15%"><strong>Product Category</strong></td>
                                        <td width="10%"><strong>Photo</strong></td>
                                        <td width="10%"><strong>Store</strong></td>
                                        <td width="15%"><strong>Status</strong></td>
                                        <td width="10%"><strong>Action</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product_categories as $productCategory)
                                    <tr>
                                        <td>{{ $productCategory->name}} </td>
                                        <td>
                                            @if(!empty($productCategory->photo))
                                                <img src="{{url('public/images/shop/product_category/' . $productCategory->photo)}}" class="rounded-circle rounded-circle-custom-trans">
                                            @else
                                                <img src="{{url('public/dist/img/shop/product_category.png')}}" class="rounded-circle rounded-circle-custom-trans">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $productCategory->store->name}}
                                        </td>
                                        <td class="">
                                            @if($productCategory->status =='Inactive')
                                                <span class="badge badge-danger">{{ $productCategory->status }}</span>
                                            @elseif($productCategory->status =='Active')
                                                <span class="badge badge-success">{{ $productCategory->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('product-categories/edit/'.$productCategory->id) }}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>

                                            {{-- <form action="{{ url('product-categories/delete') }}" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $productCategory->id }}">
                                                <a class="btn btn-sm btn-danger" data-toggle="modal"
                                                   data-target="#delete-warning-modal" data-title="{{__("Delete Data")}}"
                                                   data-message="{{__("Do you really want delete this category? If you delete this category, then its products and all other associated information will be deleted.")}}"
                                                   data-row="{{ $productCategory->id }}"
                                                   href=""><i class="fa fa-trash"></i></a>
                                            </form> --}}

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                                <h5 style="padding: 15px 20px; ">@lang('message.dashboard.ticket.no-ticket')</h5>
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $product_categories->links('vendor.pagination.bootstrap-4') }}
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

@endsection

