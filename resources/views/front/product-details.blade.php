@include('front.theme.header')

<section class="product-details-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="product-details-img owl-carousel owl-theme">
                    @foreach ($getimages as $images)
                    <div class="item">
                        <a data-fancybox="gallery" href="{{$images->image }}">
                            <img src='{{$images->image }}' alt="">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7 pro-details-display">
                <div class="pro-details-name-wrap">
                    <h3 class="sec-head mt-0">{{$getitem->item_name}}</h3>
                    @foreach ($getitem->variation as $key => $value)
                        <input type="hidden" name="price" id="price" value="{{$value->product_price}}">
                        @break
                    @endforeach
                    @if (Session::get('id'))
                        @if ($getitem->is_favorite == 1)
                            <i class="fas fa-heart i"></i>
                        @else
                            <i class="fal fa-heart i" onclick="MakeFavorite('{{$getitem->id}}','{{Session::get('id')}}')"></i>
                        @endif
                    @else
                        <a class="i" href="{{URL::to('/signin')}}"><i class="fal fa-heart i"></i></a>
                    @endif
                </div>

                <small>{{$getitem['category']->category_name}}</small>

                @if (count($getitem['variation']) > 1)

                    {{ trans('messages.select_variation') }}
                    <select class="form-control readers" name="variation" id="variation" style="width: 50%">
                        @foreach($getitem['variation'] as $key => $variation)
                            <option value="{{$variation->id}}" data-price="{{$variation->product_price}}" data-saleprice="{{$variation->sale_price}}" data-variation="{{$variation->variation}}">{{$variation->variation}}</option>
                        @endforeach
                    </select>
                @else

                    <select class="form-control readers" name="variation" id="variation" style="width: 50%; display: none;">
                        @foreach($getitem['variation'] as $key => $variation)
                            <option value="{{$variation->id}}" data-price="{{$variation->product_price}}" data-saleprice="{{$variation->sale_price}}" data-variation="{{$variation->variation}}">{{$variation->variation}}</option>
                        @endforeach
                    </select>

                @endif
                <div class="row">
                <div class="col-md-8 extra-food-wrap addons-box">
                    <div class="col-md-12 w3-bar w3-black tab">
                        <h3>Addons</h3>
            <!-- Ingredients start -->
                    @if (isset($getingredientsByTypes[0]->name)) 
                    <button class="w3-bar-item w3-button" onclick="openCity('Ingredients')">Ingredients</button>
                                <!-- <div id="ingredientsOptions" class="ingredientsOptions">  -->
                                @foreach($getingredientsByTypes as $ingredientsByType)
                                <div id="Ingredients" class="addon tabcontent" >
                                    <div class="selectIngredients" >
                                        <h4>{{$ingredientsByType->name}}</h4>
                                        <p>You can select {{$ingredientsByType->available_ing_option}} option<?php echo ($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 's' : '' ; ?>.</p>
                                         <ul class="list-unstyled extra-food" ingredient_type="{{$ingredientsByType->name}}">
                                         @foreach($ingredientsByType->ingredients as $ingredientsItems)
                                        <li class="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? '' : 'Radio'}}">
                                            <input  class="Checkbox ingredients" type="{{($ingredientsByType->available_ing_option > 1 || $ingredientsByType->available_ing_option == 'all')? 'checkbox' : 'radio'}}" name="ingredients['{{$ingredientsByType->name}}']" value="{{$ingredientsItems->id}}" data-option-allowed="{{$ingredientsByType->available_ing_option}}" ingredient_name="{{$ingredientsItems->ingredients}}" >
                                            <p>{{$ingredientsItems->ingredients}}</p>
                                        </li>
                                         @endforeach
                                         </ul>
                                     </div>
                                </div>
                                @endforeach
                                <!-- </div> -->
                            @endif
            <!-- End Ingredients -->    
            <!-- ------Paid group addon start----- -->  
                        <!-- Paid Group Addon -->
                    @if (isset($getAddonsByGroups[0]->name)) 
                    
                        @foreach ($getAddonsByGroups as $getAddonsByGroup)

                            @if ($getAddonsByGroup->price != 0)
                            <button class="w3-bar-item w3-button" onclick="openCity('PaidGroup')">Paid Group Addon</button>
                            <div id="PaidGroup" class="addon tabcontent" style="display:none">
                                <ul class="list-unstyled extra-food addon_group paid" data-price="{{$getAddonsByGroup->price}}" group_name="{{$getAddonsByGroup->name}}">
                                    <h3>{{$getAddonsByGroup->name}} : {{$getdata->currency}}{{number_format($getAddonsByGroup->price, 2)}}</h3>
                                    <p>You can select {{$getAddonsByGroup->available_add_option}} option<?php echo ($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 's' : '' ; ?>.</p>
                                    @foreach($getAddonsByGroup->addons as $addon)
                                        <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                            <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->id}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}" addon_name="{{$addon->name}}">
                                            <p>{{$addon->name}}</p>
                                       </li>
                                    @endforeach
                                </ul>
                                </div>    
                            @endif
                        @endforeach
                    @endif 
            <!-- End Paid Group Addon -->
            <!-- ------free group start---- -->
            <!--  Free Group Addon -->
                    @if (isset($getAddonsByGroups[0]->name)) 
                    <button class="w3-bar-item w3-button" onclick="openCity('FreeGroup')">Free Group Addon</button>
                        @foreach ($getAddonsByGroups as $getAddonsByGroup)
                            @if ($getAddonsByGroup->price == 0)
                            <div id="FreeGroup" class="addon tabcontent" style="display:none">
                            <ul class="list-unstyled extra-food addon_group" group_name="{{$getAddonsByGroup->name}}"  data-price="{{$getAddonsByGroup->price}}">
                                <h3>{{$getAddonsByGroup->name}}</h3>
                                 <p>You can select {{$getAddonsByGroup->available_add_option}} option<?php echo ($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 's' : '' ; ?>.</p>
                                @foreach($getAddonsByGroup->addons as $addon)
                                    <li class="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? '' : 'Radio'}}">
                                        <input type="{{($getAddonsByGroup->available_add_option > 1 || $getAddonsByGroup->available_add_option == 'all')? 'checkbox' : 'radio'}}" name="addons['{{$getAddonsByGroup->name}}'][]" class="Checkbox group_addon" value="{{$addon->id}}" data-option-allowed="{{$getAddonsByGroup->available_add_option}}"  addon_name="{{$addon->name}}">
                                        <p>{{$addon->name}}</p>
                                    </li>
                                @endforeach
                                </ul>
                                </div>
                            @endif
                        @endforeach
                    @endif
            <!-- ------free group end---- -->
            <!-- ---------free group addon start---- -->   
                    <!-- Free Single Addon -->
                    @if (count($freeaddons['value']) != 0)
                     <button class="w3-bar-item w3-button" onclick="openCity('Freesingle')">Single Add-ons</button>
                     <div id="Freesingle" class="addon tabcontent" style="display:none"> 
                        <ul class="list-unstyled extra-food single-addon">
                            @if ($freeaddons['value'] != "")
                                <h3>{{ trans('labels.free_addons') }}</h3>
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
                
                    <!-- End Free Single Addon -->
            <!-- ---------free group addon end---- -->
            <!-- -----free single addon start---- -->
                        <!-- Paid Single Addon --> 
                    @if (count($paidaddons['value']) != 0)
                    <button class="w3-bar-item w3-button" onclick="openCity('Freeaddon')">Group Add-ons</button>
                    <div id="Freeaddon" class="addon tabcontent" style="display:none">
                        <ul class="list-unstyled extra-food single-addon">
                            <h3>{{ trans('labels.paid_addons') }}</h3>
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
                        
                    <!-- End Paid Single Addon -->
        <!-- -----free single addon end---- -->
        <!-- -----combos tab start---- -->  
                <!-- Combos -->
                @if (isset($ComboGroups[0]->name)) 
                <button class="w3-bar-item w3-button" onclick="openCity('combos')">Combo</button>
                <div id="cobmos" class="addon tabcontent" style="display:none">
                    <div id="comboGroup" style="flex: 0 0 100%; max-width: 100%;">
                        <p>Make it Combo : {{$getdata->currency}}{{$totalComboPrice}}<input type="checkbox" id="makeItCombo" class="Checkbox" data-price="{{$totalComboPrice}}"></p>
                        <div class="comboWrapp"></div>
                    </div>
                </div>
                @endif
        <!-- -----combos tab end---- -->
    </div>   
                </div>
                <div class="col-md-4 price-detail">
                <div class="pro-details-add-wrap">

           

                        <p class="pricing">
                            @foreach ($getitem->variation as $key => $value)
                                <h3 id="temp-pricing" class="product-price">{{$getdata->currency}}{{number_format($value->product_price,2)}}</h3>
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

                        <p class="open-time"><i class="far fa-clock"></i> {{$getitem->delivery_time}}</p>
                        @if (Session::get('id'))
                            @if ($getitem->item_status == '1')
                                <button class="btn" onclick="AddtoCart('{{$getitem->id}}','{{Session::get('id')}}')">{{ trans('labels.add_to_cart') }}</button>
                            @else 
                                <button class="btn" disabled="">{{ trans('labels.unavailable') }}</button>
                            @endif
                        @else
                            @if ($getitem->item_status == '1')
                                <button class="btn" onclick="AddtoCart('{{$getitem->id}}','guest')">{{ trans('labels.add_to_cart') }}</button>
                            @else 
                                <button class="btn" disabled="">{{ trans('labels.unavailable') }}</button>
                            @endif
                        @endif
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12">
                <h4 class="sec-head">{{ trans('labels.description') }}</h4>
                <p>{{$getitem->item_description}}</p> 
            </div>
            <div class="col-md-12 text-area-detail">
                <h4>Add Notes</h4>
                <textarea id="item_notes" name="item_notes" placeholder="Write Notes..."></textarea>
            </div>
            
            

            <div class="col-12">
                <h2 class="sec-head text-center">{{ trans('labels.related_food') }}</h2>
                <div class="pro-ref-carousel owl-carousel owl-theme">
                    @foreach($relatedproduct as $item)
                    <div class="item">
                        <div class="pro-box">
                            <div class="pro-img">
                                @foreach ($item->variation as $key => $value)
                                    @if($value->sale_price > 0)
                                        <div class="ribbon-wrapper">
                                            <div class="ribbon">ON SALE</div>
                                        </div>
                                    @endif
                                    @break
                                @endforeach
                                <a href="{{URL::to('product-details/'.$item->id)}}">
                                    <img src='{{$item["itemimage"]->image }}' alt="">
                                </a>
                                @if (Session::get('id'))
                                    @if ($item->is_favorite == 1)
                                        <i class="fas fa-heart i"></i>
                                    @else
                                        <i class="fal fa-heart i"  onclick="MakeFavorite('{{$item->id}}','{{Session::get('id')}}')"></i>
                                    @endif
                                @else
                                    <a href="{{URL::to('/signin')}}"><i class="fal fa-heart i"></i></a>
                                @endif
                            </div>
                            <div class="product-details-wrap">
                                <div class="product-details">
                                    <a href="{{URL::to('product-details/'.$item->id)}}">
                                        <h4>{{$item->item_name}}</h4>
                                    </a>
                                    <p class="pro-pricing">
                                        @foreach ($item->variation as $key => $value)
                                            {{$getdata->currency}}{{number_format($value->product_price,2)}}
                                            @break
                                        @endforeach
                                    </p>
                                </div>
                                <div class="product-details">
                                    <p>{{ Str::limit($item->item_description, 60) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>

@include('front.theme.footer')
<script type="text/javascript">
@if (isset($ComboGroups[0]->name)) 
$('#makeItCombo').click(function(){    
    $('#temp-pricing').hide();
    var total = parseFloat($("#price").val()); 
    var html = `@foreach ($ComboGroups as $ComboGroup)<ul class="list-unstyled extra-food ComboGroups"  id=""><h3>{{$ComboGroup->name}}</h3>@foreach ($ComboGroup->ComboItem as $ComboItem)<li><input type="radio" name="ComboItem['{{$ComboGroup->name}}']" class="Radio comboItem" value="{{$ComboItem->id}}"><p>{{$ComboItem->name}}</p></li>@endforeach</ul>@endforeach`;

    if($(this).is(':checked')){
        total += parseFloat($(this).attr('data-price')) || 0;
        $('.comboWrapp').html(html);
    }
    else{
        total -= parseFloat($(this).attr('data-price')) || 0;
        $('.comboWrapp').html('');
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
    $('#temp-pricing').hide();
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
    $('#temp-pricing').hide();
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
        $('#temp-pricing').hide();
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

function openCity(cityName) {queueMicrotask
  var i;
  var x = document.getElementsByClassName("addon");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  document.getElementById(cityName).style.display = "block";
}
</script>