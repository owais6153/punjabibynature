@extends('theme.default')

@section('content')
<!-- row -->

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">OTP Configurations</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <!-- End Row -->

    <div class="row">
        @if (\Session::has('success'))
            <div class="alert alert-success w-100 alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {!! \Session::get('success') !!}
            </div>
        @endif
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Twilio Credential</h4>
                    <div class="basic-form">
                        <form method="post" action="{{ URL::to('admin/otp-configuration/update') }}">
                            @csrf
                            <input type="hidden" class="form-control" name="id" id="id" value="{{$twilioconfigurations->id}}">
                            <input type="hidden" class="form-control" name="name" id="name" value="{{$twilioconfigurations->name}}">
                            <div class="form-group">
                                <label id="twilio_sid">Twilio SID</label>
                                <input type="text" class="form-control" name="twilio_sid" id="twilio_sid" placeholder="Enter Twilio SID" value="{{$twilioconfigurations->twilio_sid}}">   
                            </div>
                            <div class="form-group">
                                <label id="twilio_auth_token">Twilio Auth Token</label>
                                <input type="text" class="form-control" name="twilio_auth_token" id="twilio_auth_token" placeholder="Enter Twilio Auth Token" value="{{$twilioconfigurations->twilio_auth_token}}">
                            </div>
                            <div class="form-group">
                                <label id="twilio_mobile_number">Twilio Mobile number</label>
                                <input type="text" class="form-control" name="twilio_mobile_number" id="twilio_mobile_number" placeholder="Enter Twilio Mobile number" value="{{$twilioconfigurations->twilio_mobile_number}}">
                            </div>
                            <div class="form-group">
                                <label id="status">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="1" @if ($twilioconfigurations->status == "1") {{ 'selected' }} @endif>Active</option>
                                    <option value="0" @if ($twilioconfigurations->status == "0") {{ 'selected' }} @endif>Deactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">MSG91 Credential</h4>
                    <div class="basic-form">
                        <form method="post" action="{{ URL::to('admin/otp-configuration/update') }}">
                            @csrf
                            <input type="hidden" class="form-control" name="id" id="id" value="{{$msg91configurations->id}}">
                            <input type="hidden" class="form-control" name="name" id="name" value="{{$msg91configurations->name}}">
                            <div class="form-group">
                                <label id="msg_authkey">Auth key</label>
                                <input type="text" class="form-control" name="msg_authkey" id="msg_authkey" placeholder="Enter AUTHKEY" value="{{$msg91configurations->msg_authkey}}">   
                            </div>
                            <div class="form-group">
                                <label id="msg_template_id">Template id</label>
                                <input type="text" class="form-control" name="msg_template_id" id="msg_template_id" placeholder="Enter template id" value="{{$msg91configurations->msg_template_id}}">
                            </div>
                            <div class="form-group">
                                <label id="status">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="1" @if ($msg91configurations->status == "1") {{ 'selected' }} @endif>Active</option>
                                    <option value="0" @if ($msg91configurations->status == "0") {{ 'selected' }} @endif>Deactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->

<!-- #/ container -->
@endsection

@section('script')