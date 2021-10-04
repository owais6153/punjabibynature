<!DOCTYPE html>
<html>

<head>
	<title>{{$getabout->title}}</title>

	<!-- meta tag -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

	<meta property="og:title" content="{{$getabout->og_title}}" />
	<meta property="og:description" content="{{$getabout->og_description}}" />
	<meta property="og:image" content='{!! asset("storage/app/public/images/about/".$getabout->og_image) !!}' />

	<!----style css---->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/assets/css/custom-style.css') !!}">
	<!-- favicon-icon  -->
	<link rel="icon" href='{!! asset("storage/app/public/images/about/".$getabout->favicon) !!}' type="image/x-icon">

	<!-- font-awsome css  -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/css/font-awsome.css') !!}">

	<!-- fonts css -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/fonts/fonts.css') !!}">

	<!-- bootstrap css -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/css/bootstrap.min.css') !!}">

	<!-- fancybox css -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

	<!-- owl.carousel css -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/css/owl.carousel.min.css') !!}">

	<link href="{!! asset('storage/app/public/assets/plugins/sweetalert/css/sweetalert.css') !!}" rel="stylesheet">
	<!-- style css  -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/css/style.css') !!}">

	<!-- responsive css  -->
	<link rel="stylesheet" type="text/css" href="{!! asset('storage/app/public/front/css/responsive.css') !!}">
</head>

<body>

	<!--*******************
	    Preloader start
	********************-->
	<div id="preloader" style="display: none;">
	    <div class="loader">
	        <img src="{!! asset('storage/app/public/front/images/loader.gif') !!}">
	    </div>
	</div>
	<!--*******************
	    Preloader end
	********************-->

	<!-- navbar -->
	<header>
		<nav class="navbar navbar-expand-lg nav-bar-top">
			<div class="container">
				<a class="navbar-brand" href="{{URL::to('/')}}"><img src='{!! asset("storage/app/public/assets/images/logo-main.png") !!}' alt=""></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<div class="menu-icon">
						<div class="bar1"></div>
						<div class="bar2"></div>
						<div class="bar3"></div>
					</div>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					  
					<ul class="navbar-nav">
						<!-- <li><a href="{{ url('locale/en') }}" ><i class="fa fa-language"></i> EN</a></li>

						<li><a href="{{ url('locale/vi') }}" ><i class="fa fa-language"></i> VI</a></li> -->

						
							<li class="nav-item search">
								<form method="get" action="{{URL::to('/search')}}">
									<div class="search-input">
										<input type="search" id="search-box" name="item" placeholder="{{ trans('messages.search_here') }}" required="" autocomplete="off">
									</div>
									<button type="submit" class="nav-link"><i class="far fa-search"></i></button>
								</form>
								<div id="countryList" class="item-list"></div>
							</li>
							<li class="nav-item cart-btn dropdown">
								@isset($cartdata[0])
								
								<a class="nav-link dropdown-toggle " href="{{URL::to('cart/')}}" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fad fa-shopping-cart"></i></a>
								<div class="dropdown-menu cart-dropdown" aria-labelledby="dropdownMenuButton">
								@foreach ($cartdata as $cartIndex => $cart)
								<?php
                        		$data[] = array(
                            		"total_price" => $cart->qty * $cart->price,
                            		"tax" => ($cart->qty*$cart->price)*$cart->tax/100
                        		);

                        		if (isset($cart->id)) {
		                            $id = $cart->id;
		                        }
		                        else{
		                            $id = $cartIndex;
		                        }

                    			?>
							
								
									<div class="item-cart">
										<div class="images-cart">
											@if (Session::get('id'))
											<img src='{{$cart->item_image }}' alt="">
											@else
											<img src='{{url('/storage/app/public/images/item/') . '/' .$cart->item_image }}' alt="">
											@endif
										</div>
										<div class="description-cart">
											<p>{{$cart->item_name}} - {{$cart->variation}}</p>
											<p>{{$cart->qty}}</p>
											<p>{{$cart->price}}</p>
										</div>
										<div class="delete-item">
											<a href="javascript:void(0)" onclick="RemoveCart({{$id}})"><i class="far fa-trash-alt"></i></a>
										</div>	
									</div>

															
						@endforeach
							<div class="cart-btn-header">
							  								<!-- View order btn -->
