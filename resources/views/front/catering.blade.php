
@include('front.theme.header')

	<section class="fifth-sec-home sec-catering" style="background: url('storage/app/public/assets/images/live-catring-video-img.jpg');">
    <div class="container">
        <div class="about-box fifth-section-home">
            <div class="sectionhome-contant">
                <h2 class="sec-head text-left">Live Catering <br>Make It Memorable</h2>

            </div>
        </div>
    </div>
</section>

<input type="hidden" id="type_catering" value="true" name="">

     <section class="sec2-catering">
     	<div class="container">
     	<h2 class="sec-head">Catering Menu</h2>
     	<div class="cat-aside-wrap">
            @foreach ($catering_category as $category)
            <a href="#category{{$category->id}}" class="cat-check border-top-no @if (request()->id == $category->id) active @endif">
                <p>{{$category->name}}</p>
            </a>
            @endforeach
        </div>

@csrf
                <div class="row catering">
                     <div class="cat-new-product col-md-8">
                    @foreach ($catering_category as $category)
     	         

                    
                  <h3 id="category{{$category->id}}">{{$category->name}}</h3>
<div class="row">
                        @foreach ($category->items as $item)

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
@endforeach
                  
</div>
            <div class="cart-box-catering col-md-4">
                 <h3>Cart </h3>
                 <div class="cart-catering-body">
                    <div class="cart-total-catering cart-items-catering">
                        @empty (!$cateringcartdata)
                            @foreach ($cateringcartdata as $cartIndex => $cart)
                                @if($cart->product_type == 'catering')
                                    @php
                                        $data[] = array(
                                            "total_price" => $cart->qty * $cart->price,
                                            "tax" => ($cart->qty*$cart->price)*$cart->tax/100,
                                            "qty" => $cart->qty
                                        );

                                        if (isset($cart->id)) {
                                            $id = $cart->id;
                                        }
                                        else{
                                            $id = $cartIndex;
                                        }
                                    @endphp
                                    <div class="total-values">
                                        <div>6</div>
                                        <div>{{$cart->item_name}}<br/>serves {{$cart->qty}}</div>
                                        <div>{{$taxval->currency}}{{number_format($cart->qty * $cart->price,2)}}</div>
                                        <div><a href="javascript:void(0)" onclick="RemoveCart({{$id}})"><i class="fas fa-times"></i></a></div>
                                    </div>                      
                                @endif  
                            @endforeach
                        @endif
                    </div>
                    @empty (!@$data)
                        @php 
                            $order_total = array_sum(array_column(@$data, 'total_price'));
                            $tax = array_sum(array_column(@$data, 'tax'));
                            $total = array_sum(array_column(@$data, 'total_price'))+$tax;
                        @endphp
                    @else    
                        @php 
                            $order_total = 0;
                            $tax = 0;
                            $total = 0;
                        @endphp
                    @endif
                    <div class="cart-total-catering">
                       <div class="total-values">
                            <div>Subtotal</div>
                            <div>{{$taxval->currency}}{{$order_total}}</div>
                        </div>
                        <div class="total-values">
                            <div>Tax</div>
                            <div>{{$taxval->currency}}{{$tax}}</div>
                        </div>
                        <div class="total-values">
                            <div>Delivery charge</div>
                            <div>$0</div>
                        </div>

                    </div>

                    <div class="cart-total-catering">
                        <div class="total-values">
                            <div><b>Total</b></div>
                            <div><b>{{$taxval->currency}}{{$order_total}}</b></div>
                        </div>
                    </div>

           
                        <p class="text-left mb-0"><label>Date & Time</label></p>
                        <input type="datetime-local" name="calendar" id="calendar" class="quantity form-control">
                    

                 	<button disabled="" class="checkout-btn">Checkout <i class="fas fa-arrow-right"></i></button>
                 	<p>$100.00 minimum for delivery</p>
                 </div> 
            </div>
         </div>
    </div>
     </section>
@section('script')
<script type="text/javascript">
    $( ".cat-aside-wrap a" ).click(function(e) {
  e.preventDefault();
$('html,body').animate({
            scrollTop: $($(this).attr('href')).offset().top -100},
        'slow');
});

</script>
@endsection
@include('front.theme.footer')
