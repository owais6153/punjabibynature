
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

                    <h4 class="card-title">{{ trans('labels.add_item') }}</h4>
                    <p class="text-muted"><code></code>
                    </p>
                    <div id="privacy-policy-three" class="privacy-policy">
                        <form method="post" action="{{ URL::to('admin/item/store') }}" name="about" id="about" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-sm-3 col-md-12">
                                    <div class="form-group">
                                        <label for="cat_id" class="col-form-label">{{ trans('labels.category') }}</label>
                                        <select name="cat_id" class="form-control" id="cat_id">
                                            <option value="">{{ trans('messages.select_category') }}</option>
                                            <?php
                                            foreach ($getcategory as $category) {
                                            ?>
                                            <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        @if ($errors->has('cat_id'))
                                            <span class="text-danger">{{ $errors->first('cat_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="item_name" class="col-form-label">{{ trans('labels.item_name') }}</label>
                                        <input type="text" class="form-control" name="item_name" id="item_name" placeholder="{{ trans('messages.enter_item_name') }}">
                                        @if ($errors->has('item_name'))
                                            <span class="text-danger">{{ $errors->first('item_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_time" class="col-form-label">{{ trans('labels.delivery_time') }}</label>
                                        <input type="text" class="form-control" name="delivery_time" id="delivery_time" placeholder="{{ trans('messages.enter_delivery_time') }}">
                                    </div>
                                </div>
                            </div>

                   
                            <div id="ingredient_field">
                                <h5>Add Ingredients</h5>
                                    
 
                                 <div class="col-sm-1">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-info mt-4" type="button"  onclick="add_ingredient_field();"> + </button>
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
                                          
                                            <select name="addons_id[]" class="form-control selectpicker" multiple data-live-search="true" id="getaddons_id">
                                               @foreach($getaddons as $supplier)
                                                 <option value="{{ $supplier->id }}">{{ $supplier->name}}</option>
                                               @endforeach
                                            </select>
                                        </div>
                                    </div>
                                   
                                </div>

                                <h5>Group Add-ons</h5>
                                <div id="add_group_addons">

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


                            <h5 class="mt-4">Add Variation</h5>
                            <div class="row panel-body">
                                <div class="col-sm-3 nopadding">
                                    <div class="form-group">
                                        <label for="variation" class="col-form-label">{{ trans('labels.variation') }}</label>
                                        <input type="text" class="form-control" name="variation[]" id="variation" placeholder="{{ trans('messages.enter_variation') }}" required="">
                                    </div>
                                </div>
                                <div class="col-sm-4 nopadding">
                                    <div class="form-group">
                                        <label for="product_price" class="col-form-label">{{ trans('labels.product_price') }}</label>
                                        <input type="text" class="form-control" id="product_price" name="product_price[]" placeholder="{{ trans('messages.enter_product_price') }}" required="">
                                    </div>
                                </div>
                                <div class="col-sm-4 nopadding">
                                    <div class="form-group">
                                        <label for="sale_price" class="col-form-label">{{ trans('labels.sale_price') }}</label>
                                        <input type="text" class="form-control" id="sale_price" name="sale_price[]" placeholder="{{ trans('messages.enter_sale_price') }}" required="" value="0">
                                    </div>
                                </div>
                                <div class="col-sm-1 nopadding">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button class="btn btn-info" type="button"  onclick="education_fields();"> + </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               <div class="clear"></div>
                            </div>

                            <div id="education_fields"></div>

                            <div class="row">
                                <div class="col-sm-3 col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="col-form-label">{{ trans('labels.description') }}</label>
                                        <textarea class="form-control" rows="5" name="description" id="description" placeholder="{{ trans('messages.enter_description') }}"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="file" class="col-form-label">{{ trans('labels.images') }} (500x250)</label>
                                        <input type="file" multiple="true" class="form-control" name="file[]" id="file" required="" accept="image/*">
                                        <input type="hidden" name="removeimg" id="removeimg">
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="tax" class="col-form-label">{{ trans('labels.tax') }} (%)</label>
                                        <input type="text" class="form-control" name="tax" id="tax" value="0" placeholder="{{ trans('messages.enter_tax') }}">
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
                                    <div class="col-sm-4 pt-3">
                                        <label for="make_combo">Make this product as combo product.</label><br>
                                        <input type="checkbox" disabled="disabled" id="make_combo" name="make_combo" value="1">
                                    </div>
                                </div>
                            </div>
                            @if (env('Environment') == 'sendbox')
                                <button type="button" class="btn btn-primary" onclick="myFunction()">{{ trans('labels.save') }}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{ trans('labels.save') }}</button>
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
    var room = 1;
    function education_fields() {
        "use strict";
        room++;
        var objTo = document.getElementById('education_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass"+room);
        var rdiv = 'removeclass'+room;
        divtest.innerHTML = '<div class="row panel-body"><div class="col-sm-3 nopadding"><div class="form-group"><label for="variation" class="col-form-label">{{ trans('labels.variation') }}</label><input type="text" class="form-control" name="variation[]" id="variation" placeholder="{{ trans('messages.enter_variation') }}" required=""></div></div><div class="col-sm-4 nopadding"><div class="form-group"><label for="product_price" class="col-form-label">{{ trans('labels.product_price') }}</label><input type="text" class="form-control" id="product_price" name="product_price[]" placeholder="{{ trans('messages.enter_product_price') }}" required=""></div></div><div class="col-sm-4 nopadding"><div class="form-group"><label for="product_price" class="col-form-label">{{ trans('labels.sale_price') }}</label><input type="text" class="form-control" id="sale_price" name="sale_price[]" placeholder="{{ trans('messages.enter_sale_price') }}" required="" value="0"></div></div><div class="col-sm-1 nopadding"><div class="form-group"><div class="input-group"><div class="input-group-btn"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"> - </button></div></div></div></div><div class="clear"></div></div>';
        
        objTo.appendChild(divtest)
    }
    function remove_education_fields(rid) {
        "use strict";
       $('.removeclass'+rid).remove();
    }



var ingRoom = 1;
function add_ingredient_field() {
    "use strict";
    ingRoom++;
    var objTo = document.getElementById('ingredient_field')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", " removeclassIng"+ingRoom);
    var rdiv = 'removeclassIng'+ingRoom;
    divtest.innerHTML = '<div class="row mt-3 ingredients_row"><div class="col-sm-4"><label>{{ trans('messages.select_ingredients') }}</label><select name="ingredients_id[]" required class="form-control ingredients_add" id=""><option value="">{{ trans('messages.select_ingredients') }}</option> <?php foreach ($getingredientTypes as $ingredients) { ?> <option value="{{$ingredients->id}}">{{$ingredients->name}} (Ingredients: {{$ingredients->countIngredients}})</option> <?php } ?> </select> </div><div class="col-sm-4"><label>Option to be selected.</label><input required type="number" name="available_ing_option[]" min="1"  class="form-control" placeholder="Options to be selected" required></div><div class="col-sm-3"><label>Allow all options to be selected.<br><input type="checkbox" name="available_ing_option[]" class="mt-3 allow_all" value="allow_all" ></label></div><div class="col-sm-1"><div class="form-group"><div class="input-group"><div class="input-group-btn"><button class="btn btn-danger mt-4" type="button"  onclick="remove_ingredient_field('+ingRoom+');"> - </button></div></div></div></div></div></div>';



    objTo.appendChild(divtest)
}
function remove_ingredient_field(rid) {
    "use strict";
   $('.removeclassIng'+rid).remove();
}



var addonRoom = 1;
function add_addons_field() {
    "use strict";
    addonRoom++;
    var objTo = document.getElementById('add_group_addons')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "removeclassAddon"+addonRoom);
    var rdiv = 'removeclassaddon'+addonRoom;
    divtest.innerHTML = '<div class="row mt-3 addons_row"><div class="col-sm-4"><label>Select Addons Group</label><select name="addons_groups_id[]" required class="form-control addon_groups" id=""><option value="">Select Addons Group</option> <?php foreach ($addonGroups as $addonGroup) { ?> <option value="{{$addonGroup->id}}">{{$addonGroup->name}} (Addons: {{$addonGroup->countAddons}})</option> <?php } ?> </select> </div><div class="col-sm-4"><label>Option to be selected.</label><input required type="number" name="available_addons_option[]" min="1" required class="form-control" placeholder="Options to be selected"></div><div class="col-sm-3"><label>Allow all options to be selected.<br><input type="checkbox" name="available_addons_option[]" class="mt-3 allow_all" value="allow_all" ></label></div><div class="col-sm-1"><div class="form-group"><div class="input-group"><div class="input-group-btn"><button class="btn btn-danger mt-4" type="button"  onclick="remove_addon_field('+addonRoom+');"> - </button></div></div></div></div></div></div>';



    objTo.appendChild(divtest)
}
function remove_addon_field(rid) {
    "use strict";
   $('.removeclassAddon'+rid).remove();
}




    $('#tax').keyup(function(){
        "use strict";
        var val = $(this).val();
        if(isNaN(val)){
             val = val.replace(/[^0-9\.]/g,'');
             if(val.split('.').length>2) 
                 val =val.replace(/\.+$/,"");
        }
        $(this).val(val); 
    });


    var ingredients_add = [];
        <?php
            foreach ($getingredientTypes as  $value) { ?>
              ingredients_add[{{ $value->id }}] = {{$value->countIngredients}};
            <?php }            
         ?>
    

    $(document).on('change', '.ingredients_add', function(){
        let available_ing_option = $(this).parents('.ingredients_row').find('input[type="number"]');
        
        if ($(this).val() == '') {
             $(available_ing_option).attr('max', ''); 
        }
        else{
           $(available_ing_option).attr('max', ingredients_add[$(this).val()]);         
        }
    })
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

    var comboRoom = 1;
    function addComboFields(){    
        "use strict";
        comboRoom++;
        var objTo = document.getElementById('comboOptions')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "row combo_row mb-4 removeclasscombo"+comboRoom);
        var rdiv = 'removeclassaddon'+comboRoom;
        divtest.innerHTML = '<div class="col-md-4"><label>Combo Group</label><select class="form-control" name="combo_group[]"><option value="">Select Combo Group</option><?php foreach ($getComboGroup as $comboGroup) { ?> <option value="{{$comboGroup->id}}">{{$comboGroup->name}} (Items: {{$comboGroup->countCombos}})</option> <?php } ?></select></div><div class="col-md-1"><button class="btn btn-danger mt-4" type="button" onclick="remove_combo_row('+comboRoom+');"> - </button></div>';



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