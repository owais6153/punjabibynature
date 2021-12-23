<?php 
    // print_r($getingredientsByTypes[0]->ingredients[0]->ingredients);
    print_r($getAddonsByGroups[0]->addons[0]->price);
    
?>
              <img src='{{$getimages[0]->image }}' alt="">
                <input type="hidden" name="item_id" id="item_id" value="{{$getitem->id}}">
                <input type="hidden" name="product_type" id="product_type" value="product">
                <input type="hidden" name="food_type" id="food_type" value="{{$getitem->food_type}}">
                 @foreach ($getitem->variation as $key => $value)
                        <input type="hidden" name="price" id="price" value="{{($getitem->is_default_combo != 1) ? $value->product_price : ($value->product_price + $totalComboPrice)}}">
                        @break
                    @endforeach
                    <div class="row title-price">
                    <div class="col-md-9" style="padding-left: 10px;">
                        <h3>{{$getitem->item_name}}</h3> 
                       <p>{{ Str::limit($getitem->item_description, 200) }}</p>
                       </div>
                    <div class="col-md-3" style="padding-left: 10px;">
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
                                <!-- <p style="color: #ff0000;" class="mt-3">+ {{$getitem->tax}}% Additional Tax</p> -->
                            @else
                                <!-- <p style="color: #03a103;" class="mt-3">Inclusive of all taxes</p> -->
                            @endif
                        </p> 
                    </div>
                    
                        @if (count($getitem['variation']) > 1)
                        <div class="col-md-6" style="padding-left: 10px;">
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
                    
                    <div class="col-md-6" style="padding-left: 10px;">
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
                </ul>
                @if (isset($getingredientsByTypes[0]->name))    
                    @foreach($getingredientsByTypes as $ingredientsByType)
                    <div class="ingredientsWrapper" option-allowed="{{$ingredientsByType->available_ing_option}}">
                        <h5>{{$ingredientsByType->name}} <span style="color:#000;font-size: 13px;">(You can select {{$ingredientsByType->available_ing_option}} option<?php echo ($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 's' : '' ; ?>)</span>.</h5>
                        <!-- <div id="{{$ingredientsByType->name}}" class="addon tabcontent"  >
                            <div class="selectIngredients" >        -->                                 
                            
                            <ul class="list-unstyled extra-food" ingredient_type="{{$ingredientsByType->name}}">
                            @foreach($ingredientsByType->ingredients as $ingredientsItems)
                            <li class="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? '' : 'Radio'}}">
                                <input  class="Checkbox ingredients" type="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 'checkbox' : 'radio'}}" name="ingredients['{{$ingredientsByType->name}}']" value="{{$ingredientsItems->ingredients}}" data-option-allowed="{{$ingredientsByType->available_ing_option}}" ingredient_name="{{$ingredientsItems->ingredients}}" >
                                <p>{{$ingredientsItems->ingredients}}</p>
                            </li>
                            @endforeach
                            </ul><br />
                            <!-- </div>
                        </div> --> 
                </div>
                    @endforeach
                @endif
            
            </div></div>
        <div class="alert alert-danger" style="display: none;" id="AddToCartError"></div>
          <div class="extra-food-wrap addons-box"> 
                <ul class="col-md-12 nav nav-tabs">
                      @if (isset($getAddonsByGroups[0]->name) && $getAddonsByGroups[0]->addons[0]->price != NULL)
                          <li><a class="active" data-toggle="tab" href="#ingredients">Free Add-on</a></li>
                      @endif
                </ul>
                 @if (isset($getAddonsByGroups[0]->name)) 
                    @foreach ($getAddonsByGroups as $getAddonsByGroup)
                        @if ($getAddonsByGroup->price == 0)
                        <h5>{{$getAddonsByGroup->name}}</h5>
                        <!-- <div id="{{$ingredientsByType->name}}" class="addon tabcontent"  >
                            <div class="selectIngredients" >        -->                                 
                            
                            <ul class="list-unstyled extra-food" ingredient_type="{{$ingredientsByType->name}}">
                            @foreach($getAddonsByGroup->addons as $addon)
                                    <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                        <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->name}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}"  addon_name="{{$addon->name}}">
                                        <p>{{$addon->name}}</p>
                                    </li>
                            @endforeach
                            </ul><br />
                            <!-- </div>
                        </div> --> 
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
         
         <div class="alert alert-danger" style="display: none;" id="AddToCartError"></div>
          <div class="extra-food-wrap addons-box"> 
                <ul class="col-md-12 nav nav-tabs">
                      @if (isset($getAddonsByGroups[0]->name))
                      <li><a data-toggle="tab" href="#paid">Paid Add-on</a></li>
                      @endif
                </ul>
                @if (isset($getAddonsByGroups[0]->name))
                    @foreach ($getAddonsByGroups as $getAddonsByGroup)
                        @if ($getAddonsByGroup->price != 0)
                        <h5>{{$getAddonsByGroup->name}} : {{$getdata->currency}}{{number_format($getAddonsByGroup->price, 2)}}</h5>
                        <!-- <div id="{{$ingredientsByType->name}}" class="addon tabcontent"  >
                            <div class="selectIngredients" >        -->                                 
                            
                            <ul class="list-unstyled extra-food" ingredient_type="{{$ingredientsByType->name}}">
                             @foreach($getAddonsByGroup->addons as $addon)
                                <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                    <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->name}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}" addon_name="{{$addon->name}}">
                                    <p>{{$addon->name}}</p>
                               </li>
                            @endforeach
                            </ul><br />
                            <!-- </div>
                        </div> --> 
                        @endif
                    @endforeach
                @endif
            
            
            </div></div>


      


                <textarea id="item_notes" name="item_notes" placeholder="Write Notes..."></textarea>

                <script type="text/javascript">
                    @if (isset($ComboGroups[0]->name) && $getitem->is_default_combo == 0) 
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


    
        var total = parseFloat($("#price").val());
    $('#pricelist .quaty').on('keyup', function(){
    $('.temp-pricing').hide();
    $('.qty').hide();
        basic_price = total; 
        let addon_price = $(this).parent('li').find('input[type="checkbox"]').attr('price');
        let qty = $(this).val();
        var value = addon_price*qty;
        var final_price = value+basic_price;
        
        $('p.pricing').text('{{$getdata->currency}}'+final_price.toFixed(2));

        $('#price').val(final_price.toFixed(2));


    })
    

// $('.single-addon input[type="checkbox"]').change(function() {
//     "use strict";    
//     $('.temp-pricing').hide();
//     var total = parseFloat($("#price").val()); 
//     var qty= '';
//     qty = $(this).parent('li').find('.quaty').val();
//     if (qty == '') {
//         qty = 1;
//     }
//     if($(this).is(':checked')){

//         total += parseFloat(($(this).attr('price') * qty)) || 0;

//     }

//     else{

//         total -= parseFloat(($(this).attr('price') * qty)) || 0;

//     }

//     $('p.pricing').text('{{$getdata->currency}}'+total.toFixed(2));

//     $('#price').val(total.toFixed(2));

// })


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
    $('.comboWrapp > .w3-black').html('');
    $('.combotab').fadeOut();
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

// function openCity(cityName) {
//   var i;
//   var x = document.getElementsByClassName("addon");
//   for (i = 0; i < x.length; i++) {
//     x[i].style.display = "block";
//   }
//   document.getElementById(cityName).style.display = "block";
// }
                </script>