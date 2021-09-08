<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>          
            <th>Group Name</th>
            <th>Created at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getComboGroups as $comboGroup) {
        ?>
        <tr id="dataid{{$comboGroup->id}}">
            <td>{{$comboGroup->id}}</td>
            <td>{{$comboGroup->name}}</td>
            <td>{{$comboGroup->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$comboGroup->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @else
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$comboGroup->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$comboGroup->id}}')" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>