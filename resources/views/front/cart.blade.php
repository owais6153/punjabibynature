@include('front.theme.header')

<section class="cart">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.my_cart') }}</h2>
        <div class="row">
            @empty ($cartdata) 
                <p>No Data found</p>
            @else 
                <div class="col-lg-8">
                    @foreach ($cartdata as $cartIndex => $cart)
                    @if ($cart->product_type == 'product' || $cart->product_type == 'catering')
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
                    <div class="cart-box">
                        <div class="cart-pro-img">
                                @if (Session::get('id'))
                                    <img src='{{$cart->item_image }}' alt="">
                                @else
                                    <img src='{{url('/storage/app/public/images/item/') . '/' .$cart->item_image }}' alt="">
                                @endif
                        </div>
                        <div class="cart-pro-details">
                            <div class="cart-pro-edit">
                                <a href="{{URL::to('product-details/'.$cart->item_id)}}" class="cart-pro-name">{{$cart->item_name}} - {{$cart->variation}}</a>
                                <a href="javascript:void(0)"><i class="fal fa-trash-alt" onclick="RemoveCart('{{$id}}')"></i></a>
                            </div>
                            <div class="cart-pro-edit">
                                <input type="hidden" name="max_qty" id="max_qty" value="{{$getdata->max_order_qty}}">
                                <div class="pro-add">
                                    <div class="value-button sub" id="decrease" onclick="qtyupdate('{{$id}}','{{$cart->item_id}}','decreaseValue')" value="Decrease Value">
                                        <i class="fal fa-minus-circle"></i>
                                    </div>
                                    <input type="number" id="number_{{$id}}" name="number" value="{{$cart->qty}}" readonly="" min="1" max="10" style="background-color: #f4f4f8;" />
                                    <div class="value-button add" id="increase" onclick="qtyupdate('{{$id}}','{{$cart->item_id}}','increase')" value="Increase Value">
                                        <i class="fal fa-plus-circle"></i>
                                    </div>
                                </div>
                                <p class="cart-pricing">{{$taxval->currency}}{{number_format($cart->qty * $cart->price,2)}}</p>
                            </div>
                                <a href="javascript:void(0)" class="view_addons">View more detail</a>
                                <div class="cart-addons-wrap" style="display: none;">

                                    @empty (!$cart->ingredients)
                                        <h5>Ingredients:</h5>
                                        @if($islogin == false)

                                        @foreach($cart->ingredients as $ingredient)
                                            <div class="cart-addons">
                                                <b>{{$ingredient}}</b>
                                            </div>
                                        @endforeach
                                        @else
                                        <?php
                                        $ingredientsL= explode('|', $cart->ingredients);
                                        ?>
                                        @foreach($ingredientsL as $ingredient)
                                            <div class="cart-addons">
                                                <b>{{$ingredient}}</b>
                                            </div>
                                        @endforeach
                                        @endif
                                    @endif    

                                    @if ($cart->addons_id != "" || $cart->group_addons != '')
                                        <h5 class="cart-addon-h"><span>Sides: </span><span>{{$taxval->currency}}{{$cart->qty}}</span></h5>
                                        @if ($cart->addons_id != "")
                                            <?php 
                                            $addons_id = explode(",",$cart->addons_id);
                                            $addons_price = explode(",",$cart->addons_price);
                                            $addons_name = explode(",",$cart->addons_name);
                                            ?>                                            
                                            @foreach ($addons_id as $key =>  $addons)                                
                                                <div class="cart-addons">
                                                    <b>{{$addons_name[$key]}}</b><br />
                                                </div>
                                            @endforeach
                                        @endif
                                        @empty (!$cart->group_addons)
                                        @if($islogin==false)
                                            @foreach($cart->group_addons as $group_addon)
                                                <div class="cart-addons">
                                                    <b>{{$group_addon}}</b>
                                                </div>
                                            @endforeach
                                            @else
                                            <?php
                                                $cart_addonL= explode('|', $cart->group_addons);
                                            ?>
                                            @foreach($cart_addonL as $group_addon)
                                                <div class="cart-addons">
                                                    <b>{{$group_addon}}</b>
                                                </div>
                                            @endforeach
                                            @endif
                                        @endif
                                    @endif

                                     @empty (!$cart->combo)
                                     <h5>Combo Options</h5>
                                     @if($islogin==false)
                                        @foreach($cart->combo as $comb)
                                            <div class="cart-addons">
                                                <b>{{$comb}}</b>
                                            </div>
                                        @endforeach
                                        @else
                                        <?php
                                        $comboL= explode('|', $cart->combo);
                                        ?>
                                        @foreach($comboL as $comb)
                                            <div class="cart-addons">
                                                <b>{{$comb}}</b>
                                            </div>
                                        @endforeach
                                        @endif
                                    @endif
                                </div>
                            

                            @if ($cart->item_notes != "")
                                <textarea placeholder="{{ trans('messages.enter_order_note') }}" readonly="">{{$cart->item_notes}}</textarea>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @empty (!@$data)
                    @if (Session::has('offer_amount'))
                        <div class="promo-code">
                            <form>
                            <div class="promo-wrap">
                                <input type="text" name="removepromocode" id="removepromocode" autocomplete="off" readonly="" value="{{Session::get('offer_code')}}">
                                <button class="btn" id="ajaxRemove">{{ trans('labels.remove') }}</button>
                            </div>
                            </form>
                        </div>
                    @else
                        <div class="promo-code">
                            <form>
                            <div class="promo-wrap">
                                <input type="text" placeholder="{{ trans('messages.enter_promocode') }}" name="promocode" id="promocode" autocomplete="off" readonly="">
                                <button class="btn" id="ajaxSubmit">{{ trans('labels.apply') }}</button>
                            </div>
                            </form>
                            <p data-toggle="modal" data-target="#staticBackdrop">{{ trans('labels.select_promocode') }}</p>
                        </div>
                    @endif
                    @endif
                    @empty (!@$data)
                        <?php 
                        $order_total = array_sum(array_column(@$data, 'total_price'));
                        $tax = array_sum(array_column(@$data, 'tax'));
                        $total = array_sum(array_column(@$data, 'total_price'))+$tax;
                        ?>
                    @endif
                    @if($islogin == false)
                        <input type="hidden" name="get_new_userID" id="get_new_userID">
                        <div class="d-block mt-5 cart-user-form">
                            <h2>Checkout</h2>
                            <h5 class="mt-4 mb-4">Personal Details</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" name="name" id="name" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" id="email" placeholder="Email">
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" id="country" name="country" value="91" />
                <input type="text" name="mobile" id="mobile" placeholder="{{ trans('messages.enter_mobile') }}" class="w-100" value="{{ old('mobile') }}">
                                </div>

                            <div class="col-md-12">
                                
                                <ul class="extra-food checkout" >

                                    <li>
                                        <input class="Checkbox" type="radio" name="register_type" value="1" checked="">
                                        <p style="font-weight: 500;">Guset Checkout</p>
                                    </li>
                                    <li>
                                        <input class="Checkbox" type="radio" name="register_type" value="2" >
                                        <p style="font-weight: 500;">Create My Account</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-12 row" id="pwd_div">
                             
                            </div>
                        @csrf
                    @if (env('Environment') == 'sendbox')
                        <div class="mb-3 mt-4 col-md-12">
                             <h5>Address</h5>
                         </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" name="address_type" id="address_type" >
                                    <option value="">{{ trans('messages.select_address_type') }}</option>
                                    <option value="1">{{ trans('labels.home') }}</option>
                                    <option value="2">{{ trans('labels.work') }}</option>
                                    <option value="3">{{ trans('labels.other') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                           
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="{{ trans('messages.enter_delivery_address') }}" name="address" id="address" value="New York, NY, USA" required="" readonly="" autocomplete="on" >
                                <input type="hidden" id="lat" name="lat" value="" />
                                <input type="hidden" id="lang" name="lang" value="" />
                                <input type="hidden" name="order_total" id="order_total" value="{{$total}}">
                                <input type="hidden" name="paid_amount" id="paid_amount" value="{{$total}}">
                                <input type="hidden" name="tax" id="tax" value="{{$tax}}">
                                <input type="hidden" name="tax_amount" id="tax_amount" value="{{$tax}}">
                                <input type="hidden" name="discount_amount" id="discount_amount" value="">
                                <input type="hidden" name="delivery_charge" id="delivery_charge" value="0">
                            </div>
                        </div>

                        <div class="col-md-4">
                         <input type="text" id="city" name="city" placeholder="City"> 
                            
                        </div>
                        <div class="col-md-4">
                           <input type="text" id="state" name="state" placeholder="State"> 
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="country" name="country" placeholder="Country">
                        </div>
                        <div class="col-md-6">
                           
                            <div class="form-group">
                                <input type="text" class="form-control" name="landmark" id="landmark" placeholder="{{ trans('messages.enter_landmark') }}" value="Central Park" readonly="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <input type="text" class="form-control" name="building" id="building" placeholder="{{ trans('messages.enter_building') }}" value="4043" readonly="">
                            </div>
                        </div>
                    @else
                        <div class="mb-3 mt-4 col-md-12">
                              <h5>Address</h5>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" name="address_type" id="address_type">
                                    <option value="">{{ trans('messages.select_address_type') }}</option>
                                    <option value="1">{{ trans('labels.home') }}</option>
                                    <option value="2">{{ trans('labels.work') }}</option>
                                    <option value="3">{{ trans('labels.other') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="{{ trans('messages.enter_delivery_address') }}" name="address" id="address" autocomplete="on">
                                <input type="hidden" id="lat" name="lat" />
                                <input type="hidden" id="lang" name="lang" />
                                <input type="hidden" name="order_total" id="order_total" value="{{$total}}">
                                <input type="hidden" name="paid_amount" id="paid_amount" value="{{$total}}">
                                <input type="hidden" name="tax" id="tax" value="{{$tax}}">
                                <input type="hidden" name="tax_amount" id="tax_amount" value="{{$tax}}">
                                <input type="hidden" name="delivery_charge" id="delivery_charge" value="0">
                                <input type="hidden" name="discount_amount" id="discount_amount" value="">
                            </div>
                        </div>

                        <div class="col-md-4">
                         <input type="text" id="city" name="city" placeholder="City"> 
                            
                        </div>
                        <div class="col-md-4">
                           <input type="text" id="state" name="state" placeholder="State"> 
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="country" name="country" placeholder="Country">
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <input type="text" class="form-control" name="landmark" id="landmark" placeholder="{{ trans('messages.enter_landmark') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <input type="text" class="form-control" name="building" id="building" placeholder="{{ trans('messages.enter_building') }}">
                            </div>
                        </div>
                        
                    @endif
                        <div class="col-md-12">
                            <h5 class="mb-3 mt-4">Order Notes</h5>
                            <textarea name="notes" id="notes" placeholder="{{ trans('messages.enter_order_note') }}" rows="3"></textarea>
                        </div>
                               
                            </div>
                            
                                                                                   
                          
                        </div>
                    @endif

                </div>
                <div class="col-lg-4">
                    @empty (!@$data)
                    
                    <div class="cart-summary">
                        <h2 class="sec-head">{{ trans('labels.payment_summary') }}</h2>

                        <p class="pro-total">{{ trans('labels.order_total') }} <span>{{$taxval->currency}}{{number_format($order_total, 2)}}</span></p>
                        <p class="pro-total">{{ trans('labels.tax') }} <span>{{$taxval->currency}}{{number_format($tax, 2)}}</span></p>
                        <p class="pro-total" id="delivery_charge_hide">{{ trans('labels.delivery_charge') }}<span id="shipping_charge">{{$taxval->currency}}0.00</span></p>

                        @if (Session::has('offer_amount'))
                            <p class="pro-total offer_amount">{{ trans('labels.discount') }} ({{Session::get('offer_code')}})</span>
                                <span id="offer_amount">
                                    -{{$taxval->currency}}{{number_format($order_total*Session::get('offer_amount')/100, 2)}}
                                </span>
                            </p>
                        @else
                            <p class="pro-total offer_amount" style="display: none">{{ trans('labels.discount') }} <span id="offer_amount"></span></p>
                        @endif

                        @if (Session::has('offer_amount'))

                            <p class="cart-total">{{ trans('labels.total_amount') }} 
                                <span id="total_amount">
                                    {{$taxval->currency}}{{number_format($order_total+$taxval->delivery_charge+$tax-$order_total*Session::get('offer_amount')/100, 2)}}
                                </span>
                            </p>
                        @else
                            <p class="cart-total">{{ trans('labels.total_amount') }} <span id="total_amount">{{$taxval->currency}}{{number_format($total, 2)}}</span></p>
                        @endif

                        <h4 class="sec-head openmsg mt-5" style="color: red; display: none;">Restaurant is closed.</h4>
                        
                        <div class="cart-delivery-type open">
                            <label for="cart-delivery">
                                <input type="radio" name="cart-delivery" id="cart-delivery" checked value="1">
                                <div class="cart-delivery-type-box">
                                    <img src="{!! asset('storage/app/public/front/images/pickup-truck.png') !!}" height="40" width="40" alt="">                                   
                                    <p>{{ trans('labels.delivery') }}</p>
                                </div>
                            </label>
                            <label for="cart-pickup">
                                <input type="radio" name="cart-delivery" id="cart-pickup" value="2">
                                <div class="cart-delivery-type-box">
                                    <img src="{!! asset('storage/app/public/front/images/delivery.png') !!}" height="40" width="40" alt="">
                                    <p>{{ trans('labels.pickup') }}</p>
                                </div>
                            </label>
                        </div>
                         @if (Session::has('id'))
                        <div class="select_add">
                            @if (!$addressdata->isEmpty())
                                <p data-toggle="modal" data-target="#select_address" style="width: 50%;" class="btn">{{ trans('labels.select_address') }}</p>
                            @else
                                <a href="{{URL::to('/address')}}" style="width: 50%;" class="btn">{{ trans('labels.select_address') }}</a>
                            @endif
                            
                        </div>

                        @if (!$addressdata->isEmpty())
                            <div class="promo-wrap open mt-3">
                                <input type="text" placeholder="{{ trans('messages.enter_delivery_address') }}" name="address" id="address" required="" readonly=""> 
                            </div>

                            <div class="promo-wrap open">
                                <input type="text" id="postal_code" name="postal_code" placeholder="{{ trans('messages.enter_pincode') }}" required="" readonly=""/> 
                            </div>

                            <div class="promo-wrap open">
                                <input type="text" placeholder="{{ trans('messages.enter_building') }}" name="building" id="building" required="" readonly=""> 
                            </div>

                            <div class="promo-wrap open">
                                <input type="text" placeholder="{{ trans('messages.enter_landmark') }}" name="landmark" id="landmark" required="" readonly=""> 
                            </div>
                        @endif

                        <div class="promo-wrap open mt-3">
                            <textarea name="notes" id="notes" placeholder="{{ trans('messages.enter_order_note') }}" rows="3"></textarea>
                        </div>

                        <input type="hidden" id="lat" name="lat" />
                        <input type="hidden" id="lang" name="lang" />
                        <input type="hidden" id="city" name="city" /> 
                        <input type="hidden" id="state" name="state" /> 
                        <input type="hidden" id="country" name="country" />

                        <input type="hidden" name="order_total" id="order_total" value="{{$order_total}}">
                        <input type="hidden" name="tax" id="tax" value="{{$tax}}">
                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{$tax}}">
                        <input type="hidden" name="email" id="email" value="{{Session::get('email')}}">
                        <input type="hidden" name="delivery_charge" id="delivery_charge" value="0">

                        @if (Session::has('offer_amount'))
                            <input type="hidden" name="discount_amount" id="discount_amount" value="{{$order_total*Session::get('offer_amount')/100}}">
                        @else
                            <input type="hidden" name="discount_amount" id="discount_amount" value="">
                        @endif

                        @if (Session::has('offer_amount'))
                            <input type="hidden" name="paid_amount" id="paid_amount" value="{{$order_total+$taxval->delivery_charge+$tax-$order_total*Session::get('offer_amount')/100}}">
                        @else
                            <input type="hidden" name="paid_amount" id="paid_amount" value="{{$total}}">
                        @endif

                        @if (Session::has('offer_amount'))
                            <input type="hidden" name="discount_pr" id="discount_pr" value="{{Session::get('offer_amount')}}">
                        @else
                            <input type="hidden" name="discount_pr" id="discount_pr" value="">
                        @endif

                        @if (Session::has('offer_amount'))
                            <input type="hidden" name="getpromo" id="getpromo" value="{{Session::get('offer_code')}}">
                        @else
                            <input type="hidden" name="getpromo" id="getpromo" value="">
                        @endif

                       
                        <div class="mt-3">                            
                            <button type="button" style="width: 100%;" class="btn open comman" onclick="WalletOrder()">{{ trans('labels.my_wallet') }} ({{$taxval->currency}}{{number_format($userinfo->wallet, 2)}})</button>
                        </div>
                        

                        @foreach($getpaymentdata as $paymentdata)

                            @if ($paymentdata->payment_name == "COD")
                                <div class="mt-3">
                                    <button type="button" style="width: 100%;" class="btn open comman" onclick="CashonDelivery()">{{ trans('labels.cash_payment') }}</button>
                                </div>
                            @endif

                            @if ($paymentdata->payment_name == "RazorPay")
                                <div class="mt-3">
                                    <button type="button" style="width: 100%;" class="btn buy_now open comman">{{ trans('labels.razorpay_payment') }}</button>
                                </div>

                                @if($paymentdata->environment=='1')
                                    <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->test_public_key}}">
                                @else
                                    <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->live_public_key}}">
                                @endif

                            @endif

                            @if ($paymentdata->payment_name == "Stripe")
                                <div class="mt-3">
                                    <button id="customButton" class="btn comman" style="display: none; width: 100%;">{{ trans('labels.stripe_payment') }}</button>
                                    <button class="btn open stripe comman" style="width: 100%;" onclick="stripe()">{{ trans('labels.stripe_payment') }}</button>
                                </div>

                                @if($paymentdata->environment=='1')
                                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->test_public_key}}">
                                @else
                                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->live_public_key}}">
                                @endif
                            @endif

                        @endforeach
                        @else
                          
                          <a href="{{URL::to('/signin')}}"  style="width: 100%;" class="btn mt-4 mb-2">Login</a>
                            @foreach($getpaymentdata as $paymentdata)
                           @if ($paymentdata->payment_name == "Stripe")
                               <button id="customButton" class="btn comman" style="display: none; width: 100%;">{{ trans('labels.stripe_payment') }}</button>
                              <a href="javascript:void(0)" onclick="checkout_guest()"  style="width: 100%;" class="btn mb-4">Place Order (Stripe)</a>
                                @if($paymentdata->environment=='1')
                                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->test_public_key}}">
                                @else
                                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->live_public_key}}">
                                @endif
                             @endif
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            @endif
            
        </div>
    </div>
