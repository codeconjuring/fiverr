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
                        <h4>Buy Product</h4>
                    </div>
                    <div class="wap-wed mb20 p-4 cart-details">
                        
                        
                        <div class="row mt20">
                            <div class="col-6">
                                <p><u>Product name</u></p>
                                <br>
                                <p><strong>{{ $product->title }}</strong> </p>
                            </div>
                            
                            <div class="col-3">
                                <p><u>Code</u></p>
                                <br>
                                <p><strong>#{{ $product->product_code }}</strong> </p>
                            </div>
                        
                            <div class="col-3">
                                <p><u>Price</u></p>
                                <br>
                                <p><strong>{{ moneyFormat($product->currency->symbol, $product->price) }}</strong> </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer" style="margin-left: 0 auto">
                        <div style="float: left;">
                            <a href="{{ url('shop/product/'.$product->id) }}" class="btn btn-cust">
                                <strong><i class="fa fa-angle-left"></i>&nbsp;&nbsp;Back</strong>
                            </a>
                        </div>
                        <div style="float: right;">
                            
                            <a href="{{ url('shop/product/confirm/'.$product->id) }}" class="btn btn-cust buyConfirm">
                                <i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
                                <span class="buyConfirmText">Confirm</span>
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
