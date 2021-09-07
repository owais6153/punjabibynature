@include('front.theme.header')>

<section class="order-details">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.order_details') }}</h2>
        <p>({{$summery['order_number']}} - {{$summery['created_at']}})</p>
        @if($summery['order_type'] == 1)
            @if($summery['status'] == 1)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_on_the_way') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 2)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li class="active">{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_on_the_way') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 3)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li class="active">{{ trans('labels.order_ready') }}</li>
                    <li class="active">{{ trans('labels.order_on_the_way') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 4)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li class="active">{{ trans('labels.order_ready') }}</li>
                    <li class="active">{{ trans('labels.order_on_the_way') }}</li>
                    <li class="active">{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 5)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.cancel_by_user') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_on_the_way') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 6)
                <ul class="progressbar">
                    <li class="active">{{ trans('labels.cancel_by_admin') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_on_the_way') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @endif
        @else
            @if($summery['status'] == 1)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 2)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li class="active">{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 4)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">{{ trans('labels.order_placed') }}</li>
                    <li class="active">{{ trans('labels.order_ready') }}</li>
                    <li class="active">{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 5)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">{{ trans('labels.cancel_by_user') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @elseif($summery['status'] == 6)
                <ul class="progressbar" style="text-align: center;">
                    <li class="active">{{ trans('labels.cancel_by_admin') }}</li>
                    <li>{{ trans('labels.order_ready') }}</li>
                    <li>{{ trans('labels.order_delivered') }}</li>
                </ul>
            @endif
        @endif
        <div class="row">
            <div class="col-lg-8">
                @foreach ($orderdata as $orders)
                <div class="order-details-box">
                    <div class="order-details-img">
                        <img src='{{$orders->item_image}}' alt="">
                    </div>
                    <div class="order-details-name">
                        <a href="javascript:void(0)">
                            <a href="{{URL::to('product-details/'.$orders->id)}}">
                                <h3>{{$orders->item_name}} - ({{$orders->variation}} - {{$getdata->currency}}{{$orders->variation_price}})<span>
                                    {{$getdata->currency}}{{number_format($orders->qty * $orders->total_price,2)}}</span></h3>
                            </a>
                        </a>
                        <p>{{ trans('labels.qty') }} : {{$orders->qty}}</p>

                        <?php 
                        $addons_id = explode(",",$orders->addons_id);
                        $addons_price = explode(",",$orders->addons_price);
                        $addons_name = explode(",",$orders->addons_name);
                        ?>

                        @if ($orders->addons_id != "")
                        @foreach ($addons_id as $key =>  $addons)                                
                            <div class="cart-addons-wrap">
                                <div class="cart-addons">
                                <b>{{$addons_name[$key]}}</b> : {{$getdata->currency}}{{number_format($addons_price[$key], 2)}}
                                </div>
                            </div>
                        @endforeach
                        @endif
                        @if ($orders->item_notes != "")
                            <p class="cart-pro-note">{{$orders->item_notes}}</p>
                        @endif
                        
                    </div>
                </div>
                <?php
                    $data[] = array(
                        "total_price" => $orders->qty * $orders->total_price,
                    );
                ?>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="order-payment-summary">
                    <h3>{{ trans('labels.payment_summary') }}</h3>
                    <p>{{ trans('labels.order_total') }} <span>{{$getdata->currency}}{{number_format(array_sum(array_column(@$data, 'total_price')), 2)}}</span></p>
                    
                    <p>{{ trans('labels.tax') }}<span>{{$getdata->currency}}{{number_format($summery['tax_amount'], 2)}}</span></p>

                    <p>{{ trans('labels.delivery_charge') }} <span>{{$getdata->currency}}{{number_format(@$summery['delivery_charge'], 2)}}</span></p>

                    @if ($summery['promocode'] !="")
                    <p>{{ trans('labels.discount') }} ({{$summery['promocode']}}) <span>- {{$getdata->currency}}{{number_format($summery['discount_amount'], 2)}}</span></p>
                    @endif
                    <?php
                    $a = array_sum(array_column(@$data, 'total_price'));
                    $b = $summery['tax_amount'];
                    $c = $summery['delivery_charge'];
                    $d = $summery['discount_amount'];
                    
                    if ($d == "NaN") {
                        $total = $a+$b+$c;
                    } else {
                        $total = $a+$b+$c-$d;
                    }
                    
                    ?>
                    <p class="order-details-total">{{ trans('labels.total_amount') }} <span>{{$getdata->currency}}{{number_format($total, 2)}}</span></p>
                </div>

                @if($summery['driver_name'] != "")
                
                    <div class="order-add">
                        <h6>{{ trans('labels.driver_info') }}</h6>
                            <div class="order-details-img">
                                <img src='{{$summery["driver_profile_image"]}}' alt="">
                            </div>
                        <p class="mt-3">{{$summery['driver_name']}}</p>
                        <p>
                            <a href="tel:{{$summery['driver_mobile']}}"> {{$summery['driver_mobile']}}</a>
                        </p>
                    </div>

                @endif

                @if($summery['order_type'] == 1)
                
                    <div class="order-add">
                        <h6>{{ trans('labels.delivery_address') }}</h6>
                        <p>{{$summery['address']}}</p>
                        <h6>Door / Flat no.</h6>
                        <p>{{$summery['building']}}</p>
                        <h6>Landmark</h6>
                        <p>{{$summery['landmark']}}</p>
                        <h6>Pincode</h6>
                        <p>{{$summery['pincode']}}</p>
                    </div>

                @endif
                
                @if ($summery['order_notes'] !="")
                <div class="order-add">
                    <h6>{{ trans('labels.notes') }}</h6>
                    <p>{{$summery['order_notes']}}</p>
                </div>
                @endif

                @if ($summery['status'] == 1)
                <div class="delivery-btn-wrap">
                    <button type="button" class="btn open comman" onclick="OrderCancel('{{$summery['id']}}')">{{ trans('labels.cancel_order') }}</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')