
@extends('theme.default')

@section('content')

<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">{{ trans('labels.dashboard') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ trans('labels.items') }}</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        {{ Session::get('danger') }}
                        @php
                            Session::forget('danger');
                        @endphp
                    </div>
                    @endif

                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endforeach

                    <h4 class="card-title">{{ trans('labels.edit_item') }}</h4>
                    <p class="text-muted"><code></code>
                    </p>
                    <div id="privacy-policy-three" class="privacy-policy">
                        <form method="post" action="{{ URL::to('admin/item/update') }}" name="about" id="about" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" class="form-control" id="id" name="id" value="{{$item->id}}">

                            <div class="row">
                                <div class="col-sm-3 col-md-12">
                                    <div class="form-group">
                                        <label for="getcat_id" class="col-form-label">{{ trans('labels.category') }}</label>
                                        <select name="getcat_id" class="form-control" id="getcat_id">
                                            <option value="">{{ trans('messages.select_category') }}</option>
                                            <?php
                                            foreach ($getcategory as $category) {
                                            ?>
                                            <option value="{{$category->id}}" {{ $item->cat_id == $category->id ? 'selected' : ''}}>{{$category->category_name}}</option>
                                            <?php
                                            }
                                            ?>
                                            @if ($errors->has('get_cat_id'))
                                                <span class="text-danger">{{ $errors->first('get_cat_id') }}</span>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getitem_name" class="col-form-label">{{ trans('labels.item_name') }}</label>
                                        <input type="text" class="form-control" id="getitem_name" name="item_name" placeholder="{{ trans('messages.enter_item_name') }}" value="{{$item->item_name}}">
                                        @if ($errors->has('getitem_name'))
                                            <span class="text-danger">{{ $errors->first('getitem_name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getdelivery_time" class="col-form-label">{{ trans('labels.delivery_time') }}</label>
                                        <input type="text" class="form-control" name="getdelivery_time" id="getdelivery_time" placeholder="{{ trans('messages.enter_delivery_time') }}" value="{{$item->delivery_time}}">
                                    </div>
                                </div>
                            </div>

         
                                <input type="hidden" name="item_type" value="product">
                            <div id="ingredient_field">
                                <h5>Edit Ingredients</h5>
                                @if ($item->ingredients_id != null)
                                <?php
                                    $iselected = explode(",", $item->ingredients_id);
                                    $available_ing_option = explode(",", $item->available_ing_option);
                                ?>
                                @foreach($iselected as $ingredients_key => $ingredients_row)
                                    <div class="row ingredients_row ">
                                        <div class="col-sm-4">
                                                <label>{{ trans('messages.select_ingredients') }}</label>
                                              <select name="ingredients_id[]"  class="form-control ingredients_add" id="">
                                                <option value="">{{ trans('messages.select_ingredients') }}</option>
                                                <?php
                                                foreach ($getingredientTypes as $ingredients) {
                                                ?>
                                                <option value="{{$ingredients->id}}" {{ ($ingredients->id == $iselected[$ingredients_key]) ? 'selected' : '' }} >{{$ingredients->name}} (Ingredients: {{$ingredients->countIngredients}})</option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Option to be selected.</label>
                                            <input type="number" name="available_ing_option[]" min="1" class="form-control" placeholder="Options to be selected" 
                                            @if ($available_ing_option[$ingredients_key] != 'allow_all') 
                                            value="{{$available_ing_option[$ingredients_key]}}" required
                                            @else
                                             disabled="disabled"
                                            @endif
                                             >
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Allow all options to be selected.<br>
                                            <input type="checkbox" name="available_ing_option[]" class="mt-3 allow_all" value="allow_all" 
                                            @if ($available_ing_option[$ingredients_key] == 'allow_all') 
                                            checked="checked"
                                            @endif ></label>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger mt-4" type="button"  onclick="delete_ingredient_field($(this).parents('.ingredients_row'));"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button class="btn btn-info mt-4" type="button"  onclick="add_ingredient_field();">+</i> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="add_addons mt-4">
                                <h5>Single Add-ons</h5>

                                <div class="row">

                                    <div class="col-sm-3 col-md-6">
                                        <div class="form-group">
                                            <label for="getaddons_id" class="col-form-label">{{ trans('labels.addons') }}</label>
                                          
                                            <?php
                                            $selected = explode(",", $item->addons_id);
                                            ?>
                                            <select name="addons_id[]" class="form-control selectpicker" multiple data-live-search="true" id="getaddons_id">
                                               @foreach($getaddons as $supplier)
                                                 <option value="{{ $supplier->id }}" {{ (in_array($supplier->id, $selected)) ? 'selected' : '' }}>{{ $supplier->name}}</option>
                                               @endforeach
                                            </select>
                                        </div>
                                    </div>
                                   
                                </div>

                                <h5>Group Add-ons</h5>
                                <div id="add_group_addons">
                                     @if ($item->addongroups_id != null)
                                <?php
                                    $addselected = explode(",", $item->addongroups_id);
                                    $available_addons_option = explode(",", $item->available_addons_option);
                                ?>
                                @foreach($addselected as $add_groups_key => $add_groups_row)
                                    <div class="row addons_row ">
                                        <div class="col-sm-4">
                                                <label>{{ trans('messages.select_ingredients') }}</label>
                                              <select name="addons_groups_id[]"  class="form-control addon_groups" id="">
                                                <option value="">{{ trans('messages.select_ingredients') }}</option>
                                                <?php
                                                foreach ($addonGroups as $addonGroup) {
                                                ?>
                                                <option value="{{$addonGroup->id}}" {{ ($addonGroup->id == $addselected[$add_groups_key]) ? 'selected' : '' }} >{{$addonGroup->name}} (Ingredients: {{$addonGroup->countAddons}})</option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Option to be selected.</label>
                                            <input type="number" name="available_addons_option[]" min="1" class="form-control" placeholder="Options to be selected" 
                                            @if ($available_addons_option[$add_groups_key] != 'allow_all') 
                                            value="{{$available_addons_option[$add_groups_key]}}" required
                                            @else
                                             disabled="disabled"
                                            @endif
                                             >
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Allow all options to be selected.<br>
                                            <input type="checkbox" name="available_addons_option[]" class="mt-3 allow_all" value="allow_all" 
                                            @if ($available_addons_option[$add_groups_key] == 'allow_all') 
                                            checked="checked"
                                            @endif ></label>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger mt-4" type="button"  onclick="delete_ingredient_field($(this).parents('.addons_row'));"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                                     <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-info mt-4" type="button"  onclick="add_addons_field();"> + </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="mt-4">Edit Variation</h5>
                            @foreach ($getvariation as $ky => $variation)
                            <div class="row panel-body" id="del-{{$variation->id}}">
                                <input type="hidden" class="form-control" id="variation_id" name="variation_id[{{$ky}}]" value="{{$variation->id}}">

                                <div class="col-sm-3 nopadding">
                                    <div class="form-group">
                                        <label for="variation" class="col-form-label">{{ trans('labels.variation') }}</label>
                                        <input type="text" class="form-control" name="variation[{{$ky}}]" id="variation" placeholder="{{ trans('messages.enter_variation') }}" required="" value="{{$variation->variation}}">
                                    </div>
                                </div>
                                <div class="col-sm-4 nopadding">
                                    <div class="form-group">
                                        <label for="product_price" class="col-form-label">{{ trans('labels.product_price') }}</label>
                                        <input type="text" class="form-control" id="product_price" name="product_price[{{$ky}}]" placeholder="{{ trans('messages.enter_product_price') }}" required="" value="{{$variation->product_price}}">
                                    </div>
                                </div>
                                <div class="col-sm-4 nopadding">
                                    <div class="form-group">
                                        <label for="sale_price" class="col-form-label">{{ trans('labels.sale_price') }}</label>
                                        <input type="text" class="form-control" id="sale_price" name="sale_price[{{$ky}}]" placeholder="{{ trans('messages.enter_sale_price') }}" required="" value="{{$variation->sale_price}}">
                                    </div>
                                </div>
                                <div class="col-sm-1 nopadding">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button class="btn btn-danger" type="button"  onclick="DeleteVariation('{{$variation->id}}','{{$item->id}}');"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $currentdata[] = array(
                                    "currentkey" => $ky
                                );
                            ?>
                            @endforeach

                            <hr>
                            <p id="counter" style="display: none;">{{count(array_column(@$currentdata, 'currentkey'))-1}}</p>
                            <label> Add Varation <button class="btn btn-success" type="button"  onclick="edititem_fields();"> + </button></label>

                            <div class="customer_records_dynamic"></div>

                            <div id="edititem_fields"></div>

                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getdescription" class="col-form-label">{{ trans('labels.description') }}:</label>
                                        <textarea class="form-control" rows="3" name="getdescription" id="getdescription" placeholder="Product Description">{{$item->item_description}}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="tax" class="col-form-label">{{ trans('labels.tax') }} (%)</label>
                                        <input type="text" class="form-control" name="tax" id="tax" value="{{$item->tax}}" placeholder="{{ trans('messages.enter_tax') }}">
                                    </div>
                                </div>
                            </div>


                            <h3 class="mt-4">Add Combo</h3>
                            <div id="comboOptions">
                                <div class="row">
                                   <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-info mt-4" type="button"  onclick="addComboFields();"> + </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php
                                    $combo_groups = explode(',', $item->combo_group_id);
                                ?>
                                    <div class="col-sm-4 pt-3">
                                        <label for="make_combo">Make this product as combo product.</label><br>
                                        <input type="checkbox" {{($item->is_default_combo == 1) ? 'checked' : '' }} 
                                        {{(empty($combo_groups)) ? 'disabled="disabled"' : '' }} 
                                         id="make_combo" name="make_combo" value="1">
                                    </div>
                                </div>


                                @foreach ($combo_groups as $combo_group_id)
                                <div class="row combo_row mb-4 removeclasscombo">
                                    <div class="col-md-4"><label>Combo Group</label><select class="form-control" name="combo_group[]" required><option value="">Select Combo Group</option><?php foreach ($getComboGroup as $comboGroup) { ?> <option  {{ ($combo_group_id == $comboGroup->id) ? 'selected' : '' }} value="{{$comboGroup->id}}">{{$comboGroup->name}} (Items: {{$comboGroup->countCombos}})</option> <?php } ?></select></div><div class="col-md-1"><button class="btn btn-danger mt-4" type="button" onclick="$(this).parents('.combo_row').remove();"> - </button></div>         
                                </div>
                                @endforeach                   
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

        <div class="col-12">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddProduct" data-whatever="@addProduct">{{ trans('labels.add_image') }}</button>
            <div id="card-display">
                <div class="row" style="margin-top: 20px;">
                <?php
                foreach ($getitemimages as $itemimage) {

                ?>
                <div class="col-md-6 col-lg-3 dataid{{$itemimage->id}}" id="table-image">
                    <div class="card">
                        <img class="img-fluid" src='{!! asset("storage/app/public/images/item/".$itemimage->image) !!}' style="max-height: 255px; min-height: 255px;" >
                        <div class="card-body">
                            <button type="button" onClick="EditDocument('{{$itemimage->id}}')" class="btn mb-2 btn-sm btn-primary">{{ trans('labels.edit') }}</button>
                            @if (env('Environment') == 'sendbox')
                                <button type="button" class="btn mb-2 btn-sm btn-danger" onclick="myFunction()">{{ trans('labels.delete') }}</button>
                            @else
                                <button type="submit" onclick="DeleteImage('{{$itemimage->id}}','{{$itemimage->item_id}}')" class="btn mb-2 btn-sm btn-danger">{{ trans('labels.delete') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Images -->
<div class="modal fade" id="EditImages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" name="editimg" class="editimg" id="editimg" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabeledit">{{ trans('labels.images') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <span id="emsg"></span>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('labels.images') }} (500x250)</label>
                        <input type="hidden" id="idd" name="id">
                        <input type="hidden" class="form-control" id="old_img" name="old_img">
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <input type="hidden" name="removeimg" id="removeimg">
                    </div>
                    <div class="galleryim"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btna-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                    @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.update') }}</button>
                    @else
                        <button type="submit" class="btn btn-primary">{{ trans('labels.update') }}</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Item Image -->
<div class="modal fade" id="AddProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" name="addproduct" class="addproduct" id="addproduct" enctype="multipart/form-data">
            <span id="msg"></span>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('labels.images') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('labels.close') }}"><span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <span id="iiemsg"></span>
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label for="colour" class="col-form-label">{{ trans('labels.images') }}:</label>
                        <input type="file" multiple="true" class="form-control" name="file[]" id="file" accept="image/*" required="">
                    </div>
                    <div class="gallery"></div>

                    <input type="hidden" name="itemid" id="itemid" value="{{request()->route('id')}}">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('labels.close') }}</button>
                    @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                    @else
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">{{ trans('labels.save') }}</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {
    "use strict";
        $('#addproduct').on('submit', function(event){
            event.preventDefault();
            var form_data = new FormData(this);
            form_data.append('file',$('#file')[0].files);
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('admin/item/storeimages') }}",
                method:"POST",
                data:form_data,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(result) {
                    $('#preloader').hide();
                    var msg = '';
                    $('div.gallery').html('');  
                    if(result.error.length > 0)
                    {
                        for(var count = 0; count < result.error.length; count++)
                        {
                            msg += '<div class="alert alert-danger">'+result.error[count]+'</div>';
                        }
                        $('#iiemsg').html(msg);
                        setTimeout(function(){
                          $('#iiemsg').html('');
                        }, 5000);
                    }
                    else
                    {
                        msg += '<div class="alert alert-success mt-1">'+result.success+'</div>';
                        $('#message').html(msg);
                        $("#AddProduct").modal('hide');
                        $("#addproduct")[0].reset();
                        location.reload();
                    }
                },
            })
        });

        $('#editimg').on('submit', function(event){
            event.preventDefault();
            var form_data = new FormData(this);
            $('#preloader').show();
            $.ajax({
                url:"{{ URL::to('admin/item/updateimage') }}",
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
                        location.reload();
                    }
                },
            });
        });
    });

    function EditDocument(id) {
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('admin/item/showimage') }}",
            data: {
                id: id
            },
            method: 'POST', //Post method,
            dataType: 'json',
            success: function(response) {
                $('#preloader').hide();
                jQuery("#EditImages").modal('show');
                $('#idd').val(response.ResponseData.id);
                $('.galleryim').html("<img src="+response.ResponseData.img+" class='img-fluid' style='max-height: 200px;'>");
                $('#old_img').val(response.ResponseData.image);
            },
            error: function(error) {
                $('#preloader').hide();
            }
        })
    }

    function DeleteImage(id,item_id) {
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
                    url:"{{ URL::to('admin/item/destroyimage') }}",
                    data: {
                        id: id,
                        item_id: item_id
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

     $(document).ready(function() {
         var imagesPreview = function(input, placeToInsertImagePreview) {
              if (input.files) {
                  var filesAmount = input.files.length;
                  $('div.gallery').html('');
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

         $('#file').on('change', function() {
             imagesPreview(this, 'div.gallery');
         });
     
    });
    var images = [];
    function removeimg(id){
        images.push(id);
        $("#img_"+id).remove();
        $('#remove_'+id).remove();
        $('#removeimg').val(images.join(","));
    }

    function edititem_fields() {

        var counter = document.getElementById('counter');
        var editroom = counter.innerHTML;

       editroom++;
       var editobjTo = document.getElementById('edititem_fields')
       var editdivtest = document.createElement("div");
       editdivtest.setAttribute("class", "form-group editremoveclass"+editroom);
       var rdiv = 'editremoveclass'+editroom;
       editdivtest.innerHTML = '<input type="hidden" class="form-control" id="variation_id" name="variation_id['+ editroom +']"><div class="row panel-body"><div class="col-sm-3 nopadding"><div class="form-group"><label for="variation" class="col-form-label">{{trans('labels.variation')}}</label><input type="text" class="form-control" name="variation['+ editroom +']" id="variation" placeholder="{{trans('messages.enter_variation')}}" required=""></div></div><div class="col-sm-4 nopadding"><div class="form-group"><label for="product_price" class="col-form-label">{{trans('labels.product_price')}}</label><input type="text" class="form-control" id="product_price" name="product_price['+ editroom +']" placeholder="{{trans('messages.enter_product_price')}}" required=""></div></div><div class="col-sm-4 nopadding"><div class="form-group"><label for="product_price" class="col-form-label">{{trans('labels.sale_price')}}</label><input type="text" class="form-control" id="sale_price" name="sale_price['+ editroom +']" placeholder="{{trans('messages.enter_sale_price')}}" required="" value="0"></div></div><div class="col-sm-1 nopadding"> <div class="form-group"> <div class="input-group"> <div class="input-group-btn"> <button class="btn btn-danger" type="button" onclick="remove_edit_fields('+ editroom +');"> - </button></div></div></div></div><div class="clear"></div>';
       counter.innerHTML = editroom;
       editobjTo.appendChild(editdivtest)
    }
    function remove_edit_fields(rid) {
      $('.editremoveclass'+rid).remove();
    }

    function DeleteVariation(id,item_id) {
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
                    url:"{{ URL::to('admin/item/deletevariation') }}",
                    data: {
                        id: id,
                        item_id: item_id,
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        if (response == 1) {
                            swal.close();
                            location.reload();
                        } else if  (response == 2) {
                            swal("Cancelled", "{{ trans('messages.cannot_delete') }} :(", "error");
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



var ingRoom = 1;
function add_ingredient_field() {
    "use strict";
    ingRoom++;
    var objTo = document.getElementById('ingredient_field')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", " removeclass"+ingRoom);
    var rdiv = 'removeclassIng'+ingRoom;
    divtest.innerHTML = '<div class="row mt-3 ingredients_row"><div class="col-sm-4"><label>{{ trans('messages.select_ingredients') }}</label><select name="ingredients_id[]" required class="form-control ingredients_add" id=""><option value="">{{ trans('messages.select_ingredients') }}</option> <?php foreach ($getingredientTypes as $ingredients) { ?> <option value="{{$ingredients->id}}">{{$ingredients->name}} (Ingredients: {{$ingredients->countIngredients}})</option> <?php } ?> </select> </div><div class="col-sm-4"><label>Option to be selected.</label><input required type="number" name="available_ing_option[]" min="1"  class="form-control" placeholder="Options to be selected"></div><div class="col-sm-3"><label>Allow all options to be selected.<br><input type="checkbox" name="available_ing_option[]" class="mt-3 allow_all" value="allow_all" ></label></div><div class="col-sm-1"><div class="form-group"><div class="input-group"><div class="input-group-btn"><button class="btn btn-danger mt-4" type="button"  onclick="remove_ingredient_field('+ingRoom+');"> - </button></div></div></div></div></div></div>';



    objTo.appendChild(divtest)
}
function remove_ingredient_field(rid) {
    "use strict";
   $('.removeclass'+rid).remove();
}
    var ingredients_add = [];
        <?php
            foreach ($getingredientTypes as  $value) { ?>
              ingredients_add[{{ $value->id }}] = {{$value->countIngredients}};
            <?php }            
         ?>
$(document).ready(function() {
$('.ingredients_add').each(function(index, item){
    if ($(item).val() != '') {
         let available_ing_option = $(item).parents('.ingredients_row').find('input[type="number"]');
          $(available_ing_option).attr('max', ingredients_add[$(this).val()]); 
    }
});
});


    $('#tax').keyup(function(){
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });




    

    $(document).on('change', '.ingredients_add', function(){
        let available_ing_option = $(this).parents('.ingredients_row').find('input[type="number"]');
        
        if ($(this).val() == '') {
             $(available_ing_option).attr('max', ''); 
        }
        else{
           $(available_ing_option).attr('max', ingredients_add[$(this).val()]); 
           console.log(available_ing_option);
        }
    })

    

      $(document).on('change', '.allow_all', function(){
        let available_ing_option = $(this).parents('.ingredients_row, .addons_row').find('input[type="number"]');
        
        if ($(this).is(':checked')) {
             $(available_ing_option).val('');              
             $(available_ing_option).attr('disabled', 'disabled'); 

             $(available_ing_option).removeAttr('required');              
        }
        else{           
             $(available_ing_option).removeAttr('disabled'); 
             $(available_ing_option).attr('required', 'required');
        }
    })

    function delete_ingredient_field(elem){
            $(elem).remove();  
    }



var addonRoom = 1;
function add_addons_field() {
    "use strict";
    addonRoom++;
    var objTo = document.getElementById('add_group_addons')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", " removeclassAddons"+addonRoom);
    var rdiv = 'removeclassaddon'+addonRoom;
    divtest.innerHTML = '<div class="row mt-3 addons_row"><div class="col-sm-4"><label>Select Addons Group</label><select name="addons_groups_id[]" required class="form-control addon_groups" id=""><option value="">Select Addons Group</option> <?php foreach ($addonGroups as $addonGroup) { ?> <option value="{{$addonGroup->id}}">{{$addonGroup->name}} (Addons: {{$addonGroup->countAddons}})</option> <?php } ?> </select> </div><div class="col-sm-4"><label>Option to be selected.</label><input required type="number" name="available_addons_option[]" min="1" required class="form-control" placeholder="Options to be selected"></div><div class="col-sm-3"><label>Allow all options to be selected.<br><input type="checkbox" name="available_addons_option[]" class="mt-3 allow_all" value="allow_all" ></label></div><div class="col-sm-1"><div class="form-group"><div class="input-group"><div class="input-group-btn"><button class="btn btn-danger mt-4" type="button"  onclick="remove_addon_fields('+addonRoom+');"> - </button></div></div></div></div></div></div>';



    objTo.appendChild(divtest)
}
function remove_addon_fields(rid) {
    "use strict";
   $('.removeclassAddons'+rid).remove();
}
var addons_add = [];
        <?php
            foreach ($addonGroups as  $value) { ?>
              addons_add[{{ $value->id }}] = {{$value->countAddons}};
            <?php }            
         ?>
    $(document).on('change', '.addon_groups', function(){
        let available_ing_option = $(this).parents('.addons_row').find('input[type="number"]');
        
        if ($(this).val() == '') {
             $(available_ing_option).attr('max', ''); 
        }
        else{
           $(available_ing_option).attr('max', addons_add[$(this).val()]); 
        }
    })


    var comboRoom = 1;
    function addComboFields(){    
        "use strict";
        comboRoom++;
        var objTo = document.getElementById('comboOptions')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "row combo_row mb-4 removeclasscombo"+comboRoom);
        var rdiv = 'removeclassaddon'+comboRoom;
        divtest.innerHTML = '<div class="col-md-4"><label>Combo Group</label><select class="form-control" name="combo_group[]" required><option value="">Select Combo Group</option><?php foreach ($getComboGroup as $comboGroup) { ?> <option value="{{$comboGroup->id}}">{{$comboGroup->name}} (Items: {{$comboGroup->countCombos}})</option> <?php } ?></select></div><div class="col-md-1"><button class="btn btn-danger mt-4" type="button" onclick="remove_combo_row('+comboRoom+');"> - </button></div>';



        objTo.appendChild(divtest)
        if ($('.combo_row').length > 0) {
            $('#make_combo').removeAttr('disabled');   
        }
        else{
            $('#make_combo').attr('disabled', 'disabled');  
            $('#make_combo').prop('checked', false);  
        } 
    }
function remove_combo_row(rid) {
    "use strict";
    $('.removeclasscombo'+rid).remove();
    if ($('.combo_row').length > 0) {
        $('#make_combo').removeAttr('disabled');   
    }
    else{
        $('#make_combo').attr('disabled', 'disabled');  
        $('#make_combo').prop('checked', false);  
    } 
}





</script>
@endsection