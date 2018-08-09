<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.item_name') }}</th>
        {{--<th>{{ Lang::get('views.category_name') }}</th>--}}
        <th>{{ Lang::get('views.sku') }}</th>
        <th>{{ Lang::get('views.item_sold') }}</th>
        <th>{{ Lang::get('views.gross_sale') }}</th>
        <th>{{ Lang::get('views.percentage') }}</th>
    </tr>
    <?php $grandTotal = [];?>
    @if(empty($orderDetail))
        <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($orderDetail as $order)
            <?php $grandTotal[] = isset($order['grand_total'])?$order['grand_total']:0; ?>
            <tr>
                <td>
                    {{$order['product_name']}}
                </td>
                {{--<td>
                    {{$order['category_name']}}
                </td>--}}
                <td>
                    {{$order['product_code']}}
                </td>
                <td>
                    {{$order['quantity']}}
                </td>
                <td>
                    {{$order['grand_total']}}
                </td>
                <td>
                    {{ $order['percent']}} %
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" align="right">{{ Lang::get('views.total') }}</td>
            <td colspan="2">
                {{ array_sum($grandTotal) }}
            </td>
        </tr>
    @endif
    </tbody>
</table>