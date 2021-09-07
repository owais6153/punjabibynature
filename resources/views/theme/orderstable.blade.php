<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('labels.username') }}</th>
            <th>{{ trans('labels.order_number') }}</th>
            <th>{{ trans('labels.payment_type') }}</th>
            <th>{{ trans('labels.payment_id') }}</th>
            <th>{{ trans('labels.order_type') }}</th>
            <th>{{ trans('labels.order_status') }}</th>
            <th>{{ trans('labels.order_assigned_to') }}</th>
            <th>{{ trans('labels.created_at') }}</th>
            <th>{{ trans('labels.change_status') }}</th>
            <th>{{ trans('labels.action') }}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($getorders as $orders) {
        ?>
        <tr id="dataid{{$orders->id}}">
            <td>{{$i}}</td>
            <td>{{$orders['users']->name}}</td>
            <td>{{$orders->order_number}}</td>
            <td>
                @if($orders->payment_type == 1)
                    {{ trans('labels.razorpay_payment') }}
                @elseif($orders->payment_type == 2)
                    {{ trans('labels.stripe_payment') }}
                @elseif($orders->payment_type == 3)
                    {{ trans('labels.wallet_payment') }}
                @else
                    {{ trans('labels.cash_payment') }}
                @endif
            </td>
            <td>
                @if($orders->razorpay_payment_id == '')
                    --
                @else
                    {{$orders->razorpay_payment_id}}
                @endif
            </td>
            <td>
                @if($orders->order_type == 1)
                    {{ trans('labels.delivery') }}
                @else
                    {{ trans('labels.pickup') }}
                @endif
            </td>
            <td>
                @if($orders->status == '1')
                    {{ trans('labels.order_received') }}
                @elseif ($orders->status == '2')
                    {{ trans('labels.order_on_the_way') }}
                @elseif ($orders->status == '3')
                    {{ trans('labels.assigned_to_driver') }}
                @elseif ($orders->status == '4')
                    {{ trans('labels.delivered') }}
                @else
                    {{ trans('labels.cancelled') }}
                @endif
            </td>
            <td>
                @if ($orders->name == "")
                    --
                @else
                    {{$orders->name}}
                @endif
            </td>
            <td>{{$orders->created_at}}</td>
            <td>
                @if($orders->status == '1')
                    <a ddata-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','2')" title="" data-original-title="{{ trans('labels.order_received') }}">
                        <span class="badge badge-secondary px-2" style="color: #fff;">{{ trans('labels.order_received') }}</span>
                    </a>
                @elseif ($orders->status == '2')
                    @if ($orders->order_type == '2')
                        <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">{{ trans('labels.pickup') }}</a>
                    @else
                        <a class="open-AddBookDialog badge badge-primary px-2" data-toggle="modal" data-id="{{$orders->id}}" data-target="#myModal" style="color: #fff;">{{ trans('labels.assign_to_driver') }}</a>
                    @endif
                @elseif ($orders->status == '3')
                    <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                        <span class="badge badge-success px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">{{ trans('labels.assigned_to_driver') }}</span>
                    </a>
                @elseif ($orders->status == '4')
                    <a ddata-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                        <span class="badge badge-success px-2" style="color: #fff;">{{ trans('labels.delivered') }}</span>
                    </a>
                @else
                    <span class="badge badge-danger px-2">{{ trans('labels.cancelled') }}</span>
                @endif

                @if ($orders->status != '4' && $orders->status != '5' && $orders->status != '6')
                    <a data-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','6')" title="" data-original-title="{{ trans('labels.cancel') }}">
                        <span class="badge badge-danger px-2" style="color: #fff;">{{ trans('labels.cancel') }}</span>
                    </a>
                @endif
            </td>
            <td>
                <span>
                    <a data-toggle="tooltip" href="{{URL::to('admin/invoice/'.$orders->id)}}" data-original-title="{{ trans('labels.view') }}">
                        <span class="badge badge-warning">{{ trans('labels.view') }}</span>
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