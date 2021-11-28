@extends('frontend.shop.app')

@section('title')
    Shop
@endsection

@section('topcategory')
    <div class="block-nav-categori">

        <div class="block-title">
            <span>Categories</span>
        </div>

        <div class="block-content">
            <ul class="ui-categori">
                <?php
                $i = 0;
                foreach ($productCategories as $productCategory) {
                    $i++;

                    if ($i < 12) {
                        ?>
                        <li>
                            <a href="{{ url('shop/'.$productCategory->store_id.'/'.$productCategory->store->slug.'/'. $productCategory->id) }}">
                                <span class="icon">
                                    @if(!empty($productCategory->photo))
                                        <img src="{{ url('public/images/stores/product_category/' . $productCategory->photo)}}" alt="nav-cat">
                                    @else
                                        <img src="{{ url('public/dist/img/product_category.png') }}">    
                                    @endif
                                </span>
                                {{ $productCategory->name }}
                            </a>
                        </li>
                        <?php

                    } else {
                        ?>
                        <li class="cat-link-orther">
                            <a href="{{ url('shop/'.$productCategory->store_id.'/'.$productCategory->store->slug.'/'. $productCategory->id) }}">
                                <span class="icon">
                                    @if(!empty($productCategory->photo))
                                        <img src="{{ url('public/images/stores/product_category/' . $productCategory->photo)}}" alt="nav-cat">
                                    @else
                                        <img src="{{ url('public/dist/img/product_category.png') }}">     
                                    @endif
                                </span>
                                {{ $productCategory->name }}
                            </a>
                        </li>
                        <?php
                    }
                }
               ?>
            </ul>

            <div class="view-all-categori">
                <a  class="open-cate btn-view-all">All Categories</a>
            </div>
        </div>
        
    </div>
@endsection

@section('maincategory')
    <div class="block-nav-categori">

        <div class="block-title">
            <span>Categories</span>
        </div>

        <div class="block-content">
            <ul class="ui-categori">
                <?php
                $i = 0;
                foreach ($productCategories as $productCategory) {
                    $i++;

                    if ($i < 12) {
                        ?>
                        <li>
                            <a href="{{ url('shop/'.$productCategory->store_id.'/'.$productCategory->store->slug.'/'. $productCategory->id) }}">
                                <span class="icon">
                                    @if(!empty($productCategory->photo))
                                        <img src="{{ url('public/images/stores/product_category/' . $productCategory->photo)}}" alt="nav-cat">
                                    @else
                                        <img src="{{url('public/dist/img/product_category.png')}}" class="rounded-circle rounded-circle-custom-trans">                               
                                    @endif
                                </span>
                                {{ $productCategory->name }}
                            </a>
                        </li>
                        <?php

                    } else {
                        ?>
                        <li class="cat-link-orther">
                            <a href="{{ url('shop/'.$productCategory->store_id.'/'.$productCategory->store->slug.'/'. $productCategory->id) }}">
                                <span class="icon">
                                    @if(!empty($productCategory->photo))
                                        <img src="{{ url('public/images/stores/product_category/' . $productCategory->photo)}}" alt="nav-cat">
                                    @else
                                        <img src="{{url('public/dist/img/product_category.png')}}" class="rounded-circle rounded-circle-custom-trans">                               
                                    @endif
                                </span>
                                {{ $productCategory->name }}
                            </a>
                        </li>
                        <?php
                    }
                }
               ?>
            </ul>

            <div class="view-all-categori">
                <a  class="open-cate btn-view-all">All Categories</a>
            </div>
        </div>
        
    </div>
@endsection

@section('banner')
    <div class="block-slide-main slide-opt-7">
        <img src="{{ asset('public/frontend/shop/images/shopping.jpeg') }}" alt="">
    </div>
@endsection

@section('products')
    <div class="block-deals-of-opt2">
        <div class="block-title ">
            <span class="title">Products</span>
        </div>
        
        
        @if (count($products) > 0)
            <!-- Single product -->
            @foreach ($products as $product)
                @php
                    $wrap = $product->title;
                    if (strlen($product->title) > 30) {
                        $array = explode('<br>', wordwrap($product->title, 28, '<br>'));
                        $wrap = $array[0] . '...';
                    }
                @endphp
                <div class="block-content" style="float:left;margin-left:5px; margin-bottom: 15px;">
                    <div class="product-item product-item-opt-2">
                        <div class="product-item-info">
                            <div class="product-item-photo">
                                <a class="product-item-img" href="{{ url('shop/product/'.$product->id) }}">                          
                                    @if(!empty($product->photo))
                                        <img alt="product name" src="{{ url('public/images/stores/product/' . $product->photo) }}" width="208" height="258">
                                    @else
                                        <img alt="product name" src="{{ url('public/dist/img/product.jpg') }}" width="208" height="258">
                                    @endif
                                </a>
                                
                                <button type="button" class="btn btn-cart"><span>Buy Now</span></button>
                                
                            </div>
                            <hr style=" padding:0; margin:0;">
                            <div class="product-item-detail">
                                <div class="clearfix">
                                    <div class="product-item-price">
                                        <span class="price">{{ $product->currency->symbol }} {{ $product->price }}</span>
                                    </div>
                                    
                                </div>
                                <strong class="product-item-name"><a href="{{ url('shop/product/'.$product->id) }}">{{ $wrap }}</a></strong>
                            </div>
                        </div>
                    </div>                                    
                </div>

            @endforeach
            <!-- Single product -->
        @else 
            <h1 style="color:red;text-align:center;"><strong>No Product Found !</strong></h1>
        @endif

    </div>
@endsection

@section('paginate')
    <hr style="margin-left:10%;margin-right:10%">
    <div class="block-showcase-opt7">
        <div class="container">
            {!! $products->render() !!}
        </div>
    </div>
@endsection
