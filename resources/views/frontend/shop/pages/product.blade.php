@extends('frontend.layouts.app')
@section('content')

<div class="container mt-3">
    
        @if(Session::has('message'))
            <div class="alert {{ Session::get('alert-class') }} text-center" style="margin-bottom:0px;" role="alert">
            {{ Session::get('message') }}
            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
            </div>
        @endif
        <div class="alert alert-success text-center" id="success_message_div" style="margin-bottom:0px;display:none;" role="alert">
            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
            <p id="success_message"></p>
        </div>

        <div class="alert alert-danger text-center" id="error_message_div" style="margin-bottom:0px;display:none;" role="alert">
            <p><a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a></p>
            <p id="error_message"></p>
        </div>
</div>

<div class="product-details-area mt-5 mb-3">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="single-product-item-wrapper mb-4">
                    <div class="product-image view py-4 px-4">
                        <a href="#">
                            @if(!empty($product->photo))
                                <img alt="product name" src="{{ url('public/images/shop/product/' . $product->photo) }}" style="height: 340px !important;">
                            @else
                                <img alt="product name" src="{{ url('public/dist/img/shop/product.jpg') }}">
                            @endif
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="product-details-contents text-left">
                    <h1>{{ $product->title }}</h1>
                    <p class="mb-4">price : <span><strong>{{ moneyFormat($product->currency->symbol, $product->price) }}</strong></span></p>
                        <table class="mb-4">
                            <tr>
                                <td>Availability</td>
                                <td>:</td>
                                <td>
                                    @if ($product->stock > 0)
                                        <span class="text-success"><strong>In stock ({{$product->stock}})</strong></span>
                                    @else
                                        <span class="text-danger"><strong>Out of Stock</strong></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Product-Code</td>
                                <td>:</td>
                                <td><strong>#{{ $product->product_code }}</strong></td>
                            </tr>

                            <tr>
                                <td>Category</td>
                                <td>:</td>
                                <td><strong>{{ $product->product_category->name }}</strong></td>
                            </tr>
                            
                            <tr>
                                <td>store</td>
                                <td>:</td>
                                <td>
                                    <a href="{{ url('shop/'.$product->store_id.'/'.$product->store->slug) }}"  target="_blank" class="tip-right" data-toggle="tooltip" data-placement="right" title="Click here to see more product of {{ $product->store->name }} store"><strong style="text-decoration:underline;color:black;">{{ $product->store->name }}</strong></a>
                                </td>
                            </tr>
                            
                            
                        </table>
                        <p style="line-height: normal;">Details :  <br> <br> {{ $product->description }}</p>
                        <a href="{{ url('shop/product/buy/' . $product->id) }}" class="btn btn-info btn-block mt-4" style="background-color:#5B7DCA;border:none;">
                            <strong>Buy Now</strong>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    
    
    @endsection
    @section('js')
    <script>
        $(document).ready(function(){
           
           $(".tip-right").tooltip({
               placement : 'right'
           });
       });
    </script>
    @endsection
