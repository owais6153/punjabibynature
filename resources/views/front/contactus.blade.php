<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Contact Us</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/images/about/favicon-61379b1f625c7.png') !!}">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<header>@include('front.theme.header')</header>

<body>

        <!--**********************************
            Content body start
        ***********************************-->
            <!-- row -->
        <div class="contact-page">
            <section class="contact-from">
		    <div class="container">
			        <div class="row align-items-center ">
			            <div class="col-lg-6 left-details-contact">
			                <h2 class="sec-head">Looking forward to hearing from you</h2>
			                @if($getabout->address != "")
			                <div class="address-box">
			                	<h4><i class="fas fa-map-marker-alt"></i> STORE ADDRESS</h4>
			                	<ul>
			                		<li>Brampton: 8887 The Gore Road: (905) 794-4667</li>
			                		<li>Brampton: 9980 Airport Road: (905) 791-1500</li>
			                		<li>North York: 2501 Finch Ave W: (647) 352-8090</li>


			                	</ul>
			              
			                </div>
			                @endif

			                @if($getabout->email != "")
			                   <div class="address-box">
			                	<h4><i class="far fa-clock"></i> STORE HOURS</h4>
			                	<ul>
			                		<li>Mon-Fri: 10:00 - 20:00</li>
			                		<li>Weekend: 12:00 - 16:00</li>
			                	</ul>
			              
			                </div>
			                @endif

			                
			            </div>
			            <div class="col-lg-6">
			                <form class="contact-form" id="contactform" method="post">
			                    {{csrf_field()}}
			                    <input type="text" name="firstname" placeholder="{{ trans('messages.enter_firstname') }}" id="firstname" required="">
			                    <input type="text" name="lastname" placeholder="{{ trans('messages.enter_lastname') }}" id="lastname" required="">
			                    <input type="email" name="email" placeholder="{{ trans('messages.enter_email') }}" id="email" required="">
			                    <textarea name="message" placeholder="{{ trans('messages.enter_message') }}" id="message" required=""></textarea>
			                    <button type="button" name="submit" class="btn" onclick="contact()">{{ trans('labels.submit') }}</button>
			                </form>
			            </div>
			        </div>
		    	</div>
			</section>
         </div>
            <!-- #/ container -->
        <!--**********************************
            Content body end
        ***********************************-->

</body>

<footer>@include('front.theme.footer')</footer>
</html>