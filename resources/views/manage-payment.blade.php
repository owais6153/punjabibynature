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
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{$paymentdetails->payment_name}}</h4>
                    <div class="basic-form">
                        <form action="{{ URL::to('admin/payment/update') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>{{ trans('labels.Environment') }}</label>
                                <select id="environment" name="environment" class="form-control">
                                    <option selected="selected" value="">{{ trans('labels.Choose') }}</option>
                                    <option value="0" {{$paymentdetails->environment == 0  ? 'selected' : ''}}>{{ trans('labels.Production') }}</option>
                                    <option value="1" {{$paymentdetails->environment == 1  ? 'selected' : ''}}>{{ trans('labels.Sendbox') }}</option>
                                </select>
                            </div>

                            <input type="hidden" name="id" class="form-control" value="{{$paymentdetails->id}}">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            {{ trans('labels.Stripe_public_key') }}
                                    </label>
                                    <input type="text" name="test_public_key" class="form-control" placeholder="{{ trans('labels.Stripe_public_key') }}" value="{{$paymentdetails->test_public_key}}">
                                    @else 
                                    <label>
                                            {{ trans('labels.RazorPay_public_key') }}
                                    </label>
                                    <input type="text" name="test_public_key" class="form-control" placeholder="{{ trans('labels.RazorPay_public_key') }}" value="{{$paymentdetails->test_public_key}}">
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            {{ trans('labels.Stripe_Secret_key') }}
                                    </label>
                                    <input type="text" name="test_secret_key" class="form-control" placeholder="{{ trans('labels.Stripe_Secret_key') }}" value="{{$paymentdetails->test_secret_key}}">
                                    @else 
                                    <label>
                                            {{ trans('labels.RazorPay_Secret_key') }}
                                    </label>
                                    <input type="text" name="test_secret_key" class="form-control" placeholder="{{ trans('labels.RazorPay_Secret_key') }}" value="{{$paymentdetails->test_secret_key}}">
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">

                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            {{ trans('labels.Stripe_Production_Public_key') }}
                                    </label>
                                    <input type="text" name="live_public_key" class="form-control" placeholder="{{ trans('labels.Stripe_Production_Public_key') }}" value="{{$paymentdetails->live_public_key}}">
                                    @else 
                                    <label>
                                            {{ trans('labels.RazorPay_Production_Public_key') }}
                                    </label>
                                    <input type="text" name="live_public_key" class="form-control" placeholder="{{ trans('labels.RazorPay_Production_Public_key') }}" value="{{$paymentdetails->live_public_key}}">
                                    @endif

                                </div>
                                <div class="form-group col-md-6">

                                    @if($paymentdetails->payment_name == "Stripe")
                                    <label>
                                            {{ trans('labels.Stripe_Production_Secret_key') }}
                                    </label>
                                    <input type="text" name="live_secret_key" class="form-control" placeholder="{{ trans('labels.Stripe_Production_Secret_key') }}" value="{{$paymentdetails->live_secret_key}}">
                                    @else 
                                    <label>
                                            {{ trans('labels.RazorPay_Production_Secret_key') }}
                                    </label>
                                    <input type="text" name="live_secret_key" class="form-control" placeholder="{{ trans('labels.RazorPay_Production_Secret_key') }}" value="{{$paymentdetails->live_secret_key}}">
                                    @endif
                                </div>
                            </div>
                            
                            <!-- <div class="form-group">
                                <label>Currency code</label>
                                <input type="text" class="form-control" placeholder="Enter your Currency for Payment">
                            </div> -->
                            @if (env('Environment') == 'sendbox')
                                <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.update') }}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{ trans('labels.update') }}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- #/ container -->
@endsection
@section('script')

@endsection