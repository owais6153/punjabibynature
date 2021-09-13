@include('front.theme.header')

<section class="product-prev-sec product-list-sec">
    <div class="container">
        <div class="product-rev-wrap">
            <div class="cat-aside">
                <h3 class="text-center">{{ trans('labels.categories') }}</h3>
                <div class="cat-aside-wrap">
                    @foreach ($getcategory as $category)
                    <a href="{{URL::to('/product/'.$category->id)}}" class="cat-check border-top-no @if (request()->id == $category->id) active @endif">
                        <img src='{!! asset("storage/app/public/images/category/".$category->image) !!}' alt="">
                        <p>{{$category->category_name}}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="cat-product">
                @csrf
                <div class="cart-pro-head">
                    <h2 class="sec-head">{{ trans('labels.our_products') }}</h2>
                    <div class="btn-wrap" data-toggle="buttons">
                        <label id="list" class="btn">
                            <input type="radio" name="layout" id="layout1"> <i class="fas fa-list"></i>
                        </label>
                        <label id="grid" class="btn active">
                            <input type="radio" name="layout" id="layout2" checked> <i class="fas fa-th"></i>
                        </label>
                    </div>
                </div>
                <div class="row">
                    @foreach ($getitem as $item)
                    <div class="col-xl-4 col-md-6">
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
                <nav aria-label="Page navigation example">
                    @if ($getitem->hasPages())
                    <ul class="pagination" role="navigation">
                        {{-- Previous Page Link --}}
                        @if ($getitem->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                <span class="page-link" aria-hidden="true">&lsaquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $getitem->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                            </li>
                        @endif

                        <?php
                            $start = $getitem->currentPage() - 2; // show 3 pagination links before current
                            $end = $getitem->currentPage() + 2; // show 3 pagination links after current
                            if($start < 1) {
                                $start = 1; // reset start to 1
                                $end += 1;
                            } 
                            if($end >= $getitem->lastPage() ) $end = $getitem->lastPage(); // reset end to last page
                        ?>

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $getitem->url(1) }}">{{1}}</a>
                            </li>
                            @if($getitem->currentPage() != 4)
                                {{-- "Three Dots" Separator --}}
                                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                            @endif
                        @endif
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ ($getitem->currentPage() == $i) ? ' active' : '' }}">
                                    <a class="page-link" href="{{ $getitem->url($i) }}">{{$i}}</a>
                                </li>
                            @endfor
                        @if($end < $getitem->lastPage())
                            @if($getitem->currentPage() + 3 != $getitem->lastPage())
                                {{-- "Three Dots" Separator --}}
                                <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $getitem->url($getitem->lastPage()) }}">{{$getitem->lastPage()}}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($getitem->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $getitem->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
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
</section>



@include('front.theme.footer')