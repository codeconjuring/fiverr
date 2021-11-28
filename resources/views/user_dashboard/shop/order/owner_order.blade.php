@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        .my-order {
            color: #5B7DCA;;
        }
        .my-order:hover {
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
                                    <li>
                                        <a href="{{ url('products') }}">Products</a>
                                    </li>
                                    <li class="active">
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
                                <a href="{{url('/shop') }}" target="_blank" class="pull-right my-order">Go to Shop</a>
                            </div>

                        </div>
                        
                        <div class="right mb10" style="padding:10px;">
                            
                        </div>
                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            @if($ownerOrders->count() > 0)
    
                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td width="8%"><strong>Order Id</strong></td>
                                        <td width="16%"><strong>Product</strong></td>
                                        <td width="8%"><strong>Paid Amount</strong></td>
                                        <td width="8%"><strong>Currency</strong></td>
                                        <td width="16%"><strong>Store Name</strong></td>
                                        <td width="14%"><strong>Store Owner</strong></td>
                                        <td width="10%"><strong>Order Date</strong></td>
                                        <td width="8%"><strong>Status</strong></td>
                                        {{-- <td width="12%"><strong>Action</strong></td> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ownerOrders as $ownerOrder)
                                    <tr>
                                        <td>{{ $ownerOrder->order_id }} </td>
                                        <td> 
                                            <a href="{{ url('shop/product/'.$ownerOrder->product->id) }}" target="_blank" style="color:#5B7DCA;">{{ $ownerOrder->product->title }}</a>
                                        </td>
                                        <td>{{ $ownerOrder->paid_amount }} </td>
                                        <td>{{ $ownerOrder->currency->code }} </td>
                                        <td> 
                                            <a href="{{ url('shop/'.$ownerOrder->store->id.'/'.$ownerOrder->store->slug) }}" target="_blank" style="color:#5B7DCA;">{{ $ownerOrder->store->name }}</a>
                                        </td>
                                        <td>{{ $ownerOrder->store->user->first_name .' '. $ownerOrder->store->user->last_name }} </td>
                                        <td>{{ $ownerOrder->order_date }} </td>
                                        <td><span class="badge badge-success">{{ $ownerOrder->status }}</span></td>
                                        {{-- <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            
                                            <form action="#" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $ownerOrder->id }}">
                                                <a class="btn btn-sm btn-danger" data-toggle="modal"
                                                   data-target="#delete-warning-modal" data-title="{{__("Delete Data")}}"
                                                   data-message="{{__("Are you sure you want to delete this Data ?")}}"
                                                   data-row="{{ $ownerOrder->id }}"
                                                   href=""><i class="fa fa-trash"></i></a>
                                            </form>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                                <h5 style="padding: 15px 20px; ">Store Not Found !</h5>
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $ownerOrders->links() }}
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


