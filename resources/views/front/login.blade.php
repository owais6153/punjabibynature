<!DOCTYPE html>
<html>

<head>
    <title>{{$getabout->title}}</title>

    <!-- meta tag -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

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
    <style type="text/css">
        .iti--allow-dropdown {
            width: 100%;
            margin-bottom: 22px;
        }
    </style>
</head>

<body>

<div id="success-msg" class="alert alert-dismissible mt-3" style="display: none;">
    <span id="msg"></span>
</div>

<div id="error-msg" class="alert alert-dismissible mt-3" style="display: none;">
    <span id="ermsg"></span>
</div>

<section class="signup-sec">
    <img src='{!! asset("storage/app/public/assets/images/bg.jpg") !!}' class="bg-img" alt="">
    <div class="container">
        <div class="signup-logo">
            <a href="{{URL::to('/')}}">
                <img src='{!! asset("storage/app/public/images/about/".$getabout->logo) !!}' alt="">
                <p>{{$getabout->short_title}}</p>
            </a>
        </div>
        <form id="login" action="{{ URL::to('/signin/login_without_otp') }}" method="post">
            @csrf

         
                <input type="email" name="email" id="email" placeholder="{{ trans('messages.enter_email') }}" class="w-100" required="">
                <input type="password" name="password" id="password" placeholder="{{ trans('messages.enter_password') }}" class="w-100" required="">
        

            <button type="submit" class="btn w-100">{{ trans('labels.login') }}</button>
            <a href="{{ url('auth/google') }}" class="btn w-50 mt-3" style="background-color: #fff;">
                <img src='{!! asset("storage/app/public/front/images/ic_google.png") !!}' alt="">
            </a>
            <a href="{{ url('auth/facebook') }}" class="btn w-50 mt-3" style="background-color: #fff;">
                <img src='{!! asset("storage/app/public/front/images/ic_fb.png") !!}' alt="">
            </a>
            <p class="already">{{ trans('labels.dont_account') }} <a href="{{URL::to('/signup')}}">{{ trans('labels.signup') }}</a></p>
            @if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated)
            @else
                <p class="already"><a href="{{URL::to('/forgot-password')}}">{{ trans('labels.forgot_password') }}</a></p>
            @endif

            @if (\Session::has('danger'))
                <div class="alert alert-danger w-100">
                    {!! \Session::get('danger') !!}
                </div>
            @endif
        </form>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- bootstrap js -->
<script src="{!! asset('storage/app/public/front/js/bootstrap.bundle.js') !!}"></script>

<!-- owl.carousel js -->
<script src="{!! asset('storage/app/public/front/js/owl.carousel.min.js') !!}"></script>

<!-- lazyload js -->
<script src="{!! asset('storage/app/public/front/js/lazyload.js') !!}"></script>

<!-- custom js -->
<script src="{!! asset('storage/app/public/front/js/custom.js') !!}"></script>
<!-- REQUIRED CDN  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"
        integrity="sha512-DNeDhsl+FWnx5B1EQzsayHMyP6Xl/Mg+vcnFPXGNjUZrW28hQaa1+A4qL9M+AiOMmkAhKAWYHh1a+t6qxthzUw=="
        crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"
    integrity="sha512-yye/u0ehQsrVrfSd6biT17t39Rg9kNc+vENcCXZuMz2a+LWFGvXUnYuWUW6pbfYj1jcBb/C39UZw2ciQvwDDvg=="
    crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    integrity="sha512-BNZ1x39RMH+UYylOW419beaGO0wqdSkO7pi1rYDYco9OL3uvXaC/GTqA5O4CVK2j4K9ZkoDNSSHVkEQKkgwdiw=="
    crossorigin="anonymous"></script>
<!-- JAVASCRIPT CODE REQUIRED -->
<script>
    

    var input = $('#mobile');
    var country = $('#country');
    var iti = intlTelInput(input.get(0))
    iti.setCountry("in");
    // listen to the telephone input for changes
    input.on('countrychange', function(e) {
      // change the hidden input value to the selected country code
      country.val(iti.getSelectedCountryData().dialCode);
    });
</script>

</body>

</html>