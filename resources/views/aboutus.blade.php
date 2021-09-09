<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ trans('labels.admin_title') }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/assets/images/favicon.png') !!}">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<header>@include('front.theme.header')</header>

<body>

        <!--**********************************
            Content body start
        ***********************************-->
            <!-- row -->
        <div class="abt-page">
            <section class="favourite abt-sec">
    			<div class="container">
                    <h2>"TELL ME"</h2>
                    <P><?php echo $getabout->about_content; ?> </P>
                 </div>
    			</div>
			</section>
			<section class="third-sec-home abt-page-sec-two" style="background:url('storage/app/public/assets/images/about-us-parallax-banner.jpg');">
			    <div class="container">
			        <div class="about-box third-section-home">
			            <div class="sectionhome-contant">
			                <h2 class="sec-head text-left">Traditional Recipes<br/>Direct From Punjab</h2>
			            </div>
			        </div>
			    </div>
			</section>
			<section class="sixth-sec-home fourth-sec-home abt-sec-third">
				    <div class="container">
				        <div class="about-box fourth-section-home">
				            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
				                <div class="home-fourth-right">
				                    <p>Each Punjabi dish will have its own different flavor and aroma which cannot come from any Curry Powder but from spices which have to be separately prepared each day afresh for each individual dish. The blending and preparation of spices is a centuries old craft and indispensable to Indian cuisine.</p>
				                </div>
				            </div>
				            <div class="about-contant">
				                <img src='{!! asset("storage/app/public/assets/images/spoon.png") !!}' alt="">
				             </div>
				            
				        </div>
				    </div>
			</section>
			<section class="sixth-sec-home fourth-sec-home abt-sec-third">
			    <div class="container">
			        <div class="about-box fourth-section-home sec-home">
			            <div class="about-contant">
			                <img src='{!! asset("storage/app/public/assets/images/dish.png") !!}' alt="">
			             </div>
			            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
			                <div class="home-fourth-right">
			                    <p>We guarantee meals of excellent quality and sufficient quantity, specially prepared to satisfy your taste of mild, medium, hot or very hot food. We suggest you visit us to sample different combinations of original Indian food of our vast menu to truly appreciate the unique flavors of all diverse dishes.</p>
			                </div>
			            </div>
			        </div>
			    </div>
			</section>
			<section class="sixth-sec-home fourth-sec-home abt-sec-third">
				    <div class="container">
				        <div class="about-box fourth-section-home abt-page-fifth">
				            <div class="home-fourth-sec-img" style="background: url('storage/app/public/assets/images/border_white1-663x399.png');">
				                <div class="home-fourth-right">
				                    <p>Punjabi By Nature can cater to any party or event irrespective of how big or small. We specialize in catering with personalized service and special menus on request. We cater an elegant and interesting cuisine with a sophisticated Indian menu. Our highlights are personalized service, radiant and hygienic restaurant, and supreme value excellent quality food.</p>
				                </div>
				            </div>
				            <div class="about-contant">
				                <img src='{!! asset("storage/app/public/assets/images/dish2.png") !!}' alt="">
				             </div>
				            
				        </div>
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