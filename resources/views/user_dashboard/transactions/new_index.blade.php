@extends('user_dashboard.layouts.new_app')

@section('css')
    <!--daterangepicker-->
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('public/user_dashboard/css/daterangepicker.css')}}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')


<div class="content-body">
    <div class="container-fluid">
    <div class="form-head mb-4">
        <h2 class="text-black font-w600 mb-0">@lang('message.dashboard.nav-menu.transactions')</h2>
    </div>
    <div class="row">

        <div class="col-xl-12 col-lg-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Filter</h4>
              </div>
              <div class="card-body">
                <div class="basic-form">
                  {{-- <form>
                    <div class="row">
                      <div class="col-sm-6">
                        <input type="text" class="form-control" placeholder="First name">
                      </div>
                      <div class="col-sm-6 mt-2 mt-sm-0">
                        <input type="text" class="form-control" placeholder="Last name">
                      </div>
                    </div>
                  </form> --}}

                  <form action="" method="get">
                    <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                    <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
                    <div class="row">
                        <div class="col-md-3">

                            <div class="daterange_btn" id="daterange-btn" style="background: #fff; cursor: pointer; padding: 14px 10px; border: 1px solid #f0f1f5; width: 100%">
                                <span id="drp" style="text-align: left; "><i class="fa fa-calendar"></i> @lang('message.dashboard.transaction.date-range')</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="type" name="type">

                                <option value="all" <?= ($type == 'all') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.all-trans-type')
                                </option>

                                <option value="{{Deposit}}" <?= ($type == Deposit) ? 'selected' : '' ?>>
                                    @lang('message.dashboard.button.deposit')
                                </option>

                                <option value="{{Withdrawal}}" <?= ($type == Withdrawal) ? 'selected' : '' ?>>
                                    @lang('message.dashboard.button.withdraw')
                                </option>

                                <option value="sent" <?= ($type == 'sent') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.payment-sent')
                                </option>

                                <option value="request" <?= ($type == 'request') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.payment-req')
                                </option>

                                <option value="received" <?= ($type == 'received') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.payment-receive')
                                </option>

                                <option value="exchange" <?= ($type == 'exchange') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.exchanges')
                                </option>

                                <option value="crypto_sent" <?= ($type == 'crypto_sent') ? 'selected' : '' ?>>
                                    <!-- TODO: translation -->
                                    Crypto Sent
                                </option>

                                <option value="crypto_received" <?= ($type == 'crypto_received') ? 'selected' : '' ?>>
                                    <!-- TODO: translation -->
                                    Crypto Received
                                </option>

                                <option value="order_product" <?= ($type == 'order_product') ? 'selected' : '' ?>>
                                    Order Product
                                </option>
                                <option value="order_received" <?= ($type == 'order_received') ? 'selected' : '' ?>>
                                    Order Received
                                </option>
                                <option value="referral_award" <?= ($type == 'referral_award') ? 'selected' : '' ?>>
                                    Referral Award
                                </option>

                                <option value="voucher" <?= ($type == 'voucher') ? 'selected' : '' ?>>
                                    vouchers
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="status" name="status">
                                <option value="all" <?= ($status == 'all') ? 'selected' : '' ?>>@lang('message.dashboard.transaction.all-status')
                                </option>
                                <option value="Success" <?= ($status == 'Success') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.success')
                                </option>
                                <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.pending')
                                </option>
                                <option value="Refund" <?= ($status == 'Refund') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.refund')
                                </option>
                                <option value="Blocked" <?= ($status == 'Blocked') ? 'selected' : '' ?>>
                                    @lang('message.dashboard.transaction.blocked')
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="wallet" name="wallet">
                                <option value="all" <?= ($wallet == 'all') ? 'selected' : '' ?>>@lang('message.dashboard.transaction.all-currency')
                                </option>
                                @foreach($wallets as $res)
                                    <option value="{{$res->currency->id}}" <?= ($res->currency_id == $wallet) ? 'selected' : '' ?>>{{$res->currency->code}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-2">
                            <button type="submit" class="btn btn-primary mb-2">@lang('message.dashboard.button.filter')</button>
                        </div>

                    </div>
                </form>

                </div>
              </div>
            </div>
          </div>

        <div class="col-xl-12">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header d-block d-sm-flex border-0">
                      <div>
                        <h4 class="fs-20 text-black">Recent Activity</h4>
                      </div>
                    </div>
                    <div class="card-body payment-bx tab-content p-0">
                      <div class="tab-pane active show fade" id="monthly" role="tabpanel">
                        <div id="accordion-one" class="accordion accordion-primary">
                            @if($transactions->count()>0)
                                @foreach($transactions as $key=>$transaction)
                                    <div class="accordion__item border-bottom mb-0">
                                        <div class="d-flex flex-wrap align-items-center accordion__header collapsed rounded show_area"  trans-id="{{$transaction->id}}" id="{{$key}}" data-toggle="collapse" data-target="#default_collapseOne{{ $key }}">
                                        <div class="mb-lg-0 mb-3 d-flex align-items-center">
                                            <div class="profile-image mr-4">
                                                @if((!empty($transaction->user->picture)))
                                                    <img src="{{url('public/user_dashboard/profile/'.$transaction->user->picture)}}" alt="" width="63" class="rounded-circle">
                                                @else
                                                    <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" alt="" width="63" class="rounded-circle">
                                                @endif
                                                    <span class="bg-success">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip3)">
                                                <path d="M10.4125 14.85C10.225 14.4625 10.3906 13.9937 10.7781 13.8062C11.8563 13.2875 12.7688 12.4812 13.4188 11.4719C14.0844 10.4375 14.4375 9.23749 14.4375 7.99999C14.4375 4.44999 11.55 1.56249 8 1.56249C4.45 1.56249 1.5625 4.44999 1.5625 7.99999C1.5625 9.23749 1.91562 10.4375 2.57812 11.475C3.225 12.4844 4.14062 13.2906 5.21875 13.8094C5.60625 13.9969 5.77187 14.4625 5.58437 14.8531C5.39687 15.2406 4.93125 15.4062 4.54062 15.2187C3.2 14.575 2.06562 13.575 1.2625 12.3187C0.4375 11.0312 -4.16897e-07 9.53749 -3.49691e-07 7.99999C-2.56258e-07 5.86249 0.83125 3.85312 2.34375 2.34374C3.85313 0.831242 5.8625 -7.37314e-06 8 -7.2797e-06C10.1375 -7.18627e-06 12.1469 0.831243 13.6563 2.34374C15.1688 3.85624 16 5.86249 16 7.99999C16 9.53749 15.5625 11.0312 14.7344 12.3187C13.9281 13.5719 12.7938 14.575 11.4563 15.2187C11.0656 15.4031 10.6 15.2406 10.4125 14.85Z" fill="white"/>
                                                <path d="M11.0407 8.41563C11.1938 8.56876 11.2688 8.76876 11.2688 8.96876C11.2688 9.16876 11.1938 9.36876 11.0407 9.52188L9.07503 11.4875C8.78753 11.775 8.40628 11.9313 8.00315 11.9313C7.60003 11.9313 7.21565 11.7719 6.93127 11.4875L4.96565 9.52188C4.6594 9.21563 4.6594 8.72188 4.96565 8.41563C5.2719 8.10938 5.76565 8.10938 6.0719 8.41563L7.22502 9.56876L7.22502 5.12814C7.22502 4.69689 7.57503 4.34689 8.00628 4.34689C8.43753 4.34689 8.78753 4.69689 8.78753 5.12814L8.78753 9.57188L9.94065 8.41876C10.2407 8.11251 10.7344 8.11251 11.0407 8.41563Z" fill="white"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip3">
                                                    <rect width="16" height="16" fill="white" transform="matrix(-4.37114e-08 1 1 4.37114e-08 0 -7.62939e-06)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            </span> </div>

                                            <div>
                                                <h6 class="fs-16 font-w700 mb-0"><a class="text-black" href="javascript:void(0)">{{ $transaction->user->first_name.' '. $transaction->user->last_name }}</a></h6>
                                                {{-- <h6 class="fs-16 font-w700 mb-0"><a class="text-black" href="javascript:void(0)">{{ dateFormat($transaction->created_at) }}</a></h6> --}}
                                            </div>
                                        </div>

                                        @php
                                            $data=explode(' ',dateFormat($transaction->created_at));
                                        @endphp

                                        <span class="mb-lg-0 mb-3 text-black px-2">{{ $data[0] }} <br> {{ $data[1].' '.$data[2] }}</span>


                                                <!-- Amount -->
                                                @if($transaction->transaction_type_id == Deposit)
                                                @if($transaction->subtotal > 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                        <p class="mb-0 text-left text-success">+{{ formatNumber($transaction->subtotal) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @endif
                                                @elseif($transaction->transaction_type_id == Withdrawal)
                                                <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                    <p class="mb-0 text-left text-danger">-{{ formatNumber($transaction->subtotal) }}</p>
                                                    <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                </span>
                                                @elseif($transaction->transaction_type_id == Payment_Received)
                                                @if($transaction->subtotal > 0)
                                                    @if($transaction->status == 'Refund')
                                                        <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                            <p class="mb-0 text-left text-danger">-{{ formatNumber($transaction->subtotal) }}</p>
                                                            <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                        </span>
                                                    @else
                                                        <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                            <p class="mb-0 text-left text-success">+{{ formatNumber($transaction->subtotal) }}</p>
                                                            <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                        </span>
                                                    @endif
                                                @elseif($transaction->subtotal == 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2" >
                                                        <p class="mb-0">{{ formatNumber($transaction->subtotal) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @elseif($transaction->subtotal < 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                        <p class="mb-0 text-left text-danger">{{ formatNumber($transaction->subtotal) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @endif
                                                @else
                                                @if($transaction->total > 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                        <p class="mb-0 text-left text-success">{{ $transaction->currency->type != 'fiat' ? "+".$transaction->total : "+".formatNumber($transaction->total) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @elseif($transaction->total == 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                        <p class="mb-0">{{ formatNumber($transaction->total) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @elseif($transaction->total < 0)
                                                    <span class="mb-0 mb-lg-0 mb-3 text-black font-w600 px-2">
                                                        <p class="mb-0 text-left text-danger">{{ $transaction->currency->type != 'fiat' ? $transaction->total : formatNumber($transaction->total) }}</p>
                                                        <p class="mb-0 text-left">{{ $transaction->currency->code }}</p>
                                                    </span>
                                                @endif
                                                @endif
                                        {{-- Card Type --}}
                                        @if(empty($transaction->merchant_id))

                                        @if(!empty($transaction->end_user_id))
                                            <td class="text-left">
                                                @if($transaction->transaction_type_id)
                                                    @if($transaction->transaction_type_id==Request_From)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b class="mb-0">@lang('Request Sent')</b>
                                                    @elseif($transaction->transaction_type_id==Request_To)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b class="mb-0">@lang('Request Received')</b>

                                                    @elseif($transaction->transaction_type_id == Transferred)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b>@lang('Transferred')</b>

                                                    @elseif($transaction->transaction_type_id == Received)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b class="mb-0">@lang('Received')</b>

                                                    @elseif($transaction->transaction_type_id == Order_Received)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b class="mb-0">Order Received</b>
                                                    @elseif($transaction->transaction_type_id == Order_Product)
                                                        <b class="mb-0">
                                                            {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                        </b>
                                                        <b class="mb-0">Order Product</b>
                                                    @else
                                                        <b class="mb-0">{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</b>
                                                    @endif
                                                @endif
                                            </td>
                                            @else

                                            <?php
                                                    if (isset($transaction->payment_method->name))
                                                    {
                                                        if ($transaction->payment_method->name == 'Mts')
                                                        {
                                                            $payment_method = getCompanyName();
                                                        }
                                                        else
                                                        {
                                                            $payment_method = $transaction->payment_method->name;
                                                        }
                                                    }
                                                ?>
                                                <td class="text-left">
                                                    <b class="mb-0">
                                                        @if($transaction->transaction_type->name == 'Deposit')
                                                            @if ($transaction->payment_method->name == 'Bank')
                                                                {{ $payment_method }} ({{ $transaction->bank->bank_name }})
                                                            @else
                                                                @if(!empty($payment_method))
                                                                    {{ $payment_method }}
                                                                @endif
                                                            @endif
                                                        @endif

                                                        @if($transaction->transaction_type->name == 'Withdrawal')
                                                            @if(!empty($payment_method))
                                                                {{ $payment_method }}
                                                            @endif
                                                        @endif

                                                        @if($transaction->transaction_type->name == 'Transferred' || $transaction->transaction_type->name == 'Request_From' && $transaction->user_type = 'unregistered')
                                                            {{ ($transaction->email) ? $transaction->email : $transaction->phone }} <!--for send money by phone - mobile app-->
                                                        @endif

                                                        <br>
                                                    @if($transaction->transaction_type_id)
                                                        @if($transaction->transaction_type_id==Request_From)
                                                          @lang('Request Sent')
                                                        @elseif($transaction->transaction_type_id==Request_To)
                                                           @lang('Request Received')

                                                        @elseif($transaction->transaction_type_id == Withdrawal)
                                                            @lang('Payout')
                                                        @else
                                                            {{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}
                                                        @endif
                                                    @endif
                                                    </b>
                                                </td>
                                            @endif
                                        @else
                                        <td class="text-left">
                                            <p class="mb-0 ">{{ $transaction->merchant->business_name }}</p>
                                            @if($transaction->transaction_type_id)
                                                <p class="mb-0">{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                            @endif
                                        </td>
                                         @endif

                                    <!-- Status -->

                                        <a href="#" class="mb-lg-0 mb-3 btn  btn-md btn-rounded mx-2 @if( $transaction->status=='Blocked') btn-outline-warning @elseif($transaction->status=='Success') btn-outline-success @else btn-outline-dark @endif"     id="status_{{$transaction->id}}">

                                          {{
                                                (
                                                    ($transaction->status == 'Blocked') ? __("Cancelled") :
                                                    (
                                                        ($transaction->status == 'Refund') ? __("Refunded") : __($transaction->status)
                                                    )
                                                )
                                            }}
                                        <br>
                                        </a>


                                        <span class="accordion__header--indicator"></span>
                                    </div>

                                        <div id="default_collapseOne{{ $key }}" class="collapse accordion__body" data-parent="#accordion-one">
                                            <div class="col-md-12 col-sm-12 text-left d-flex justify-content-between flex-wrap" id="html_{{$key}}"></div>
                                        {{-- <div class="d-flex flex-wrap align-items-center accordion__body--text p-0">
                                            <div class="mr-3 mb-3">
                                            <p class="fs-12 mb-2">ID Payment</p>
                                            <span class="text-black font-w500">#00123521kkkkkkkkkk</span> </div>
                                            <div class="mr-3 mb-3">
                                            <p class="fs-12 mb-2">Payment Method</p>
                                            <span class="text-black font-w500">MasterCard 404</span> </div>
                                            <div class="mr-3 mb-3">
                                            <p class="fs-12 mb-2">Invoice Date</p>
                                            <span class="text-black font-w500">April 29, 2020</span> </div>
                                            <div class="mr-3 mb-3">
                                            <p class="fs-12 mb-2">Due Date</p>
                                            <span class="text-black font-w500">June 5, 2020</span> </div>
                                            <div class="mr-3 mb-3">
                                            <p class="fs-12 mb-2">Date Paid</p>
                                            <span class="text-black font-w500">June 4, 2020</span> </div>
                                            <div class="d-flex p-3 rounded bgl-dark align-items-center mb-3">
                                            <svg class="mr-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 1C9.82441 1 7.69767 1.64514 5.88873 2.85384C4.07979 4.06253 2.66989 5.7805 1.83733 7.79049C1.00477 9.80047 0.786929 12.0122 1.21137 14.146C1.6358 16.2798 2.68345 18.2398 4.22183 19.7782C5.76021 21.3166 7.72022 22.3642 9.85401 22.7887C11.9878 23.2131 14.1995 22.9953 16.2095 22.1627C18.2195 21.3301 19.9375 19.9202 21.1462 18.1113C22.3549 16.3023 23 14.1756 23 12C22.9966 9.08368 21.8365 6.28778 19.7744 4.22563C17.7122 2.16347 14.9163 1.00344 12 1ZM12 21C10.22 21 8.47992 20.4722 6.99987 19.4832C5.51983 18.4943 4.36628 17.0887 3.68509 15.4442C3.0039 13.7996 2.82567 11.99 3.17294 10.2442C3.5202 8.49836 4.37737 6.89471 5.63604 5.63604C6.89472 4.37737 8.49836 3.5202 10.2442 3.17293C11.99 2.82567 13.7996 3.0039 15.4442 3.68509C17.0887 4.36627 18.4943 5.51983 19.4832 6.99987C20.4722 8.47991 21 10.22 21 12C20.9971 14.3861 20.0479 16.6736 18.3608 18.3608C16.6736 20.048 14.3861 20.9971 12 21Z" fill="#A4A4A4"/>
                                                <path d="M12 9C11.7348 9 11.4804 9.10536 11.2929 9.29289C11.1054 9.48043 11 9.73478 11 10V17C11 17.2652 11.1054 17.5196 11.2929 17.7071C11.4804 17.8946 11.7348 18 12 18C12.2652 18 12.5196 17.8946 12.7071 17.7071C12.8947 17.5196 13 17.2652 13 17V10C13 9.73478 12.8947 9.48043 12.7071 9.29289C12.5196 9.10536 12.2652 9 12 9Z" fill="#A4A4A4"/>
                                                <path d="M12 8C12.5523 8 13 7.55228 13 7C13 6.44771 12.5523 6 12 6C11.4477 6 11 6.44771 11 7C11 7.55228 11.4477 8 12 8Z" fill="#A4A4A4"/>
                                            </svg>
                                            <p class="mb-0 fs-14">Lorem ipsum dolor sit<br>
                                                amet, consectetur </p>
                                            </div>
                                        </div> --}}
                                        </div>
                                    </div>

                                    @endforeach
                                @else
                                <p class="mb-0">@lang('message.dashboard.left-table.no-transaction')</p>
                            @endif
                        </div>
                      </div>
                    </div>
                  </div>
            </div>

                </div>
                </div>

    </div>
    </div>
</div>




@endsection

@section('js')

    <!--daterangepicker-->
    {{-- <script src="{{asset('public/user_dashboard/js/daterangepicker.js')}}" type="text/javascript"></script> --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @include('user_dashboard.layouts.common.check-user-status')

    <script>
        $(window).on('load', function()
        {
            var sDate;
            var eDate;
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    sDate = moment(start, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#startfrom').val(sDate);
                    eDate = moment(end, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#endto').val(eDate);
                    $('#daterange-btn span').html(sDate + ' - ' + eDate);
                }
            )

            var startDate = "{!! $from !!}";
            var endDate = "{!! $to !!}";
            if (startDate == '') {
                $('#daterange-btn span').html('<i class="fa fa-calendar"></i> {{ __('message.dashboard.transaction.date-range') }}');
            } else {
                $('#daterange-btn span').html(startDate + ' - ' + endDate);
            }
        });
    </script>
{{-- <script>
$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
                    sDate = moment(start, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#startfrom').val(sDate);
                    eDate = moment(end, 'MMMM D, YYYY').format('DD-MM-YYYY');
                    $('#endto').val(eDate);
                    $('#daterange-btn span').html(sDate + ' - ' + eDate);
                });



});
</script> --}}

    @include('common.new-user-transactions-scripts')

@endsection
