@extends('user_dashboard.layouts.app')
@section('content')

<section class="section-06 history padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-xs-12 mb20 marginTopPlus">
                @include('user_dashboard.layouts.common.alert')
                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left">@lang('message.dashboard.vouchers.left-top.title')</h4>
                    </div>
                    <div class="card-body" style="overflow: auto; padding: 30px;">
                        <!-- Create Voucher start -->
                        <form action="{{url('vouchers')}}" method="post" accept-charset="utf-8" id="voucher_create_form">
                            <input type="hidden" value="{{csrf_token()}}" name="_token">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>@lang('message.dashboard.vouchers.left-top.amount')</label>

                                        <input class="form-control" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00" type="text">

                                        <span class="amountLimit" id="amountLimit" style="color: red;font-weight: bold"></span>

                                        @if($errors->has('amount'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('amount') }}</strong>
                                        </span>
                                        @endif

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('message.dashboard.vouchers.left-top.currency')</label>
                                        <select class="form-control" name="currency_id" id="currency_id">
                                            @foreach($wallets as $result)
                                            <option data-wallet="{{$result->id}}" value="{{$result->currency_id}}">{{$result->currency->code}}</option>
                                            @endforeach
                                        </select>
                                        <small id="walletHelp" class="form-text text-muted" style="display:none;">
                                            @lang('message.dashboard.deposit.fee')(<span class="pFees">0</span>%+<span class="fFees">0</span>)
                                            @lang('message.dashboard.deposit.total-fee') <span class="total_fees">0.00</span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="" style="padding-top: 26px">
                                        <button type="submit" class="btn btn-secondary" id="voucher_money">
                                            <i class="spinner1 fa fa-spinner fa-spin" style="display: none;"></i> <span id="voucher_text">@lang('message.dashboard.button.create')</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Create Voucher End -->
                    </div>
                </div>

                <br>

                <!-- Activate Voucher start -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left">@lang('message.dashboard.vouchers.left-bottom.title')</h4>
                    </div>

                    <div class="card-body" style="overflow: auto; padding: 30px;">
                        <form action="{{url('voucher/activated')}}" method="post" accept-charset="utf-8" id="voucher_activate_form">
                            <input type="hidden" value="{{csrf_token()}}" name="_token">

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>@lang('message.dashboard.vouchers.left-bottom.code')</label>
                                        <input class="form-control" name="code" id="code" type="text">
                                        @if($errors->has('code'))
                                        <span class="error">
                                            {{ $errors->first('code') }}
                                        </span>
                                        @endif
                                        <span id="voucher-code-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="voucher_activate" style="padding-top: 26px">
                                        <button type="submit" class="btn btn-secondary" id="voucher_activate"><i class="spinner2 fa fa-spinner fa-spin" style="display: none;"></i> <span id="voucher_activate_text">@lang('message.dashboard.button.activate')</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Activate Voucher start -->
            </div>

            <div class="col-md-7 col-xs-12 mb20 marginTopPlus">
                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left trans-inline">@lang('message.dashboard.vouchers.right.title')</h4>
                    </div>

                    <div class="table-responsive">
                        @if($list->count() > 0)
                        <table class="table recent_activity">
                            <thead>
                                <tr>
                                    <td><strong>@lang('message.dashboard.vouchers.right.code')</strong></td>
                                    <td><strong>@lang('message.dashboard.vouchers.right.amount')</strong></td>
                                    <td><strong>@lang('message.dashboard.vouchers.right.status')</strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $result)
                                <tr>
                                    <td @if($result->status == 'Success' && $result->redeemed == 'No') style="color:black;"
                                        @elseif($result->status == 'Blocked' && $result->redeemed =='No')
                                        style="color:#FF0000;"
                                        @else
                                        style="color:green;"
                                        @endif >{{$result->code}}</td>
                                    <td>{{ moneyFormat($result->currency->symbol, formatNumber($result->amount)) }}</td>

                                    <td> {{ (($result->status == 'Blocked') ? "Cancelled" :(($result->status == 'Refund') ? "Refunded" : $result->status)) }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @else
                        <h5 style="padding: 15px 10px; ">@lang('message.dashboard.vouchers.right.not-found')</h5>
                        @endif
                    </div>

                    <div class="card-footer">
                        {{ $list->links('vendor.pagination.bootstrap-4') }}
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
<script src="{{asset('public/user_dashboard/js/sweetalert/sweetalert-unpkg.min.js')}}" type="text/javascript"></script>


<script>
    jQuery.extend(jQuery.validator.messages, {
        required: "{{__('This field is required.')}}",
    });

    $('#voucher_create_form').validate({
        rules: {
            amount: {
                required: true,
            },
        },
        submitHandler: function(form) {
            $("#voucher_money").attr("disabled", true);
            $(".spinner").show();
            var pretext = $("#voucher_text").text();
            $("#voucher_text").text('Creating...');
            form.submit();
            setTimeout(function() {
                $("#voucher_text").text(pretext);
                $("#voucher_money").removeAttr("disabled");
                $(".spinner").hide();
            }, 1000);
        }
    });

    $('#voucher_activate_form').validate({
        rules: {
            code: {
                required: true,
            },
        },
        submitHandler: function(form) {
            $("#voucher_activate").attr("disabled", true);
            $(".spinner").show();
            var pretext = $("#voucher_activate_text").text();
            $("#voucher_activate_text").text('Activating...');
            form.submit();
            setTimeout(function() {
                $("#voucher_activate_text").text(pretext);
                $("#voucher_activate").removeAttr("disabled");
                $(".spinner").hide();
            }, 1000);
        }
    });

    $('#currency_id, #amount').on('change keyup', function(e) {
        var wallet_id = $('#currency_id option:selected').attr('data-wallet');

        if (wallet_id) {
            $('#wallet_id').val(wallet_id);
            var amount = $('#amount').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'post',
                url: '{{url("amount-limit")}}',
                data: {
                    'amount': amount,
                    'wallet_id': wallet_id,
                    'transaction_type_id': '{{Voucher_Activated}}'
                },
                success: function(res) {

                    if (res.success.status == 200) {
                        $('#voucher_money').attr('disabled', false);
                        $('#walletHelp').show();

                        $('.total_fees').html(res.success.totalFeesHtml);
                        $('.pFees').html(res.success.pFeesHtml);
                        $('.fFees').html(res.success.fFeesHtml);

                        if (res.success.totalAmount > res.success.balance) {
                            $('#amountLimit').html("{{__('Not have enough balance!') }}");
                            $('#voucher_money').attr('disabled', true);
                        } else {
                            $('#amountLimit').html('');
                            $('#voucher_money').removeAttr('disabled');
                        }
                    } else {
                        if (amount == '') {
                            $('#amountLimit').text('');
                            $('#walletHelp').hide();
                        } else {
                            $('#amountLimit').text(res.success.message).css('font-size', '14px');
                            $('#walletHelp').hide();
                        }
                        $('#voucher_money').attr('disabled', true);
                        return false;
                    }
                    $('#amount').focus();
                }
            });
        }
    });


    $(document).on('input', '#code', function() {
        var code = $('#code').val();
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL + "/voucher/checkVoucherCode",
                dataType: "json",
                data: {
                    'code': code,
                }
            })
            .done(function(response) {
                if (response.status == 401) {
                    if (code.length == 0) {
                        $('#voucher-code-error').html('');
                    } else {
                        $('#voucher-code-error').addClass('error').html(response.error).css("font-weight", "bold");
                        $('form').find("button[type='submit']").prop('disabled', true);
                    }
                } else {
                    $('#voucher-code-error').html('');
                    $('form').find("button[type='submit']").prop('disabled', false);
                }
            });
    });

    function executeVoucherActivationQrCode(endpoint, voucherId, voucherCode) {
        if (voucherId != '') {
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL + endpoint,
                    dataType: "json",
                    data: {
                        'voucherId': voucherId,
                        'voucherCode': voucherCode,
                    },
                    beforeSend: function() {
                        $('.preloader').show();
                    },
                })
                .done(function(response) {
                    if (response.status == true) {
                        $('.voucher-activation-qr-code').html(`<br>
                      <p style="font-weight: bold;">Scan QR Code</p>
                      <br>
                      <img src="https://api.qrserver.com/v1/create-qr-code/?data=${response.secret}&amp;size=200x200"/>`);
                        setTimeout(function() {
                            $('.preloader').hide();
                        }, 2000);
                    }
                })
                .fail(function(error) {
                    console.log(error);
                });
        } else {
            $('.express-merchant-qr-code').html('');
        }
    }

    //generate express merchant qr code
    $(document).on('click', '.generateVoucherActivationQrCode', function(e) {
        var voucherId = $(this).attr('data-voucher-id');
        var voucherCode = $(this).attr('data-voucher-code');
        var voucherRedeemed = $(this).attr('data-voucher-redeemed');
        if (voucherRedeemed == 'No') {
            $('.update-voucher-qr-code').show();
        } else {
            $('.update-voucher-qr-code').hide();
        }

        window.localStorage.setItem('voucherId', voucherId);
        window.localStorage.setItem('voucherCode', voucherCode);

        let endpoint = "/voucher/generate-voucher-activation-qr-code";
        executeVoucherActivationQrCode(endpoint, voucherId, voucherCode)
    });

    // Voucher Code print
    $(document).on('click', '#qr-code-print-voucher', function(e) {
        e.preventDefault();

        let voucherId = window.localStorage.getItem('voucherId');

        let printQrCodeUrl = SITE_URL + '/voucher/qr-code-print/' + voucherId + '/voucher';
        $(this).attr('href', printQrCodeUrl);
        window.open($(this).attr('href'), '_blank');

    });

    //update express merchant qr code
    $(document).on('click', '.update-voucher-qr-code', function(e) {
        voucherId = window.localStorage.getItem('voucherId')
        voucherCode = window.localStorage.getItem('voucherCode')
        let endpoint = "/voucher/update-voucher-activation-qr-code";
        executeVoucherActivationQrCode(endpoint, voucherId, voucherCode)
    });
</script>
@endsection