</section>

<!-- Promocode Modal -->
<div class="promo-modal modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-head">
                <h4>{{ trans('labels.select_promocode') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($getpromocode as $promocode)
                <div class="promo-box">
                    <button class="btn btn-copy" data-id="{{$promocode->offer_code}}">{{ trans('labels.copy') }}</button>
                    <p class="promo-title">{{$promocode->offer_name}}</p>
                    <p class="promo-code-here">{{ trans('labels.code') }} :: <span>{{$promocode->offer_code}}</span></p>
                    <small>{{$promocode->description}}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="promo-modal modal fade" id="select_address" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-head">
                <h4>{{ trans('labels.select_address') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($addressdata as $address)
                <div class="promo-box">
                    <button class="btn btn-select" style="padding: 0px 18px;" data-address="{{$address->address}}" data-postal_code="{{$address->pincode}}" data-building="{{$address->building}}" data-landmark="{{$address->landmark}}" data-lat="{{$address->lat}}" data-lang="{{$address->lang}}" data-city="{{$address->city}}" data-state="{{$address->state}}" data-country="{{$address->country}}" data-deliverycharge="{{$address->delivery_charge}}">Select</button>
                    <p class="promo-code-here">
                        {{ trans('labels.type') }} :: 
                        @if ($address->address_type == 1)
                            Home
                        @elseif ($address->address_type == 2)
                            Work
                        @else
                            Other
                        @endif
                    </p>
                    <p class="promo-title">{{$address->address}}</p>
                    <small>{{$address->landmark}}, {{$address->building}}, {{$address->pincode}}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<div class="promo-modal modal fade" id="otp_verify" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-head">
                <h4 class=" mt-3">Please verify code</h4>
                <p>A code has been sent to your number. Please verify it.</p>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="verification_error"></div>
                <input type="text" readonly="readonly" id="get_otp_number" name="" class="form-control" disabled="disabled">
                <input type="number" id="get_otp" name="" class="form-control">
                <input type="button" id="verify_otp" value="Verify" class="btn" placeholder="Verification code">
                <p id="didnt_get" style="font-size: 18px;text-align: center;font-weight: 400;margin-top: 20px;">Didn't get code <span id="timer"></span><span id="verifiBtn"></span></p>
            </div>
        </div>
    </div>
</div>

@include('front.theme.footer')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{$taxval->map}}&libraries=places"></script>

<script type="text/javascript">
@if($islogin == false)
    let timerOn = false;

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
      document.getElementById("verifiBtn").innerHTML = `<a href="javascript:void(0)" onclick="resendCode()">{{ trans('labels.resend') }}</a>`;
      document.getElementById("timer").innerHTML = "";
    }

    function resendCode(){
        if($('#get_otp_number').val() == ''){
                $('#verification_error').text('Mobile Required');
                $('#verification_error').fadeIn();
        }
        else{
             $('#verification_error').fadeOut();
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('/resendcode_checkout') }}",
                data: {mobile: $('#get_otp_number').val()},
                method: 'POST',
                success: function(response) {
                    $('#preloader').hide();
                    if (response.status == 1) {
                        document.getElementById("didnt_get").innerHTML = 'Code resent successfully';

                    } else {
                        $('#verification_error').text(response.message);
                        $('#verification_error').fadeIn();
                    }
                },
                error: function(error) {
                    $('#preloader').hide();
                    $('#verification_error').text(error.responseJSON.message);
                    $('#verification_error').fadeIn();
                }
            });
        }
    }

    function checkout_guest(){
        var error = '';
        var data = {
          name: $('#name').val(),
          email : $('#email').val(),
          mobile : $('#mobile').val(),
          register_type: $('input[name="register_type"]:checked').val(),
          address_type: $('#address_type').val(),
          address: $('#address').val(),
          city: $('#city').val(),
          state: $('#state').val(),
          country: $('#country').val(),
          landmark: $('#landmark').val(),
          building: $('#building').val(),
          lat: $('#lat').val(),          
          lang: $('#lang').val(),
          notes: $('#notes').val(),
          order_type : $("input:radio[name=cart-delivery]:checked").val()
        };
        if (data.register_type == 2) {
            data.password =$('#password').val();
            data.confirm_password =$('#confirm_password').val();
        }
        if (data.name == '') {
            error = 'Name is required';
        }
        if (error == '') {
            if (data.email == '') {
                error = 'Email is required';
            }
        }
        if (error == '') {
            if (data.mobile == '') {
                error = 'Mobile number is required';
            }
        }
        if (error == '' && data.register_type == 2) {
            if (data.password == '') {
                error = 'Password is required';
            }
        }
        if (error == '' && data.register_type == 2) {
            if (data.confirm_password == '') {
                error = 'Confirm Password is required';
            }
            if (error == '' && data.confirm_password != data.password) {
                error = 'Password not matched';
            }
        }

        if (error == '' && data.order_type == 1) 
        {
          if (data.address_type == '') {
            error = 'Address type is required';
          }
          else if (data.address == '') {
            error = 'Address is required';
          }
          else if (data.city == '') {
            error = 'City is required';
          }
          else if (data.state == '') {
            error = 'State is required';
          }
          else if (data.country == '') {
            error = 'Country is required';
          }
          else if (data.landmark == '') {
            error = 'Landmark is required';
          }
          else if (data.building == '') {
            error = 'Building is required';
          }
        }

        if (error != '') {
            $('#ermsg').text(error);
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        }
        else{
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('/register_on_checkout') }}",
                data: data,
                method: 'POST',
                success: function(response) {
                    $('#preloader').hide();
                    if (response.status == 1) {
                            $('#get_new_userID').val(response.data.id);
                            if (response.data.otp_status == false && response.data.otp_sent == true) {
                                $('#get_otp_number').val(response.data.mobile);                            
                                $('#otp_verify').modal('show');
                                timerOn = true;
                                timer(120);
                            }
                            else if (response.data.otp_status == true ) {
                                $('#customButton').click();
                            }


                    } else {
                        $('#ermsg').text(response.message);
                        $('#error-msg').addClass('alert-danger');
                        $('#error-msg').css("display","block");

                        setTimeout(function() {
                            $("#success-msg").hide();
                        }, 5000);
                    }
                },
                error: function(error) {
                    $('#preloader').hide();
                    $('#ermsg').text(error.responseJSON.message);
                        $('#error-msg').addClass('alert-danger');
                        $('#error-msg').css("display","block");

                        setTimeout(function() {
                            $("#success-msg").hide();
                        }, 5000);
                }
            });
        }

    }

    $(document).ready(function(){
        $('#verify_otp').click(function(){
            if ($('#get_otp').val() == '') {
                $('#verification_error').text('Code Required');
                $('#verification_error').fadeIn();
            }
            else if($('#get_otp_number').val() == ''){
                $('#verification_error').text('Mobile Required');
                $('#verification_error').fadeIn();
            }
            else{           
                $('#verification_error').fadeOut();
                $('#preloader').show()     
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:"{{ URL::to('/verifyotp-checkout') }}",
                    data: {
                        code: $('#get_otp').val(),
                        mobile: $('#get_otp_number').val()
                    },
                    method: 'POST',
                    success: function(response) {
                        $('#preloader').hide();
                        if (response.status == 1) {
                            $('#otp_verify').modal('hide');
                            $('#customButton').click();
                        } else {
                            $('#verification_error').text(response.message);
                            $('#verification_error').fadeIn();
                        }
                    },
                    error: function(error) {
                        $('#preloader').hide();
                        $('#verification_error').text(error.responseJSON.message);
                        $('#verification_error').fadeIn();
                    }
                });
            }
        })
    })
