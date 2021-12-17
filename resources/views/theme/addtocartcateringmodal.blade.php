                
            <div class="cateringpopup">
                <input type="hidden" name="item_id" id="item_id" value="{{$getitem->id}}">
                <input type="hidden" name="product_type" id="product_type" value="catering">
                <input type="hidden" name="food_type" id="food_type" value="{{$getitem->food_type}}">
                <input type="hidden" name="catering_cat" id="catering_cat" value="{{$getitem->catering_cat_id}}">
                 @foreach ($getitem->variation as $key => $value)
                        <input type="hidden" name="price" id="price" value="{{($getitem->is_default_combo != 1) ? $value->product_price : ($value->product_price + $totalComboPrice)}}">
                        @break
                    @endforeach
                    <div class="row title-price">
                    <div class="col-md-9">
                        <h3>{{$getitem->item_name}}<span style="font-size: 13px;"> - (Will Take {{$getitem->preparing_time}} Mins to Prepare this.)</span></h3> 
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
                    
                  
                    <div class="col-md-6">
                        <label>Select People</label>
                        <select name="quantity" id="quantity" class="quantity form-control">
                            <?php 
                                $number = $getitem->minimum_peeps;
                                $max = 100;
                                for ($i=$number; $i <= $max; $i++) {?> 
                                    <option value="<?= $i; ?>"><?= $i; ?></option>
                                    
                            <?php    }
                            ?>
                        </select>
                    </div>
                   </div>
                   <div class="alert alert-danger" style="display: none;" id="AddToCartError"></div>
                             <div class="extra-food-wrap addons-box">

                        

    <div class="col-md-12 tab-content main-tab-content">
                        
            <!-- addons start -->

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

                <!-- Paid Group Addon -->
                

            <div id="paid" class="tab-pane  fade">
                <div class="col-md-12 w3-bar w3-black tab">
                     @if (isset($getAddonsByGroups[0]->name))
                    @foreach ($getAddonsByGroups as $getAddonsByGroup)
                        @if ($getAddonsByGroup->price != 0)

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
        
       
    </div>
    </div>
                    
                                    <script type="text/javascript">
                 
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


$('.paid-addon input[type="checkbox"]').change(function() {
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