<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>            
            <th>Combo Name</th>
            <th>Created at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getComboItems as $comboItem) {
        ?>
        <tr id="dataid{{$comboItem->id}}">
            <td>{{$comboItem->id}}</td>
            <td>{{$comboItem->name}}</td>
            <td>{{$comboItem->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$comboItem->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @else
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$comboItem->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$comboItem->id}}')" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>