<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.image') }}</th>
            <th>{{ trans('labels.category') }}</th>
            <th>{{ trans('labels.item') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getbanner as $banner) {
        ?>
        <tr id="dataid{{$banner->id}}">
            <td>{{$banner->id}}</td>
            <td><img src='{!! asset("storage/app/public/images/banner/".$banner->image) !!}' class='img-fluid' style='max-height: 50px;'></td>
            <td>
                @if ($banner->type == "category")
                    {{@$banner['category']->category_name}}
                @else
                    --
                @endif
            </td>
            <td>
                @if ($banner->type == "item")
                    {{@$banner['item']->item_name}}
                @else
                    --
                @endif
            </td>
            <td>{{$banner->created_at}}</td>
            <td>
                @if (env('Environment') == 'sendbox')
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$banner->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="{{ trans('labels.delete') }}">
                            <span class="badge badge-danger">{{ trans('labels.delete') }}</span>
                        </a>
                    </span>
                @else
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$banner->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="DeleteData('{{$banner->id}}')" title="" data-original-title="{{ trans('labels.delete') }}">
                            <span class="badge badge-danger">{{ trans('labels.delete') }}</span>
                        </a>
                    </span>
                @endif                
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>