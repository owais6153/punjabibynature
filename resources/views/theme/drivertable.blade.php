<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.profile_image') }}</th>
            <th>{{ trans('labels.name') }}</th>
            <th>{{ trans('labels.email') }}</th>
            <th>{{ trans('labels.mobile') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getdriver as $driver)
        <tr id="dataid{{$driver->id}}">
            <td>{{$driver->id}}</td>
            <td><img src='{!! asset("storage/app/public/images/profile/".$driver->profile_image) !!}' style="width: 100px;"></td>
            <td>{{$driver->name}}</td>
            <td>{{$driver->email}}</td>
            <td>{{$driver->mobile}}</td>
            <td>{{$driver->created_at}}</td>
            <td>
                <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$driver->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                    <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                </a>
                @if (env('Environment') == 'sendbox')
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="{{ trans('labels.block') }}">
                        <span class="badge badge-danger">{{ trans('labels.block') }}</span>
                    </a>
                @else
                    @if($driver->is_available == '1')
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$driver->id}}','2')" style="color: #fff;">{{ trans('labels.block') }}</a>
                    @else
                        <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$driver->id}}','1')" style="color: #fff;">{{ trans('labels.unblock') }}</a>
                    @endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>