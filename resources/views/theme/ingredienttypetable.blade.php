<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>          
            <th>Ingredient type</th>
            <th>Created at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getingredientTypes as $ingredientsType) {
        ?>
        <tr id="dataid{{$ingredientsType->id}}">
            <td>{{$ingredientsType->id}}</td>
            <td>{{$ingredientsType->name}}</td>
            <td>{{$ingredientsType->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$ingredientsType->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @else
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$ingredientsType->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$ingredientsType->id}}')" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>