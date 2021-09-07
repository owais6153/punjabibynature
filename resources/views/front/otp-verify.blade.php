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
        <form id="login" action="{{ URL::to('/otp-verification') }}" method="post">
            @csrf
            @if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated)
                <input type="text" name="mobile" id="mobile" placeholder="Email" class="w-100" value="{{Session::get('mobile')}}" readonly="">
            @else
                <input type="email" name="email" id="email" placeholder="Email" class="w-100" value="{{Session::get('email')}}" readonly="">
            @endif
            
            @if (env('Environment') == 'sendbox')
                <input type="number" name="otp" id="otp" min="1" maxlength="6" placeholder="Verification code" class="w-100" required="" value="{{Session::get('otp')}}">
            @else
                <input type="number" name="otp" id="otp" min="1" maxlength="6" placeholder="Verification code" class="w-100" required="">
            @endif  

            
            <button type="submit" class="btn">{{ trans('labels.verify') }}</button>
            <p class="already">{{ trans('labels.didnt_email') }} <span class="Btn" id="verifiBtn"></span><span id="timer"></span></p>
            @if (\Session::has('danger'))
                <div class="alert alert-danger w-100">
                    {!! \Session::get('danger') !!}
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert alert-success w-100">
                    {!! \Session::get('success') !!}
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


<script>
    let timerOn = true;

    function timer(remaining) {
        "use strict";
      var m = Math.floor(remaining / 60);
      var s = remaining % 60;
      
      m = m < 10 ? '0' + m : m;
      s = s < 10 ? '0' + s : s;
      document.getElementById('timer').innerHTML = m + ':' + s;
      remaining -= 1;
      
      if(remaining >= 0 && timerOn) {
        setTimeout(function() {
            timer(remaining);
        }, 1000);
        return;
      }

      if(!timerOn) {
        // Do validate stuff here
        return;
      }
      
      // Do timeout stuff here
      document.getElementById("verifiBtn").innerHTML = `<a href="{{URL::to('/resend-otp')}}">{{ trans('labels.resend') }}</a>`;
      document.getElementById("timer").innerHTML = "";
    }

    timer(120);
</script>
</body>

</html>