<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.pincode') }}</th>
            <th>{{ trans('labels.delivery_charge') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getpincode as $pincode) {
        ?>
        <tr id="dataid{{$pincode->id}}">
            <td>{{$pincode->id}}</td>
            <td>{{$pincode->pincode}}</td>
            <td>{{Auth::user()->currency}}{{$pincode->delivery_charge}}</td>
            <td>{{$pincode->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$pincode->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                        <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                    </a>
                    @if (env('Environment') == 'sendbox')
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="{{ trans('labels.delete') }}">
                            <span class="badge badge-danger">{{ trans('labels.delete') }}</span>
                        </a>
                    @else
                        <a class="badge badge-danger px-2" onclick="DeleteData('{{$pincode->id}}','2')" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    @endif
                </span>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>