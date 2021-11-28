@extends('user_dashboard.layouts.app')

@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/select2/select2.min.css') }}">
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
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
                                Edit Product Category                                  
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="post" action="{{ url('product-categories/update') }}" id="edit_product_category_form" enctype="multipart/form-data">
                                        {{csrf_field()}}

                                        <input type="hidden" value="{{ base64_encode($productCategory->id) }}" name="id" id="product_category_id" />

                                        <div class="row">
                                            {{-- product Category Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="name"> Product Category Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name" value="{{ $productCategory->name }}">
                                                @if($errors->has('name'))
                                                    <span class="error">
                                                        {{ $errors->first('name') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ $productCategory->description }}">
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">

                                            {{-- Store --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_id">Store<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="store_id" id="store_id">
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}"
                                                            {{ $store->id == $productCategory->store_id ? 'selected' : '' }} >
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

                                            {{-- Status --}}
                                            <div class="form-group col-md-6">
                                                <label for="status">Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value='Active' {{ $productCategory->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value='Inactive' {{ $productCategory->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Photo --}}
                                            <div class="form-group col-md-12">
                                                <label for="photo"> Photo </label>
                                                <input type="file" name="photo" class="form-control input-file-field" data-rel="{{ isset($productCategory->photo) ? $productCategory->photo : '' }}" id="photo" value="{{ isset($productCategory->photo) ? $productCategory->photo : '' }}">
                                                
                                                <span class="text-danger">{{ $errors->first('photo') }}</span>
                                                <br>
                                                @if ($productCategory->photo)
                                                    <div class="setting-img">
                                                        <div class="img-wrap">
                                                            <img src='{{ url('public/images/shop/product_category/' . $productCategory->photo) }}'  class="img-responsive">
                                                            <span style="cursor:pointer;color: white; background: black;border: 1px solid black;border-radius:50%;" class="remove_img_preview">x</span>
                                                        </div>
                                                    </div>
                                                @else
                                                
                                                    <img src='{{ url('public/dist/img/shop/product_category.png') }}' width="70" height="100" class="img-responsive">
                                                @endif
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <a href="{{ url('product-categories') }}" class="btn btn-cust">Cancel</a>
                                                <button type="submit" class="btn btn-cust pull-right" id="edit_product_category">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="edit_product_category_text">Update</span>
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

    // flag for button disable/enable
    var product_category_error_code = false;

    $('.select2').select2({});

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!product_category_error_code) {
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

    $('#edit_product_category_form').validate({
        rules: {
            name: {
                required: true,
            },
            store_id: {
                required: true,
            },
            photo: {
                extension: "png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          photo: {
            extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
          },
        },
        submitHandler: function(form)
        {
            $("#edit_product_category").attr("disabled", true);
            $(".spinner").show();
            $("#edit_product_category_text").text('Updating . . .');
            $('#edit_product_category').click(false);
            form.submit();
        }
    });

    // Delete product category photo
    $(document).ready(function() 
    {
        $('.remove_img_preview').click(function() {
            var photo = $('#photo').attr('data-rel');
            var product_category_id = $('#product_category_id').val();
            
            if(photo) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type : "POST",
                    url : SITE_URL+"/product-categories/delete-product-categories-photo",
                    data: {
                        'photo' : photo,
                        'product_category_id' : product_category_id,
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
   

