@include('front.theme.header')
<script src="https://js.stripe.com/v3/"></script>
<section class="favourite">
    <div class="container">
        <h2 class="sec-head">Success</h2>
        <div class="p">
        	<input type="hidden" name="add_money" id="add_money" value="{{Storage::disk('local')->get('add_money')}}">
        </div>
    </div>
</section>

@include('front.theme.footer')

<script type="text/javascript">
	var SITEURL = '{{URL::to('')}}';
	 $(document).ready(function() {
	 	"use strict";
	    var add_money = parseFloat($('#add_money').val());

	    $('#preloader').show();
	    $.ajax({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
	        url:"{{ URL::to('stripe-payment/charge') }}",
	        data: {
	            add_money : add_money ,
	        },  
	        method: 'POST',
	        success: function(response) {
	            $('#preloader').hide();
	            if (response.status == 1) {
	                window.location.href = SITEURL + '/wallet';
	            } else {
	                $('#ermsg').text(response.message);
	                $('#error-msg').addClass('alert-danger');
	                $('#error-msg').css("display","block");

	                setTimeout(function() {
	                    $("#error-msg").hide();
	                }, 5000);
	            }
	        },
	        error: function(error) {

	            // $('#errormsg').show();
	        }
	    });
	});
</script>