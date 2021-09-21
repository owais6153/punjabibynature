<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Catering</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/images/about/favicon-61379b1f625c7.png') !!}">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<header>@include('front.theme.header')</header>

<body>
	<section class="fifth-sec-home sec-catering" style="background: url('storage/app/public/assets/images/live-catring-video-img.jpg');">
    <div class="container">
        <div class="about-box fifth-section-home">
            <div class="sectionhome-contant">
                <h2 class="sec-head text-left">Live Catering <br>Make It Memorable</h2>

            </div>
        </div>
    </div>
</section>
     <section class="sec2-catering">
     	<div class="container">
     	<h2 class="sec-head">Catering Menu</h2>
     	<div class="cat-aside-wrap">














                    @foreach ($getcategory as $category)
                    <a href="{{URL::to('/product/'.$category->id)}}" class="cat-check border-top-no @if (request()->id == $category->id) active @endif">
                        <p>{{$category->category_name}}</p>
                    </a>
                    @endforeach
                </div>

                <div class="row catering">
     	            <div class="cat-product">
                @csrf
              <h3>{{$category->category_name}}</h3>
                <div class="row">

                    @foreach ($getitem as $item)

                    <div class="col-xl-6 col-md-6">
                        <div class="pro-box">
                            <div class="pro-img">
                                @foreach ($item->variation as $key => $value)
                                    @if($value->sale_price > 0)
                                        <div class="ribbon-wrapper">
                                            <div class="ribbon">ON SALE</div>
                                        </div>
                                    @endif
                                    @break
                                @endforeach
                                
                                @if (Session::get('id'))
                                    @if ($item->is_favorite == 1)
                                        <i class="fas fa-heart i"></i>
                                    @else
                                        <i class="fal fa-heart i" onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="product-details-wrap">
                                <div class="product-details">
                                    <a href="{{URL::to('product-details/'.$item->id)}}">
                                        <h4>{{$item->item_name}}</h4>
                                    </a>
                                    <p class="pro-pricing">
                                        @foreach ($item->variation as $key => $value)
                                            {{$getdata->currency}}{{number_format($value->product_price, 2)}}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                                <div class="product-details">
                                    <p>{{ Str::limit($item->item_description, 255) }}</p>
                                </div>
                            </div>
                             @if (Session::get('id'))
                            @if ($item->item_status == '1')
                                <button class="btn"  onclick="openCartModal('{{$item->id}}')" >{{ trans('labels.add_to_cart') }}</button>

                            @else 
                                <button class="btn" disabled="">{{ trans('labels.unavailable') }}</button>
                            @endif
                        @else
                            @if ($item->item_status == '1')
                                <button class="btn" onclick="openCartModal('{{$item->id}}')">{{ trans('labels.add_to_cart') }}</button>
                            @else 
                                <button class="btn" disabled="">{{ trans('labels.unavailable') }}</button>
                            @endif
                        @endif                        
                        </div>
                    </div>
                    @endforeach
                </div>
                	
            </div>

            <div class="cart-box-catering">
                 <h3>Cart </h3>
                 <div class="cart-catering-body">
                 	<a href="#" class="checkout-btn">Checkout <i class="fas fa-arrow-right"></i></a>
                 	<p>$100.00 minimum for delivery</p>
                 </div> 
            </div>
         </div>
		</div>
     </section>

</body>

<footer>@include('front.theme.footer')</footer>
</html>