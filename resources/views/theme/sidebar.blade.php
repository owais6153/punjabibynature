<!--**********************************
    Sidebar start
***********************************-->
<div class="nk-sidebar">           
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{URL::to('/admin/home')}}" aria-expanded="false">
                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">{{ trans('labels.dashboard') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/slider')}}" aria-expanded="false">
                    <i class="fa fa-image"></i><span class="nav-text">{{ trans('labels.sliders') }} (Only for website)</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/category')}}" aria-expanded="false">
                    <i class="icon-menu menu-icon"></i><span class="nav-text">{{ trans('labels.categories') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/addons')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">{{ trans('labels.addons') }}</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/addons/groups')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">Add-on Groups</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/combo')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">Combo Items</span>
                </a>
            </li>


            <li>
                <a href="{{URL::to('/admin/combo/groups')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">Combo Groups</span>
                </a>
            </li>

    

            <li>
                <a href="{{URL::to('/admin/ingredients')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">{{ trans('labels.ingredients') }}</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/ingredients/types')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">{{ trans('labels.ingredientsTypes') }}</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/item')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">{{ trans('labels.items') }}</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/catering/category')}}" aria-expanded="false">
                    <i class="fa fa-plus"></i><span class="nav-text">Catering Category</span>
                </a>
            </li>

            <li>
                <a href="{{URL::to('/admin/banner')}}" aria-expanded="false">
                    <i class="fa fa-bullhorn"></i><span class="nav-text">{{ trans('labels.banners') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/pincode')}}" aria-expanded="false">
                    <i class="fa fa-map-pin" aria-hidden="true"></i><span class="nav-text">{{ trans('labels.pincodes') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/promocode')}}" aria-expanded="false">
                    <i class="fa fa-tag"></i><span class="nav-text">{{ trans('labels.promocodes') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/driver')}}" aria-expanded="false">
                    <i class="fa fa-car"></i><span class="nav-text">{{ trans('labels.drivers') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/time')}}" aria-expanded="false">
                    <i class="fa fa-clock-o"></i><span class="nav-text">{{ trans('labels.working_hours') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/payment')}}" aria-expanded="false">
                    <i class="fa fa-usd"></i><span class="nav-text">{{ trans('labels.payment_methods') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/orders')}}" aria-expanded="false">
                    <i class="fa fa-shopping-cart"></i><span class="nav-text">{{ trans('labels.orders') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/users')}}" aria-expanded="false">
                    <i class="fa fa-users"></i><span class="nav-text">{{ trans('labels.users') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/reviews')}}" aria-expanded="false">
                    <i class="fa fa-star"></i><span class="nav-text">{{ trans('labels.reviews') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/report')}}" aria-expanded="false">
                    <i class="fa fa-bar-chart"></i><span class="nav-text">{{ trans('labels.report') }}</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/notification')}}" aria-expanded="false">
                    <i class="fa fa-bell"></i><span class="nav-text">{{ trans('labels.notification') }} (Only for Mobile app)</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/contact')}}" aria-expanded="false">
                    <i class="fa fa-envelope"></i><span class="nav-text">{{ trans('labels.inquiries') }}</span>
                </a>
            </li>

            @if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated)
                <li>
                    <a href="{{URL::to('/admin/otp-configuration')}}" aria-expanded="false">
                        <i class="fa fa-key"></i><span class="nav-text">OTP Configuration <span class="badge badge-danger" style="color: #fff;">Addon</span></span>
                    </a>
                </li>
            @endif

            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-note menu-icon"></i><span class="nav-text">{{ trans('labels.cms_pages') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{URL::to('/admin/privacypolicy')}}">{{ trans('labels.privacy_policy') }}</a></li>
                    <li><a href="{{URL::to('/admin/termscondition')}}">{{ trans('labels.terms_conditions') }}</a></li>
                    <li><a href="{{URL::to('/admin/settings')}}">{{ trans('labels.about_settings') }}</a></li>
                </ul>
            </li>
            <!-- OTP Addon-->
            
            <li>
                <a href="{{URL::to('/admin/systemaddons')}}" aria-expanded="false">
                    <i class="fa fa-puzzle-piece"></i><span class="nav-text">Addons Manager</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('/admin/clear-cache')}}" aria-expanded="false">
                    <i class="fa fa-refresh"></i><span class="nav-text">Clear cache</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!--**********************************
    Sidebar end
***********************************-->