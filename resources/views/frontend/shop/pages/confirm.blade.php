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

<section class="section-06 history padding-30 mt-5">
    <div class="container-md container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-xs-12 mb20 marginTopPlus">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Purchased</h4>
                    </div>
                    <div class="wap-wed  mb20 p-4 cart-details">

                        <div class="text-center" >
                            <div class="buy-product-image">
                                    <img src="{{ url('public/dist/img/shop/success50.png') }}" height="48" width="48" alt="success">
                            </div>


                            <h3 class="text-success" style="margin-top:0;"><label>Success !</label></h3>
                            <p style="font-size:18px !important;" class="mb-2">Product Purchased Successfully.</p>
                            <p style="font-size:18px !important;" class="mb-1">Order Id: <span style="font-weight:550;margin-left:5px;">#{{ $orderId }}</span></p>
                            <p style="font-size:18px !important;">You have paid: <span style="color:#33A8C9;font-weight:550;margin-left:5px;">{{ moneyFormat($currencySymbol, $productPrice) }}</span></p>
                            <p style="font-size:18px !important;" class="mt-2"><small>Please add your <a style="text-decoration:underline" target="_blank" href="{{ url('shipping-address') }}">Shipping Address</a></small></p>
                        </div>

                    </div>

                    <div class="card-footer" style="margin-left: 0 auto">
                        <div style="float: left;">
                            <a href="{{ url('shop') }}" class="btn btn-cust">
                                <strong><i class="fa fa-angle-left"></i>&nbsp;&nbsp;Go Home</strong>
                            </a>
                        </div>
                        <div style="float: right;">
                            <a href="{{ url('shop/product/' . $productId) }}" class="btn btn-cust">
                                <strong>Buy Again&nbsp;&nbsp;<i class="fa fa-angle-right"></i></strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--/col-->
        </div>
        <!--/row-->
    </div>
</section>




@endsection
@section('js')

    <script type="text/javascript">
        $(document).on('click', '.buyConfirm', function (e)
        {
            $(".fa-spin").show().css("color","white");
            $('.buyConfirmText').text('Confirming...').css("color","white");
            $(this).attr("disabled", true);
        });
    </script>

@endsection
