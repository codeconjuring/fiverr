@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        .customer-order {
            color: #5B7DCA;;
        }
        .customer-order:hover {
            text-decoration: underline;
        }
        .customer-address {
            color: #5B7DCA;;
        }
        .customer-address:hover {
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
                                    <li>
                                        <a href="{{ url('owner-orders') }}">My Orders</a>
                                    </li>
                                    <li class="active">
                                        <a href="{{ url('customer-orders') }}">Customer Orders</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('shipping-address') }}">My Shipping Address</a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <a href="{{url('/shop') }}" target="_blank" class="pull-right customer-order">Go to Shop</a>
                            </div>

                        </div>
                        
                        <div class="right mb10" style="padding:10px;">
                            
                        </div>
                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            @if($customersOrders->count() > 0)
    
                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td width="8%"><strong>Order Id</strong></td>
                                        <td width="14%"><strong>Customer Name</strong></td>
                                        <td width="16%"><strong>Product</strong></td>
                                        <td width="8%"><strong>Paid Amount</strong></td>
                                        <td width="8%"><strong>Currency</strong></td>
                                        <td width="16%"><strong>Store Name</strong></td>
                                        
                                        <td width="10%"><strong>Order Date</strong></td>
                                        <td width="8%"><strong>Status</strong></td>
                                        {{-- <td width="12%"><strong>Action</strong></td> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customersOrders as $customersOrder)
                                    <tr>
                                        <td>{{ $customersOrder->order_id }} </td>
                                        <td>
                                            <a class="customer-address" href="{{ url('shipping-address/' . $customersOrder->user->id) }}">{{ $customersOrder->user->first_name .' '. $customersOrder->user->last_name }}</a>
                                        </td>
                                        <td>
                                            <a href="{{ url('shop/product/'.$customersOrder->product->id) }}" target="_blank" style="color:#5B7DCA;">{{ $customersOrder->product->title }}</a>
                                        </td>
                                        <td>{{ $customersOrder->paid_amount }} </td>
                                        <td>{{ $customersOrder->currency->code }} </td>
                                        <td> 
                                            <a href="{{ url('shop/'.$customersOrder->store->id.'/'.$customersOrder->store->slug) }}" target="_blank" style="color:#5B7DCA;">{{ $customersOrder->store->name }}</a>
                                        </td>                                  
                                        <td>{{ $customersOrder->order_date }} </td>
                                        <td><span class="badge badge-success">{{ $customersOrder->status }}</span></td>
                                        {{-- <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            
                                            <form action="#" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $customersOrder->id }}">
                                                <a class="btn btn-sm btn-danger" data-toggle="modal"
                                                   data-target="#delete-warning-modal" data-title="{{__("Delete Data")}}"
                                                   data-message="{{__("Are you sure you want to delete this Data ?")}}"
                                                   data-row="{{ $customersOrder->id }}"
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
                            {{ $customersOrders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


