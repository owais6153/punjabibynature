<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.profile_image') }}</th>
            <th>{{ trans('labels.name') }}</th>
            <th>{{ trans('labels.email') }}</th>
            <th>{{ trans('labels.mobile') }}</th>
            <th>{{ trans('labels.login_with') }}</th>
            <th>{{ trans('labels.otp_status') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getusers as $users) {
        ?>
        <tr id="dataid{{$users->id}}">
            <td>{{$users->id}}</td>
            <td><img src='{!! asset("storage/app/public/images/profile/".$users->profile_image) !!}' style="width: 100px;"></td>
            <td>{{$users->name}}</td>
            <td>{{$users->email}}</td>
            <td>{{$users->mobile}}</td>
            <td>
                @if($users->login_type == "facebook")
                    Facebook
                @elseif($users->login_type == "google")
                    Google
                @else
                    Normal
                @endif
            </td>
            <td>
                @if($users->is_verified == "1")
                    {{ trans('labels.verified') }}
                @else
                    {{ trans('labels.unverified') }}
                @endif
            </td>
            <td>{{$users->created_at}}</td>
            <td>
                @if (env('Environment') == 'sendbox')
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="{{ trans('labels.block') }}">
                        <span class="badge badge-danger">{{ trans('labels.block') }}</span>
                    </a>
                @else
                    @if($users->is_available == '1')
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$users->id}}','2')" style="color: #fff;">{{ trans('labels.block') }}</a>
                    @else
                        <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$users->id}}','1')" style="color: #fff;">{{ trans('labels.unblock') }}</a>
                    @endif
                @endif

                <a data-toggle="tooltip" href="{{URL::to('admin/user-details/'.$users->id)}}" data-original-title="{{ trans('labels.view') }}">
                    <span class="badge badge-warning">{{ trans('labels.view') }}</span>
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>