@endif
    var handler = StripeCheckout.configure({
      key: $('#stripe').val(),
      image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
      locale: 'auto',
      token: function(token) {
        // You can access the token ID with `token.id`.
        // Get the token ID to your server-side code for use.

        var order_total = parseFloat($('#order_total').val());
        var tax = parseFloat($('#tax').val());
        var delivery_charge = parseFloat($('#delivery_charge').val());
        var discount_amount = parseFloat($('#discount_amount').val());
        var paid_amount = parseFloat($('#paid_amount').val());
        var notes = $('#notes').val();
        var address = $('#address').val();
        var promocode = $('#getpromo').val();
        var tax_amount = $('#tax_amount').val();
        var discount_pr = $('#discount_pr').val();
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var building = $('#building').val();
        var landmark = $('#landmark').val();
        var postal_code = $('#postal_code').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var country = $('#country').val();
        var order_type = $("input:radio[name=cart-delivery]:checked").val();
        var token = token.id;


        $('#preloader').show();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            @if($islogin == true)
                url:"{{ URL::to('stripe-payment/charge') }}",
            @else
                url:"{{ URL::to('stripe-payment/charge-guest') }}",
            @endif            
            data: {
                order_total : paid_amount ,
                address: address , 
                promocode: promocode , 
                discount_amount: discount_amount , 
                discount_pr: discount_pr , 
                tax : tax,
                tax_amount : tax_amount,
                delivery_charge : delivery_charge ,
                notes : notes,
                order_type : order_type,
                lat : lat,
                lang : lang,
                building : building,
                landmark : landmark,
                postal_code : postal_code,
                city : city,
                state : state,
                country : country,
                stripeToken : token,
                @if($islogin == false)
                    user_id: $('#get_new_userID').val()
                @endif
            }, 
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    if (response.redirect_to == 1) {
                        window.location.href = SITEURL + '/orders';
                    }
                    else{
                        window.location.href = SITEURL + '/thankyou';
                    }
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

                $('#ermsg').text(error.responseJSON.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
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

    $('#customButton').on('click', function(e) {
        "use strict";
        // Open Checkout with further options:
        var paid_amount = parseFloat($('#paid_amount').val());
        var order_total = parseFloat($('#order_total').val());
        var order_type = $("input:radio[name=cart-delivery]:checked").val();
        var address = $('#address').val();
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var landmark = $('#landmark').val();
        var postal_code = $('#postal_code').val();
        var building = $('#building').val();
        var email = $('#email').val();

        if (order_type == "1") {
            // if (address == "" && lat == "" && lang == "") {
            //     $('#ermsg').text('Address is required');            
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);
            // } else if (lat == "") {
            //     $('#ermsg').text('Please select the address from suggestion');
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);

            // } else if (lang == "") {
            //     $('#ermsg').text('Please select the address from suggestion');
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);

            // } else if (building == "") {
            //     $('#ermsg').text('Door / Flat no. is required');
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);

            // } else if (landmark == "") {
            //     $('#ermsg').text('Landmark is required');
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);
            // } else if (postal_code == "") {
            //     $('#ermsg').text('Postal Code is required');
            //     $('#error-msg').addClass('alert-danger');
            //     $('#error-msg').css("display","block");

            //     setTimeout(function() {
            //         $("#error-msg").hide();
            //     }, 5000);
            // } else {
                handler.open({
                    name: 'Punjabi by Nature',
                    description: 'Order payment',
                    amount: paid_amount*100,
                    currency: "USD",
                    email: email
                });
                e.preventDefault();
                // Close Checkout on page navigation:
                $(window).on('popstate', function() {
                  handler.close();
                });
            // }
        } else {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('/home/checkpincode') }}",
                data: {
                    postal_code: postal_code,
                    order_total: order_total,
                },
                method: 'POST',
                success: function(result) {
                    if (result.status == 1) {
                        handler.open({
                            name: 'Punjabi by Nature',
                            description: 'Order payment',
                            amount: paid_amount*100,
                            currency: "USD",
                            email: email
                        });
                        e.preventDefault();
                        // Close Checkout on page navigation:
                        $(window).on('popstate', function() {
                          handler.close();
                        });
                    } else {
                        $('#ermsg').text(result.message);
                        $('#error-msg').addClass('alert-danger');
                        $('#error-msg').css("display","block");

                        setTimeout(function() {
                            $("#error-msg").hide();
                        }, 5000);
                    }
                },
            });
        }

    });

