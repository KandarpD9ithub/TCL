<table class="table table-responsive panel-body">
    <tbody>
    <tr>
        <th>{{ Lang::get('views.band_UUID') }}</th>
        <th>{{ Lang::get('views.customer_name') }}</th>
        <th>{{ Lang::get('views.customer_mobile') }}</th>
        <th>{{ Lang::get('views.status') }}</th>
        <th>{{ Lang::get('views.issued_data') }}</th>
        <th>{{ Lang::get('views.active_inactive') }}</th>
    </tr>
    <?php $grandTotal = [];?>
    @if(empty($band_detail))
        <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
    @else
        @foreach($band_detail as $key => $list)
            <tr>
                <td>
                    {{$list['original_UUID']}}
                </td>
                <td>
                    {{$list['customer_name']}}
                </td>
                <td>
                    {{$list['customer_mobile']}}
                </td>
                <td>
                    {{$list['issued_at']}}
                </td>
                <td>
                    {{$list['status']}}
                </td>
                <td>
                    {{ $list['is_active']}}
                </td>
            </tr>
            @endforeach
    @endif
    </tbody>
</table>