@if ( isset($cartdata[0]))
  <a href="{{URL::to('/cart')}}" class="head-cart">{{ trans('labels.view_my_order') }}</a>
@endif
<!-- View order btn -->

	  							</div>	

						</div>
						</li>
						@endif
						@if (Session::get('id'))
							<li class="nav-item dropdown  {{(!isset($cartdata[0])) ? 'ml-2' : ''}}">
								<a class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)">
									<img src='{!! asset("storage/app/public/images/profile/".Session::get("profile_image")) !!}' alt="">
								</a>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#EditProfile">{{ trans('labels.hello') }}, {{Session::get('name')}}</a>
									<a class="dropdown-item" href="{{URL::to('/address')}}">{{ trans('labels.my_address') }}</a>
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#AddReview">{{ trans('labels.add_review') }}</a>
									@if (Session::get('login_type') == "email")
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#ChangePasswordModal">{{ trans('labels.change_password') }}</a>
									@endif
									<a class="dropdown-item" href="{{URL::to('/logout')}}">{{ trans('labels.logout') }}</a>
								</div>
							</li>
						@else 
							<li class="nav-item">
								<a class="nav-link btn sign-btn" href="{{URL::to('/signin')}}">
									<i class="far fa-user"></i>
								{{ trans('labels.login') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link btn sign-btn" href="#">
									<i class="fas fa-phone-alt"></i>
									Call us</a>
							</li>
						@endif
						
					</ul>
				</div>
			
			</div>
		</nav>
		<nav class="navbar navbar-expand-lg nav-bottom-bar">
		<div class="container">
		
				<div class="collapse navbar-collapse" id="navbarNav">
							  
							<ul class="navbar-nav">
								<li class="nav-item {{ request()->is('product') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/product')}}">
										<i class="fas fa-bars"></i>
									Our Menu</a>
									<ul class="sub-menu">
										@foreach ($getcategory as $category)
										<li>
							              	<h5>					              		
							              		<a href="{{URL::to('/product/'.$category->id)}}">{{$category->category_name}}</a>
							              	</h5>
							              	<figure>
						              			<img src='{!! asset("storage/app/public/images/category/".$category->image) !!}' alt="">
						              		</figure>
						                </li>
						                @endforeach
									</ul>

								</li>
								<li class="nav-item {{ request()->is('catering') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/catering')}}">
										<i class="far fa-knife-kitchen"></i>
									Catering</a>
								</li>
								<li class="nav-item {{ request()->is('about') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/aboutus')}}">
										<i class="fal fa-hat-chef"></i>
									About Us</a>
								</li>
								<li class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/fanclub')}}">
										<i class="fas fa-pen-fancy"></i> 
									Fan Club</a>
								</li>
								<li class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/contactus')}}">
										<i class="fas fa-mobile"></i> 
									Contact</a>
								</li>
								@if (Session::get('id'))
						
							<li class="nav-item {{ request()->is('orders') ? 'active' : '' }}">
								<a class="nav-link" href="{{URL::to('/orders')}}"><i class="fal fa-shopping-cart"></i>
								{{ trans('labels.my_orders') }}</a>

							</li>
							<li class="nav-item {{ request()->is('wallet') ? 'active' : '' }}">
								<a class="nav-link" href="{{URL::to('/wallet')}}"><i class="fas fa-wallet"></i>
								{{ trans('labels.my_wallet') }}</a>
							</li>
							
						
						@endif	
								
								
								
							</ul>
						</div>
		
	</div>
	</nav>
	</header>
	<!-- navbar -->
	<div id="success-msg" class="alert alert-dismissible mt-3" style="display: none;">
	    <span id="msg"></span>
	</div>

	<div id="error-msg" class="alert alert-dismissible mt-3" style="display: none;">
	    <span id="ermsg"></span>
	</div>

	@include('cookieConsent::index')