</script>

<script>


    $(document).ready(function() {
        "use strict";


        $('input[name="register_type"]').change(function(){
            let html = '<div class="mb-3 mt-4 col-md-12"><h5>Passwords</h5></div><div class="col-md-6"><input type="password" id="password" name="password" placeholder="Password"></div><div class="col-md-6"><input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"></div>';
            if ($(this).val() == 2) {
                $('#pwd_div').html(html);
            }
            else{
                 $('#pwd_div').html('');
            }
        })
        $("input[name$='cart-delivery']").click(function() {
            var test = $(this).val();

            if (test == 1) {
                $("#address").show();
                $("#delivery_charge_hide").show();
                $("#building").show();
                $("#landmark").show();
                $("#postal_code").show();
                $(".stripe").show();
                $(".select_add").show();
                $("#dummy-msg").show();
                $("#customButton").hide();

                var order_total = parseFloat($('#order_total').val());
                var delivery_charge = parseFloat($('#delivery_charge').val());
                var tax_amount =  parseFloat($('#tax_amount').val());
                var discount_amount =  parseFloat($('#discount_amount').val());

                if (isNaN(discount_amount)) {
                    $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount+delivery_charge).toFixed(2));

                    $('#paid_amount').val((order_total+tax_amount+delivery_charge).toFixed(2));
                } else {
                    $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount+delivery_charge-discount_amount).toFixed(2));

                    $('#paid_amount').val((order_total+tax_amount+delivery_charge-discount_amount).toFixed(2));
                }

            } else {
                $("#address").hide();
                $("#delivery_charge_hide").hide();
                $("#building").hide();
                $("#landmark").hide();
                $("#postal_code").hide();
                $("#dummy-msg").hide();
                $(".stripe").hide();
                $(".select_add").hide();
                $("#customButton").show();
            
                var order_total = parseFloat($('#order_total').val());
                var delivery_charge = parseFloat($('#delivery_charge').val());
                var tax_amount =  parseFloat($('#tax_amount').val());
                var discount_amount =  parseFloat($('#discount_amount').val());

                if (isNaN(discount_amount)) {
                    $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount).toFixed(2));
                    $('#paid_amount').val((order_total+tax_amount).toFixed(2));
                } else {
                    $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount-discount_amount).toFixed(2));

                    $('#paid_amount').val((order_total+tax_amount-discount_amount).toFixed(2));
                }
            }
        });
    });


   var SITEURL = '{{URL::to('')}}';
   $.ajaxSetup({
     headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
   }); 
   $('body').on('click', '.buy_now', function(e){
    "use strict";
    var order_total = parseFloat($('#order_total').val());
    var tax = parseFloat($('#tax').val());
    var delivery_charge = parseFloat($('#delivery_charge').val());
    var discount_amount = parseFloat($('#discount_amount').val());
    var paid_amount = parseFloat($('#paid_amount').val());
    var notes = $('#notes').val();
    var address = $('#address').val();
    var promocode = $('#getpromo').val();
    var tax_amount = $('#tax_amount').val();
    var discount_pr = $('#discount_pr').val();
    var lat = $('#lat').val();
    var lang = $('#lang').val();
    var building = $('#building').val();
    var landmark = $('#landmark').val();
    var postal_code = $('#postal_code').val();
    var order_type = $("input:radio[name=cart-delivery]:checked").val();

    if (order_type == "1") {
        if (address == "" && lat == "" && lang == "") {
            $('#ermsg').text('Address is required');            
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else if (lat == "") {
            $('#ermsg').text('Please select the address from suggestion');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (lang == "") {
            $('#ermsg').text('Please select the address from suggestion');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (building == "") {
            $('#ermsg').text('Door / Flat no. is required');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (landmark == "") {
            $('#ermsg').text('Landmark is required');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('/home/checkpincode') }}",
                data: {
                    postal_code: postal_code,
                    order_total: order_total,
                },
                method: 'POST',
                success: function(result) {
                    if (result.status == 1) {
                        var options = {
                            "key": $('#razorpay').val(),
                            "amount": (parseInt(paid_amount*100)), // 2000 paise = INR 20
                            "name": "Punjabi by Nature",
                            "description": "Order payment",
                            "image": '{!! asset("storage/app/public/images/about/".$getabout->logo) !!}',
                            "handler": function (response){
                                $('#preloader').show();
                                $.ajax({
                                    url: SITEURL + '/payment',
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        order_total : paid_amount ,
                                        razorpay_payment_id: response.razorpay_payment_id , 
                                        address: address , 
                                        promocode: promocode , 
                                        discount_amount: discount_amount , 
                                        discount_pr: discount_pr , 
                                        tax : tax ,
                                        tax_amount : tax_amount ,
                                        delivery_charge : delivery_charge ,
                                        notes : notes,
                                        order_type : order_type,
                                        lat : lat,
                                        lang : lang,
                                        building : building,
                                        landmark : landmark,
                                        postal_code : postal_code,
                                    }, 
                                    success: function (msg) {
                                    $('#preloader').hide();
                                    window.location.href = SITEURL + '/orders';
                                }
                            });
                           
                        },
                            "prefill": {
                                "contact": '{{@$userinfo->mobile}}',
                                "email":   '{{@$userinfo->email}}',
                                "name":   '{{@$userinfo->name}}',
                            },
                            "theme": {
                                "color": "#366ed4"
                            }
                        };

                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                        e.preventDefault();
                    } else {
                        $('#ermsg').text(result.message);
                        $('#error-msg').addClass('alert-danger');
                        $('#error-msg').css("display","block");

                        setTimeout(function() {
                            $("#error-msg").hide();
                        }, 5000);
                    }
                },
            });
        }
    } else {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/home/checkpincode') }}",
            data: {
                order_total: order_total,
            },
            method: 'POST',
            success: function(result) {
                if (result.status == 1) {
                    var options = {
                        "key": $('#razorpay').val(),
                        "amount": (parseInt(paid_amount*100)), // 2000 paise = INR 20
                        "name": "Punjabi by Nature",
                        "description": "Order payment",
                        "image": '{!! asset("storage/app/public/images/about/".$getabout->logo) !!}',
                        "handler": function (response){
                            $('#preloader').show();
                            $.ajax({
                                url: SITEURL + '/payment',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    order_total : paid_amount ,
                                    razorpay_payment_id: response.razorpay_payment_id , 
                                    address: address , 
                                    promocode: promocode , 
                                    discount_amount: discount_amount , 
                                    discount_pr: discount_pr , 
                                    tax : tax ,
                                    tax_amount : tax_amount ,
                                    delivery_charge : '0.00',
                                    notes : notes,
                                    order_type : order_type,
                                    lat : lat,
                                    lang : lang,
                                    building : building,
                                    landmark : landmark,
                                    postal_code : postal_code,
                                }, 
                                success: function (msg) {
                                $('#preloader').hide();
                                window.location.href = SITEURL + '/orders';
                            }
                        });
                       
                    },
                        "prefill": {
                            "contact": '{{@$userinfo->mobile}}',
                            "email":   '{{@$userinfo->email}}',
                            "name":   '{{@$userinfo->name}}',
                        },
                        "theme": {
                            "color": "#366ed4"
                        }
                    };

                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    e.preventDefault();
                } else {
                    $('#ermsg').text(result.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
        });
    }
});
/*document.getElementsClass('buy_plan1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}*/
    
    function WalletOrder() 
    {
        "use strict";
        var total_order = parseFloat($('#order_total').val());
        var tax = parseFloat($('#tax').val());
        var delivery_charge = parseFloat($('#delivery_charge').val());
        var discount_amount = parseFloat($('#discount_amount').val());
        var paid_amount = parseFloat($('#paid_amount').val());
        var notes = $('#notes').val();
        var address = $('#address').val();
        var promocode = $('#getpromo').val();
        var tax_amount = $('#tax_amount').val();
        var discount_pr = $('#discount_pr').val();
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var postal_code = $('#postal_code').val();
        var building = $('#building').val();
        var landmark = $('#landmark').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var country = $('#country').val();
        var order_type = $("input:radio[name=cart-delivery]:checked").val();

        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/orders/walletorder') }}",
            data: {
                order_total : paid_amount ,
                total_order : total_order ,
                address: address , 
                promocode: promocode , 
                discount_amount: discount_amount , 
                discount_pr: discount_pr , 
                tax : tax,
                tax_amount : tax_amount,
                delivery_charge : delivery_charge ,
                notes : notes,
                order_type : order_type,
                lat : lat,
                lang : lang,
                postal_code : postal_code,
                building : building,
                landmark : landmark,
                city : city,
                state : state,
                country : country,
            }, 
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    window.location.href = SITEURL + '/orders';
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
    }

    function CashonDelivery() 
    {
        var total_order = parseFloat($('#order_total').val());
        var tax = parseFloat($('#tax').val());
        var delivery_charge = parseFloat($('#delivery_charge').val());
        var discount_amount = parseFloat($('#discount_amount').val());
        var paid_amount = parseFloat($('#paid_amount').val());
        var notes = $('#notes').val();
        var address = $('#address').val();
        var promocode = $('#getpromo').val();
        var tax_amount = $('#tax_amount').val();
        var discount_pr = $('#discount_pr').val();
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var postal_code = $('#postal_code').val();
        var building = $('#building').val();
        var landmark = $('#landmark').val();
        var order_type = $("input:radio[name=cart-delivery]:checked").val();

        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/orders/cashondelivery') }}",
            data: {
                order_total : paid_amount ,
                total_order : total_order ,
                address: address , 
                promocode: promocode , 
                discount_amount: discount_amount , 
                discount_pr: discount_pr , 
                tax : tax,
                tax_amount : tax_amount,
                delivery_charge : delivery_charge ,
                notes : notes,
                order_type : order_type,
                lat : lat,
                lang : lang,
                postal_code : postal_code,
                building : building,
                landmark : landmark,
            }, 
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    window.location.href = SITEURL + '/orders';
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
    }

    function stripe() {
        var postal_code = $('#postal_code').val();
        var order_total = $('#order_total').val();
        if ($('input[name="cart-delivery"]:checked').val() == 1 && ($('input#address').val() == '' || $('input#address').length == 0)) {
             $('#ermsg').text('Address is required');
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
        }
        else{
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/home/checkpincode') }}",
            data: {
                postal_code: postal_code,
                order_total: order_total,
            },
            method: 'POST',
            success: function(result) {
                if (result.status == 1) {
                    $("#customButton").click();
                } else {
                    $('#ermsg').text(result.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
        });
    }
    }

</script>

<script>
jQuery(document).ready(function(){
jQuery('#ajaxSubmit').click(function(e){
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
    $('#preloader').show();
    jQuery.ajax({
        url: "{{ URL::to('/cart/applypromocode') }}",
        method: 'post',
        data: {
            promocode: jQuery('#promocode').val()
        },
        success: function(response){
            $('#preloader').hide();

            if (response.status == 1) {

                $('.offer_amount').css("display","flex");
                var order_total = parseFloat($('#order_total').val());
                var delivery_charge = parseFloat($('#delivery_charge').val());
                var tax_amount =  parseFloat($('#tax_amount').val());
                var offer_amount = (order_total*response.data.offer_amount/100);

                $('#discount_pr').val(response.data.offer_amount);
                $('#getpromo').val(response.data.offer_code);

                $('#offer_amount').text('-{{$taxval->currency}}'+(order_total*response.data.offer_amount/100).toFixed(2));
                $('#discount_amount').val((order_total*response.data.offer_amount/100));

                $('#total_amount').text('{{$taxval->currency}}'+((order_total+delivery_charge-offer_amount)+tax_amount).toFixed(2));

                $('#paid_amount').val(((order_total+delivery_charge-offer_amount)+tax_amount).toFixed(2));

                $('#msg').text(response.message);
                $('#success-msg').addClass('alert-success');
                $('#success-msg').css("display","block");

                location.reload();
            } else {
                $('#ermsg').text(response.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display","block");

                setTimeout(function() {
                    $("#success-msg").hide();
                }, 5000);
            }

        }});
    });
});

jQuery(document).ready(function(){
jQuery('#ajaxRemove').click(function(e){
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });
    $('#preloader').show();
    jQuery.ajax({
        url: "{{ URL::to('/cart/removepromocode') }}",
        method: 'post',
        data: {
            promocode: jQuery('#promocode').val()
        },
        success: function(response){

            $('#preloader').hide();
            if (response.status == 1) {
                $('.offer_amount').css("display","none");
                var order_total = parseFloat($('#order_total').val());
                var delivery_charge = parseFloat($('#delivery_charge').val());
                var tax_amount =  parseFloat($('#tax_amount').val());

                $('#discount_pr').val('');

                $('#discount_amount').val('');

                $('#total_amount').text('{{$taxval->currency}}'+((order_total+delivery_charge)+tax_amount).toFixed(2));

                $('#paid_amount').val(((order_total+delivery_charge)+tax_amount).toFixed(2));

                $('#msg').text(response.message);
                $('#success-msg').addClass('alert-success');
                $('#success-msg').css("display","block");

                location.reload();
            } else {

            }            
        }});
    });
});

