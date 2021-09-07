@extends('theme.default')

@section('content')

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.payment_methods') }}</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if (\Session::has('success'))
                <div class="alert alert-success w-100 alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!! \Session::get('success') !!}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ trans('labels.all_payments') }}</h4>
                    <div class="table-responsive" id="table-display">
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('labels.name') }}</th>
                                    <th>{{ trans('labels.status') }}</th>
                                    <th>{{ trans('labels.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($getpayment as $payment) {
                                ?>
                                <tr>
                                    <td>{{$payment->id}}</td>
                                    <td>{{$payment->payment_name}}</td>
                                    <td>
                                        @if($payment->is_available == '1')
                                            <a class="badge badge-info px-2" onclick="StatusUpdate('{{$payment->id}}','2')" style="color: #fff;">{{ trans('labels.active') }}</a>
                                        @else
                                            <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$payment->id}}','1')" style="color: #fff;">{{ trans('labels.deactive') }}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->payment_name != 'COD')
                                            <a data-toggle="tooltip" href="{{URL::to('admin/manage-payment/'.$payment->id)}}" data-original-title="{{ trans('labels.view') }}">
                                                <span class="badge badge-warning">{{ trans('labels.view') }}</span>
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <?php
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
                url:"{{ URL::to('admin/payment/status') }}",
                data: {
                    id: id,
                    status: status
                },
                method: 'POST',
                success: function(response) {
                    if (response == 1) {
                        swal.close();
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

</script>
@endsection