<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ trans('labels.terms_conditions') }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/images/about/favicon-61379b1f625c7.png') !!}">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>


<header>@include('front.theme.header')</header>
<body>
	<section class="favourite terms-page">
    <div class="container">
        <h2 class="sec-head">{{ trans('labels.terms_conditions') }}</h2>
        <div class="row">
            {!!$gettermscondition->termscondition_content!!}
        </div>
    </div>
</section>


</body>



<footer>@include('front.theme.footer')</footer>

</html>