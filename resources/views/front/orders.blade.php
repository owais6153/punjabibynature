@include('front.theme.header')>
@if(Session::has('success'))
    <div class="alert alert-success"> {{ Session::get('success') }}</div>
@endif
<section class="favourite">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.my_orders') }}</h2>
        <div class="row">
            @if (count($orderdata) == 0)
                <p>{{ trans('labels.no_data') }}</p>
            @else 
                @foreach ($orderdata as $orders)
                <div class="col-lg-4">
                    <a href="{{URL::to('order-details/'.$orders->id)}}" class="order-box">
                        <div class="order-box-no">
                            {{$orders->date}}
                            <h4>Order ID : <span>{{$orders->order_number}}</span></h4>
                            <span style="color: #fe734c; font-weight: 400">
                                @if($orders->payment_type == 1)
                                    {{ trans('labels.razorpay_payment') }}
                                @elseif($orders->payment_type == 2)
                                    {{ trans('labels.stripe_payment') }}
                                @elseif($orders->payment_type == 3)
                                    {{ trans('labels.wallet_payment') }}
                                @else
                                    {{ trans('labels.cash_payment') }}
                                @endif
                            </span>
                            @if($orders->status == 1)
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.order_placed') }}</span></p>
                            @elseif($orders->status == 2)
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.order_ready') }}</span></p>
                            @elseif($orders->status == 3)
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.order_on_the_way') }}</span></p>
                            @elseif($orders['status'] == 5)
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.cancel_by_user') }}</span></p>
                            @elseif($orders['status'] == 6)
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.cancel_by_admin') }}</span></p>
                            @else
                                <p class="order-status">{{ trans('labels.order_status') }} : <span>{{ trans('labels.order_delivered') }}</span></p>
                            @endif
                        </div>
                        <div class="order-box-price">
                            <h5>{{$getdata->currency}}{{number_format($orders->total_price, 2)}}</h5>
                            @if($orders->order_type == 1)
                                {{ trans('labels.delivery') }}
                            @else
                                {{ trans('labels.pickup') }}
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
        <nav aria-label="Page navigation example">
            @if ($orderdata->hasPages())
            <ul class="pagination" role="navigation">
                {{-- Previous Page Link --}}
                @if ($orderdata->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $orderdata->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                    </li>
                @endif

                <?php
                    $start = $orderdata->currentPage() - 2; // show 3 pagination links before current
                    $end = $orderdata->currentPage() + 2; // show 3 pagination links after current
                    if($start < 1) {
                        $start = 1; // reset start to 1
                        $end += 1;
                    } 
                    if($end >= $orderdata->lastPage() ) $end = $orderdata->lastPage(); // reset end to last page
                ?>

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $orderdata->url(1) }}">{{1}}</a>
                    </li>
                    @if($orderdata->currentPage() != 4)
                        {{-- "Three Dots" Separator --}}
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                @endif
                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ ($orderdata->currentPage() == $i) ? ' active' : '' }}">
                            <a class="page-link" href="{{ $orderdata->url($i) }}">{{$i}}</a>
                        </li>
                    @endfor
                @if($end < $orderdata->lastPage())
                    @if($orderdata->currentPage() + 3 != $orderdata->lastPage())
                        {{-- "Three Dots" Separator --}}
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $orderdata->url($orderdata->lastPage()) }}">{{$orderdata->lastPage()}}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($orderdata->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $orderdata->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
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