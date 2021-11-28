@extends('user_dashboard.layouts.app')

@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">
@endsection

@section('content')
    <section class="section-06 history padding-30">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-xs-12 mb20 marginTopPlus">
                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                Edit Store                                  
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="post" action="{{ url('stores/update/'.$store->id) }}" id="edit_store_form" enctype="multipart/form-data">
                                        {{csrf_field()}}

                                        <input type="hidden" value="{{ $store->id }}"  id="store_id" />

                                        <div class="row">

                                            {{-- Store Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_name"> Store Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="store_name" id="store_name" value="{{ $store->name }}">
                                                @if($errors->has('store_name'))
                                                    <span class="error">
                                                        {{ $errors->first('store_name') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Store Code --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_code"> Store Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="store_code" id="store_code" value="{{ $store->store_code }}">
                                                @if($errors->has('store_code'))
                                                    <span class="error" id="store_code_error">
                                                        {{ $errors->first('store_code') }}
                                                    </span>
                                                @endif
                                                <p  id="store_code_error"></p>
                                                <p  id="store_code_ok"></p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ $store->description }}">
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Phone --}}
                                            <div class="form-group col-md-6">
                                                <label for="phone"> Phone </label>
                                                <input type="text" class="form-control" name="phone" id="phone" value="{{ $store->phone }}">
                                                @if($errors->has('phone'))
                                                    <span class="error">
                                                        {{ $errors->first('phone') }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- Email --}}
                                            <div class="form-group col-md-6">
                                                <label for="email"> Email </label>
                                                <input type="text" class="form-control" name="email"  value="{{ $store->email }}">
                                                @if($errors->has('email'))
                                                    <span class="error">
                                                        {{ $errors->first('email') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Website --}}
                                            <div class="form-group col-md-6">
                                                <label for="website"> Website </label>
                                                <input type="text" class="form-control" name="website" id="website" value="{{ $store->website }}">
                                                @if($errors->has('website'))
                                                    <span class="error">
                                                        {{ $errors->first('website') }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- City --}}
                                            <div class="form-group col-md-6">
                                                <label for="city"> City </label>
                                                <input type="text" class="form-control" name="city" id="city" value="{{ $store->city }}">
                                                @if($errors->has('city'))
                                                    <span class="error">
                                                        {{ $errors->first('city') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Address 1 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_1">Address 1</label>
                                                <input class="form-control" name="address_line_1" id="address_line_1" value="{{ $store->address_line_1 }}">
                                                @if($errors->has('address_line_1'))
                                                    <span class="error">
                                                        {{ $errors->first('address_line_1') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- Address 2 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_2">Address 2</label>
                                                <input class="form-control" name="address_line_2" id="address_line_2" value="{{ $store->address_line_2 }}">
                                                @if($errors->has('address_line_2'))
                                                    <span class="error">
                                                        {{ $errors->first('address_line_2') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- State --}}
                                            <div class="form-group col-md-6">
                                                <label for="state"> State </label>
                                                <input type="text" class="form-control" name="state" id="state" value="{{ $store->state }}">
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
                                                <input type="text" class="form-control" name="zip" id="zip" value="{{ $store->zip }}">
                                                @if($errors->has('zip'))
                                                    <span class="error">
                                                        {{ $errors->first('zip') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Country --}}
                                            <div class="form-group col-md-6">
                                                <label for="country"> Country </label>
                                                <input type="text" class="form-control" name="country" id="country" value="{{ $store->country }}">
                                                @if($errors->has('country'))
                                                    <span class="error">
                                                        {{ $errors->first('country') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- Status --}}
                                            <div class="form-group col-md-6">
                                                <label for="status">Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value='Active' {{ $store->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value='Inactive' {{ $store->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>

                                            {{-- Photo --}}
                                            <div class="form-group col-md-6">
                                                <label for="photo"> Photo </label>
                                                <input type="file" name="photo" class="form-control input-file-field" data-rel="{{ isset($store->photo) ? $store->photo : '' }}" id="photo" value="{{ isset($store->photo) ? $store->photo : '' }}">
                                                
                                                <span class="text-danger">{{ $errors->first('photo') }}</span>
                                                <br>
                                                
                                                @if ($store->photo)
                                                    <div class="setting-img">
                                                        <div class="img-wrap">
                                                            <img src='{{ url('public/images/shop/store/' . $store->photo) }}'  class="img-responsive">
                                                            <span style="cursor:pointer;color:white;background: black;border: 1px solid black;border-radius:50%;" class="remove_img_preview">x</span>
                                                        </div>
                                                        
                                                    </div>
                                                @else
                                                    <img src='{{ url('public/dist/img/shop/store.jpg') }}' width="70" height="100" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                   
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                    <a href="{{ url('stores') }}" class="btn btn-cust">Cancel</a>
                                                <button type="submit" class="btn btn-cust pull-right" id="edit_store">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="edit_store_text">Update</span>
                                                </button>
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

<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/backend/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

<script>

     // flag for button disable/enable
    var store_error_code = false;

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!store_error_code) {
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

    $('#edit_store_form').validate({
        rules: {
            store_name: {
                required: true,
            },
            photo: {
                extension: "png|jpg|jpeg|gif|bmp",
            },
            email: {
                email: true,
            },
            website: {
                url: true,
            }
        },
        messages: {
          photo: {
            extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
          },
        },
        submitHandler: function(form)
        {
            $("#edit_store").attr("disabled", true);
            $(".spinner").show();
            $("#edit_store_text").text('Updating...');
            $('#edit_store').click(false);
            form.submit();
        }
    });

    $(document).ready(function()
    {
        $("#store_code").on('input', function(e)
        {
            var store_code = $('#store_code').val();
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/stores/update-store-code-check",
                dataType: "json",
                data: {
                    'store_code': store_code,
                }
            })
            .done(function(response) {
                if (response.status == false) {

                    $('#store_code_error').addClass('error').text(response.fail).css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                    $('#store_code_ok').text('');
                    store_error_code = true;
                    enableDisableButton();

                } else if (response.status == true) {

                    if (!($('#store_code').val())) {

                        $('#store_code_ok').text('');
                        $('#store_code_error').addClass('error').text('Store code is required').css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                        store_error_code = true;
                        enableDisableButton();

                    } else if (store_code.length <= 10 && store_code.length > 3) {

                        $('#store_code_ok').addClass('success').text(response.success).css({'color':'#367C3D', 'font-weight':'700','font-size':'14px'});
                        $('#store_code_error').text('');
                        store_error_code = false;
                        enableDisableButton();

                    } else if (store_code.length > 10 || store_code.length < 4){
                        $('#store_code_error').addClass('error').text('Store code must be between 4 to 10 characters.').css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                        $('#store_code_ok').text('');
                        store_error_code = true;
                        enableDisableButton();

                    }
                }
            });
        });
    });

    // Delete Store photo
    $(document).ready(function() 
    {
        $('.remove_img_preview').click(function()
        {
            var photo = $('#photo').attr('data-rel');
            var store_id = $('#store_id').val();
        
            if(photo) {
                $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "POST",
                url : SITE_URL+"/stores/delete-store-photo",
                data: {
                    'photo' : photo,
                    'store_id' : store_id,
                },
                dataType : 'json',
                success: function(reply)
                {
                    if (reply.success == 1) {
                        swal({
                            title: "Deleted!", 
                            text: reply.message, 
                            type: "success"
                        }, function () {
                            location.reload();
                        });
                    } else {
                        alert(reply.message);
                        location.reload();
                    }
                }
            });
        }
        });
    });

</script>

@endsection
   