function qtyupdate(cart_id,item_id,type) 
{
    var qtys= parseInt($("#number_"+cart_id).val());
    var max_qty = $("#max_qty").val();
    var item_id= item_id;
    var cart_id= cart_id;

    if (type == "decreaseValue") {
        qty= qtys-1;
    } else {
        qty= qtys+1;
    }

    if (qty >= "1" && qty <= max_qty) {
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/cart/qtyupdate') }}",
            data: {
                cart_id: cart_id,
                qty:qty,
                item_id,item_id,
                type,type
            },
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    location.reload();
                } else {
                    $('#ermsg').text(response.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display","block");

                    setTimeout(function() {
                        $("#success-msg").hide();
                    }, 5000);
                }
            },
            error: function(error) {

                // $('#errormsg').show();
            }
        });
    } else {

        if (qty < "1") {
            $('#ermsg').text("You've reached the minimum units allowed for the purchase of this item");
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else {
            $('#ermsg').text("You've reached the maximum units allowed for the purchase of this item");
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display","block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        }
    }
}


$('body').on('click','.btn-copy',function(e) {
            
    var text = $(this).attr('data-id');
    // navigator.clipboard.writeText(text).then(function() {
        $('#promocode').val(text);
        $('#staticBackdrop').modal('hide');
    // }, function(err) {
         // console.error('Async: Could not copy text: ', err);
    // });
    
});

