@extends('frontend.layouts.app')
@section('content')
    <!--Start banner Section-->
    <section class="inner-banner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>@lang('message.login.title') </h1>
                </div>
            </div>
        </div>
    </section>
    <!--End banner Section-->

    <!--Start Section-->
    <section class="section-01 padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-6 mx-auto">
                            <!-- form card login -->
                            <div class="card rounded-0">
                                <div class="card-header">
                                    <h3 class="mb-0 text-left">Enter Verification Code</h3>
                                </div>
                                <div class="card-body">

                                    @include('frontend.layouts.common.alert')
                                    <br>
                                    <style>
                                        .error{
                                            font-weight: bold;
                                        }
                                    </style>
                                    <div class="row ">
                                        <div class="col-md-9">
                                            <form action="{{ url('verifyOtp') }}" method="post" id="otp_form">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="user_id" value={{$user_id}}>
                                                    <input type="hidden" name="user_phone" value={{$user_phone}}>

                                                <div class="form-row">
                                                        <input type="number"  id="otp_code" name="otp_code" class="form-control" placeholder="Enter the OTP">	
                                                </div>
                                                
                                                <button type="submit" class="btn btn-cust float-left" style="margin-top: 15px" id="otp-btn">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="login-btn-text" style="font-weight: bolder;">Verify
                                                    </span>
                                                </button>	
                                            </form>
                                        </div>
                                        <div class="col-md-3"> 
                                            <form action="{{ url('resendOtp') }}" method="post" id="resendOtp">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="user_id" value={{$user_id}}>
                                                <input type="hidden" name="user_phone" value={{$user_phone}}>
                                                <button Class="btn btn-success border" id="resend">
                                                    <i class="resend_spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                    <span id="resend-btn-text" style="font-weight: bolder;">Resend Code</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <!--/card-block-->
                            </div>
                            <!-- /form card login -->
                        </div>
                    </div>
                    <!--/row-->
                </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    </section>
@endsection
@section('js')

<script src="{{ url('public/backend/fpjs2/fpjs2.js') }}" type="text/javascript"></script>
<script src="{{asset('public/frontend/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {
        required: "{{__('This field is required.')}}",
    })
</script>

<script>
   
    $('#otp_form').validate({
        rules:
        {
            otp_code: {
                required: true,
                // email: true
            },
        },
        submitHandler: function(form)
        {
            $("#otp-btn").attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".spinner").show();
            $("#login-btn-text").text("{{__('Verifying...')}}");
            ('#otp_form').submit();
        }
    });


    $(document).ready(function(){
        $("#resend").click(()=>{
            $(".resend_spinner").show();
            $("#resend-btn-text").text("{{__('Resending...')}}");    
            $('#resendOtp').submit();
        })         
    });

    $(document).ready(function()
    {
        new Fingerprint2().get(function(result, components)
        {
            $('#browser_fingerprint').val(result);
        });
    });
</script>

@endsection
