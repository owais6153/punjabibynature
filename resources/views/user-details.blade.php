@extends('theme.default')

@section('content')
<!-- row -->

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.users') }}</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <!-- End Row -->

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("storage/app/public/images/profile/".$getusers->profile_image) !!}' width="100px" class="rounded-circle" alt="">
                        <h5 class="mt-3 mb-1">{{$getusers->name}}</h5>
                        <p class="m-0">{{$getusers->email}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("storage/app/public/front/images/wallet.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">{{ trans('labels.wallet_balance') }}</h5>
                        <p class="m-0">{{Auth::user()->currency}}{{number_format($getusers->wallet, 2)}}</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addMoney" data-whatever="@addMoney"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('labels.add_money') }}</button>

                        <button class="btn btn-danger" data-toggle="modal" data-target="#deductMoney" data-whatever="@deductMoney"><i class="fa fa-minus" aria-hidden="true"></i> {{ trans('labels.deduct_money') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("storage/app/public/front/images/shopping-cart.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">{{count($getorders)}}</h5>
                        <p class="m-0">{{ trans('labels.orders') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src='{!! asset("storage/app/public/front/images/referral-admin.png") !!}' width="100px" alt="">
                        <h5 class="mt-3 mb-1">
                            @if ($getusers->referral_code == "")
                                -
                            @else
                                {{$getusers->referral_code}}
                            @endif
                        </h5>
                        <p class="m-0">{{ trans('labels.user_referral_code') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ trans('labels.all_orders') }}</h4>
                    <div class="table-responsive" id="table-display">
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('labels.order_number') }}</th>
                                    <th>{{ trans('labels.payment_type') }}</th>
                                    <th>{{ trans('labels.payment_id') }}</th>
                                    <th>{{ trans('labels.order_type') }}</th>
                                    <th>{{ trans('labels.order_status') }}</th>
                                    <th>{{ trans('labels.order_assigned_to') }}</th>
                                    <th>{{ trans('labels.created_at') }}</th>
                                    <th>{{ trans('labels.change_status') }}</th>
                                    <th>{{ trans('labels.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($getorders as $orders) {
                                ?>
                                <tr id="dataid{{$orders->id}}">
                                    <td>{{$i}}</td>
                                    <td>{{$orders->order_number}}</td>
                                    <td>
                                        @if($orders->payment_type == 1)
                                            {{ trans('labels.razorpay_payment') }}
                                        @elseif($orders->payment_type == 2)
                                            {{ trans('labels.stripe_payment') }}
                                        @elseif($orders->payment_type == 3)
                                            {{ trans('labels.wallet_payment') }}
                                        @else
                                            {{ trans('labels.cash_payment') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->razorpay_payment_id == '')
                                            --
                                        @else
                                            {{$orders->razorpay_payment_id}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->order_type == 1)
                                            {{ trans('labels.delivery') }}
                                        @else
                                            {{ trans('labels.pickup') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($orders->status == '1')
                                            {{ trans('labels.order_received') }}
                                        @elseif ($orders->status == '2')
                                            {{ trans('labels.order_on_the_way') }}
                                        @elseif ($orders->status == '3')
                                            {{ trans('labels.assigned_to_driver') }}
                                        @elseif ($orders->status == '4')
                                            {{ trans('labels.delivered') }}
                                        @else
                                            {{ trans('labels.cancelled') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orders->name == "")
                                            --
                                        @else
                                            {{$orders->name}}
                                        @endif
                                    </td>
                                    <td>{{$orders->created_at}}</td>
                                    <td>
                                        @if($orders->status == '1')
                                            <a ddata-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','2')" title="" data-original-title="{{ trans('labels.order_received') }}">
                                                <span class="badge badge-secondary px-2" style="color: #fff;">{{ trans('labels.order_received') }}</span>
                                            </a>
                                        @elseif ($orders->status == '2')
                                            @if ($orders->order_type == '2')
                                                <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">{{ trans('labels.pickup') }}</a>
                                            @else
                                                <a class="open-AddBookDialog badge badge-primary px-2" data-toggle="modal" data-id="{{$orders->id}}" data-target="#myModal" style="color: #fff;">{{ trans('labels.assign_to_driver') }}</a>
                                            @endif
                                        @elseif ($orders->status == '3')
                                            <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                                                <span class="badge badge-success px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">{{ trans('labels.assigned_to_driver') }}</span>
                                            </a>
                                        @elseif ($orders->status == '4')
                                            <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                                                <span class="badge badge-success px-2" style="color: #fff;">{{ trans('labels.delivered') }}</span>
                                            </a>
                                        @else
                                            <span class="badge badge-danger px-2">{{ trans('labels.cancelled') }}</span>
                                        @endif

                                        @if ($orders->status != '4' && $orders->status != '5' && $orders->status != '6')
                                            <a data-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','6')" title="" data-original-title="{{ trans('labels.cancel') }}">
                                                <span class="badge badge-danger px-2" style="color: #fff;">{{ trans('labels.cancel') }}</span>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span>
                                            <a data-toggle="tooltip" href="{{URL::to('admin/invoice/'.$orders->id)}}" data-original-title="{{ trans('labels.view') }}">
                                                <span class="badge badge-warning">{{ trans('labels.view') }}</span>
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->

<!-- Add money -->
<div class="modal fade" id="addMoney" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('labels.add_money') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <span id="a_message"></span>
            <form id="add_pincode">
            <div class="modal-body">
                <span id="msg"></span>
                @csrf
                <input type="hidden" class="form-control" name="user_id" id="user_id" value="{{$getusers->id}}" readonly="">
                <div class="form-group">
                    <label for="current_balance" class="col-form-label">{{ trans('labels.current_balance') }}</label>
                    <input type="text" class="form-control" name="current_balance" id="current_balance" value="{{$getusers->wallet}}" readonly="">
                </div>
                <div class="form-group">
                    <label for="amount" class="col-form-label">{{ trans('labels.amount') }}</label>
                    <input type="text" class="form-control" name="amount" placeholder="{{ trans('messages.enter_amount') }}" id="amount">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                    @else
                        <button type="button" class="btn btn-primary" onclick="addbalance()">{{ trans('labels.save') }}</button>
                    @endif
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Deduct money -->
<div class="modal fade" id="deductMoney" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('labels.deduct_money') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <span id="d_message"></span>
            <form id="add_pincode">
            <div class="modal-body">
                <span id="msg"></span>
                @csrf
                <input type="hidden" class="form-control" name="user_id" id="user_id" value="{{$getusers->id}}" readonly="">
                <div class="form-group">
                    <label for="current_balance_d" class="col-form-label">{{ trans('labels.current_balance') }}</label>
                    <input type="text" class="form-control" name="current_balance_d" id="current_balance_d" value="{{$getusers->wallet}}" readonly="">
                </div>
                <div class="form-group">
                    <label for="d_amount" class="col-form-label">{{ trans('labels.amount') }}</label>
                    <input type="text" class="form-control" name="d_amount" placeholder="{{ trans('messages.enter_amount') }}" id="d_amount">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                    @else
                        <button type="button" class="btn btn-primary" onclick="deductbalance()">{{ trans('labels.save') }}</button>
                    @endif
            </div>
            </form>
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
                    <label for="category_id" class="col-form-label">{{ trans('labels.order_id') }}</label>
                    <input type="text" class="form-control" id="bookId" name="bookId" readonly="">
                </div>
                <div class="form-group">
                    <label for="category_id" class="col-form-label">{{ trans('messages.select_driver') }}</label>
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
    function StatusUpdate(id,status) {
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

    function addbalance(){     
        var user_id=$("#user_id").val();
        var current_balance = $('#current_balance').val();
        var amount = $('#amount').val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/users/addmoney') }}",
            method:'POST',
            data:{'user_id':user_id,'current_balance':current_balance,'amount':amount},
            dataType:"json",
            success:function(data){
                $('#preloader').hide();
                if (data == 1) {
                    location.reload();
                } else {
                    $('#a_message').html("<div class='alert alert-danger' role='alert'>{{ trans('messages.enter_amount') }}</div>");
                }
            },error:function(data){
               
            }
        });
    }

    function deductbalance(){     
        var user_id=$("#user_id").val();
        var current_balance_d = $('#current_balance_d').val();
        var d_amount = $('#d_amount').val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/users/deductmoney') }}",
            method:'POST',
            data:{'user_id':user_id,'current_balance_d':current_balance_d,'d_amount':d_amount},
            dataType:"json",
            success:function(data){
                $('#preloader').hide();
                if (data == 1) {
                    location.reload();
                } else if (data == 2) {
                    $('#d_message').html("<div class='alert alert-danger' role='alert'>{{ trans('messages.invalid_amount') }}</div>");
                } else {
                    $('#d_message').html("<div class='alert alert-danger' role='alert'>{{ trans('messages.enter_amount') }}</div>");
                }
            },error:function(data){
               
            }
        });
    }

    $('#amount').keyup(function(){
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });

    $('#d_amount').keyup(function(){
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });
</script>
@endsection