$('body').on('click','.btn-select',function(e) {
            
    var address = $(this).attr('data-address');
    var postal_code = $(this).attr('data-postal_code');
    var building = $(this).attr('data-building');
    var landmark = $(this).attr('data-landmark');
    var lat = $(this).attr('data-lat');
    var lang = $(this).attr('data-lang');
    var city = $(this).attr('data-city');
    var state = $(this).attr('data-state');
    var country = $(this).attr('data-country');
    var deliverycharge = ($(this).attr('data-deliverycharge') != '') ? $(this).attr('data-deliverycharge') : 0;

    $('#shipping_charge').text('{{$taxval->currency}}'+deliverycharge);
    $('#delivery_charge').val(deliverycharge);

    var order_total = parseFloat($('#order_total').val());
    var tax_amount =  parseFloat($('#tax_amount').val());
    var discount_amount =  parseFloat($('#discount_amount').val());
    var delivery_charge = parseFloat($('#delivery_charge').val());

    if (isNaN(discount_amount)) {
        $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount+delivery_charge).toFixed(2));

        $('#paid_amount').val((order_total+tax_amount+delivery_charge).toFixed(2));
    } else {
        $('#total_amount').text('{{$taxval->currency}}'+(order_total+tax_amount+delivery_charge-discount_amount).toFixed(2));

        $('#paid_amount').val((order_total+tax_amount+delivery_charge-discount_amount).toFixed(2));
    }


        $('#address').val(address);
        $('#postal_code').val(postal_code);
        $('#building').val(building);
        $('#landmark').val(landmark);
        $('#lat').val(lat);
        $('#lang').val(lang);
        $('#city').val(city);
        $('#state').val(state);
        $('#country').val(country);
        $('#select_address').modal('hide');    
});

