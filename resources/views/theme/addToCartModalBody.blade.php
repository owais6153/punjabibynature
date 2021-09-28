@if($source == 'product')
              <img src='{{$getimages[0]->image }}' alt="">


@endif




                <input type="hidden" name="item_id" id="item_id" value="{{$getitem->id}}">

                 @foreach ($getitem->variation as $key => $value)
                        <input type="hidden" name="price" id="price" value="{{($getitem->is_default_combo != 1 && $source != 'product') ? $value->product_price : ($value->product_price + $totalComboPrice)}}">
                        @break
                    @endforeach
                    <div class="row title-price">
                    <div class="col-md-9">
                        <h3>{{$getitem->item_name}}</h3> 
                       <p>{{ Str::limit($getitem->item_description, 200) }}</p>
                       </div>
                    <div class="col-md-3">
                       <p class="pricing">
                            @foreach ($getitem->variation as $key => $value)

                                <h3 id="temp-pricing" class="temp-pricing product-price">{{$getdata->currency}}{{($getitem->is_default_combo != 1) ? number_format($value->product_price,2) : number_format($value->product_price + $totalComboPrice , 2)}}</h3>
                                @if ($value->sale_price > 0)
                                    <h3 id="card2-oldprice">{{$getdata->currency}}{{number_format($value->sale_price,2)}}</h3>
                                @endif
                                @break
                            @endforeach
                            <p class="card2-oldprice-show"></p>
                            @if($getitem->tax > 0)
                                <p style="color: #ff0000;" class="mt-3">+ {{$getitem->tax}}% Additional Tax</p>
                            @else
                                <p style="color: #03a103;" class="mt-3">Inclusive of all taxes</p>
                            @endif
                        </p> 
                    </div>
                    
                        @if (count($getitem['variation']) > 1)
                        <div class="col-md-6">
                    <label>{{ trans('messages.select_variation') }}</label>
                    <select class="form-control readers" name="variation" id="variation">
                        @foreach($getitem['variation'] as $key => $variation)
                            <option value="{{$variation->id}}" data-price="{{$variation->product_price}}" data-saleprice="{{$variation->sale_price}}" data-variation="{{$variation->variation}}">{{$variation->variation}}</option>
                        @endforeach
                    </select>
                    </div>
                @else

                    <select class="form-control readers" name="variation" id="variation" style="display: none;">
                        @foreach($getitem['variation'] as $key => $variation)
                            <option value="{{$variation->id}}" data-price="{{$variation->product_price}}" data-saleprice="{{$variation->sale_price}}" data-variation="{{$variation->variation}}">{{$variation->variation}}</option>
                        @endforeach
                    </select>

                @endif
                    
                    <div class="col-md-6">
                        <label>Select Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="quantity form-control">
                    </div>
                   </div>
                    
                


                        
                        
                        

