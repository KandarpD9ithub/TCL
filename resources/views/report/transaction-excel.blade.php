<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.total_transaction') }}</th>
        <th>{{ Lang::get('views.total_tax') }}</th>
        <th>{{ Lang::get('views.gross_sale') }}</th>
        <th>{{ Lang::get('views.net_sale') }}</th>
    </tr>
    @if(empty($total))
        <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        <tr>
            <td>
                {{ $total['total_transaction'] }}
            </td>
            <td>
                {{$total['total_tax']}}
            </td>
            <td>
                {{$total['grand_total']}}
            </td>
            <td>
                {{$total['subtotal']}}
            </td>
        </tr>
    @endif
    </tbody>
</table>