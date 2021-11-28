@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        .store {
            color: #5B7DCA;;
        }
        .store:hover {
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
                                    <li class="active">
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
                                    <li>
                                        <a href="{{ url('customer-orders') }}">Customer Orders</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('shipping-address') }}">My Shipping Address</a>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <a href="{{url('/shop') }}" target="_blank" class="pull-right store">Go to Shop</a>
                            </div>

                        </div>
                        
                        <div class="right mb10" style="padding:10px;">
                            <a href="{{url('/stores/add')}}" class="btn btn-cust ticket-btn pull-right"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i>&nbsp; Create Store</a>
                        </div>
                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            @if($stores->count() > 0)
    
                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td class="text-left" width="15%"><strong>Store Name</strong></td>
                                        <td width="10%"><strong>Photo</strong></td>
                                        <td class="text-left" width="10%"><strong>Store Code</strong></td>
                                        <td width="10%"><strong>Phone</strong></td>
                                        <td width="15%"><strong>Email</strong></td>
                                        <td width="15%"><strong>Website</strong></td>
                                        <td width="15%"><strong>Status</strong></td>
                                        <td width="10%"><strong>Action</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stores as $store)
                                    <tr>
                                        <td class="text-left">
                                            <a class="store" href="{{ url('shop/'.$store->id.'/'.$store->slug) }}" target="_blank">{{ $store->name }}</a>
                                             
                                        </td>
                                        <td>
                                            @if(!empty($store->photo))
                                                <img src="{{url('public/images/shop/store/' . $store->photo)}}" class="rounded-circle rounded-circle-custom-trans">
                                            @else
                                                <img src="{{url('public/dist/img/shop/store.jpg')}}" class="rounded-circle rounded-circle-custom-trans">
                                            
                                            @endif
                                        </td>
                                        <td class="text-left">{{ $store->store_code }} </td>
                                        <td class="text-left">{{ $store->phone }} </td>
                                        <td class="">{{ $store->email }} </td>
                                        <td class="">{{ !empty($store->website) ? $store->website : "-" }} </td>
                                        <td class="">
                                            @if($store->status =='Inactive')
                                                <span class="badge badge-danger">{{ $store->status }}</span>
                                            @elseif($store->status =='Active')
                                                <span class="badge badge-success">{{ $store->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('stores/edit/'.$store->id) }}" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></a>
                                            
                                            {{-- <form action="{{ url('stores/delete') }}" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$store->id}}">
                                                <a class="btn btn-sm btn-danger" data-toggle="modal"
                                                   data-target="#delete-warning-modal" data-title="{{__("Delete Store")}}"
                                                   data-message="{{__("Do you really want delete this Store? If you delete this store, then its categories, products and all other associated information will be deleted.")}}"
                                                   data-row="{{$store->id}}"
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
                            {{ $stores->links('vendor.pagination.bootstrap-4') }}
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

