<!DOCTYPE html>
<html lang="en">


  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{!isset($exception) ? meta(Route::current()->uri(),'description'):$exception->description}}">
    <meta name="keywords" content="{{!isset($exception) ? meta(Route::current()->uri(),'keyword'):$exception->keyword}}">
    <title>{{!isset($exception) ? meta(Route::current()->uri(),'title'):$exception->title}} <?= isset($additionalTitle)?'| '.$additionalTitle :'' ?></title>
    <!---favicon-->
    @if (!empty(getfavicon()))
    <link rel="shortcut icon" href="{{asset('public/images/logos/'.getfavicon())}}" />
    @endif

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/favicon.png">
    <link href="{{ asset('public/new_dashboard/public/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/new_dashboard/public/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/new_dashboard/public/vendor/chartist/css/chartist.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('public/new_dashboard/public/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet" type="text/css"/>


	<link href="{{ asset('public/new_dashboard/public/css/style.css') }}" rel="stylesheet" type="text/css"/>
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('public/user_dashboard/css/style.css')}}"> --}}
    @yield('css');
    <script type="text/javascript">
        var SITE_URL = "{{url('/')}}";
    </script>
</head>

<body>

    <?php
        $user = Auth::user();
        $socialList = getSocialLink();
        $menusHeader = getMenuContent('Header');
        //$logo = session('company_logo'); //from session
        $logo = getCompanyLogoWithoutSession(); //direct query
        $isMiningReferralEnabled = isMiningReferralEnabled();
    ?>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        <div class="nav-header">
            @if (isset($logo))
                <a href="{{url('/')}}" class="brand-logo">
                    <img class="logo-abbr" src="{{asset('public/images/logos/'.$logo)}}" alt="">
                    <img class="logo-compact" src="{{asset('public/images/logos/'.$logo)}}" alt="">
                    <img class="brand-title" src="{{asset('public/images/logos/'.$logo)}}" alt="">
                </a>
            @else
                <a href="{{url('/')}}" class="brand-logo">
                    <img class="logo-abbr" src="{{ url('public/uploads/userPic/default-logo.jpg') }}" alt="">
                    <img class="logo-compact" src="{{ url('public/uploads/userPic/default-logo.jpg') }}" alt="">
                    <img class="brand-title" src="{{ url('public/uploads/userPic/default-logo.jpg') }}" alt="">
                </a>
            @endif


            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>


        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
								<div class="input-group search-area d-lg-inline-flex d-none">
									<div class="input-group-append">
										<button class="input-group-text"><i class="flaticon-381-search-2"></i></button>
									</div>
									<input type="text" class="form-control" placeholder="Search here...">
								</div>
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">


                                @php
                                    $fullName = strlen($user->first_name.' '.$user->last_name) > 20 ? substr($user->first_name.' '.$user->last_name,0,20)."..." : $user->first_name.' '.$user->last_name; //change in pm_v2.1
                                @endphp
                                <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
									<div class="header-info">
										<span class="text-black">Hello,<strong>{{ $fullName }}</strong></span>
									</div>
                                        @if(Auth::user()->picture)
                                        <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}"
                                              width="20">
                                        @else
                                            <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" width="20">
                                        @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{url('/profile')}}" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ml-2">Setting </span>
                                    </a>
                                    <a href="{{url('/logout')}}" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ml-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>

                    </div>
                </nav>
            </div>
        </div>

<div class="deznav">
    <div class="deznav-scroll">
<ul class="metismenu" id="menu">

        <li class="ai-icon <?= isset($menu) && ($menu == 'dashboard') ? 'active' : '' ?>"><a href="{{url('/dashboard')}}"><i class="flaticon-381-networking"></i><span class="nav-text">@lang('message.dashboard.nav-menu.dashboard')</span></a></li>

        @if(Common::has_permission(auth()->id(),'manage_transaction'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'transactions') ? 'active mm-active' : '' ?>"><a href="{{url('/transactions')}}"><i class="flaticon-381-controls-3"></i><span class="nav-text">@lang('message.dashboard.nav-menu.transactions')</span></a></li>
        @endif

        @if(Common::has_permission(auth()->id(),'manage_transfer'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'send_receive') ? 'active' : '' ?>"><a href="{{url('/moneytransfer')}}"><i class="flaticon-381-network"></i><span class="nav-text">@lang('message.dashboard.nav-menu.send-req')</span></a></li>
        @elseif(Common::has_permission(auth()->id(),'manage_request_payment'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'request_payment') ? 'active' : '' ?>">
                <a href="{{url('/request_payment/add')}}"><i class="flaticon-381-settings-2"></i><span class="nav-text">@lang('message.dashboard.nav-menu.send-req')</span></a>
            </li>
        @endif

        @if(Common::has_permission(auth()->id(),'manage_merchant'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'merchant') ? 'active' : '' ?>"><a
                        href="{{url('/merchants')}}"><i class="flaticon-381-heart"></i><span class="nav-text">@lang('message.dashboard.nav-menu.merchants')</span></a></li>
        @endif

        @if(Common::has_permission(auth()->id(),'manage_store'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'shop') ? 'active' : '' ?>">
                <a href="{{url('/stores')}}"><i class="flaticon-381-notepad"></i><span class="nav-text">Shop</span></a>
            </li>
        @endif

        @if(Common::has_permission(auth()->id(),'manage_voucher'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'voucher') ? 'active' : '' ?>"><a href="{{url('/vouchers')}}"><i class="flaticon-381-network"></i><span class="nav-text">@lang('message.dashboard.nav-menu.vouchers')</span></a></li>
        @endif

        @if(Common::has_permission(auth()->id(),'manage_dispute'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'dispute') ? 'active' : '' ?>"><a
                        href="{{url('/disputes')}}"><i class="flaticon-381-network"></i><span class="nav-text">@lang('message.dashboard.nav-menu.disputes')</span></a></li>
        @endif
        @if(Common::has_permission(auth()->id(),'manage_ticket'))
            <li class="ai-icon <?= isset($menu) && ($menu == 'ticket') ? 'active' : '' ?>"><a
                        href="{{url('/tickets')}}"><i class="flaticon-381-settings-2"></i><span class="nav-text">@lang('message.dashboard.nav-menu.tickets')</span></a></li>
        @endif
        @if($isMiningReferralEnabled == 'yes')
            <li class="ai-icon <?= isset($menu) && ($menu == 'refer') ? 'active' : '' ?>">
                <a href="{{url('/refer-friend')}}"><i class="flaticon-381-television"></i><span class="nav-text">Refer a friend</span></a>
            </li>
        @endif


        </ul>

<div class="copyright">
  <p><strong>Mophy Payment Admin Dashboard</strong> © 2021 All Rights Reserved</p>
  <p>Made with <span class="heart"></span> by DexignZone</p>
</div>
</div>
</div>

<div class="content-body">
    <div class="container-fluid">
        @yield('content')

    </div>
</div>

<div class="footer">
    <div class="copyright">
        <p>Copyright © Designed &amp; Developed by <a href="http://dexignzone.com/" target="_blank">DexignZone</a> 2021</p>
    </div>
</div>
    </div>
	<script src="{{ asset('public/new_dashboard/public/vendor/global/global.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/vendor/chart.js/Chart.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/vendor/owl-carousel/owl.carousel.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/vendor/peity/jquery.peity.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/vendor/apexchart/apexchart.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/js/dashboard/dashboard-1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/js/custom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/new_dashboard/public/js/deznav-init.js') }}" type="text/javascript"></script>
    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    </script>


    @yield('js');
	</body>

</html>