<div class="alert alert-danger" style="display: none;" id="AddToCartError"></div>
          <div class="extra-food-wrap addons-box">

                        

    <ul class="col-md-12 nav nav-tabs">
      @if (isset($getingredientsByTypes[0]->name))
          <li><a class="active" data-toggle="tab" href="#ingredients">Ingredients</a></li>
      @endif
      @if (isset($getAddonsByGroups[0]->name))
          <li><a class="{{(isset($getingredientsByTypes[0]->name)) ? '' : 'active'}}" data-toggle="tab" href="#free">Free Add-on</a></li>
      @endif

      @if (isset($getAddonsByGroups[0]->name))
      <li><a data-toggle="tab" href="#paid">Paid Add-on</a></li>
      @endif
      @if (isset($ComboGroups[0]->name)) 
      <li class="combotab" style="{{($getitem->is_default_combo != 1) ? 'display: none;' : '' }}"><a data-toggle="tab" href="#combocontent" >Combo</a></li>
      @endif
    </ul>

    <div class="col-md-12 tab-content main-tab-content">
                        
            <!-- Ingredients start -->
            <div id="ingredients" class="tab-pane in active">
                <div class="col-md-12 w3-bar w3-black tab">
            @if (isset($getingredientsByTypes[0]->name))
                    <!-- <div id="ingredientsOptions" class="ingredientsOptions">  -->
                    @foreach($getingredientsByTypes as $ingredientsByType)
                    <!-- <button class="w3-bar-item w3-button" onclick="openCity('{{$ingredientsByType->name}}')">{{$ingredientsByType->name}}</button> -->
                    <div class="ingredientsWrapper" option-allowed="{{$ingredientsByType->available_ing_option}}">
                    <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{$ingredientsByType->name}}')">
                       <h3>{{$ingredientsByType->name}}</h3> 
                       <p>You can select {{$ingredientsByType->available_ing_option}} option<?php echo ($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 's' : '' ; ?>.</p>
                       <span class="required_label">Required</span>

                    </div>
                    <div id="{{$ingredientsByType->name}}" class="addon tabcontent"  >
                        <div class="selectIngredients" >                                        
                            
                             <ul class="list-unstyled extra-food" ingredient_type="{{$ingredientsByType->name}}">
                             @foreach($ingredientsByType->ingredients as $ingredientsItems)
                            <li class="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? '' : 'Radio'}}">
                                <input  class="Checkbox ingredients" type="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 'checkbox' : 'radio'}}" name="ingredients['{{$ingredientsByType->name}}']" value="{{$ingredientsItems->ingredients}}" data-option-allowed="{{$ingredientsByType->available_ing_option}}" ingredient_name="{{$ingredientsItems->ingredients}}" >
                                <p>{{$ingredientsItems->ingredients}}</p>
                            </li>
                             @endforeach
                             </ul>
                         </div>
                    </div>
                    </div>
                    @endforeach
                    <!-- </div> -->
            @endif
            </div>
            </div>
            <!-- End Ingredients -->    
            <!-- ------Paid group addon start----- -->  
                <!-- Paid Group Addon -->
                <div id="free" class="tab-pane fade">
                    <div class="col-md-12 w3-bar w3-black tab">
               
            <!-- ------free group start---- -->
            <!--  Free Group Addon -->
                    @if (isset($getAddonsByGroups[0]->name)) 
                   
                        @foreach ($getAddonsByGroups as $getAddonsByGroup)
                         @if ($getAddonsByGroup->price == 0)
                         <div class="group_addon_wrapper">
                         <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{$getAddonsByGroup->name}}{{$getAddonsByGroup->id}}free')">
                       <h3>{{$getAddonsByGroup->name}}</h3> 
                       <p>You can select {{$getAddonsByGroup->available_add_option}} option<?php echo ($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 's' : '' ; ?>.</p>
                        </div>
                            <div id="{{$getAddonsByGroup->name}}{{$getAddonsByGroup->id}}free" class="addon tabcontent" style="display:none">
                            <ul class="list-unstyled extra-food addon_group" group_name="{{$getAddonsByGroup->name}}"  data-currency="{{$getdata->currency}}" data-price="{{$getAddonsByGroup->price}}">
                                 
                                @foreach($getAddonsByGroup->addons as $addon)
                                    <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                        <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->name}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}"  addon_name="{{$addon->name}}">
                                        <p>{{$addon->name}}</p>
                                    </li>
                                @endforeach
                                </ul>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endif

            <!-- ------free group end---- -->
            <!-- ---------free group addon start---- -->   
                    <!-- Free Single Addon -->
                    @if (count($freeaddons['value']) != 0)
                     <!-- <button >{{ trans('labels.free_addons') }}</button> -->

                     <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{ trans('labels.free_addons') }}')">
                       <h3>{{ trans('labels.free_addons') }}</h3> 
                        <p>Select Additional Add-ons</p>
                        </div>

                     <div id="{{ trans('labels.free_addons') }}" class="addon tabcontent" style="display:none"> 
                        <ul class="list-unstyled extra-food single-addon">
                            @if ($freeaddons['value'] != "")
                                @foreach ($freeaddons['value'] as $addons)
                                <li>
                                    <input type="checkbox" name="addons[]" class="Checkbox single_addon" value="{{$addons->id}}" price="{{$addons->price}}" addons_name="{{$addons->name}}">
                                    <p>{{$addons->name}}</p>
                                </li>
                                @endforeach
                            @else

                            @endif
                        </ul>
                        </div>
                    @endif
                    </div>
                    </div>
                    <!-- End Free Single Addon -->
            <!-- ---------free group addon end---- -->
            <!-- -----free single addon start---- -->
            <div id="paid" class="tab-pane fade">
                <div class="col-md-12 w3-bar w3-black tab">
                     @if (isset($getAddonsByGroups[0]->name))
                    @foreach ($getAddonsByGroups as $getAddonsByGroup)
                        @if ($getAddonsByGroup->price != 0)

                   <!--  <button class="w3-bar-item w3-button" onclick="openCity('{{$getAddonsByGroup->name}}{{$getAddonsByGroup->id}}paid')">{{$getAddonsByGroup->name}} : {{$getdata->currency}}{{number_format($getAddonsByGroup->price, 2)}}</button> -->

                   <div class="group_addon_wrapper">
                    <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{$getAddonsByGroup->name}}{{$getAddonsByGroup->id}}paid')">
                        <h3>{{$getAddonsByGroup->name}} : {{$getdata->currency}}{{number_format($getAddonsByGroup->price, 2)}}</h3>
                        <p>You can select {{$getAddonsByGroup->available_add_option}} option<?php echo ($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 's' : '' ; ?>.</p>
                    </div>
                    <div id="{{$getAddonsByGroup->name}}{{$getAddonsByGroup->id}}paid" class="addon tabcontent" style="display:none">
                        <ul class="list-unstyled extra-food addon_group paid" data-currency="{{$getdata->currency}}" data-price="{{$getAddonsByGroup->price}}" group_name="{{$getAddonsByGroup->name}}">
                            
                            @foreach($getAddonsByGroup->addons as $addon)
                                <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                    <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->name}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}" addon_name="{{$addon->name}}">
                                    <p>{{$addon->name}}</p>
                               </li>
                            @endforeach
                        </ul>
                    </div>   
                    </div> 
                        @endif
                    @endforeach
                @endif 
                
            <!-- End Paid Group Addon -->
                        <!-- Paid Single Addon --> 
                    @if (count($paidaddons['value']) != 0)
                    <!-- <button class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{ trans('labels.paid_addons') }}')"></button> -->

                    <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{ trans('labels.paid_addons') }}')">
                        <h3>{{ trans('labels.paid_addons') }}</h3>
                        <p>Select Additional Add-ons</p>
                    </div>

                    <div id="{{ trans('labels.paid_addons') }}" class="addon tabcontent" style="display:none">
                        <ul class="list-unstyled extra-food single-addon">
                            <div id="pricelist">
                            @foreach ($paidaddons['value'] as $addons)
                            <li>
                                <input type="checkbox" name="addons[]" class="Checkbox single_addon" value="{{$addons->id}}" price="{{$addons->price}}" addons_name="{{$addons->name}}">
                                <p>{{$addons->name}} : {{$getdata->currency}}{{number_format($addons->price, 2)}}</p>
                            </li>
                            @endforeach
                            </div>
                        </ul>
                        </div>
                    @endif
            </div>   
            </div>
                    <!-- End Paid Single Addon -->
        <!-- -----free single addon end---- -->
         <!-- -----combos tab start---- --> 
            @if (isset($ComboGroups[0]->name)) 

               
                            <!-- <div id="comboGroup" class="combo-div" style="flex: 0 0 100%; max-width: 100%;">
                            <p class="{{($getitem->is_default_combo == 0) ? 'not_required' : 'required_combo' }}">
                                @if ($getitem->is_default_combo == 0)
                                    <input type="checkbox" id="makeItCombo" class="Checkbox checkbox-detail" data-price="{{$totalComboPrice}}">
                                @endif
                                {{($getitem->is_default_combo == 0) ? 'Make it Combo : ' . $getdata->currency . $totalComboPrice : 'Combo Option' }}         
                            </p> -->
                            <div id="combocontent" class="comboWrapp tab-pane fade ">
                                <div class="col-md-12 w3-bar w3-black tab">
                                @if ($getitem->is_default_combo == 1)
                                    @foreach ($ComboGroups as $ComboGroup)
                                    <div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{$ComboGroup->name}}{{$ComboGroup->id}}combo')">
                                        <h3>{{$ComboGroup->name}}</h3>
                                        <p>You can select 1 option.</p>
                                        <span class="required_label">Required</span>
                                    </div>

                                    <div id="{{$ComboGroup->name}}{{$ComboGroup->id}}combo" class="addon tabcontent" style="display:none">
                                        <ul class="list-unstyled extra-food single-addon ComboGroups">
                                            <div id="pricelist">
                                            @foreach ($ComboGroup->ComboItem as $ComboItem)<li><input type="radio" name="ComboItem['{{$ComboGroup->name}}']" class="Radio comboItem" value="{{$ComboItem->name}}"><p>{{$ComboItem->name}}</p></li>@endforeach
                                            </div>
                                        </ul>
                                        </div>
                                        @endforeach
                                @endif
                            </div>
                        </div>
                            </div>
                        
                        @endif
                    <!-- -----combos tab end---- -->
       
    </div>
                    
                </div>
                @if($source == 'product')
                 <div id="comboGroup" class="w3-bar-item w3-button addons-tabs-cart combo-div" style="flex: 0 0 100%; max-width: 100%;">
                        <p class="{{($getitem->is_default_combo == 0) ? 'not_required' : 'required_combo' }}">
                                @if ($getitem->is_default_combo == 0 && isset($ComboGroups[0]->name))
                                    <input type="checkbox" id="makeItCombo" class="Checkbox checkbox-detail" data-price="{{$totalComboPrice}}">
                                @endif
                                {{($getitem->is_default_combo == 0 && isset($ComboGroups[0]->name)) ? 'Make it Combo : ' . $getdata->currency . $totalComboPrice : '' }}         
                        </p>
                </div>
                @endif
                <textarea id="item_notes" name="item_notes" placeholder="Write Notes..."></textarea>

                <script type="text/javascript">
                    @if (isset($ComboGroups[0]->name) && $getitem->is_default_combo == 0 && $source == 'catering') 
