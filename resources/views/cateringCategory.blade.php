@extends('theme.default')

@section('content')

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/catering/')}}">Catering </a></li>            
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.categories') }}</a></li>
        </ol>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategory" data-whatever="@addCategory">{{ trans('labels.add_category') }}</button>
        <!-- Add Category -->
        <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ trans('labels.add_category') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <form id="add_category" enctype="multipart/form-data">
                    <div class="modal-body">
                        <span id="msg"></span>
                        @csrf
                        <div class="form-group">
                            <label for="category_name" class="col-form-label">{{ trans('labels.category') }}</label>
                            <input type="text" class="form-control" name="name" id="category_name" placeholder="{{ trans('messages.enter_category') }}">
                        </div>
                        <div class="form-group">
                            <label for="option_allowed" class="col-form-label">Option Allowed</label>
                            <input type="text" class="form-control" name="option_allowed" id="option_allowed" placeholder="Option Allowed">
                        </div>
                        <div class="form-group">
                            <label for="allowed_veg" class="col-form-label">Veg Allowed</label>
                            <input type="text" class="form-control" name="allowed_veg" id="allowed_veg" placeholder="Veg Allowed">
                        </div>
                        <div class="form-group">
                            <label for="allowed_nonveg" class="col-form-label">Non Veg Allowed</label>
                            <input type="text" class="form-control" name="allowed_nonveg" id="allowed_nonveg" placeholder="Non Veg Allowed">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                        @if (env('Environment') == 'sendbox')
                            <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                        @else
                            <button type="submit" class="btn btn-primary">{{ trans('labels.save') }}</button>
                        @endif
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category -->
        <div class="modal fade" id="EditCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" name="editcategory" class="editcategory" id="editcategory" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabeledit">{{ trans('labels.edit_category') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <span id="emsg"></span>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="form-group">
                                <label for="getcategory_name" class="col-form-label">{{ trans('labels.category') }}</label>
                                <input type="text" class="form-control" id="getcategory_name" name="name" placeholder="{{ trans('messages.enter_category') }}">
                            </div>
                            <div class="form-group">
                                <label for="getoption_allowed" class="col-form-label">Option Allowed</label>
                                <input type="text" class="form-control" name="option_allowed" id="getoption_allowed" placeholder="Option Allowed">
                            </div>
                            <div class="form-group">
                                <label for="getallowed_veg" class="col-form-label">Veg Allowed</label>
                                <input type="text" class="form-control" name="allowed_veg" id="getallowed_veg" placeholder="Veg Allowed">
                            </div>
                            <div class="form-group">
                                <label for="getallowed_nonveg" class="col-form-label">Non Veg Allowed</label>
                                <input type="text" class="form-control" name="allowed_nonveg" id="getallowed_nonveg" placeholder="Non Veg Allowed">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                            @if (env('Environment') == 'sendbox')
                                <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{ trans('labels.save') }}</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ trans('labels.all_category') }}</h4>
                    <div class="table-responsive" id="table-display">
                        @include('theme.cateringCategorytable')
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
    $('.table').dataTable({
      aaSorting: [[0, 'DESC']]
    });
$(document).ready(function() {
     "use strict";
    $('#add_category').on('submit', function(event){
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url:"{{ URL::to('admin/catering/category/store') }}",
            method:"POST",
            data:form_data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                $("#preloader").hide();
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
                    msg += '<div class="alert alert-success mt-1">'+result.success+'</div>';
                    CatetgoryTable();
                    $('#message').html(msg);
                    $("#addCategory").modal('hide');
                    $("#add_category")[0].reset();
                    $('.gallery').html('');
                    setTimeout(function(){
                      $('#message').html('');
                    }, 5000);
                }
            },
        })
    });

    $('#editcategory').on('submit', function(event){
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url:"{{ URL::to('admin/catering/category/update') }}",
            method:'POST',
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
                    $('#emsg').html(msg);
                    setTimeout(function(){
                      $('#emsg').html('');
                    }, 5000);
                }
                else
                {
                    msg += '<div class="alert alert-success mt-1">'+result.success+'</div>';
                    CatetgoryTable();
                    $('#message').html(msg);
                    $("#EditCategory").modal('hide');
                    $("#editcategory")[0].reset();
                    $('.gallery').html('');
                    setTimeout(function(){
                      $('#message').html('');
                    }, 5000);
                }
            },
        });
    });
});
function GetData(id) {
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:"{{ URL::to('admin/catering/category/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditCategory").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#getcategory_name').val(response.ResponseData.name);
            $('#getoption_allowed').val(response.ResponseData.option_allowed);
            $('#getallowed_veg').val(response.ResponseData.allowed_veg);
            $('#getallowed_nonveg').val(response.ResponseData.allowed_nonveg);
            $('#getis_admin').val(response.ResponseData.is_admin);

        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function Delete(id) {
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
                url:"{{ URL::to('admin/catering/category/delete') }}",
                data: {
                    id: id
                },
                method: 'POST',
                success: function(response) {
                    if (response == 1) {
                        swal.close();
                        CatetgoryTable();
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
function CatetgoryTable() {
    $('#preloader').show();
    $.ajax({
        url:"{{ URL::to('admin/catering/category/list') }}",
        method:'get',
        success:function(data){
            $('#preloader').hide();
            $('#table-display').html(data);
            $(".zero-configuration").DataTable({
              aaSorting: [[0, 'DESC']]
            })
        }
    });
}

$(document).ready(function() {
     var imagesPreview = function(input, placeToInsertImagePreview) {
          if (input.files) {
              var filesAmount = input.files.length;
              $('.gallery').html('');
              $('.gallerys').html('');
              var n=0;
              for (i = 0; i < filesAmount; i++) {
                  var reader = new FileReader();
                  reader.onload = function(event) {
                       $($.parseHTML('<div>')).attr('class', 'imgdiv').attr('id','img_'+n).html('<img src="'+event.target.result+'" class="img-fluid">').appendTo(placeToInsertImagePreview); 
                      n++;
                  }
                  reader.readAsDataURL(input.files[i]);                                  
             }
          }
      };

     $('#image').on('change', function() {
         imagesPreview(this, '.gallerys');
         imagesPreview(this, '.gallery');
     });
 
});
var images = [];
function removeimg(id){
    images.push(id);
    $("#img_"+id).remove();
    $('#remove_'+id).remove();
    $('#removeimg').val(images.join(","));
}
</script>
@endsection