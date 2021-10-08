<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.category') }}</th>
            <th>{{ trans('labels.created_at') }}</th>        
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getcateringcat as $category) {
        ?>
        <tr id="dataid{{$category->id}}">
            <td>{{$category->id}}</td>
            <td>{{$category->name}}</td>
            <td>{{$category->created_at}}</td>
            @if (env('Environment') == 'sendbox')

                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$category->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @else

                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$category->id}}')" title="" data-original-title="{{ trans('labels.edit') }}">
                            <span class="badge badge-success">{{ trans('labels.edit') }}</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$category->id}}')" style="color: #fff;">{{ trans('labels.delete') }}</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>