<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Message</th>
            <th>Created at</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getnotification as $notification) {
        ?>
        <tr>
            <td>{{$notification->id}}</td>
            <td>{{$notification->title}}</td>
            <td>{{$notification->message}}</td>
            <td>{{$notification->created_at}}</td>            
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>