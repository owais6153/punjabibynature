
@extends('theme.default')

@section('content')

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.about_settings') }}</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <span id="msg"></span>
                    <h4 class="card-title">{{ trans('labels.about_settings') }}</h4>
                    <p class="text-muted"><code></code>
                    </p>
                    <div id="privacy-policy-three" class="privacy-policy">
                        <form method="post" name="about" id="about" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="about_content" class="col-form-label">{{ trans('labels.about_content') }}</label>
                                <textarea class="form-control" id="about_content" rows="5" name="about_content">{{$getabout->about_content}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="image" class="col-form-label">{{ trans('labels.about_image') }} (Only for website)</label>
                                <input type="file" class="form-control" name="image" id="image" value="{{$getabout->image}}">
                                <img src='{!! asset("storage/app/public/images/about/".$getabout->image) !!}' class='img-fluid mt-3' style='max-height: 150px;'>
                            </div>
                            <div class="form-group">
                                <label for="fb" class="col-form-label">{{ trans('labels.facebook_link') }}</label>
                                <input type="text" class="form-control" name="fb" id="fb" value="{{$getabout->fb}}">
                            </div>
                            <div class="form-group">
                                <label for="twitter" class="col-form-label">{{ trans('labels.twitter_link') }}</label>
                                <input type="text" class="form-control" name="twitter" id="twitter" value="{{$getabout->twitter}}">
                            </div>
                            <div class="form-group">
                                <label for="insta" class="col-form-label">{{ trans('labels.instagram_link') }}</label>
                                <input type="text" class="form-control" name="insta" id="insta" value="{{$getabout->insta}}">
                            </div>
                            <div class="form-group">
                                <label for="android" class="col-form-label">{{ trans('labels.android_app_link') }} (Only for website)</label>
                                <input type="text" class="form-control" name="android" id="android" value="{{$getabout->android}}">
                            </div>
                            <div class="form-group">
                                <label for="ios" class="col-form-label">{{ trans('labels.iOS_app_link') }} (Only for website)</label>
                                <input type="text" class="form-control" name="ios" id="ios" value="{{$getabout->ios}}">
                            </div>

                            <div class="form-group">
                                <label for="copyright" class="col-form-label">{{ trans('labels.Copyright') }} (Only for website)</label>
                                <input type="text" class="form-control" name="copyright" id="copyright" value="{{$getabout->copyright}}">
                            </div>

                            <div class="form-group">
                                <label for="title" class="col-form-label">{{ trans('labels.Title_for_Title_bar') }} (Only for website)</label>
                                <input type="text" class="form-control" name="title" id="title" value="{{$getabout->title}}">
                            </div>
                            <div class="form-group">
                                <label for="short_title" class="col-form-label">{{ trans('labels.Short_Title') }} (Only for website)</label>
                                <input type="text" class="form-control" name="short_title" id="short_title" value="{{$getabout->short_title}}">
                            </div>
                            <div class="form-group">
                                <label for="logo" class="col-form-label">{{ trans('labels.logo') }} (Only for website)</label>
                                <input type="file" class="form-control" name="logo" id="logo" value="{{$getabout->logo}}">
                                <img src='{!! asset("storage/app/public/images/about/".$getabout->logo) !!}' class='img-fluid mt-3' style='max-height: 150px;'>
                            </div>
                            <div class="form-group">
                                <label for="footer_logo" class="col-form-label">{{ trans('labels.footer_logo') }} (Only for website)</label>
                                <input type="file" class="form-control" name="footer_logo" id="footer_logo" value="{{$getabout->footer_logo}}">
                                <img src='{!! asset("storage/app/public/images/about/".$getabout->footer_logo) !!}' class='img-fluid mt-3' style='max-height: 150px;'>
                            </div>
                            <div class="form-group">
                                <label for="favicon" class="col-form-label">{{ trans('labels.Favicon') }} (Only for website)</label>
                                <input type="file" class="form-control" name="favicon" id="favicon" value="{{$getabout->favicon}}">
                                <img src='{!! asset("storage/app/public/images/about/".$getabout->favicon) !!}' class='img-fluid mt-3' style='max-height: 150px;'>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="mobile" class="col-form-label">{{ trans('labels.mobile') }}</label>
                                <input type="text" class="form-control" name="mobile" id="mobile" value="{{$getabout->mobile}}">
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">{{ trans('labels.email') }}</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{$getabout->email}}">
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-form-label">{{ trans('labels.address') }}</label>
                                <input type="text" class="form-control" name="address" id="address" value="{{$getabout->address}}">
                            </div>
                            <div class="form-group">
                                <label for="og_image" class="col-form-label">OG Image</label>
                                <input type="file" class="form-control" name="og_image" id="og_image">
                                <img src='{!! asset("storage/app/public/images/about/".$getabout->og_image) !!}' class='img-fluid mt-3' style='max-height: 150px;'>
                            </div>
                            <div class="form-group">
                                <label for="og_title" class="col-form-label">OG Title</label>
                                <input type="text" class="form-control" name="og_title" id="og_title" value="{{$getabout->og_title}}">
                            </div>
                            <div class="form-group">
                                <label for="og_description" class="col-form-label">OG Description</label>
                                <textarea name="og_description" id="og_description" class="form-control">{{$getabout->og_description}}</textarea>
                            </div>
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
<script type="text/javascript">
$(document).ready(function() {

    $('#about').on('submit', function(event){
        "use strict";
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url:"{{ URL::to('admin/about/update') }}",
            method:"POST",
            data:form_data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                $('#preloader').hide();
                var msg = '';
                if(result.error.length > 0)
                {
                    for(var count = 0; count < result.error.length; count++)
                    {
                        msg += '<div class="alert alert-danger">'+result.error[count]+'</div>';
                    }
                    $('#msg').html(msg);
                    setTimeout(function(){
                      $('#msg').html('');
                    }, 5000);
                }
                else
                {
                    location.reload();
                }
            },
        })
    });
});

</script>
@endsection