$('#makeItCombo').click(function(){    
    $('.temp-pricing').hide();
    var total = parseFloat($("#price").val()); 
    var html = `@foreach ($ComboGroups as $ComboGroup)<div class="comboWrapper"><div class="w3-bar-item w3-button addons-tabs-cart" onclick="openCity('{{$ComboGroup->name}}{{$ComboGroup->id}}combo')">
                                        <h3>{{$ComboGroup->name}}</h3>
                                        <p>You can select 1 option.</p>
                                        <span class="required_label">Required</span>
                                    </div>
                                    <div id="{{$ComboGroup->name}}{{$ComboGroup->id}}combo" class="addon tabcontent" style="display:none">
                                    <ul class="list-unstyled extra-food single-addon ComboGroups">
                                     <div id="pricelist">
                                    @foreach ($ComboGroup->ComboItem as $ComboItem)<li><input type="radio" name="ComboItem['{{$ComboGroup->name}}']" class="Radio comboItem" value="{{$ComboItem->name}}"><p>{{$ComboItem->name}}</p></li>@endforeach</div></ul></div></div>@endforeach`;

    if($(this).is(':checked')){
        total += parseFloat($(this).attr('data-price')) || 0;
        $('.comboWrapp > .w3-black').html(html);
        $('.combotab').fadeIn();
    }
    else{
        total -= parseFloat($(this).attr('data-price')) || 0;
        $('.comboWrapp > .w3-black').html('');
        $('.combotab').fadeOut();
        $('ul.col-md-12.nav.nav-tabs li:first-child() a').click();
    }
    $('p.pricing').text('{{$getdata->currency}}'+total.toFixed(2));

    $('#price').val(total.toFixed(2));
})
@endif
$('input[type="checkbox"]').change(function() {
    let option_allowed = $(this).attr('data-option-allowed');
    if (option_allowed != 'all') {
        let attr_name = $(this).attr('name');
        if($(this).is(':checked')){
            if($('input[name="'+attr_name+'"]:checked').length == option_allowed){
                $('input[name="'+attr_name+'"]:not(:checked)').attr('disabled', 'disabled');
                $('input[name="'+attr_name+'"]:not(:checked)').parents('li').addClass('disabled');
            }
        }
        else{
            $('input[name="'+attr_name+'"]').removeAttr('disabled');
            $('input[name="'+attr_name+'"]:not(:checked)').parents('li').removeClass('disabled');
        }
    }
});


