@include('front.theme.header')>

<section class="order-details">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.my_wallet') }}</h2>

        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="order-payment-summary" style="background-color: #fd3f32;">
                    <div class="col-4 mx-auto text-center">
                        <img src='{!! asset("storage/app/public/front/images/ic_wallet.png") !!}' width="100px" alt="" class="text-center">
                    </div>
                    
                    <h2 class="text-center mt-3" style="color: #fff;">{{ trans('labels.wallet_balance') }}</h2>
                    <h1 class="text-center" style="color: #fff;"><span>{{@$getdata->currency}}{{number_format(@$walletamount->wallet, 2)}}</span></h1>
                </div>
                @foreach($getpaymentdata as $paymentdata)
                
                    @if ($paymentdata->payment_name == "RazorPay")
                        <div class="mt-3">
                            <button type="button" data-toggle="modal" data-target="#AddMoneypay" style="width: 100%;" class="btn">{{ trans('labels.add_razorpay') }}</button>
                        </div>

                        @if($paymentdata->environment=='1')
                            <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->test_public_key}}">
                        @else
                            <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->live_public_key}}">
                        @endif

                    @endif

                    @if ($paymentdata->payment_name == "Stripe")
                        <div class="mt-3">
                            <button type="button" data-toggle="modal" data-target="#AddMoneyStripe" style="width: 100%;" class="btn">{{ trans('labels.add_stripe') }}</button>
                        </div>

                        @if($paymentdata->environment=='1')
                            <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->test_public_key}}">
                        @else
                            <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->live_public_key}}">
                        @endif
                    @endif

                @endforeach
            </div>
            <div class="col-lg-8">
                @foreach ($transaction_data as $orders)
                    @if ($orders->transaction_type == 1)
                    <div class="order-details-box">
                        <div class="wallet-details-img">
                            <img src='{!! asset("storage/app/public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                        </div>
                        <div class="order-details-name mt-3">
                            <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                            <h3><span style="color: #ff0000;">Order Cancelled</span> <span style="color: #00c56a;"> {{@$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                        </div>
                    </div>
                    @elseif ($orders->transaction_type == 2)

                    <div class="order-details-box">
                        <div class="wallet-details-img">
                            <img src='{!! asset("storage/app/public/front/images/ic_trRed.png") !!}' alt="" class="mt-1">
                        </div>
                        <div class="order-details-name mt-3">
                            <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                            <h3><span style="color: #00c56a;">{{ trans('labels.order_confirmed') }}</span> <span style="color: #ff0000;"> - {{@$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                        </div>
                    </div>

                    @elseif ($orders->transaction_type == 3)

                        <div class="order-details-box">
                            <div class="wallet-details-img">
                                <img src='{!! asset("storage/app/public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                            </div>
                            <div class="order-details-name mt-3">
                                <a href="javascript:void(0)">
                                    <a href="#">
                                        <h3> {{$orders->username}} <span style="color: #000;">{{$orders->date}}</span></h3>
                                    </a>
                                </a>
                                <h3><span style="color: #00c56a;">{{ trans('labels.referral_earning') }}</span> <span style="color: #00c56a;">{{@$getdata->currency}}{{number_format($orders->wallet, 2)}}</span></h3>
                            </div>
                        </div>

                    @elseif ($orders->transaction_type == 4)

                        <div class="order-details-box">
                            <div class="wallet-details-img">
                                <img src='{!! asset("storage/app/public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                            </div>
                            <div class="order-details-name mt-3">
                                <a href="javascript:void(0)">
                                    <a href="#">
                                        <h3> 
                                            <h3>
                                                {{ trans('labels.wallet_recharge') }}
                                                <span style="color: #000;">{{$orders->date}}</span>
                                            </h3>
                                        </h3>
                                    </a>
                                </a>
                                <h3>
                                    <span style="color: #00c56a;">
                                        @if ($orders->order_type == 3)
                                            {{ trans('labels.razorpay') }}
                                        @else
                                            {{ trans('labels.stripe') }}
                                        @endif
                                    </span> 
                                    <span style="color: #00c56a;">
                                        {{@$getdata->currency}}{{number_format($orders->wallet, 2)}}
                                    </span>
                                </h3>
                            </div>
                        </div>

                    @elseif ($orders->transaction_type == 5)

                        <div class="order-details-box">
                            <div class="wallet-details-img">
                                <img src='{!! asset("storage/app/public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                            </div>
                            <div class="order-details-name mt-3">
                                <a href="javascript:void(0)">
                                    <a href="#">
                                        <h3> 
                                            <h3>
                                                {{ trans('labels.wallet_recharge') }}
                                                <span style="color: #000;">{{$orders->date}}</span>
                                            </h3>
                                        </h3>
                                    </a>
                                </a>
                                <h3>
                                    <span style="color: #00c56a;">
                                        {{ trans('labels.by_admin') }}
                                    </span> 
                                    <span style="color: #00c56a;">
                                        {{@$getdata->currency}}{{number_format($orders->wallet, 2)}}
                                    </span>
                                </h3>
                            </div>
                        </div>

                    @elseif ($orders->transaction_type == 6)

                        <div class="order-details-box">
                            <div class="wallet-details-img">
                                <img src='{!! asset("storage/app/public/front/images/ic_trRed.png") !!}' alt="" class="mt-1">
                            </div>
                            <div class="order-details-name mt-3">
                                <a href="javascript:void(0)">
                                    <a href="#">
                                        <h3> 
                                            <h3>
                                                {{ trans('labels.wallet_deduction') }}
                                                <span style="color: #000;">{{$orders->date}}</span>
                                            </h3>
                                        </h3>
                                    </a>
                                </a>
                                <h3>
                                    <span style="color: #ff0000;">
                                        {{ trans('labels.by_admin') }}
                                    </span> 
                                    <span style="color: #ff0000;">
                                        {{@$getdata->currency}}{{number_format($orders->wallet, 2)}}
                                    </span>
                                </h3>
                            </div>
                        </div>

                    @endif
                @endforeach
                <nav aria-label="Page navigation example">
                    @if ($transaction_data->hasPages())
                    <ul class="pagination" role="navigation">
                        {{-- Previous Page Link --}}
                        @if ($transaction_data->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $transaction_data->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                            </li>
                        @endif

                        <?php
                            $start = $transaction_data->currentPage() - 2; // show 3 pagination links before current
                            $end = $transaction_data->currentPage() + 2; // show 3 pagination links after current
                            if($start < 1) {
                                $start = 1; // reset start to 1
                                $end += 1;
                            } 
                            if($end >= $transaction_data->lastPage() ) $end = $transaction_data->lastPage(); // reset end to last page
                        ?>

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $transaction_data->url(1) }}">{{1}}</a>
                            </li>
                            @if($transaction_data->currentPage() != 4)
                                {{-- "Three Dots" Separator --}}
                                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                            @endif
                        @endif
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ ($transaction_data->currentPage() == $i) ? ' active' : '' }}">
                                    <a class="page-link" href="{{ $transaction_data->url($i) }}">{{$i}}</a>
                                </li>
                            @endfor
                        @if($end < $transaction_data->lastPage())
                            @if($transaction_data->currentPage() + 3 != $transaction_data->lastPage())
                                {{-- "Three Dots" Separator --}}
                                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $transaction_data->url($transaction_data->lastPage()) }}">{{$transaction_data->lastPage()}}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($transaction_data->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $transaction_data->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                <span class="page-link" aria-hidden="true">&rsaquo;</span>
                            </li>
                        @endif
                    </ul>
                    @endif
                </nav>
            </div>
        </div>
    </div>


    <!-- Modal Add Money RazorPay-->
    <div class="modal fade text-left" id="AddMoneypay" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
    aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.add_money') }}</label>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="errorr" style="color: red;"></div>
          
          <form method="post">
          {{csrf_field()}}
            <div class="modal-body">

              <label>{{ trans('labels.amount') }} </label>
              <div class="form-group">
                <input type="text" name="add_money" id="add_money" class="form-control" required="">
                <input type="hidden" name="user_id" class="form-control" value="{{Session::get('id')}}">
              </div>

            </div>
            <div class="modal-footer">
              <input type="reset" class="btn open comman" data-dismiss="modal"
              value="Close">
              <input type="button" class="btn open comman addmoney" value="Submit">
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Add Money Stripe-->
    <div class="modal fade text-left" id="AddMoneyStripe" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
    aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.add_money') }}</label>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="errorr" style="color: red;"></div>
          
          <form method="post">
          {{csrf_field()}}
            <div class="modal-body">

              <label>{{ trans('labels.amount') }} </label>
              <div class="form-group">
                <input type="text" name="add_money_stripe" id="add_money_stripe" class="form-control" required="">
                <input type="hidden" name="user_id" class="form-control" value="{{Session::get('id')}}">
                <input type="hidden" name="email" id="email" class="form-control" value="{{Session::get('email')}}">
              </div>

            </div>
            <div class="modal-footer">
              <input type="reset" class="btn open comman" data-dismiss="modal"
              value="Close">
              <input type="button" class="btn open comman addmoneystripe" value="Submit">
            </div>
          </form>
        </div>
      </div>
    </div>
