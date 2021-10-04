@extends('theme.default')

@section('content')
<!-- row -->

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.users') }}</a></li>
        </ol>
        <a href="{{URL::to('/admin/createsystem-addons')}}" class="btn btn-primary">Install/Update addons</a>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <!-- End Row -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="default-tab">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#installed">Installed Addons</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#available">Available Addons</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="installed" role="tabpanel">
                                <div class="p-t-15">
                                    @forelse(\App\SystemAddons::all() as $key => $addon)
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card">
                                                <img class="img-fluid" src='{!! asset("storage/app/public/addons/".$addon->image) !!}' alt="">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ ucfirst($addon->name) }}</h5>
                                                </div>
                                                <div class="card-footer">
                                                    <p class="card-text d-inline"><small class="text-muted">Version : {{ $addon->version }}</small></p>
                                                    @if($addon->activated)
                                                        <a href="#" class="btn btn-sm btn-primary float-right" onclick="StatusUpdate('{{$addon->id}}','0')">Activated</a>
                                                    @else
                                                        <a href="#" class="btn btn-sm btn-danger float-right" onclick="StatusUpdate('{{$addon->id}}','1')">Deactivated</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Col -->
                                    @empty
                                        <div class="col-md-6 col-lg-3 mt-4">
                                            <h4>No Addon Installed</h4>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="available">
                                <div class="p-t-15">
                                    <?php
                                    $payload = file_get_contents('https://gravityinfotech.net/api/addonsapi.php');
                                    $obj = json_decode($payload);
                                    ?>
                                    <div class="row">
                                        @if( isset($obj->data) && is_object($obj->data) )
                                        @foreach($obj->data as $item)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="card">
                                                    <img class="img-fluid" src='{{$item->image}}' alt="">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{$item->name}}</h5>
                                                        <p>{{$item->short_description}}</p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="{{$item->purchase}}" target="_blank" class="btn btn-sm btn-primary">Purchase</a>
                                                        <a href="{{$item->link}}" target="_blank" class="btn btn-sm btn-success float-right">Preview</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <!-- End Col -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 m-b-30">
            
            <div class="row mt-4">
                
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->

<!-- #/ container -->
@endsection
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
                    url:"{{ URL::to('admin/systemaddons/update') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST',
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
</script>
@section('script')