<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.name') }}</th>
            <th>{{ trans('labels.rating') }}</th>
            <th>{{ trans('labels.comment') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i=1;
        foreach ($getreview as $reviews) {
        ?>
        <tr id="dataid{{$reviews->id}}">
            <td>{{$i}}</td>
            <td>{{$reviews['users']->name}}</td>
            <td><i class="fa fa-star"></i> {{$reviews->ratting}}</td>
            <td>{{$reviews->comment}}</td>
            <td>{{$reviews->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="DeleteData('{{$reviews->id}}')" title="" data-original-title="{{ trans('labels.delete') }}">
                        <span class="badge badge-danger">{{ trans('labels.delete') }}</span>
                    </a>
                </span>
            </td>
        </tr>
        <?php
        $i++;
        }
        ?>
    </tbody>
</table>