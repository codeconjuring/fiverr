@extends('user_dashboard.layouts.app')

@section('css')
    <!--sweetalert-->
    <link href="{{ asset('public/user_dashboard/css/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="col-md-12 col-xs-12 mb20 marginTopPlus">

                {{-- Header information part --}}
                <div class="refer-hero text-center">
                    <div class="refer-child">
                        <h2>Earn {{ moneyFormat($referralLevel->currency->symbol, formatNumber($referralLevel->amount)) }} when you refer friends to {{ $company_name }}</h2>
                        <br>
                        <h2>Here's how it works</h2>
                        <br>
                        <br>

                        <div class="row">
                            <div class="col-md-3">
                                <h3>Tell your friend about {{ $company_name }}</h4>
                            </div>
                            <div class="col-sm-1">
                                <p><i class="fa fa-share"></i></p>
                            </div>
                            <div class="col-md-3">
                                <h3>Your friend signs up*</h4>
                            </div>
                            <div class="col-sm-1">
                                <p><i class="fa fa-share"></i></p>
                            </div>
                            <div class="col-md-3">
                                <h3>You both earn {{ moneyFormat($referralLevel->currency->symbol, formatNumber($referralLevel->amount)) }}</h4>
                            </div>
                            <div class="col-md-1">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Header information part --}}


                <div class="text-center" style="padding: 40px;">
                    <h3>Share with Friends</h3>
                    <br>

                    @php

                    if (!empty(auth()->user()->referral_code)) 
                    {
                        $referralCodeWithSite = url("/referral-link", auth()->user()->referral_code->code);

                    } 
                    else 
                    {
                        $referralCodeWithSite = url("/referral-link", $referralCode);
                        
                    }
                        
                    @endphp

                    <div id="social-links">
                        <span>
                            <a class="email" title="Refer a frined" href="#" onclick="javascript:window.location=
                            `mailto:someone@example.com?Subject=Refer a friend&body=Get ${window.localStorage.getItem('referrelAmount')} by registering on ${window.localStorage.getItem('getCompanyName')} using the following referral-link:\n\n
                            ${window.localStorage.getItem('referrelLink')}`"><span class="fa fa-envelope social-refer-icons"></span></a>

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $referralCodeWithSite }}" class="social-button"><span class="fa fa-facebook-official social-refer-icons"></span></a>

                            <a href="#" class="social-button twitter-social"><span class="fa fa-twitter social-refer-icons"></span></a>

                            <a href="#" class="social-button linkedin-social" id="">
                                <span class="fa fa-linkedin social-refer-icons"></span>
                            </a>
                        </span>
                    </div>
                    <br>
                    <!--referral-code-input-->
                    <input type="text" class="form-control referral-code" id="referral-code" readonly="readonly" value="{{ $referralCodeWithSite }}" >
                    <button class="btn btn-cust referral-copy-btn" type="button">Copy</button>
                </div>
            </div>

            <p class="text-center" style="font-size: 80%;font-weight: 400;">* After your friend signs up and receives a total of {{ moneyFormat($referralPreferenceCurrency->symbol, formatNumber($min_referral_amount)) }}, you earn a {{ moneyFormat($referralLevel->currency->symbol, formatNumber($referralLevel->amount)) }} reward.</p>
        </div>
    </section>
@endsection

@section('js')

<script src="{{asset('public/user_dashboard/js/share.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

<script>

    $(window).on('load',function(){
        let currentPageUrl = '{{ url('/') }}'
        let referrelLink = '{{ $referralCodeWithSite }}';
        let referrelAmount = '{{ moneyFormat($referralLevel->currency->symbol, formatNumber($referralLevel->amount)) }}';
        let getCompanyName = '{{ getCompanyName() }}';
        window.localStorage.setItem("referrelLink", referrelLink);
        window.localStorage.setItem("referrelAmount", referrelAmount);
        window.localStorage.setItem("getCompanyName", getCompanyName);

        let linkedinHref = `http://www.linkedin.com/shareArticle?mini=true&amp;url=${referrelLink}&amp;title=Refer a friend&amp;summary=Get ${referrelAmount} by registering on ${getCompanyName} using the following referral-link:&amp;url=${referrelLink}`;
        $('.linkedin-social').attr('href', linkedinHref);

        let twitterHref = `https://twitter.com/intent/tweet?text=Get ${referrelAmount} by registering on ${getCompanyName} using the following referral-link:&amp;url=${referrelLink}`;
        $('.twitter-social').attr('href', twitterHref);
    })

    $(window).unload(function(){
      window.localStorage.removeItem('referrelLink');
      window.localStorage.removeItem('referrelAmount');
      window.localStorage.removeItem('getCompanyName');
    });

    $(document).on('click','.referral-copy-btn',function ()
    {
        $('#referral-code').select();
        document.execCommand('copy');
        swal({
            title: "Copied!",
            text: "Referral Link Copied!",
            type: "info",
            icon: "success",
            closeOnClickOutside: false,
            closeOnEsc: false,
        });
    })
</script>

@endsection
