@include('front.theme.header')

<section class="favourite">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.favourite_list') }}</h2>
        <div class="row">
            @if (count($favorite) == 0)
                <p>{{ trans('labels.no_data') }}</p>
            @else 
                @foreach ($favorite as $item)
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
                            <i class="fas fa-heart i" onclick="Unfavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                        </div>
                        <div class="product-details-wrap">
                            <div class="product-details">
                                <a href="{{URL::to('product-details/'.$item->id)}}">
                                    <h4>{{$item->item_name}}</h4>
                                </a>
                                
                                <p class="pro-pricing">
                                    @foreach ($item->variation as $key => $value)
                                        {{$getdata->currency}}{{number_format($value->product_price,2)}}
                                        <span id="card2-oldprice">{{$getdata->currency}}{{number_format($value->sale_price, 2)}}</span>
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
                @endforeach
            @endif
        </div>
        <nav aria-label="Page navigation example">
            @if ($favorite->hasPages())
            <ul class="pagination" role="navigation">
                {{-- Previous Page Link --}}
                @if ($favorite->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $favorite->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                    </li>
                @endif

                <?php
                    $start = $favorite->currentPage() - 2; // show 3 pagination links before current
                    $end = $favorite->currentPage() + 2; // show 3 pagination links after current
                    if($start < 1) {
                        $start = 1; // reset start to 1
                        $end += 1;
                    } 
                    if($end >= $favorite->lastPage() ) $end = $favorite->lastPage(); // reset end to last page
                ?>

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $favorite->url(1) }}">{{1}}</a>
                    </li>
                    @if($favorite->currentPage() != 4)
                        {{-- "Three Dots" Separator --}}
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                @endif
                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ ($favorite->currentPage() == $i) ? ' active' : '' }}">
                            <a class="page-link" href="{{ $favorite->url($i) }}">{{$i}}</a>
                        </li>
                    @endfor
                @if($end < $favorite->lastPage())
                    @if($favorite->currentPage() + 3 != $favorite->lastPage())
                        {{-- "Three Dots" Separator --}}
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $favorite->url($favorite->lastPage()) }}">{{$favorite->lastPage()}}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($favorite->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $favorite->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
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
</section>

@include('front.theme.footer')