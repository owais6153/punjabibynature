<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Fan Club</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/images/about/favicon-61379b1f625c7.png') !!}">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<header>@include('front.theme.header')</header>

<body>

        <!--**********************************
            Content body start
        ***********************************-->
            <!-- row -->
        <div class="fanclub-page">
         <section class="fanclub-sec">
         	<div class="container">
  				<h2 class="sec-head">{{ trans('labels.our_review') }}</h2> 
	  			<div class="row review-row">
		            
	  				 <?php
        				foreach ($getfans as $fans) {
        			?>
		            <div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>{{$fans->reviewer_name}}</h3>
			                    <p class="stars-review">
			                    	<?php for ($i=0; $i < $fans->reviewer_rating; $i++) {?> 
			                    			<i class="fas fa-star"></i>		
			                    	<?php 
			                    	}?>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">{{$fans->reviewer_review}}</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
					            <a href="https://search.google.com/local/reviews?placeid=ChIJyw63k688K4gRjXAtI4RJERk" target="_blank" style="">
					            <img width="20px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/google.com.png">
					            {{$fans->reviewer_link}}
					            </a>
		          			</p>
		                </div>
		            </div>

		            <?php
        				}
        			?>
		            <!-- <div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>Harry Jackson</h3>
			                    <p class="stars-review">
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
					            <a href="https://search.google.com/local/reviews?placeid=ChIJyw63k688K4gRjXAtI4RJERk" target="_blank" style="">
					            <img width="20px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/google.com.png">
					            Google review
					            </a>
		          			</p>
		                </div>
	            
	        		</div>
	        		  <div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>Harry Jackson</h3>
			                    <p class="stars-review">
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
					            <a href="https://search.google.com/local/reviews?placeid=ChIJyw63k688K4gRjXAtI4RJERk" target="_blank" style="">
					            <img width="20px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/google.com.png">
					            Google review
					            </a>
		          			</p>
		                </div>
	            
	        		</div>
	        		  <div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>Harry Jackson</h3>
			                    <p class="stars-review">
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
					            <a href="https://search.google.com/local/reviews?placeid=ChIJyw63k688K4gRjXAtI4RJERk" target="_blank" style="">
					            <img width="20px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/google.com.png">
					            Google review
					            </a>
		          			</p>
		                </div>
	            
	        		</div>
	        		<div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>Harry Jackson</h3>
			                    <p class="stars-review">
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
					            <a href="https://search.google.com/local/reviews?placeid=ChIJyw63k688K4gRjXAtI4RJERk" target="_blank" style="">
					            <img width="20px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/google.com.png">
					            Google review
					            </a>
		          			</p>
		                </div>
	            
	        		</div>
	        		<div class="col-md-4 item-review">
		            	<div class="review-box">
			                <div class="review-img">
			                    <img src='{!! asset("storage/app/public/assets/images/boss.png") !!}' alt="">
			                    <div class="head-star">
			                    <h3>Harry Jackson</h3>
			                    <p class="stars-review">
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                    <i class="fas fa-star"></i>
			                	</p>
			                	</div>
			                </div>
			                <p class="des-review">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			                <p class="ndrsl-testimonial-source" style="pointer-events:all; ">
				            <a href="https://facebook.com/10156544508808365" target="_blank" style=""><img width="18px" aria-hidden="true" focusable="false" class="ndrsl-source-icon ndrsl-facebook-icon svg-inline--fa fa-google fa-w-16-" role="img" height="20" src="https://d2umh4u76e9b4y.cloudfront.net/fit-in/40x40/integrations/facebook.com.png">
				            Facebook review</a>
          					</p>
		                </div>
	            
	        		</div> -->
	         	</div>
         </section>
         </div>
            <!-- #/ container -->
        <!--**********************************
            Content body end
        ***********************************-->

</body>

<footer>@include('front.theme.footer')</footer>
</html>