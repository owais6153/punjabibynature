<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.category') }}</th>
            <th>{{ trans('labels.item_name') }}</th>
            <th>{{ trans('labels.tax') }}</th>
            <th>{{ trans('labels.delivery_time') }}</th>
            <th>{{ trans('labels.status') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getitem as $item) {
        ?>
        <tr id="dataid{{$item->id}}">
            <td>{{$item->id}}</td>
            <td>{{@$item['category']->category_name}}</td>
            <td>{{$item->item_name}}</td>
            <td>{{$item->tax, 2}}%</td>
            <td>{{$item->delivery_time}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    @if ($item->item_status == 1)
                        <a class="badge badge-success px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.active') }}</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.deactive') }}</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a data-toggle="tooltip" href="{{URL::to('admin/edititem/'.$item->id)}}" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @else
                <td>
                    @if ($item->item_status == 1)
                        <a class="badge badge-success px-2" onclick="StatusUpdate('{{$item->id}}','2')" style="color: #fff;">{{ trans('labels.active') }}</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$item->id}}','1')" style="color: #fff;">{{ trans('labels.deactive') }}</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a data-toggle="tooltip" href="{{URL::to('admin/edititem/'.$item->id)}}" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$item->id}}')" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>