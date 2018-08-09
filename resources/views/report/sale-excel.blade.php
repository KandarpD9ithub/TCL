<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.order') }}</th>
        <th>{{ Lang::get('views.handler') }}</th>
        <th>{{ Lang::get('views.payment') }}</th>
        @if (!empty($payment_method_Id) && $payment_method_Id == 5) 
                                 <th>NC Name</th>
                                  <th>NC Comment</th>
                                @endif
        <th>{{ Lang::get('views.created_at') }}</th>
        {{--<th>{{ Lang::get('views.discount') }}</th>
        <th>{{ Lang::get('views.gross_sale') }}</th>--}}
        <th>{{ Lang::get('views.net_sale') }}</th>
    </tr>
    </tr>
    @if(empty($ordersPrice))
        <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($ordersPrice as $order)
            <?php
            $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order['created_at'], 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
            ?>
            <tr>
                <td>
                    {{$order['order_number']}}
                </td>
                <td>
                    {{$users[$order['order_taken_by']]}}
                </td>
                <td>
                    {{$order['payment_method']}}
                </td>
                 @if (!empty($payment_method_Id) && $payment_method_Id == 5) 
                                         <td>{{isset($order['nc_order'])? $order['nc_order']['non_chargeable_people']['name']: 'N/A'}}</td>
                                          <td>{{isset($order['nc_order'])? $order['nc_order']['comment']: 'N/A'}}</td>
                                        @endif
                <td>
                    {{$date}}
                </td>
                {{--<td>
                    {{isset($order['discount'])? $order['discount']: 0}}
                </td>
                <td>
                    {{$order['grand_total']}}
                </td>--}}
                <td>
                    {{$order['grand_total']}}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>