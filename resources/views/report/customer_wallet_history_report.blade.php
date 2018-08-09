@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.customer_wallet_report') }}
                        </h3>
                    </div>
                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'time-track-filter']) }}
                        <div class="form-group col-md-4">
                            <?php
                            $AddElement='<div class="copy menu">
                                <div class =" select-category _margin_ product">
                                '.Form::select('customer', ['All']+$customersList, null, ['class' =>'category', 'id' => 'category']).'
                                </div></div>';
                                $template = str_replace(['_margin_','[0]'], ['margintop8','[_index_]'], $AddElement);
                                echo $AddElement;
                                ?>
                        {{--
                            {{ Form::select('customer', ['All']+$customer, !empty($customerId) ? $customerId : null,
                            ['class' => 'form-control', 'id' => 'customerId']) }}
                        --}}
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::text('from_date',  !empty($fromDate) ? $fromDate : null,
                            ['class' => 'form-control', 'id' => 'from', 'placeholder' => 'from']) }}
                        </div>
                        <div class="form-group col-md-2">
                            {{ Form::text('to_date', !empty($toDate) ? $toDate : null,
                            ['class' => 'form-control', 'id' => 'to', 'placeholder' => 'to']) }}
                        </div>
                        <div class="col-md-1">
                            {{ Form::submit('Filter', ['class' => 'btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                        <div class="col-md-1">
                            @if(!empty($customerId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = "wallet_history?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate
                                ?>

                            @elseif(!empty($customerId) && !empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = "wallet_history?customer=". $customerId."from_date=". $fromDate."&to_date="
                                ?>
                            @elseif(!empty($customerId) && empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = "wallet_history?customer=". $customerId."from_date=&to_date="
                                ?>

                            @elseif(empty($customerId) && !empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = "wallet_history?customer=". $customerId."from_date=&to_date="
                                ?>
                            @elseif(empty($customerId) && empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?customer=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = "wallet_history?customer=". $customerId."from_date=&to_date="
                                ?>
                            
                            @elseif(!empty($customerId))
                                <?php $route = route('transactionExcel')."?customer=". $customerId ;
                                $setPath = "wallet_history?from_date=". $fromDate."&to_date=".$toDate
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrWalletHistoryExcel')."?from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = "wallet_history?from_date=". $fromDate."&to_date=".$toDate
                                ?>
                            @else
                                <?php $route = route('CustoemrWalletHistoryExcel') ;
                                $setPath = "wallet_history?from_date=". $fromDate."&to_date=".$toDate
                                ?>
                            @endif
                            <a href="{{$route}}"
                               class="col-md-offset-10 btn btn-primary">
                                Export
                            </a>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div id="printSectionId">
                        <table class="table table-responsive panel-body">
                            <tbody>
                            <tr>
                                <th width="20%">{{ Lang::get('views.date_time') }}</th>
                                <th>{{ Lang::get('views.customer_name') }}</th>
                                 <th>{{ Lang::get('views.customer_mobile') }}</th>
                                 <th>{{ Lang::get('views.transaction_mode') }}</th>
                                <th>{{ Lang::get('views.credit_amount') }}</th>
                                <th>{{ Lang::get('views.debit_amount') }}</th>
                                <th>{{ Lang::get('views.comment') }}</th>
                                <th>{{ Lang::get('views.wallet_balance') }}</th>
                                
                            </tr>
                            @if( isset($paginatedItems) and count($paginatedItems) > 0)
                                @foreach($paginatedItems as $list)
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
                                         @if($list['credit_amount'] != null) &#x20B9;@endif  {{$list['credit_amount']}}
                                        </td>
                                        <td>
                                           @if($list['debit_amount'] != null) &#x20B9;@endif {{$list['debit_amount']}}
                                        </td>
                                        <td>
                                            {{$list['comment']}}
                                        </td>
                                        <td>
                                          &#x20B9;  {{$list['wallet_balance']}}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="8" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @endif
                            </tbody>
                        </table>
                        </div>
                    <div class="row col-md-12">
                        <div class="pull-right">
                            <?php $path = (Request::getPathInfo() . (Request::getQueryString() ? ('?' . Request::getQueryString()) : ''))?>
                          {!!  $paginatedItems->setPath($path)->render() !!} 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @include('layouts.scripts.reports')
    @include('layouts.scripts.menu')
@endsection

