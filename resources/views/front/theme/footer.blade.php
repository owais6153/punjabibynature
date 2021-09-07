<!-- Modal Change Password-->
<div class="modal fade text-left" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.change_password') }}</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="errors" style="color: red;"></div>
      
      <form method="post" id="change_password_form">
      {{csrf_field()}}
        <div class="modal-body">
          <label>{{ trans('labels.old_password') }} </label>
          <div class="form-group">
              <input type="password" placeholder="{{ trans('messages.enter_old_password') }}" class="form-control" name="oldpassword" id="oldpassword">
          </div>

          <label>{{ trans('labels.new_password') }}</label>
          <div class="form-group">
              <input type="password" placeholder="{{ trans('messages.enter_new_password') }}" class="form-control" name="newpassword" id="newpassword">
          </div>

          <label>{{ trans('labels.confirm_password') }}</label>
          <div class="form-group">
              <input type="password" placeholder="{{ trans('messages.enter_confirm_password') }}" class="form-control" name="confirmpassword" id="confirmpassword">
          </div>

        </div>
        <div class="modal-footer">
          <input type="reset" class="btn open comman" data-dismiss="modal"
          value="{{ trans('labels.close') }}">
          <input type="button" class="btn open comman" onclick="changePassword()"  value="{{ trans('labels.save') }}">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Profile-->