$('.single-addon input[type="checkbox"]').change(function() {
    "use strict";    
    $('.temp-pricing').hide();
    var total = parseFloat($("#price").val()); 

    if($(this).is(':checked')){

        total += parseFloat($(this).attr('price')) || 0;

    }

    else{

        total -= parseFloat($(this).attr('price')) || 0;

    }

    $('p.pricing').text('{{$getdata->currency}}'+total.toFixed(2));

    $('#price').val(total.toFixed(2));

})


$('.addon_group.paid input').change(function() {
    "use strict";
    $('.temp-pricing').hide();
    var total = parseFloat($("#price").val()); 
    var attrName = $(this).attr('name');
    if($(this).attr('type') == 'checkbox'){
        if($(this).is(':checked')){
            if (!$(this).parents('ul').hasClass('counted')) {
                total += parseFloat($(this).parents('ul').attr('data-price')) || 0;
                $(this).parents('ul').addClass('counted');
            }
        }
        else{
            if ($(this).parents('ul').hasClass('counted') && $('input[name="'+attrName+'"]:checked').length == 0) {
                total -= parseFloat($(this).parents('ul').attr('data-price')) || 0;
                $(this).parents('ul').removeClass('counted');
            }
        }
    }
    else if($(this).attr('type') == 'radio'){
         $(this).parents('ul').addClass('counted');
         total += parseFloat($(this).parents('ul').attr('data-price')) || 0;
    }
    $('p.pricing').text('{{$getdata->currency}}'+total.toFixed(2));

    $('#price').val(total.toFixed(2));

})


// ------------------------

$(".readers").change(function() {
    "use strict";
    $('input[type=checkbox]').prop('checked',false);
    $('.comboWrapp').html('');
    $(".readers option:selected").each(function() {
        $('.temp-pricing').hide();
        $('#card2-oldprice').hide();
        var ttl = parseFloat($(this).attr('data-price'));
        var ttlsaleprice = parseFloat($(this).attr('data-saleprice').replace(/"|\,|\./g, ''));
        console.log(ttl);
        $('p.pricing').text('{{$getdata->currency}}'+ttl.toFixed(2));
        console.log(ttl.toFixed(2));
        if (ttlsaleprice > 0) {
            $('p.card2-oldprice-show').text('{{$getdata->currency}}'+ttlsaleprice.toFixed(2));
        }
        
        $('#price').val($(this).attr('data-price'));
    });
});
$(document).ready(function(){
    $('.readers').prop('selectedIndex',0);
});

function openCity(cityName) {
  var i;
  var x = document.getElementsByClassName("addon");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  document.getElementById(cityName).style.display = "block";
}
                </script>