$('.view_addons').click(function (){
    
    if ($(this).hasClass('active')) {
        $(this).text('View more detail');
        
        $(this).next('.cart-addons-wrap').slideUp();
        $(this).removeClass('active');
    }
    else{
        $(this).text('View less');
        $(this).next('.cart-addons-wrap').slideDown();
        $(this).addClass('active');
    }
    

})

</script>
@if (env('Environment') != 'sendbox')
<script>
    function initialize() {
        "use strict";
      var input = document.getElementById('address');
      var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                
                if (addressType == "administrative_area_level_1") {
                  document.getElementById("state").value = place.address_components[i].short_name;
                }

                if (addressType == "locality") {
                  document.getElementById("city").value = place.address_components[i].short_name;
                }

                // for the country, get the country code (the "short name") also
                if (addressType == "country") {
                  document.getElementById("country").value = place.address_components[i].short_name;
                }
              }

            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('lang').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    function addinitialize() {
        "use strict";
      var input = document.getElementById('get_address');
      var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                
                if (addressType == "administrative_area_level_1") {
                  document.getElementById("get_state").value = place.address_components[i].short_name;
                }

                if (addressType == "locality") {
                  document.getElementById("get_city").value = place.address_components[i].short_name;
                }

                // for the country, get the country code (the "short name") also
                if (addressType == "country") {
                  document.getElementById("get_country").value = place.address_components[i].short_name;
                }
              }

            document.getElementById('get_lat').value = place.geometry.location.lat();
            document.getElementById('get_lang').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', addinitialize);



</script>
@endif