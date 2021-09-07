@extends('theme.default')

@section('content')


    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/category')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.categories') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getcategory)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-list-alt"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/item')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.items') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getitems)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-cutlery"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/addons')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.addons') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($addons)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-plus"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/users')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.users') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getusers)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-users"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.orders') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getorders)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-shopping-cart"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/reviews')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.reviews') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getreview)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-star"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/promocode')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.promocodes') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getpromocode)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-gift"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/driver')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.drivers') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($driver)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-car"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <a href="{{URL::to('/admin/pincode')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.pincodes') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($getpincode)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-map-pin"></i></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.tax') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{Auth::user()->currency}}{{ number_format($order_tax, 2) }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-calculator"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <a href="{{URL::to('/admin/orders')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.earnings') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{Auth::user()->currency}}{{ number_format($order_total-$order_tax, 2) }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-usd"></i></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <a href="{{URL::to('/admin/banner')}}">
                        <div class="card-body">
                            <h3 class="card-title text-white">{{ trans('labels.banners') }}</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{count($banners)}}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"  style="color:#fff;"><i class="fa fa-bullhorn"></i></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ trans('labels.today_order') }}</h4>
                        <div class="table-responsive" id="table-display">
                            @include('theme.todayorderstable')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" id="assign">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_id" class="col-form-label">{{ trans('labels.order_id') }}:</label>
                        <input type="text" class="form-control" id="bookId" name="bookId" readonly="">
                    </div>
                    <div class="form-group">
                        <label for="category_id" class="col-form-label">{{ trans('messages.select_driver') }}:</label>
                        <select class="form-control" name="driver_id" id="driver_id" required="">
                            <option value="">{{ trans('messages.select_driver') }}</option>
                            @foreach ($getdriver as $driver)
                                <option value="{{$driver->id}}">{{$driver->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="assign()" data-dismiss="modal">{{ trans('labels.save') }}</button>
                </div>
                </form>
            </div>

        </div>
    </div>
    <!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">
    $('.table').dataTable({
      aaSorting: [[0, 'DESC']]
    });
    function StatusUpdate(id,status) {
        "use strict";
        swal({
            title: "{{ trans('messages.are_you_sure') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ trans('messages.yes') }}",
            cancelButtonText: "{{ trans('messages.no') }}",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true,
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:"{{ URL::to('admin/orders/update') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        if (response == 1) {
                            location.reload();
                        } else {
                            swal("Cancelled", "{{ trans('messages.wrong') }} :(", "error");
                        }
                    },
                    error: function(e) {
                        swal("Cancelled", "{{ trans('messages.wrong') }} :(", "error");
                    }
                });
            } else {
                swal("Cancelled", "{{ trans('messages.record_safe') }} :)", "error");
            }
        });
    }

    $(document).on("click", ".open-AddBookDialog", function () {
         var myBookId = $(this).data('id');
         $(".modal-body #bookId").val( myBookId );
    });

    function assign(){     
        var bookId=$("#bookId").val();
        var driver_id = $('#driver_id').val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/orders/assign') }}",
            method:'POST',
            data:{'bookId':bookId,'driver_id':driver_id},
            dataType:"json",
            success:function(data){
                $('#preloader').hide();
                if (data == 1) {
                    location.reload();
                }
            },error:function(data){
               
            }
        });
    }
</script>
@endsection