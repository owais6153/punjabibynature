@extends('theme.default')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.report') }}</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ trans('labels.report') }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="example">
                                <h5 class="box-title m-t-30">{{ trans('labels.date_range') }}</h5>
                                <form method="post" id="get_report">
                                {{csrf_field()}}
                                    <div class="input-daterange input-group" id="date-range">
                                        <input type="text" class="form-control" name="startdate" id="startdate" readonly="" placeholder="{{ trans('labels.start_date') }}">

                                        <input type="text" class="form-control" name="enddate" id="enddate" readonly="" placeholder="{{ trans('labels.end_date') }}">

                                        <button type="button" class="btn mb-1 btn-flat btn-primary" onclick="GetReport()">{{ trans('labels.submit') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" id="table-display">
                        
                        @include('theme.reporttable')
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
                <input type="hidden" name="bookId" id="bookId" value=""/>
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ trans('labels.name') }}</th>
                        <th scope="col">{{ trans('labels.email') }}</th>
                        <th scope="col">{{ trans('labels.mobile') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($getdriver as $driver)
                        <tr>
                            <th scope="row"><input type="checkbox" name="driver_id" id="driver_id" value="{{$driver->id}}"></th>
                            <td>{{$driver->name}}</td>
                            <td>{{$driver->email}}</td>
                            <td>{{$driver->mobile}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.colVis.min.js"></script>


<script type="text/javascript">

    var table = $('#example').DataTable( {
        lengthChange: false,
        buttons: [ 'excel']
    } );
 
    table.buttons().container()
        .appendTo( '#example_wrapper .col-md-6:eq(0)' );

    function GetReport() {
        var startdate=$("#startdate").val();
        var enddate=$("#enddate").val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
        
        if($("#get_report").valid()) {
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/report/show') }}",
                method:'POST',
                data:{'startdate':startdate,'enddate':enddate},
                beforeSend: function() {
                  $("#loading-image").show();
                },
                success:function(data){
                    $('#table-display').html(data);
                    var table = $('#example').DataTable( {
                        lengthChange: false,
                        buttons: [ 'excel']
                    } );
                 
                    table.buttons().container()
                        .appendTo( '#example_wrapper .col-md-6:eq(0)' );
                },error:function(data){
                   
                }
            });
        }
    }

    function ReportTable() {
        $.ajax({
            url:"{{ URL::to('admin/report/list') }}",
            method:'get',
            success:function(data){
                $('#table-display').html(data);
                // $(".zero-configuration").DataTable();
                var table = $('#example').DataTable( {
                    lengthChange: false,
                    buttons: [ 'excel']
                } );
             
                table.buttons().container()
                    .appendTo( '#example_wrapper .col-md-6:eq(0)' );
            }
        });
    }

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

        var driver_id = [];
        $.each($("input[name='driver_id']:checked"), function(){
            driver_id.push($(this).val());
        });
        var did = driver_id.join(", ");
        
        var CSRF_TOKEN = $('input[name="_token"]').val();
        // alert(driver_id);
        $.ajax({
            headers: {
                'X-CSRF-Token': CSRF_TOKEN 
            },
            url:"{{ URL::to('admin/orders/assign') }}",
            method:'POST',
            data:{'bookId':bookId,'driver_id':did},
            dataType:"json",
            success:function(data){
                if (data == 1) {
                    location.reload();
                }
            },error:function(data){
               
            }
        });
    }
</script>
@endsection