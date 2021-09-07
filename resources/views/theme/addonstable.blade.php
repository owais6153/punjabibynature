<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.addons_name') }}</th>
            <th>{{ trans('labels.price') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.status') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getaddons as $addons) {
        ?>
        <tr id="dataid{{$addons->id}}">
            <td>{{$addons->id}}</td>
            <td>{{$addons->name}}</td>
            <td>{{Auth::user()->currency}}{{number_format($addons->price, 2)}}</td>
            <td>{{$addons->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    @if ($addons->is_available == 1)
                        <a class="badge badge-success px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.active') }}</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.deactive') }}</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$addons->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @else
                <td>
                    @if ($addons->is_available == 1)
                        <a class="badge badge-success px-2" onclick="StatusUpdate('{{$addons->id}}','2')" style="color: #fff;">{{ trans('labels.active') }}</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$addons->id}}','1')" style="color: #fff;">{{ trans('labels.deactive') }}</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$addons->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$addons->id}}')" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>