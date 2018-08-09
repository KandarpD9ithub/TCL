<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.created_at') }}</th>
        <th>{{ Lang::get('views.total_tax') }}</th>
        <th>{{ Lang::get('views.discount') }}</th>
        <th>{{ Lang::get('views.gross_sale') }}</th>
        <th>{{ Lang::get('views.net_sale') }}</th>
    </tr>
    </tr>
    @if(empty($totalSale))
        <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($totalSale as $order)
            <tr>
                <td>
                    {{ date('Y-m-d', strtotime($order['created_at']))}}
                </td>
                <td>
                    {{$order['tax_collected']}}
                </td>
                <td>
                    {{isset($order['discount'])? $order['discount']: 0}}
                </td>
                <td>
                    {{$order['sub_total']}}
                </td>
                <td>
                    {{$order['grand_total']}}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>