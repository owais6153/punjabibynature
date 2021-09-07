<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('labels.admin_title') }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('storage/app/public/assets/images/favicon.png') !!}">
    <!-- Pignose Calender -->
    <link href="{!! asset('storage/app/public/assets/plugins/pg-calendar/css/pignose.calendar.min.css') !!}" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="{!! asset('storage/app/public/assets/plugins/chartist/css/chartist.min.css') !!}">

    <link href="{!! asset('storage/app/public/assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') !!}" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('storage/app/public/assets/plugins/sweetalert/css/sweetalert.css') !!}" rel="stylesheet">

    <!-- Date picker plugins css -->
    <link href="{!! asset('storage/app/public/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') !!}" rel="stylesheet">
    <!-- Daterange picker plugins css -->
    <link href="{!! asset('storage/app/public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <img src="{!! asset('storage/app/public/front/images/loader.gif') !!}" style="width: 80px;">
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <div id="main-wrapper">

        @include('theme.header')
        @include('theme.sidebar')
        <div class="content-body">

            @if (\Session::has('clear'))
                <div class="alert alert-success alert-dismissible">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  {!! \Session::get('clear') !!}
                </div>
            @endif
            
            @yield('content')
        </div>
        
        <!-- /#page-wrapper -->
        <div class="card-content collapse show">
          <div class="card-body">
            <div class="row my-2">
              <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="form-group">
                  <!-- Modal Change Password-->
                  <div class="modal fade text-left" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
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
                        
                        <form method="post" id="change_password_form">
                        {{csrf_field()}}
                          <div class="modal-body">
                            <label>{{ trans('labels.email') }} </label>
                            <div class="form-group">
                                <input type="email" placeholder="{{ trans('messages.enter_email') }}" class="form-control" name="email" id="email" value="{{{Auth::user()->email}}}">
                            </div>

                            <label>{{ trans('labels.mobile') }} </label>
                            <div class="form-group">
                                <input type="text" placeholder="{{ trans('messages.enter_mobile') }}" class="form-control" name="mobile" id="mobile" value="{{{Auth::user()->mobile}}}">
                            </div>

                            <hr>

                            <h5>{{ trans('labels.change_password') }}</h5>

                            <label>{{ trans('labels.old_password') }} </label>
                            <div class="form-group">
                                <input type="password" placeholder="{{ trans('messages.enter_old_password') }}" class="form-control" name="oldpassword" id="oldpassword">
                            </div>

                            <label>{{ trans('labels.new_password') }} </label>
                            <div class="form-group">
                                <input type="password" placeholder="{{ trans('messages.enter_new_password') }}" class="form-control" name="newpassword" id="newpassword">
                            </div>

                            <label>{{ trans('labels.confirm_password') }} </label>
                            <div class="form-group">
                                <input type="password" placeholder="{{ trans('messages.enter_confirm_password') }}" class="form-control" name="confirmpassword" id="confirmpassword">
                            </div>

                          </div>
                          <div class="modal-footer">
                            <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                            value="{{ trans('labels.close') }}">
                            @if (env('Environment') == 'sendbox')
                                <input type="button" onclick="myFunction()" class="btn btn-outline-primary btn-lg" value="{{ trans('labels.update') }}">
                            @else
                                <input type="button" onclick="changePassword()" class="btn btn-outline-primary btn-lg" value="{{ trans('labels.update') }}">
                            @endif
                            
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                    <!-- Modal Settings-->
                    <div class="modal fade text-left" id="Selltings" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
                    aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <label class="modal-title text-text-bold-600" id="RditProduct">{{ trans('labels.settings') }}</label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div id="errors" style="color: red;"></div>
                          
                          <form method="post" id="settings">
                          {{csrf_field()}}
                            <div class="modal-body">

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.referral_amount') }} </label>
                                              <input type="text" placeholder="{{ trans('messages.enter_referral_amount') }}" value="{{{Auth::user()->referral_amount}}}" class="form-control" name="referral_amount" id="referral_amount">
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.currency') }} </label>
                                              <input type="text" placeholder="{{ trans('messages.enter_currency') }}" value="{{{Auth::user()->currency}}}" class="form-control" name="currency" id="currency">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                <div class="row">
                                  <div class="col-sm-3 col-md-12">
                                     <label>{{ trans('labels.current_location') }} </label>
                                    <div class="form-group">
                                        <a href="#" class="badge badge-primary px-2" onclick="getLocation()" >
                                            {{ trans('labels.get_current_location') }}
                                        </a>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.latitude') }} </label>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" name="lat" id="lat" value="{{{Auth::user()->lat}}}">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.longitude') }} </label>
                                              <input type="text" class="form-control" name="lang" id="lang" value="{{{Auth::user()->lang}}}">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>{{ trans('labels.max_order_qty') }} </label>
                                              <div class="form-group">
                                                  <input type="text" placeholder="{{ trans('messages.enter_max_order_qty') }}" value="{{{Auth::user()->max_order_qty}}}" class="form-control" name="max_order_qty" id="max_order_qty">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>{{ trans('labels.min_amount') }} </label>
                                              <input type="text" placeholder="{{ trans('messages.enter_min_order_amount') }}" value="{{{Auth::user()->min_order_amount}}}" class="form-control" name="min_order_amount" id="min_order_amount">
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>{{ trans('labels.max_amount') }} </label>
                                              <input type="text" placeholder="{{ trans('messages.enter_max_order_amount') }}" value="{{{Auth::user()->max_order_amount}}}" class="form-control" name="max_order_amount" id="max_order_amount">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.firebase_key') }} </label>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" name="firebase" id="firebase" value="{{{Auth::user()->firebase}}}" placeholder="{{ trans('messages.enter_firebase_key') }}">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>{{ trans('labels.map_key') }} </label>
                                              <input type="text" class="form-control" name="map" id="map" value="{{{Auth::user()->map}}}" placeholder="{{ trans('messages.enter_map_key') }}">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                <div class="row">
                                  <div class="col-sm-3 col-md-12">
                                    <label>{{ trans('labels.timezone') }} </label>
                                    <div class="form-group">
                                        <select class="form-control selectpicker" name="timezone" id="timezone" data-live-search="true">
                                          <option value="">{{ trans('messages.select_timezone') }}</option>
                                          <option value="Pacific/Midway" {{Auth::user()->timezone == "Pacific/Midway"  ? 'selected' : ''}}>(GMT-11:00) Midway Island, Samoa</option>
                                          <option value="America/Adak" {{Auth::user()->timezone == "America/Adak"  ? 'selected' : ''}}>(GMT-10:00) Hawaii-Aleutian</option>
                                          <option value="Etc/GMT+10" {{Auth::user()->timezone == "Etc/GMT+10"  ? 'selected' : ''}}>(GMT-10:00) Hawaii</option>
                                          <option value="Pacific/Marquesas" {{Auth::user()->timezone == "Pacific/Marquesas"  ? 'selected' : ''}}>(GMT-09:30) Marquesas Islands</option>
                                          <option value="Pacific/Gambier" {{Auth::user()->timezone == "Pacific/Gambier"  ? 'selected' : ''}}>(GMT-09:00) Gambier Islands</option>
                                          <option value="America/Anchorage" {{Auth::user()->timezone == "America/Anchorage"  ? 'selected' : ''}}>(GMT-09:00) Alaska</option>
                                          <option value="America/Ensenada" {{Auth::user()->timezone == "America/Ensenada"  ? 'selected' : ''}}>(GMT-08:00) Tijuana, Baja California</option>
                                          <option value="Etc/GMT+8" {{Auth::user()->timezone == "Etc/GMT+8"  ? 'selected' : ''}}>(GMT-08:00) Pitcairn Islands</option>
                                          <option value="America/Los_Angeles" {{Auth::user()->timezone == "America/Los_Angeles"  ? 'selected' : ''}}>(GMT-08:00) Pacific Time (US & Canada)</option>
                                          <option value="America/Denver" {{Auth::user()->timezone == "America/Denver"  ? 'selected' : ''}}>(GMT-07:00) Mountain Time (US & Canada)</option>
                                          <option value="America/Chihuahua" {{Auth::user()->timezone == "America/Chihuahua"  ? 'selected' : ''}}>(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                          <option value="America/Dawson_Creek" {{Auth::user()->timezone == "America/Dawson_Creek"  ? 'selected' : ''}}>(GMT-07:00) Arizona</option>
                                          <option value="America/Belize" {{Auth::user()->timezone == "America/Belize"  ? 'selected' : ''}}>(GMT-06:00) Saskatchewan, Central America</option>
                                          <option value="America/Cancun" {{Auth::user()->timezone == "America/Cancun"  ? 'selected' : ''}}>(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                          <option value="Chile/EasterIsland" {{Auth::user()->timezone == "Chile/EasterIsland"  ? 'selected' : ''}}>(GMT-06:00) Easter Island</option>
                                          <option value="America/Chicago" {{Auth::user()->timezone == "America/Chicago"  ? 'selected' : ''}}>(GMT-06:00) Central Time (US & Canada)</option>
                                          <option value="America/New_York" {{Auth::user()->timezone == "America/New_York"  ? 'selected' : ''}}>(GMT-05:00) Eastern Time (US & Canada)</option>
                                          <option value="America/Havana" {{Auth::user()->timezone == "America/Havana"  ? 'selected' : ''}}>(GMT-05:00) Cuba</option>
                                          <option value="America/Bogota" {{Auth::user()->timezone == "America/Bogota"  ? 'selected' : ''}}>(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                                          <option value="America/Caracas" {{Auth::user()->timezone == "America/Caracas"  ? 'selected' : ''}}>(GMT-04:30) Caracas</option>
                                          <option value="America/Santiago" {{Auth::user()->timezone == "America/Santiago"  ? 'selected' : ''}}>(GMT-04:00) Santiago</option>
                                          <option value="America/La_Paz" {{Auth::user()->timezone == "America/La_Paz"  ? 'selected' : ''}}>(GMT-04:00) La Paz</option>
                                          <option value="Atlantic/Stanley" {{Auth::user()->timezone == "Atlantic/Stanley"  ? 'selected' : ''}}>(GMT-04:00) Faukland Islands</option>
                                          <option value="America/Campo_Grande" {{Auth::user()->timezone == "America/Campo_Grande"  ? 'selected' : ''}}>(GMT-04:00) Brazil</option>
                                          <option value="America/Goose_Bay" {{Auth::user()->timezone == "America/Goose_Bay"  ? 'selected' : ''}}>(GMT-04:00) Atlantic Time (Goose Bay)</option>
                                          <option value="America/Glace_Bay" {{Auth::user()->timezone == "America/Glace_Bay"  ? 'selected' : ''}}>(GMT-04:00) Atlantic Time (Canada)</option>
                                          <option value="America/St_Johns" {{Auth::user()->timezone == "America/St_Johns" ? 'selected' : ''}}>(GMT-03:30) Newfoundland</option>
                                          <option value="America/Araguaina" {{Auth::user()->timezone == "America/Araguaina"  ? 'selected' : ''}}>(GMT-03:00) UTC-3</option>
                                          <option value="America/Montevideo" {{Auth::user()->timezone == "America/Montevideo"  ? 'selected' : ''}}>(GMT-03:00) Montevideo</option>
                                          <option value="America/Miquelon" {{Auth::user()->timezone == "America/Miquelon"  ? 'selected' : ''}}>(GMT-03:00) Miquelon, St. Pierre</option>
                                          <option value="America/Godthab" {{Auth::user()->timezone == "America/Godthab"  ? 'selected' : ''}}>(GMT-03:00) Greenland</option>
                                          <option value="America/Argentina/Buenos_Aires" {{Auth::user()->timezone == "America/Argentina/Buenos_Aires"  ? 'selected' : ''}}>(GMT-03:00) Buenos Aires</option>
                                          <option value="America/Sao_Paulo" {{Auth::user()->timezone == "America/Sao_Paulo"  ? 'selected' : ''}}>(GMT-03:00) Brasilia</option>
                                          <option value="America/Noronha" {{Auth::user()->timezone == "America/Noronha"  ? 'selected' : ''}}>(GMT-02:00) Mid-Atlantic</option>
                                          <option value="Atlantic/Cape_Verde" {{Auth::user()->timezone == "Atlantic/Cape_Verde"  ? 'selected' : ''}}>(GMT-01:00) Cape Verde Is.</option>
                                          <option value="Atlantic/Azores" {{Auth::user()->timezone == "Atlantic/Azores"  ? 'selected' : ''}}>(GMT-01:00) Azores</option>
                                          <option value="Europe/Belfast" {{Auth::user()->timezone == "Europe/Belfast"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Belfast</option>
                                          <option value="Europe/Dublin" {{Auth::user()->timezone == "Europe/Dublin"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Dublin</option>
                                          <option value="Europe/Lisbon" {{Auth::user()->timezone == "Europe/Lisbon"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Lisbon</option>
                                          <option value="Europe/London" {{Auth::user()->timezone == "Europe/London"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : London</option>
                                          <option value="Africa/Abidjan" {{Auth::user()->timezone == "Africa/Abidjan"  ? 'selected' : ''}}>(GMT) Monrovia, Reykjavik</option>
                                          <option value="Europe/Amsterdam" {{Auth::user()->timezone == "Europe/Amsterdam"  ? 'selected' : ''}}>(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                                          <option value="Europe/Belgrade" {{Auth::user()->timezone == "Europe/Belgrade"  ? 'selected' : ''}}>(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                                          <option value="Europe/Brussels" {{Auth::user()->timezone == "Europe/Brussels"  ? 'selected' : ''}}>(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                                          <option value="Africa/Algiers" {{Auth::user()->timezone == "Africa/Algiers"  ? 'selected' : ''}}>(GMT+01:00) West Central Africa</option>
                                          <option value="Africa/Windhoek" {{Auth::user()->timezone == "Africa/Windhoek"  ? 'selected' : ''}}>(GMT+01:00) Windhoek</option>
                                          <option value="Asia/Beirut" {{Auth::user()->timezone == "Asia/Beirut"  ? 'selected' : ''}}>(GMT+02:00) Beirut</option>
                                          <option value="Africa/Cairo" {{Auth::user()->timezone == "Africa/Cairo"  ? 'selected' : ''}}>(GMT+02:00) Cairo</option>
                                          <option value="Asia/Gaza" {{Auth::user()->timezone == "Asia/Gaza"  ? 'selected' : ''}}>(GMT+02:00) Gaza</option>
                                          <option value="Africa/Blantyre" {{Auth::user()->timezone == "Africa/Blantyre"  ? 'selected' : ''}}>(GMT+02:00) Harare, Pretoria</option>
                                          <option value="Asia/Jerusalem" {{Auth::user()->timezone == "Asia/Jerusalem"  ? 'selected' : ''}}>(GMT+02:00) Jerusalem</option>
                                          <option value="Europe/Minsk" {{Auth::user()->timezone == "Europe/Minsk" ? 'selected' : ''}}>(GMT+02:00) Minsk</option>
                                          <option value="Asia/Damascus" {{Auth::user()->timezone == "Asia/Damascus" ? 'selected' : ''}}>(GMT+02:00) Syria</option>
                                          <option value="Europe/Moscow" {{Auth::user()->timezone == "Europe/Moscow"  ? 'selected' : ''}}>(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                                          <option value="Africa/Addis_Ababa" {{Auth::user()->timezone == "Africa/Addis_Ababa"  ? 'selected' : ''}}>(GMT+03:00) Nairobi</option>
                                          <option value="Asia/Tehran" {{Auth::user()->timezone == "Asia/Tehran"  ? 'selected' : ''}}>(GMT+03:30) Tehran</option>
                                          <option value="Asia/Dubai" {{Auth::user()->timezone == "Asia/Dubai"  ? 'selected' : ''}}>(GMT+04:00) Abu Dhabi, Muscat</option>
                                          <option value="Asia/Yerevan" {{Auth::user()->timezone == "Asia/Yerevan"  ? 'selected' : ''}}>(GMT+04:00) Yerevan</option>
                                          <option value="Asia/Kabul" {{Auth::user()->timezone == "Asia/Kabul"  ? 'selected' : ''}}>(GMT+04:30) Kabul</option>
                                          <option value="Asia/Yekaterinburg" {{Auth::user()->timezone == "Asia/Yekaterinburg"  ? 'selected' : ''}}>(GMT+05:00) Ekaterinburg</option>
                                          <option value="Asia/Tashkent" {{Auth::user()->timezone == "Asia/Tashkent"  ? 'selected' : ''}}>(GMT+05:00) Tashkent</option>
                                          <option value="Asia/Kolkata" {{Auth::user()->timezone == "Asia/Kolkata"  ? 'selected' : ''}}>(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                                          <option value="Asia/Katmandu" {{Auth::user()->timezone == "Asia/Katmandu"  ? 'selected' : ''}}>(GMT+05:45) Kathmandu</option>
                                          <option value="Asia/Dhaka" {{Auth::user()->timezone == "Asia/Dhaka"  ? 'selected' : ''}}>(GMT+06:00) Astana, Dhaka</option>
                                          <option value="Asia/Novosibirsk" {{Auth::user()->timezone == "Asia/Novosibirsk"  ? 'selected' : ''}}>(GMT+06:00) Novosibirsk</option>
                                          <option value="Asia/Rangoon" {{Auth::user()->timezone == "Asia/Rangoon"  ? 'selected' : ''}}>(GMT+06:30) Yangon (Rangoon)</option>
                                          <option value="Asia/Bangkok" {{Auth::user()->timezone == "Asia/Bangkok"  ? 'selected' : ''}}>(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                                          <option value="Asia/Krasnoyarsk" {{Auth::user()->timezone == "Asia/Krasnoyarsk"  ? 'selected' : ''}}>(GMT+07:00) Krasnoyarsk</option>
                                          <option value="Asia/Hong_Kong" {{Auth::user()->timezone == "Asia/Hong_Kong"  ? 'selected' : ''}}>(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                                          <option value="Asia/Irkutsk" {{Auth::user()->timezone == "Asia/Irkutsk"  ? 'selected' : ''}}>(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                                          <option value="Australia/Perth" {{Auth::user()->timezone == "Australia/Perth"  ? 'selected' : ''}}>(GMT+08:00) Perth</option>
                                          <option value="Australia/Eucla" {{Auth::user()->timezone == "Australia/Eucla"  ? 'selected' : ''}}>(GMT+08:45) Eucla</option>
                                          <option value="Asia/Tokyo" {{Auth::user()->timezone == "Asia/Tokyo"  ? 'selected' : ''}}>(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                                          <option value="Asia/Seoul" {{Auth::user()->timezone == "Asia/Seoul"  ? 'selected' : ''}}>(GMT+09:00) Seoul</option>
                                          <option value="Asia/Yakutsk" {{Auth::user()->timezone == "Asia/Yakutsk"  ? 'selected' : ''}}>(GMT+09:00) Yakutsk</option>
                                          <option value="Australia/Adelaide" {{Auth::user()->timezone == "Australia/Adelaide"  ? 'selected' : ''}}>(GMT+09:30) Adelaide</option>
                                          <option value="Australia/Darwin" {{Auth::user()->timezone == "Australia/Darwin"  ? 'selected' : ''}}>(GMT+09:30) Darwin</option>
                                          <option value="Australia/Brisbane" {{Auth::user()->timezone == "Australia/Brisbane"  ? 'selected' : ''}}>(GMT+10:00) Brisbane</option>
                                          <option value="Australia/Hobart" {{Auth::user()->timezone == "Australia/Hobart"  ? 'selected' : ''}}>(GMT+10:00) Hobart</option>
                                          <option value="Asia/Vladivostok" {{Auth::user()->timezone == "Asia/Vladivostok"  ? 'selected' : ''}}>(GMT+10:00) Vladivostok</option>
                                          <option value="Australia/Lord_Howe" {{Auth::user()->timezone == "Australia/Lord_Howe"  ? 'selected' : ''}}>(GMT+10:30) Lord Howe Island</option>
                                          <option value="Etc/GMT-11" {{Auth::user()->timezone == "Etc/GMT-11"  ? 'selected' : ''}}>(GMT+11:00) Solomon Is., New Caledonia</option>
                                          <option value="Asia/Magadan" {{Auth::user()->timezone == "Asia/Magadan"  ? 'selected' : ''}}>(GMT+11:00) Magadan</option>
                                          <option value="Pacific/Norfolk" {{Auth::user()->timezone == "Pacific/Norfolk"  ? 'selected' : ''}}>(GMT+11:30) Norfolk Island</option>
                                          <option value="Asia/Anadyr" {{Auth::user()->timezone == "Asia/Anadyr"  ? 'selected' : ''}}>(GMT+12:00) Anadyr, Kamchatka</option>
                                          <option value="Pacific/Auckland" {{Auth::user()->timezone == "Pacific/Auckland"  ? 'selected' : ''}}>(GMT+12:00) Auckland, Wellington</option>
                                          <option value="Etc/GMT-12" {{Auth::user()->timezone == "Etc/GMT-12"  ? 'selected' : ''}}>(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                          <option value="Pacific/Chatham" {{Auth::user()->timezone == "Pacific/Chatham"  ? 'selected' : ''}}>(GMT+12:45) Chatham Islands</option>
                                          <option value="Pacific/Tongatapu" {{Auth::user()->timezone == "Pacific/Tongatapu"  ? 'selected' : ''}}>(GMT+13:00) Nuku'alofa</option>
                                          <option value="Pacific/Kiritimati" {{Auth::user()->timezone == "Pacific/Kiritimati"  ? 'selected' : ''}}>(GMT+14:00) Kiritimati</option>
                                        </select>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                            @if (env('Environment') == 'sendbox')
                              <div class="modal-footer">
                                <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                                value="{{ trans('labels.close') }}">
                                <input type="button" class="btn btn-outline-primary btn-lg" onclick="myFunction()"  value="{{ trans('labels.submit') }}">
                              </div>
                            @else
                                <div class="modal-footer">
                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                                  value="{{ trans('labels.close') }}">
                                  <input type="button" class="btn btn-outline-primary btn-lg" onclick="settings()"  value="{{ trans('labels.submit') }}">
                                </div>
                            @endif
                          </form>
                        </div>
                      </div>
                    </div>

                    <!--Modal: your-modal-->
                    <div class="modal fade" id="your-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-notify modal-info" role="document">
                            <!--Content-->
                            <div class="modal-content text-center">
                                <!--Header-->
                                <div class="modal-header d-flex justify-content-center">
                                    <p class="heading">Be always up to date</p>
                                </div>

                                <!--Body-->
                                <div class="modal-body">

                                    <i class="fa fa-bell fa-4x animated rotateIn mb-4"></i>

                                    <p>New Order Arrived..</p>

                                </div>

                                <!--Footer-->
                                <div class="modal-footer flex-center">
                                    <a role="button" class="btn btn-outline-secondary-modal waves-effect" onClick="window.location.reload();" data-dismiss="modal">Ok</a>
                                </div>
                            </div>
                            <!--/.Content-->
                        </div>
                    </div>
                    <!--Modal: modalPush-->

                </div>
              </div>
            </div>
          </div>
        </div>

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; Designed & Developed by <a href="https://infotechgravity.com/" target="_blank">Gravity Infotech</a> 2020</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!-- /#wrapper -->

    @include('theme.script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        function myFunction() {
          "use strict";
          alert("You don't have rights in Demo Admin panel");
        }
    </script>
    <script type="text/javascript">
        function getLocation() {
          "use strict";
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
          } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
          }
        }

        function showPosition(position) {
          "use strict";
          $('#lat').val(position.coords.latitude);
          $('#lang').val(position.coords.longitude);
        }
    </script>
    <script type="text/javascript">
        function changePassword(){  
        "use strict";   
          var email=$("#email").val();
          var oldpassword=$("#oldpassword").val();
          var mobile=$("#mobile").val();
          var newpassword=$("#newpassword").val();
          var confirmpassword=$("#confirmpassword").val();
          var CSRF_TOKEN = $('input[name="_token"]').val();
          
          if($("#change_password_form").valid()) {
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/changePassword') }}",
                method:'POST',
                data:{'email':email,'mobile':mobile,'oldpassword':oldpassword,'newpassword':newpassword,'confirmpassword':confirmpassword},
                dataType:"json",
                success:function(data){
                  $('#preloader').hide();
                    $("#loading-image").hide();
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
                   $('#preloader').hide();
                }
            });
          }
        }

        function settings(){
        "use strict";
        var currency=$("#currency").val();
        var max_order_qty=$("#max_order_qty").val();
        var min_order_amount=$("#min_order_amount").val();
        var max_order_amount=$("#max_order_amount").val();
        var lat=$("#lat").val();
        var lang=$("#lang").val();
        var map=$("#map").val();
        var firebase=$("#firebase").val();
        var referral_amount=$("#referral_amount").val();
        var timezone=$("#timezone").val();
        var CSRF_TOKEN = $('input[name="_token"]').val();
            
          if($("#settings").valid()) {
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/settings') }}",
                method:'POST',
                data:{'currency':currency,'lat':lat,'lang':lang,'max_order_qty':max_order_qty,'min_order_amount':min_order_amount,'max_order_amount':max_order_amount,'map':map,'firebase':firebase,'referral_amount':referral_amount,'timezone':timezone},
                dataType:"json",
                beforeSend: function() {
                  $('#preloader').hide();
                },
                success:function(data){
                    $('#preloader').hide();
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
                   $('#preloader').hide();
                }
            });
          }
        }

        $(document).ready(function() {
          "use strict";
            $( "#settings" ).validate({
                rules :{
                    currency:{
                        required: true
                    },                 
                },

            });        
        });
        
        var noticount = 0;

        (function noti() {
          var CSRF_TOKEN = $('input[name="_token"]').val();
          $.ajax({
              headers: {
                  'X-CSRF-Token': CSRF_TOKEN 
              },
              url:"{{ url('admin/getorder') }}",
              method: 'GET', //Get method,
              dataType:"json",
              success:function(response){
                noticount = localStorage.getItem("count");

                $('#notificationcount').text(response);
                if (response != 0) {
                  if (noticount != response) {
                    localStorage.setItem("count", response);
                    jQuery("#your-modal").modal('show');

                    var audio = new Audio("{{url('/')}}/storage/app/public/assets/notification/notification.mp3");
                    audio.play();
                  }
                }else{
                  localStorage.setItem("count", response);
                }
                setTimeout(noti, 5000);
              }
          });
        })();

        function clearnoti(){
            var CSRF_TOKEN = $('input[name="_token"]').val();
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/clearnotification') }}",
                dataType:"json",
                success:function(response){
                  $('#preloader').hide();
                }
            });
        }

        $('#min_order_amount').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });

        $('#max_order_qty').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });

        $('#max_order_amount').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });

        $('#lat').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });

        $('#lang').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });

        $('#referral_amount').keyup(function(){
          "use strict";
          var val = $(this).val();
          if(isNaN(val)){
               val = val.replace(/[^0-9\.]/g,'');
               if(val.split('.').length>2) 
                   val =val.replace(/\.+$/,"");
          }
          $(this).val(val); 
        });
    </script>
</body>

</html>