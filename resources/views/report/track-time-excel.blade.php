<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.order_number') }}</th>
        <th>{{ Lang::get('views.order_taken_by') }}</th>
        <th>{{ Lang::get('views.created_at') }}</th>
        <th>{{ Lang::get('views.delivered_at') }}</th>
        <th>{{ Lang::get('views.time') }}</th>
    </tr>
    @if(empty($orders))
        <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($orders as $order)
            <?php
            $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order['created_at'], 'UTC')
                    ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
            $deliveredAt = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order['delivered_at'], 'UTC')
                    ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A);
            ?>
            <tr>
                <td>
                    {{$order['order_number']}}
                </td>
                <td>
                    {{$order['order_taken']}}
                </td>
                <td>
                    {{$date}}
                </td>
                <td>
                    {{$deliveredAt}}
                </td>
                <td>
                    {{$order['time']}}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>