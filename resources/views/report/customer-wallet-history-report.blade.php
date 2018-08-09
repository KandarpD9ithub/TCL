<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.date_time') }}</th>
        <th>{{ Lang::get('views.customer_name') }}</th>
        <th>{{ Lang::get('views.customer_mobile') }}</th>
        <th>{{ Lang::get('views.transaction_mode') }}</th>
        <th>{{ Lang::get('views.credit_amount') }}</th>
        <th>{{ Lang::get('views.debit_amount') }}</th>
        <th>{{ Lang::get('views.comment') }}</th>
        <th>{{ Lang::get('views.wallet_balance') }}</th>
    </tr>
    <?php $grandTotal = [];?>
    @if(empty($band_detail))
        <tr><td colspan="7" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($band_detail as $key => $list)
            <tr>
                <td>
                <?php
                $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $list['created_at'], 'UTC')->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                ?>
                    {{$date}}
                </td>
                <td>
                    {{$list['customer_name']}}
                </td>
                <td>
                    {{$list['customer_mobile']}}
                </td>
                 <td>
                    {{$list['payment_mode']}}
                </td>
                <td>
                    {{$list['credit_amount']}}
                </td>
                <td>
                    {{$list['debit_amount']}}
                </td>
                <td>
                    {{$list['comment']}}
                </td>
                <td>
                    {{$list['wallet_balance']}}
                </td>
            </tr>
            @endforeach
    @endif
    </tbody>
</table>