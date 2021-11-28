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
                                {{ isset($customerAddress) ? $customerAddress->user->first_name . ' ' . $customerAddress->user->last_name : ''}} Shipping  Address                       
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12">
                                  
                                        <div class="row">
                                            {{-- customerAddress Name --}}
                                            <div class="form-group col-md-6">
                                                <label for="store_name"> User name</label>
                                                <input type="text" class="form-control" name="title" id="title" value="{{ isset($customerAddress) ? $customerAddress->user->first_name . ' ' . $customerAddress->user->last_name : '' }}" placeholder="It will be placed automatically" disabled>
                                               
                                            </div>
                                            {{-- Description --}}
                                            <div class="form-group col-md-6">
                                                <label for="description"> Description </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->description) ? $customerAddress->description : '' }}" >
                                                
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        {{-- Address Line --}}
                                        <div class="row">
                                            {{-- address line 1 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_1"> Address line 1</label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->address_line_1) ? $customerAddress->address_line_1 : '' }}" >
                                               
                                            </div>
                                            {{-- address line 2 --}}
                                            <div class="form-group col-md-6">
                                                <label for="address_line_2"> Address line 2 </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->address_line_2) ? $customerAddress->address_line_2 : '' }}" >
                                                
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- City --}}
                                            <div class="form-group col-md-6">
                                                <label for="city"> City </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->city) ? $customerAddress->city : '' }}" >
                                              
                                            </div>
                                            {{-- State --}}
                                            <div class="form-group col-md-6">
                                                <label for="state"> State </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->state) ? $customerAddress->state : '' }}" >
                                               
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            {{-- Zip --}}
                                            <div class="form-group col-md-6">
                                                <label for="zip"> Zip </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->zip) ? $customerAddress->zip : '' }}" >
                                               
                                            </div>
                                            {{-- Country --}}
                                            <div class="form-group col-md-6">
                                                <label for="country"> Country </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->country) ? $customerAddress->country : '' }}" >
                                                
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Email --}}
                                            <div class="form-group col-md-6">
                                                <label for="email"> Email </label>
                                                <input type="text" class="form-control" disabled  value="{{ isset($customerAddress->email) ? $customerAddress->email : '' }}" >
                                                
                                            </div>
                                            {{-- Phone --}}
                                            <div class="form-group col-md-6">
                                                <label for="phone"> Phone </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->phone) ? $customerAddress->phone : '' }}" >
                                                
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- Fax --}}
                                            <div class="form-group col-md-6">
                                                <label for="fax"> Fax </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->fax) ? $customerAddress->fax : '' }}" >
                                               
                                            </div>
                                            {{-- Website --}}
                                            <div class="form-group col-md-6">
                                                <label for="website"> Website </label>
                                                <input type="text" class="form-control" disabled value="{{ isset($customerAddress->website) ? $customerAddress->website : '' }}" > 
                                                
                                            </div>

                                        </div>
                                        <div class="clearfix"></div>
                                        <br>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <a href="{{ url('customer-orders') }}" class="btn btn-cust pull-right">Back</a>
                                                
                                            </div>
                                        </div>

                                        
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

