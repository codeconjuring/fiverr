@extends('frontend.shop.app')
@section('content')

<div class="shop-page-wrapper mt-3">
    <div class="container">
        <div class="row">

            {{-- sidebar catagori area   --}}
           
                
                
                <div class="col-md-12 pl-md-0">
                    <div class="shop-search-bar">
                        <form action="{{ url('shop/search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
                                <div class="input-group-append">
                                    
                                    <button class="input-group-text" style="cursor:pointer;" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{-- <div class="shop-main-display-image pl-md-2 pt-2">
                        <img src="{{ url('public/frontend/images/banner.png') }}" alt="">
                    </div> --}}
                
            </div>
        </div>
    </div>

    {{-- sidebar catagori area for mobile  --}}
    {{-- <div class="mobile-menu-area">
        <div class="catagori-menu">
            <div class="catagori-inner-menu">
                <div class="catagori-item-menu">
                    <ul>
                        
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        <li>
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items</a>
                        </li>
                        
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                        <li class="catagori-list">
                            <span class="catagori-item-menu-image">
                                <img src="https://via.placeholder.com/50x50" alt="">
                            </span>
                            <a href="#"> items hidden</a>
                        </li>
                    </ul>
                    <div class="view-all-categori">
                        <a  class="btn-view-all open-cate">All Categories</a>
                    </div>
                </div>
            </div>
        </div>  
    </div> --}}
    
    {{-- Product arear --}}
    <div class="shop-page-product-area">
        <div class="container">
            <div class="product-area-heading mb-4">
                <h1>Product</h1>
            </div>
            <div class="row">

                @if (count($products) > 0)

                @foreach ($products as $product)
                    @php
                        $wrap = $product->title;
                        if (strlen($product->title) > 30) 
                        {
                            $array = explode('<br>', wordwrap($product->title, 28, '<br>'));
                            $wrap = $array[0] . '...';
                        }
                    @endphp
                    <div class="col-md-3">
                        <div class="single-product-item-wrapper mb-4">
                            <div class="product-image">
                                <a href="{{ url('shop/product/' . $product->id) }}">
                                    @if(!empty($product->photo))
                                        <img alt="product name" src="{{ url('public/images/shop/product/' . $product->photo) }}" width="197" height="258">
                                    @else
                                        <img alt="product name" src="{{ url('public/dist/img/shop/product.jpg') }}" width="197" height="258">
                                    @endif
                                </a>
                                <div data-link="{{ url('shop/product/' . $product->id) }}" class="overlay"><i class="fa fa-shopping-cart"></i> buy now</div>
                            </div>
                            <div class="product-text">
                                <p class="product-price">{{ moneyFormat($product->currency->symbol,  $product->price) }}</p>
                                <a title="{{ $product->title }}" href="{{ url('shop/product/' . $product->id) }}" class="product-name">{{ $wrap }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

                @else
                    <h1 style="color:red;text-align:center;"><strong>No Product Found !</strong></h1>
                @endif
                
                
            </div>
            
            <hr style="margin-left:10%;margin-right:10%">

            {{-- pagination --}}
            <div class="col-md-6 col-offset-3">
                <div class="container">
                    {!! $products->render() !!}
                </div>
            </div>
            <br>

        </div>
    </div>
    @endsection
    
    
    
    @section('js')
    <script>
        $('.catagori-item-menu ul li a').click(function(e) {
            // e.preventDefault();
            $('a').removeClass('active');
            $(this).addClass('active');
        });
        
        
        // all Catagoris
        // ------------------
        $(document).on('click', '.open-cate', function(){
            $(this).closest('.catagori-menu').find('li.catagori-list').each(function(){
                $(this).slideDown(500, function(){
                    $('.btn-view-all').addClass('close-cate').removeClass("open-cate").html('Close');
                });
            });
            return false;
        });
        
        /* Close Categorie */
        $(document).on('click', '.close-cate', function(){
            $(this).closest('.catagori-menu').find('li.catagori-list').each(function(){
                $(this).slideUp(500, function(){
                    $('.btn-view-all').removeClass('close-cate').addClass("open-cate").html('ALL Catagori <span class="ml-1"><i class="fa fa-angle-right"></i> <i class="fa fa-angle-right"></i></span>');
                });
            });
        });
        
        
        // mobile menu 
        // ------------------
        // $('.mobile-btn').on('click', function(){
            //     $('.mobile-menu-area').addClass('masud-mobile');
            // });
            
            $(document).on('click','.mobile-btn',function(){
                $('.mobile-menu-area').toggleClass('masud-mobile');
            });
            

        $('.overlay').click(function() {

            var productLink = $('.overlay').attr('data-link');
            window.location.href = productLink;
            
        });
            
            
            
        </script>
        @endsection
