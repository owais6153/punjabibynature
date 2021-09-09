@include('front.theme.header')

<section class="banner-sec">
    <div class="container-fluid px-0">
        <div class="banner-carousel owl-carousel owl-theme">
            @foreach ($getslider as $slider)
            <div class="item">
                <img src='{!! asset("storage/app/public/images/slider/".$slider->image) !!}' alt="">
                <div class="banner-contant">
                    <h1>{{$slider->title}}</h1>
                    <p>{{$slider->description}}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- <section class="feature-sec">
    <div class="container">
        <div class="feature-carousel owl-carousel owl-theme">
            @foreach ($getbanner as $banner)
            <div class="item">
                <div class="feature-box">
                    @if ($banner->type != "")
                        @if ($banner->type == "category")
                            <a href="{{URL::to('product/'.$banner->cat_id)}}">
                        @else
                            <a href="{{URL::to('product-details/'.$banner->item_id)}}">
                        @endif
                            <img src='{!! asset("storage/app/public/images/banner/".$banner->image) !!}' alt="">
                        </a>
                    @else
                        <img src='{!! asset("storage/app/public/images/banner/".$banner->image) !!}' alt="">
                    @endif
                    <div class="feature-contant">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section> -->
<!-- <section class="product-prev-sec">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.our_products') }}</h2>
        <div id="sync2" class="owl-carousel owl-theme">
            <?php $i=1; ?>
            @foreach ($getcategory as $category)
            <div class="item product-tab">
                <img src='{!! asset("storage/app/public/images/category/".$category->image) !!}' alt=""> {{$category->category_name}}
            </div>
            <?php $i++; ?>
            @endforeach
        </div>
        <div id="sync1" class="owl-carousel owl-theme">
            <?php $i=1; ?>
            @foreach($getcategory as $category)
            <div class="item">
                <div class="tab-pane">
                    <div class="row">
                        <?php $count = 0; ?>
                        @foreach($getitem as $item)
                        @if($item->cat_id==$category->id)
                        <?php if($count == 6) break; ?>
                        <div class="col-lg-4 col-md-6">
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
                                    <a href="{{URL::to('product-details/'.$item->id)}}">
                                        <img src='{{$item["itemimage"]->image }}' alt="">
                                    </a>
                                    @if (Session::get('id'))
                                        @if ($item->is_favorite == 1)
                                            <i class="fas fa-heart i"></i>
                                        @else
                                            <i class="fal fa-heart i" onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                                        @endif
                                    @else
                                        <a class="i" href="{{URL::to('/signin')}}"><i class="fal fa-heart"></i></a>
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
                                        <p>{{ Str::limit($item->item_description, 60) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $count++; ?>
                        @endif
                        @endforeach
                    </div>
                    <a href="{{URL::to('product/')}}" class="btn">{{ trans('labels.view_more') }}</a>
                </div>
            </div>
            <?php $i++; ?>
            @endforeach
        </div>
    </div>
</section> -->
<section class="first-sec-home">
    <div class="container">
        <div class="about-box first-section-home">
            <div class="about-contant">
                <h2 class="sec-head text-left">FRESH TAKES ON OLD STANDARDS</h2>
                <p>The Punjabi cuisine is a culinary style which incorporates rich traditions of many distinct and local ways of cooking. One of them being a special form of tandoori cooking which is now famous in many parts of the world. We are also heavily influenced by the agriculture and farming lifestyle, which is why we pride ourselves on sourcing out the freshest ingredients, including seasonal produce to ensure our customers have the very best dining experience. Our chefs excellently craft traditional Punjabi dishes packed with flavourful herbs and spices combined with textures to appease taste buds.</p>
                <a href="{{URL::to('product/')}}" class="btn-order-now">{{ trans('labels.view_more') }}</a>

            </div>
            <div class="home-first-sec-img">
                <img src='{!! asset("storage/app/public/assets/images/fresh-takes.png") !!}' alt="">
            </div>
        </div>
    </div>
</section>
<section class="third-sec-home" style="background:url('storage/app/public/assets/images/Appetizer.jpg');">
    <div class="container">
        <div class="about-box third-section-home">
            <div class="sectionhome-contant">
                <h2 class="sec-head text-left">AMBASSADORS OF <br/>HOSPITALITY</h2>
                <a href="{{URL::to('product/')}}" class="btn-order-now">About Us</a>

            </div>
        </div>
    </div>
</section>
<section class="fourth-sec-home">
    <div class="container">
        <div class="about-box fourth-section-home">
            <div class="about-contant">
                <img src='{!! asset("http://localhost/punjabibynature/storage/app/public/assets/images/bowl-panner.png") !!}' alt="">
             </div>
            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
                <div class="home-fourth-right">
                    <h2 class="sec-head text-left">A MENU WITH RICH BUTTERY FLAVOURS</h2>
                    <p>Every dish here at Punjabi By Nature has its own story from authentic traditional recipes. With an seasoned team of cooks and chefs who have left their mark on our kitchen. We are always finding inspiration for our menu to keep it fresh. From new ingredients, new approaches, and infuzed partnership of modern and traditional cooking styles to bring our customers dishes they are sure to love.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="fifth-sec-home" style="background: url('storage/app/public/assets/images/live-catring-video-img.jpg');">
    <div class="container">
        <div class="about-box fifth-section-home">
            <div class="sectionhome-contant">
                <h2 class="sec-head text-left">Live Catering <br/>Make It Memorable</h2>

            </div>
        </div>
    </div>
</section>
<section class="sixth-sec-home fourth-sec-home">
    <div class="container">
        <div class="about-box fourth-section-home">
            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
                <div class="home-fourth-right">
                    <h2 class="sec-head text-left">Live Catering</h2>
                    <p>Whether you are looking for a special lunch or dinner to reward your team for their hard work, or to serve and important client a tasteful meal in your home, we can cater events with live-cooking as an added bonus from 5 to 400 guests.</p>
                </div>
            </div>
            <div class="about-contant">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/vpUM1y9S8Gg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
             </div>
            
        </div>
    </div>
</section>
<section class="fourth-sec-home">
    <div class="container">
        <div class="about-box fourth-section-home sec-home">
            <div class="about-contant">
                <img src='{!! asset("storage/app/public/assets/images/next-event.jpg") !!}' alt="">
             </div>
            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
                <div class="home-fourth-right">
                    <h2 class="sec-head text-left">HOST YOUR NEXT EVENT AT PBN</h2>
                    <p>We specialize in private parties, corporate and press events, weddings and other celebrations from groups 5 to 400. We feature private event space perfect for many occasions, as well as the option to book the entire restaurant.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 
<section class="about-sec">
    <div class="container">
        <div class="about-box">
            <div class="about-img">
                <img src='{!! asset("storage/app/public/images/about/".$getabout->image) !!}' alt="">
            </div>
            <div class="about-contant">
                <h2 class="sec-head text-left">{{ trans('labels.about_us') }}</h2>
                <p>{!! \Illuminate\Support\Str::limit(htmlspecialchars($getabout->about_content, ENT_QUOTES, 'UTF-8'), $limit = 500, $end = '...') !!}</p>
            </div>
        </div>
    </div>
</section> -->

<section class="review-sec">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.our_review') }}</h2>
        <div class="review-carousel owl-carousel owl-theme">
            @foreach($getreview as $review)
            <div class="item">
                <div class="review-profile">
                    <img src='{!! asset("storage/app/public/images/profile/".$review["users"]->profile_image) !!}' alt="">
                </div>
                <h3>{{$review['users']->name}}</h3>
                <p>{{$review->comment}}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

<section class="our-app">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="sec-head">{{ trans('labels.banner_title') }}</h2>
                <p>{{ trans('labels.banner_description') }}</p>
            </div>
            <div class="col-lg-6">
                @if($getabout->ios != "")
                    <a href="{{$getabout->ios}}" class="our-app-icon" target="_blank">
                        <img src="{!! asset('storage/app/public/front/images/apple-store.svg') !!}" alt="">
                    </a>
                @endif

                @if($getabout->android != "")
                    <a href="{{$getabout->android}}" class="our-app-icon" target="_blank">
                        <img src="{!! asset('storage/app/public/front/images/play-store.png') !!}" alt="">
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="contact-from">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="sec-head">{{ trans('labels.contact_us') }}</h2>
                @if($getabout->mobile != "")
                    <a href="tel:{{$getabout->mobile}}" class="contact-box">
                        <i class="fas fa-phone-alt"></i>
                        <p>{{$getabout->mobile}}</p>
                    </a>
                @endif

                @if($getabout->email != "")
                    <a href="mailto:{{$getabout->email}}" class="contact-box">
                        <i class="fas fa-envelope"></i>
                        <p>{{$getabout->email}}</p>
                    </a>
                @endif

                @if($getabout->address != "")
                    <div class="contact-box">
                        <i class="fas fa-home"></i>
                        <p>{{$getabout->address}}</p>
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

@include('front.theme.footer')