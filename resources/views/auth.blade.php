<!DOCTYPE html>
<html class="h-100" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ trans('labels.admin_title') }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/assets/images/favicon.png') !!}">
    <link href="{{asset('storage/app/public/assets/css/style.css')}}" rel="stylesheet">
    
</head>

<body class="h-100">

    @if(session()->has('danger'))
        <div class="alert alert-danger" style="text-align: center;">
            {{ session()->get('danger') }}
        </div>
    @endif

    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                <a class="text-center" href="#"><center><img src="{!! asset('storage/app/public/assets/images/logo.png') !!}" height="100" width="100" alt=""></center></a>

                                <form method="POST" class="mt-5 mb-5 login-input" action="{{ URL::to('auth') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input id="envato_username" type="text" class="form-control @error('envato_username') is-invalid @enderror" name="envato_username" value="{{ old('envato_username') }}" required autocomplete="envato_username" autofocus placeholder="Envato Username">

                                        @error('envato_username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input id="purchase_key" type="text" class="form-control @error('purchase_key') is-invalid @enderror" name="purchase_key" required autocomplete="current-purchase_key" placeholder="Purchase Key">

                                        @error('purchase_key')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <?php
                                    $text = str_replace('auth', 'admin', url()->current());
                                    ?>

                                    <div class="form-group">
                                        <input id="domain" type="hidden" class="form-control @error('domain') is-invalid @enderror" name="domain" required autocomplete="current-domain" value="{{$text}}" placeholder="domain" readonly="">
                                    </div>

                                    <button type="submit" class="btn login-form__btn submit w-100">
                                        {{ __('Submit') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--**********************************
        Scripts
    ***********************************-->
    <script src="{{asset('storage/app/public/assets/plugins/common/common.min.js')}}"></script>
    <script src="{{asset('storage/app/public/assets/js/custom.min.js')}}"></script>
    <script src="{{asset('storage/app/public/assets/js/settings.js')}}"></script>
    <script src="{{asset('storage/app/public/assets/js/gleek.js')}}"></script>
    <script src="{{asset('storage/app/public/assets/js/styleSwitcher.js')}}"></script>
</body>
</html>