<div class="modal fade text-left" id="EditProfile" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.edit_profile') }}</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="errors" style="color: red;"></div>
      
      <form id="edit_profile" enctype="multipart/form-data">
      {{csrf_field()}}
        <div class="modal-body">
          <label>{{ trans('labels.name') }} </label>
          <div class="form-group">
              <input type="text" placeholder="{{ trans('messages.enter_name') }}" class="form-control" name="name" id="name" value="{{Session::get('name')}}">
          </div>

          <label>{{ trans('labels.email') }} </label>
          <div class="form-group">
              <input type="email" placeholder="{{ trans('messages.enter_email') }}" class="form-control" name="email" value="{{Session::get('email')}}" readonly="">
          </div>

          <label>{{ trans('labels.mobile') }} </label>
          <div class="form-group">
              <input type="text" placeholder="{{ trans('messages.enter_mobile') }}" class="form-control" name="mobile" id="mobile" value="{{Session::get('mobile')}}" readonly="">
          </div>

          <label>{{ trans('labels.image') }}</label>
          <div class="form-group">
              <input type="file" class="form-control" name="profile_image" id="profile_image">
          </div>

        </div>
        <div class="modal-footer">
          <input type="reset" class="btn open comman w-50" data-dismiss="modal" value="{{ trans('labels.close') }}">
          <button type="submit" class="btn open comman w-50">{{ trans('labels.update') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Add Review-->
<div class="modal fade text-left" id="AddReview" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.add_review') }}</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="errorr" style="color: red;"></div>
      
      <form method="post">
      {{csrf_field()}}
        <div class="modal-body">
            <div class="rating"> 
            <input type="radio" name="rating" value="5" id="star5"><label for="star5">☆</label> 
            <input type="radio" name="rating" value="4" id="star4"><label for="star4">☆</label> 
            <input type="radio" name="rating" value="3" id="star3"><label for="star3">☆</label> 
            <input type="radio" name="rating" value="2" id="star2"><label for="star2">☆</label> 
            <input type="radio" name="rating" value="1" id="star1"><label for="star1">☆</label>
          </div>

          <label>{{ trans('labels.comment') }} </label>
          <div class="form-group">
            <textarea class="form-control" name="comment" id="comment" rows="5" required=""></textarea>
            <input type="hidden" name="user_id" id="user_id" class="form-control" value="{{Session::get('id')}}">
          </div>

        </div>
        <div class="modal-footer">
          <input type="reset" class="btn open comman" data-dismiss="modal"
          value="{{ trans('labels.close') }}">
          <input type="button" class="btn open comman" onclick="addReview()"  value="{{ trans('labels.submit') }}">
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Add Refer-->
<div class="modal fade text-left" id="Refer" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.refer_earn') }}</label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="errorr" style="color: red;"></div>
      
        <div class="modal-body">
            <img src='{!! asset("storage/app/public/front/images/referral.png") !!}' alt="img1" border="0">
            <p style="color: #464648;font-size: 16px;font-weight: 500;margin-bottom: 0; text-align: center;">Share this code with a friend and you both could be eligible for <span style="color: #366ed4">{{$getdata->currency}}{{number_format(Session::get('referral_amount'), 2)}}</span> bonus amount under our Referral Program.</p>
            <hr>
            <div class="text-center mt-2">
              <label>{{ trans('labels.referral_code') }}</label>
              <p style="color: #366ed4;font-size: 35px;font-weight: 500;margin-bottom: 0; text-align: center;">{{Session::get('referral_code')}}</p>
            </div>

            <p style="text-align: center;">-----{{ trans('labels.or') }}-----</p>

            <div class="text-center mt-2">
              <label>{{ trans('labels.link_share') }}</label>
              <div class="form-group">
                <input type="text" class="form-control text-center" value="{{url('/signup')}}/?referral_code={{Session::get('referral_code')}}" id="myInput" readonly="">

                <div class="tooltip-refer">
                  <button onclick="myFunction()" class="btn btn-outline-secondary" onmouseout="outFunc()">
                    <span class="tooltiptext" id="myTooltip">{{ trans('labels.copy_link') }}</span>
                    {{ trans('labels.copy_link') }}
                  </button>
                </div>
              </div>
            </div>

        </div>
    </div>
  </div>
</div>


<footer>
  <div class="container d-flex justify-content-between flex-wrap">
    <div class="footer-head">
      <div class="footer-logo"><img src='{!! asset("storage/app/public/images/about/".$getabout->footer_logo) !!}' alt=""></div>
      <p>{!! \Illuminate\Support\Str::limit(htmlspecialchars($getabout->about_content, ENT_QUOTES, 'UTF-8'), $limit = 200, $end = '...') !!}</p>
    </div>
    <div class="footer-socialmedia">
      @if($getabout->fb != "")
        <a href="{{$getabout->fb}}" target="_blank"><i class="fab fa-facebook-f"></i></a>
      @endif

      @if($getabout->twitter != "")
        <a href="{{$getabout->twitter}}" target="_blank"><i class="fab fa-twitter"></i></a>
      @endif

      @if($getabout->insta != "")
        <a href="{{$getabout->insta}}" target="_blank"><i class="fab fa-instagram"></i></a>
      @endif
    </div>
    <div class="download-app">
      <p>{{ trans('labels.download_app') }}</p>
      <div class="download-app-wrap">
        @if($getabout->ios != "")
          <div class="download-app-icon">
            <a href="{{$getabout->ios}}" target="_blank"><img src="{!! asset('storage/app/public/front/images/apple-store.svg') !!}" alt=""></a>
          </div>
        @endif

        @if($getabout->android != "")
          <div class="download-app-icon">
            <a href="{{$getabout->android}}" target="_blank"><img src="{!! asset('storage/app/public/front/images/play-store.png') !!}" alt=""></a>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="copy-right text-center">
    <a href="{{URL::to('/privacy')}}" style="color: #fff;"> {{ trans('labels.privacy_policy') }} </a>
    <p>{{$getabout->copyright}} <br> Designed & Developed by <a href="http://infotechgravity.com/" target="_blank" style="color: #000;"><b>Gravity Infotech</b>.</a></p>
  </div>
</footer>

<a onclick="topFunction()" id="myBtn" title="Go to top" style="display: block;"><i class="fad fa-long-arrow-alt-up"></i></a>

<!-- footer -->


<!-- View order btn -->
@if (Session::get('cart') && !request()->is('cart'))
  <a href="{{URL::to('/cart')}}" class="view-order-btn">{{ trans('labels.view_my_order') }}</a>
@else
  <a href="{{URL::to('/cart')}}" class="view-order-btn" style="display: none;">{{ trans('labels.view_my_order') }}</a>
@endif
<!-- View order btn -->


<!-- jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- bootstrap js -->
<script src="{!! asset('storage/app/public/front/js/bootstrap.bundle.js') !!}"></script>

<!-- owl.carousel js -->
<script src="{!! asset('storage/app/public/front/js/owl.carousel.min.js') !!}"></script>

<!-- lazyload js -->
<script src="{!! asset('storage/app/public/front/js/lazyload.js') !!}"></script>

<!-- custom js -->
<script src="{!! asset('storage/app/public/front/js/custom.js') !!}"></script>

<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<script src="{!! asset('storage/app/public/assets/plugins/sweetalert/js/sweetalert.min.js') !!}"></script>

<script type="text/javascript">

  function myFunction() {
    "use strict";
    var copyText = document.getElementById("myInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    var tooltip = document.getElementById("myTooltip");
    tooltip.innerHTML = "Copied";
  }

  function outFunc() {
    "use strict";
    var tooltip = document.getElementById("myTooltip");
    tooltip.innerHTML = "Copy to clipboard";
  }

  function changePassword(){
    "use strict";
    var oldpassword=$("#oldpassword").val();
    var newpassword=$("#newpassword").val();
    var confirmpassword=$("#confirmpassword").val();
    var CSRF_TOKEN = $('input[name="_token"]').val();
    
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-Token': CSRF_TOKEN 
        },
        url:"{{ url('/home/changePassword') }}",
        method:'POST',
        data:{'oldpassword':oldpassword,'newpassword':newpassword,'confirmpassword':confirmpassword},
        dataType:"json",
        success:function(data){
          $("#preloader").hide();
            if(data.error.length > 0)
            {
                var error_html = '';
                for(var count = 0; count < data.error.length; count++)
                {
                    error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';
                }
                $('#errors').html(error_html);
                setTimeout(function(){
                    $('#errors').html('');
                }, 10000);
            }
            else
            {
                location.reload();
            }
        },error:function(data){
           
        }
    });
  }
  var ratting = "";
  $('.rating input').on('click', function(){
    "use strict";
    ratting = $(this).val();
  });
  function addReview(){
    "use strict";
    var comment=$("#comment").val();
    var user_id=$("#user_id").val();

    var CSRF_TOKEN = $('input[name="_token"]').val();

    $.ajax({
      headers: {
          'X-CSRF-Token': CSRF_TOKEN 
      },
      url:"{{ url('/home/addreview') }}",
      method:'POST',
      data: 'comment='+comment+'&ratting='+ratting+'&user_id='+user_id,
      dataType: 'json',
      success:function(data){
      $("#preloader").hide();
        if(data.error.length > 0)
        {
            var error_html = '';
            for(var count = 0; count < data.error.length; count++)
            {
                error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';
            }
            $('#errorr').html(error_html);
            setTimeout(function(){
                $('#errorr').html('');
            }, 10000);
        }
        else
        {
            location.reload();
        }
      },error:function(data){
         
      }
    });
  }

  function contact() {
    "use strict";
    var firstname=$("#firstname").val();
    var lastname=$("#lastname").val();
    var email=$("#email").val();
    var message=$("#message").val();
    var CSRF_TOKEN = $('input[name="_token"]').val();
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:"{{ URL::to('/home/contact') }}",
        data: {
            firstname: firstname,
            lastname: lastname,
            email: email,
            message: message
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
          $("#preloader").hide();
            if (response.status == 1) {
                $('#msg').text(response.message);
                $('#success-msg').addClass('alert-success');
                $('#success-msg').css("display","block");
                $("#contactform")[0].reset();
                setTimeout(function() {
                    $("#success-msg").hide();
                }, 5000);
            } else {
                $('#ermsg').text(response.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display","block");

                setTimeout(function() {
                    $("#error-msg").hide();
                }, 5000);
            }
        }
    })
  }
  function AddtoCart(id,user_id) {
    "use strict";
    var price = $('#price').val();
    var item_notes = $('#item_notes').val();
    var variation_id = $('#variation').val();
    var variation = $(".readers option:selected").attr("data-variation");
    var variation_price = $(".readers option:selected").attr("data-price");

    var addons_id = ($('.single_addon.Checkbox:checked').map(function() {
        return this.value;
    }).get().join(', '));

    var addons_name = ($('.single_addon.Checkbox:checked').map(function() {
      return $(this).attr('addons_name');
    }).get().join(', '));
    
    var addons_price = ($('.single_addon.Checkbox:checked').map(function() {
      return $(this).attr('price');
    }).get().join(', '));

    

    var ingredients = ($('.ingredients:checked').map(function() {
      return $(this).val();
    }).get().join(', '));

    var addon_group = ($('.group_addon:checked').map(function() {
      return $(this).val();
    }).get().join(', '));




   

     
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:"{{ URL::to('/product/addtocart') }}",
        data: {
            item_id: id,
            addons_id: addons_id,
            addons_name: addons_name,
            addons_price: addons_price,
            qty: '1',
            price: price,
            variation_id: variation_id,
            variation_price: variation_price,
            variation: variation,
            item_notes: item_notes,
            ingredients: ingredients,
            addon_group: addon_group,
            user_id: user_id,

        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
          $("#preloader").hide();
            if (response.status == 1) {
              $('#cartcnt').text(response.cartcnt);
                // $('#msg').text(response.message);
                // $('#success-msg').addClass('alert-success');
                // $('#success-msg').css("display","block");
                $('.view-order-btn').show();
                location.reload();
                // setTimeout(function() {
                //     $("#success-msg").hide();
                // }, 5000);
            } else {
                $('#ermsg').text(response.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display","block");

                setTimeout(function() {
                    $("#success-msg").hide();
                }, 5000);
            }
        },
        error: function(error) {

            // $('#errormsg').show();
        }
    })
  };
  function Unfavorite(id,user_id) {
    "use strict";
    swal({
      title: "{{ trans('messages.are_you_sure') }}",
      type: 'error',
      showCancelButton: true,
      confirmButtonText: "{{ trans('messages.yes') }}",
      cancelButtonText: "{{ trans('messages.no') }}"
    },
    function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ URL::to('/product/unfavorite') }}",
                data: {
                      item_id: id,
                      user_id: user_id
                  },
                method: 'POST',
                success: function(response) {
                    if (response == 1) {
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

  function MakeFavorite(id,user_id) {
    "use strict";
    swal({
      title: "{{ trans('messages.are_you_sure') }}",
      type: 'error',
      showCancelButton: true,
      confirmButtonText: "{{ trans('messages.yes') }}",
      cancelButtonText: "{{ trans('messages.no') }}"
    },
    function(isConfirm) {
      if (isConfirm) {
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url:"{{ URL::to('/product/favorite') }}",
          data: {
                item_id: id,
                user_id: user_id
            },
          method: 'POST',
          success: function(response) {
                if (response == 1) {
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
  };

  function OrderCancel(id) {
    "use strict";
    swal({
      title: "{{ trans('messages.are_you_sure') }}",
      type: 'error',
      showCancelButton: true,
      confirmButtonText: "{{ trans('messages.yes') }}",
      cancelButtonText: "{{ trans('messages.no') }}"
    },
    function(isConfirm) {
      if (isConfirm) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ URL::to('/order/ordercancel') }}",
            data: {
                  order_id: id,
              },
            method: 'POST',
            success: function(response) {
              if (response == 1) {
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
  };

  function codeAddress() {
    "use strict";
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'GET',
          url:"{{ URL::to('/cart/isopenclose') }}",
          success: function(response) {
            if (response.status == 0) {
                $('.open').hide();
                $('.openmsg').show();
            } else {
                $('.openmsg').hide();
            }
          }
      });
    }
    window.onload = codeAddress;

    function SelectType(type) {
      "use strict";
      var set = setCookie('data_type',type,365);
      setCookie('data_type',type,365);
      if (set) {
        $('#invalid_msg').html("Something went wrong...");
      } else {
        location.reload();
      }
    }

    function setCookie(name,value,days) {
      "use strict";
      var expires = "";
      if (days) {
          var date = new Date();
          date.setTime(date.getTime() + (days*24*60*60*1000));
          expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
      "use strict";
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for(var i=0;i < ca.length;i++) {
          var c = ca[i];
          while (c.charAt(0)==' ') c = c.substring(1,c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
      }
      return null;
    }

    $(document).ready(function(){
      "use strict";
      $("#search-box").keyup(function(){
        var query = $(this).val(); 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url:"{{ URL::to('/product/searchitem') }}",
      
            type:"POST",
           
            data:{'keyword':query},
           
            success:function (data) {
              
                $('#countryList').html(data);
            }
        })
      });
    });
    
    $(document).ready(function() {
      "use strict";
      $('#edit_profile').on('submit', function(event){
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url:"{{ URL::to('/home/editProfile') }}",
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
                    var error_html = '';
                    for(var count = 0; count < data.error.length; count++)
                    {
                        error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';
                    }
                    $('#errors').html(error_html);
                    setTimeout(function(){
                        $('#errors').html('');
                    }, 10000);
                }
                else
                {
                    location.reload();
                }
            },
        });
      });
    });
</script>
@yield('script')
</body>

</html>