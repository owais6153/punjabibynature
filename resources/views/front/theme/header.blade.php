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
								<a class="nav-link dropdown-toggle " href="javascript:void(0)" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-shopping-cart"></i></a>
								<div class="dropdown-menu cart-dropdown" aria-labelledby="dropdownMenuButton">
									<div class="item-cart">
										<div class="images-cart">
											<img src="http://localhost/punjabibynature/storage/app/public/images/category/category-613696ce00fa7.jpg">
										</div>
										<div class="description-cart">
											<p>Hello Products</p>
											<p>29$</p>
										</div>
										<div class="delete-item">
											<a href="#"><i class="far fa-trash-alt"></i></a>
										</div>	
									</div>
									<div class="item-cart">
										<div class="images-cart">
											<img src="http://localhost/punjabibynature/storage/app/public/images/category/category-613696ce00fa7.jpg">
										</div>
										<div class="description-cart">
											<p>Hello Products</p>
											<p>29$</p>
										</div>
										<div class="delete-item">
											<a href="#"><i class="far fa-trash-alt"></i></a>
										</div>	
									</div>
								</div>
	  								
								
							</li>
						
						@if (Session::get('id'))
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)">
									<img src='{!! asset("storage/app/public/images/profile/".Session::get("profile_image")) !!}' alt="">
								</a>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#EditProfile">{{ trans('labels.hello') }}, {{Session::get('name')}}</a>
									<a class="dropdown-item" href="{{URL::to('/address')}}">{{ trans('labels.my_address') }}</a>
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#AddReview">{{ trans('labels.add_review') }}</a>
									<a class="dropdown-item" href="" data-toggle="modal" data-target="#Refer">{{ trans('labels.refer_earn') }}</a>
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
										<i class="fas fa-home"></i>
									Catering</a>
								</li>
								<li class="nav-item {{ request()->is('about') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/aboutus')}}">
										<i class="fas fa-utensils"></i>
									About Us</a>
								</li>
								<li class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
									<a class="nav-link" href="{{URL::to('/contact')}}">
										<i class="fas fa-mobile"></i> 
									Contact</a>
								</li>
								@if (Session::get('id'))
						
							<li class="nav-item {{ request()->is('orders') ? 'active' : '' }}">
								<a class="nav-link" href="{{URL::to('/orders')}}">{{ trans('labels.my_orders') }}</a>

							</li>
							<li class="nav-item {{ request()->is('favorite') ? 'active' : '' }}">
								<a class="nav-link" href="{{URL::to('/favorite')}}">{{ trans('labels.favourite_list') }}</a>
							</li>
							<li class="nav-item {{ request()->is('wallet') ? 'active' : '' }}">
								<a class="nav-link" href="{{URL::to('/wallet')}}">{{ trans('labels.my_wallet') }}</a>
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