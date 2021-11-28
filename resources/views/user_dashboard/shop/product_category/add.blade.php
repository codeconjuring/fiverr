@extends('user_dashboard.layouts.app')

@section('css')
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
                                Add Product Category                                  
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="post" action="{{ url('product-categories/store') }}" id="create_product_category_form" enctype="multipart/form-data">
                                        {{csrf_field()}}

                                        {{-- <input type="hidden" value="" name="id" id="id" /> --}}

                                        <div class="row">
                                            {{-- product Category Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="name"> Product Category Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                                                @if($errors->has('name'))
                                                    <span class="error">
                                                        {{ $errors->first('name') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" name="description" id="description" value="{{ old('description') }}">
                                                @if($errors->has('description'))
                                                    <span class="error">
                                                        {{ $errors->first('description') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Photo --}}
                                            <div class="form-group col-md-6">
                                                <label for="photo"> Photo </label>
                                                <input type="file" class="form-control" name="photo" id="photo" value="{{ old('photo') }}" style="padding-top: 4px;padding-bottom: 4px;">
                                                @if($errors->has('photo'))
                                                    <span class="error">
                                                        {{ $errors->first('photo') }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Store --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_id">Store<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="store_id" id="store_id">
                                                    <option value=''>Select Store</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}" >{{ $store->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label id="store_id-error" class="error" for="store_id"></label>
                                                @if($errors->has('store_id'))
                                                    <span class="error">
                                                        {{ $errors->first('store_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <br />
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <a href="{{ url('product-categories') }}" class="btn btn-cust">Cancel</a>
                                                <button type="submit" class="btn btn-cust pull-right" id="create_product_category">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="create_product_category_text">Add</span>
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

<!-- Select2 -->
<script src="{{ asset('public/backend/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('public/user_dashboard/js/additional-methods.min.js')}}" type="text/javascript"></script>

<script>

    $('.select2').select2({});

    // flag for button disable/enable
    var product_category_error_code = false;

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

    $('#create_product_category_form').validate({
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
            $("#create_product_category").attr("disabled", true);
            $(".spinner").show();
            $("#create_product_category_text").text('Adding . . .');
            $('#create_product_category').click(false);
            form.submit();
        }
    });

   
</script>

@endsection
   

