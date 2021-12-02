@extends('user_dashboard.layouts.new_app')

@section('css')
    <style>
        .product {
            color: #5B7DCA;;
        }
        .product:hover {
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
                                    <li>
                                        <a href="{{ url('product-categories') }}">Product Categories</a>
                                    </li>
                                    <li  class="active">
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
                                <a href="{{url('/shop') }}" target="_blank" class="pull-right product">Go to Shop</a>
                            </div>

                        </div>

                        <div class="right mb10" style="padding:10px;">
                            <a href="{{ url('/products/add') }}" class="btn btn-cust ticket-btn pull-right"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i>&nbsp; Add Product</a>
                        </div>
                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            @if($products->count() > 0)

                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td class="text-left" width="12%"><strong>Product Name</strong></td>
                                        <td width="10%"><strong>Photo</strong></td>
                                        <td width="12%"><strong>Store</strong></td>
                                        <td width="15%"><strong>Category</strong></td>
                                        <td width="8%"><strong>Currency</strong></td>
                                        <td width="13%"><strong>Code</strong></td>
                                        <td width="8%"><strong>Price</strong></td>
                                        <td width="8%"><strong>Stock</strong></td>
                                        <td width="12%"><strong>Action</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td class="text-left">{{ $product->title}} </td>
                                        <td>
                                            @if(!empty($product->photo))
                                                <img src="{{url('public/images/shop/product/' . $product->photo)}}" class="rounded-circle rounded-circle-custom-trans">
                                            @else
                                                <img src="{{url('public/dist/img/shop/product.jpg')}}" class="rounded-circle rounded-circle-custom-trans">

                                            @endif
                                        </td>
                                        <td >{{ $product->store->name }} </td>
                                        <td >{{ $product->product_category->name }} </td>
                                        <td class="">{{ $product->currency->code }} </td>
                                        <td >{{ $product->product_code }} </td>
                                        <td class="">{{ $product->price }} </td>
                                        <td class="">{{ $product->stock }} </td>
                                        <td>
                                            <a href="{{ url('products/edit/'.$product->id) }}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>

                                            {{-- <form action="{{ url('products/delete') }}" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <a class="btn btn-sm btn-danger" data-toggle="modal"
                                                   data-target="#delete-warning-modal" data-title="{{__("Delete Data")}}"
                                                   data-message="{{__("Are you sure you want to delete this Data ?")}}"
                                                   data-row="{{ $product->id }}"
                                                   href=""><i class="fa fa-trash"></i></a>
                                            </form> --}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                                <h5 style="padding: 15px 20px; ">Store Not Found !</h5>
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $products->links('vendor.pagination.bootstrap-4') }}
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

