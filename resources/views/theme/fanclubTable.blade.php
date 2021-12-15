<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>            
            <th>Reviewer Name</th>
            <th>Reviewer rating</th>
            <th>Review</th>
            <th>Reviewer Link</th>
            <th>Time and Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getfans as $fans) {
        ?>
        <tr id="dataid{{$fans->id}}">
            <td>{{$fans->id}}</td>
            <td>{{$fans->reviewer_name}}</td>
            <td>{{$fans->reviewer_rating}}</td>
            <td>{{$fans->reviewer_review}}</td>
            <td>{{$fans->reviewer_link}}</td>
            <td>{{$fans->updated_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$fans->id}}')"  title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @else
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$fans->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$fans->id}}')" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>