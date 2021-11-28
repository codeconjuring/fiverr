@extends('user_dashboard.layouts.app')

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

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                Edit Product                       
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="post" action="{{ url('products/update') }}" id="edit_product_form" enctype="multipart/form-data">
                                        {{csrf_field()}}

                                        <input type="hidden" value="{{ base64_encode($product->id) }}" name="product_id" id="product_id" />

                                        <div class="row">
                                            {{-- Product Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_name"> Product Name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="title" id="title" value="{{ $product->title }}" placeholder="Name">
                                                @if($errors->has('title'))
                                                    <span class="error">
                                                        {{ $errors->first('title') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Product Code --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_code"> Product Code<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="product_code" id="product_code" value="{{ $product->product_code }}">
                                                @if($errors->has('product_code'))
                                                    <span class="error">
                                                        {{ $errors->first('product_code') }}
                                                    </span>
                                                @endif
                                                <p id="product_code_error"></p>
                                                <p id="product_code_ok"></p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Store Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_id">Store Name<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="store_id" id="store_id">
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}" 
                                                            {{ $store->id == $product->store_id ? 'selected' : '' }}>
                                                            {{ $store->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('store_id'))
                                                    <span class="error">
                                                        {{ $errors->first('store_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Product Category --}}
                                            <div class="form-group col-md-6">
                                                <label for="product_category_id">Product Category<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="product_category_id" id="product_category_id">
                                                    @foreach($productCategories as $productCategory)
                                                        <option value="{{ $productCategory->id }}" 
                                                            {{ $product->product_category_id == $productCategory->id ? 'selected' : '' }}>
                                                            {{ $productCategory->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('product_category_id'))
                                                    <span class="error">
                                                        {{ $errors->first('product_category_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Currency --}}
                                            <div class="form-group col-md-6">
                                                <label for="currency_id">Currency<span class="text-danger">*</span></label>
                                                <select class="form-control" name="currency_id" id="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}" 
                                                            {{ $currency->id == $product->currency_id ? 'selected' : '' }}>
                                                            {{ $currency->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('currency_id'))
                                                    <span class="error">
                                                        {{ $errors->first('currency_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ $product->description }}" placeholder="Description">
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Price --}}
                                            <div class="form-group col-md-6">
                                                <label for="price"> Price<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="price" id="price" value="{{ $product->price }}" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                @if($errors->has('price'))
                                                    <span class="error">
                                                        {{ $errors->first('price') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Stock --}}
                                            <div class="form-group col-md-6">
                                                <label for="stock">Stock<span class="text-danger">*</span></label>
                                                <input class="form-control" name="stock" id="stock" value="{{ $product->stock }}" placeholder="0" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                @if($errors->has('stock'))
                                                    <span class="error">
                                                        {{ $errors->first('stock') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Photo --}}
                                            <div class="form-group col-md-12">
                                                <label for="photo"> Photo </label>
                                                <input type="file" name="photo" class="form-control input-file-field" data-rel="{{ isset($product->photo) ? $product->photo : '' }}" id="photo" value="{{ isset($product->photo) ? $product->photo : '' }}">
                                                
                                                <span class="text-danger">{{ $errors->first('photo') }}</span>
                                                <br>
                                                @if ($product->photo)
                                                    <div class="setting-img">
                                                        <div class="img-wrap">
                                                            <img src='{{ url('public/images/shop/product/' . $product->photo) }}'  class="img-responsive">
                                                            <span style="cursor:pointer;color: white; background: black;border: 1px solid black;border-radius:50%;" class="remove_img_preview">x</span>
                                                        </div>
                                                    </div>
                                                @else
                                                
                                                    <img src='{{ url('public/dist/img/shop/product.jpg') }}' width="70" height="100" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <a href="{{ url('products') }}" class="btn btn-cust">Cancel</a>
                                                <button type="submit" class="btn btn-cust pull-right" id="edit_product">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="edit_product_text">Update</span>
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
<script src="{{ asset('public/backend/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/backend/sweetalert/sweetalert.min.js')}}" type="text/javascript"></script>

<script>
    
    $('.select2').select2({});
    // flag for button disable/enable
    var product_error_code = false;

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!product_error_code) {
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

    $('#edit_product_form').validate({
        rules: {
            title: {
                required: true,
            },
            product_category_id: {
                required: true,
            },
            store_id: {
                required: true,
            },
            currency_id: {
                required: true,
            },
            price: {
                required: true,
                number  : true,
            },
            stock: {
                required: true,
                number  : true,
            },
            photo: {
                extension: "png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          photo: {
            extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
          },
          store_code: {
            maxlength: "Not more than 10 Characters!"
          },
        },
        submitHandler: function(form)
        {
            $("#edit_product").attr("disabled", true);
            $(".spinner").show();
            $("#edit_product_text").text('Updating . . .');
            $('#edit_product').click(false);
            form.submit();
        }
    });

    // Product Unique code check
    $(document).ready(function() {
        $("#product_code").on('input', function(e) {
            var product_code = $('#product_code').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL + "/products/product-code-check-update",
                dataType: "json",
                data: {
                    'product_code': product_code,
                }
            })
            .done(function(response)
            {
                if (response.status == false) {

                    $('#product_code_error').addClass('error').text(response.fail).css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                    $('#product_code_ok').text('');
                    product_error_code = true;
                    enableDisableButton();

                } else if (response.status == true) {

                    if (!($('#product_code').val())) {

                        $('#product_code_ok').text('');
                        $('#product_code_error').addClass('error').text('Product code is required.').css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                        product_error_code = true;
                        enableDisableButton();

                    } else if (product_code.length <= 10 && product_code.length >= 4) {

                        $('#product_code_ok').addClass('success').text(response.success).css({'color':'#367C3D', 'font-weight':'700','font-size':'14px'});
                        $('#product_code_error').text('');
                        product_error_code = false;
                        enableDisableButton();

                    } else if (product_code.length > 10 || product_code.length < 4) {

                        $('#product_code_error').addClass('error').text('Product code must be between 4 to 10 characters.').css({'color':'#FF0000', 'font-weight':'700','font-size':'14px'});
                        $('#product_code_ok').text('');
                        product_error_code = true;
                        enableDisableButton();

                    }
                }
            });
        });
    });

    // Get the ProductCategory List of individual Store
    $(document).ready(function() {
        $("#store_id").on('change', function (e) {
            var store_id = $('#store_id').val();
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL + "/products/get-product-categories",
                dataType: "json",
                data: {
                    'store_id' : store_id,
                }
            })
            .done(function (response) {

                if (response.success == true) {

                    var html       = '';
                    var categories = eval(response.data);

                    html += '<option value="">'+'Select product category'+'</option>';

                    $.each(categories, function(i, c) {
                        html += '<option value="'+c.id+'">'+c.name+'</option>';
                    });
                    $('#product_category_id option').each(function() {
                        $(this).remove();
                    });

                    $('#product_category_id').append(html);

                } else {
                    //$(location).prop('href', 'admin/products');
                    
                    alert('No category found! Please create a Category First !');
                    window.location.href = SITE_URL + '/' + 'admin/product-categories/add';
                }
            });
            
        });
    });


    // Delete product category photo
    $(document).ready(function() 
    {
        $('.remove_img_preview').click(function() {
            var photo = $('#photo').attr('data-rel');
            var product_id = $('#product_id').val();
            if(photo) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type : "POST",
                    url : SITE_URL+"/product/delete-product-photo",
                    data: {
                        'photo' : photo,
                        'product_id' : product_id,
                    },
                    dataType : 'json',
                    success: function(reply) {
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
   