</section>

@include('front.theme.footer')

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>

<script type="text/javascript">
    var SITEURL = '{{URL::to('')}}';
    var handler = StripeCheckout.configure({
      key: $('#stripe').val(),
      image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
      locale: 'auto',
      token: function(token) {
        // You can access the token ID with `token.id`.
        // Get the token ID to your server-side code for use.

        var add_money = parseFloat($('#add_money_stripe').val());
        var token = token.id;


        $('#preloader').show();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: SITEURL + '/addmoneystripe',
            data: {
                add_money : add_money ,
                stripeToken : token,
            }, 
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    window.location.href = SITEURL + '/wallet';
                } else {
                    $('#ermsg').text(response.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
            error: function(error) {

                // $('#errormsg').show();
            }
        });
      },
      opened: function() {
        // console.log("Form opened");
      },
      closed: function() {
        // console.log("Form closed");
      }
    });

    $(document).ready(function() {
        "use strict";
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        }); 
        $('body').on('click', '.addmoney', function(e){
            var add_money = parseFloat($('#add_money').val());

            var options = {
                "key": $('#razorpay').val(),
                "amount": (parseInt(add_money*100)), // 2000 paise = INR 20
                "name": "Restaurant website",
                "description": "Wallet recharge",
                "image": '{!! asset("storage/app/public/images/about/".$getabout->logo) !!}',
                "handler": function (response){
                    $('#preloader').show();
                    $.ajax({
                        url: SITEURL + '/addmoney',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            razorpay_payment_id: response.razorpay_payment_id ,
                            add_money : add_money
                        }, 
                        success: function (msg) {
                        $('#preloader').hide();
                        window.location.href = SITEURL + '/wallet';
                    }
                });
            
            },
                "prefill": {
                    "contact": '{{@$walletamount->mobile}}',
                    "email":   '{{@$walletamount->email}}',
                    "name":   '{{@$walletamount->name}}',
                },
                "theme": {
                    "color": "#366ed4"
                }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();
            e.preventDefault();
        });

        $('body').on('click', '.addmoneystripe', function(e){
            
            var add_money = parseFloat($('#add_money_stripe').val());
            var email = $('#email').val();

            handler.open({
                name: 'Restaurant website',
                description: 'Wallet recharge',
                amount: add_money*100,
                currency: "USD",
                email: email
            });
            e.preventDefault();
            // Close Checkout on page navigation:
            $(window).on('popstate', function() {
              handler.close();
            });
            
        });
    });


    $('#add_money').keyup(function(){
        "use strict";
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });
    $('#add_money_stripe').keyup(function(){
        "use strict";
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });
</script>