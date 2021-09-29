
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
                <p>{{$category->category_name}}</p>
            </a>
            @endforeach
        </div>

@csrf
                <div class="row catering">
                     <div class="cat-product">
                    @foreach ($catering_category as $category)
     	         

                    
                  <h3 id="category{{$category->id}}">{{$category->category_name}}</h3>
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
                                
                                @if (Session::get('id'))
                                    @if ($item->is_favorite == 1)
                                        <i class="fas fa-heart i"></i>
                                    @else
                                        <i class="fal fa-heart i" onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                                    @endif
                                @endif
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
            <div class="cart-box-catering">
                 <h3>Cart </h3>
                 <div class="cart-catering-body">
                    <div class="cart-total-catering cart-items-catering">
                        <div class="total-values">
                            <div>6</div>
                            <div>Traditional BreakFast<br/>serves 6</div>
                            <div>$43.53</div>
                            <div><a href="#"><i class="fas fa-times"></i></a></div>
                        </div>
                      
                        
                    </div>

                    <div class="cart-total-catering">
                       <div class="total-values">
                            <div>Food & Beverage</div>
                            <div>$43.53</div>
                        </div>
                        <div class="total-values">
                            <div>Restaurant Delivery Fee</div>
                            <div>$43.53</div>
                        </div>
                        <div class="total-values">
                            <div>8.875% Sales Tax</div>
                            <div>$43.53</div>
                        </div>

                    </div>

                    <div class="cart-total-catering">
                        <div class="total-values">
                            <div><b>Total</b></div>
                            <div><b>$43.53</b></div>
                        </div>
                        <div class="total-description">
                            <div>Price Per Head</div>
                            <a href="#">$7.26/person</a>
                        </div>
                    </div>

                 	<a href="#" class="checkout-btn">Checkout <i class="fas fa-arrow-right"></i></a>
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
