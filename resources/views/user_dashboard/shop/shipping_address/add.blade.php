@extends('user_dashboard.layouts.new_app')

@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/select2/select2.min.css') }}">
    <style>
        .select2-container .select2-selection--single {
            height: 36px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-top:5px !important;
        }
    </style>
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-xs-12 mb20 marginTopPlus">
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
                                Shipping Address
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="post" action="{{ url('shipping-address') }}" id="create_address_form" enctype="multipart/form-data">
                                        {{csrf_field()}}

                                        <input type="hidden" value="{{ base64_encode(isset($shippingAddress->id) ? $shippingAddress->id : '') }}" name="shipping_address_id" id="shippingAddress_id" />

                                        <div class="row">
                                            {{-- shippingAddress Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_name"> User name</label>
                                                <input type="text" class="form-control" name="title" id="title" value="{{ isset($shippingAddress) ? $shippingAddress->user->first_name . ' ' . $shippingAddress->user->last_name : '' }}" placeholder="It will be placed automatically" disabled>
                                                @if($errors->has('title'))
                                                    <span class="error">
                                                        {{ $errors->first('title') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ isset($shippingAddress->description) ? $shippingAddress->description : '' }}" >
                                                {{-- placeholder="Enter description" --}}
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        {{-- Address Line --}}
                                        <div class="row">
                                            {{-- address line 1 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_1"> Address line 1</label>
                                                <input type="text" class="form-control" name="address_line_1" id="address_line_1" value="{{ isset($shippingAddress->address_line_1) ? $shippingAddress->address_line_1 : '' }}" >
                                                {{-- placeholder="Address Line 1" --}}
                                                @if($errors->has('address_line_1'))
                                                    <span class="error">
                                                        {{ $errors->first('address_line_1') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- address line 2 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_2"> Address line 2 </label>
                                                <input type="text" class="form-control" name="address_line_2" id="address_line_2" value="{{ isset($shippingAddress->address_line_2) ? $shippingAddress->address_line_2 : '' }}" >
                                                {{-- placeholder="Address Line 2" --}}
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- City --}}
                                            <div class="form-group col-md-6">
                                                <label for="city"> City </label>
                                                <input type="text" class="form-control" name="city" id="city" value="{{ isset($shippingAddress->city) ? $shippingAddress->city : '' }}" >
                                                {{-- placeholder="Add your city" --}}
                                                @if($errors->has('city'))
                                                    <span class="error">
                                                        {{ $errors->first('city') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- State --}}
                                            <div class="form-group col-md-6">
                                                <label for="state"> State </label>
                                                <input type="text" class="form-control" name="state" id="state" value="{{ isset($shippingAddress->state) ? $shippingAddress->state : '' }}" >
                                                {{-- placeholder="Add  your state" --}}
                                                @if($errors->has('state'))
                                                    <span class="error">
                                                        {{ $errors->first('state') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Zip --}}
                                            <div class="form-group col-md-6">
                                                <label for="zip"> Zip </label>
                                                <input type="text" class="form-control" name="zip" id="zip" value="{{ isset($shippingAddress->zip) ? $shippingAddress->zip : '' }}" >
                                                {{-- placeholder="Add your zip" --}}
                                                @if($errors->has('zip'))
                                                    <span class="error">
                                                        {{ $errors->first('zip') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Country --}}
                                            <div class="form-group col-md-6">
                                                <label for="country"> Country </label>
                                                <input type="text" class="form-control" name="country" id="country" value="{{ isset($shippingAddress->country) ? $shippingAddress->country : '' }}" >
                                                {{-- placeholder="Add your country" --}}
                                                @if($errors->has('country'))
                                                    <span class="error">
                                                        {{ $errors->first('country') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Email --}}
                                            <div class="form-group col-md-6">
                                                <label for="email"> Email </label>
                                                <input type="text" class="form-control" name="email"  value="{{ isset($shippingAddress->email) ? $shippingAddress->email : '' }}" >
                                                {{-- placeholder="Add your email" --}}
                                                @if($errors->has('email'))
                                                    <span class="error">
                                                        {{ $errors->first('email') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Phone --}}
                                            <div class="form-group col-md-6">
                                                <label for="phone"> Phone </label>
                                                <input type="text" class="form-control" name="phone" id="phone" value="{{ isset($shippingAddress->phone) ? $shippingAddress->phone : '' }}" >
                                                {{-- placeholder="Add your phone" --}}
                                                @if($errors->has('phone'))
                                                    <span class="error">
                                                        {{ $errors->first('phone') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- Fax --}}
                                            <div class="form-group col-md-6">
                                                <label for="fax"> Fax </label>
                                                <input type="text" class="form-control" name="fax" id="fax" value="{{ isset($shippingAddress->fax) ? $shippingAddress->fax : '' }}" >
                                                {{-- placeholder="Add your fax" --}}
                                                @if($errors->has('fax'))
                                                    <span class="error">
                                                        {{ $errors->first('fax') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Website --}}
                                            <div class="form-group col-md-6">
                                                <label for="website"> Website </label>
                                                <input type="text" class="form-control" name="website" id="website" value="{{ isset($shippingAddress->website) ? $shippingAddress->website : '' }}" >
                                                {{-- placeholder="Add your website" --}}
                                                @if($errors->has('website'))
                                                    <span class="error">
                                                        {{ $errors->first('website') }}
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="clearfix"></div>
                                        <br>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <a href="{{ url('stores') }}" class="btn btn-cust">Back</a>

                                                @if(!empty($shippingAddress))
                                                    <button type="submit" class="btn btn-cust pull-right" id="create_store">
                                                        <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="create_store_text">Update</span>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-cust pull-right" id="create_store">
                                                        <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="create_store_text">Add</span>
                                                    </button>
                                                @endif

                                            </div>
                                        </div>


                                    </form>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<script src="{{ asset('public/backend/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/backend/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

<script>

    $('.select2').select2({});
    // flag for button disable/enable
    var shippingAddress_error_code = false;

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!shippingAddress_error_code) {
            $('form').find("button[type='submit']").prop('disabled',false);
        } else {
            $('form').find("button[type='submit']").prop('disabled',true);
        }
    }

    $.validator.setDefaults({
        highlight: function (element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorPlacement: function (error, element)
        {
            if (element.prop('name') === 'message')
            {
                $('#error-message').html(error);
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('#create_address_form').validate({
        rules: {
            address_line_1: {
                required: true,
            },
            city: {
                required: true,
            },
            state: {
                required: true,
            },
            zip: {
                required: true,
            },
            country: {
                required: true,
            },
            stock: {
                required: true,
            },
        },
        // messages: {
        //   photo: {
        //     extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
        //   },
        //   store_code: {
        //     maxlength: "Not more than 10 Characters!"
        //   },
        // },
        submitHandler: function(form)
        {
            $("#create_store").attr("disabled", true);
            $(".spinner").show();
            $("#create_store_text").text('Creating . . .');
            $('#create_store').click(false);
            form.submit();
        }
    });


</script>

@endsection


