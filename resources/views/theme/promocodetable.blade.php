<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.offer_name') }}</th>
            <th>{{ trans('labels.offer_code') }}</th>
            <th>{{ trans('labels.offer_percentage') }}</th>
            <th>{{ trans('labels.offer_description') }} </th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getpromocode as $promocode) {
        ?>
        <tr id="dataid{{$promocode->id}}">
            <td>{{$promocode->id}}</td>
            <td>{{$promocode->offer_name}}</td>
            <td>{{$promocode->offer_code}}</td>
            <td>{{$promocode->offer_amount}}</td>
            <td>{{$promocode->description}}</td>
            <td>{{$promocode->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$promocode->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                        <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                    </a>
                    @if (env('Environment') == 'sendbox')
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="{{ trans('labels.delete') }}">
                            <span class="badge badge-danger">{{ trans('labels.delete') }}</span>
                        </a>
                    @else
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$promocode->id}}','2')" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    @endif
                </span>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>