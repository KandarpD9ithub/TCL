@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.transaction') }}
                        </h3>
                    </div>
                    <div class="row margintop8 marginleft0 noprint">
                        {{ Form::open(['method' => 'get', 'id' => 'sale-filter']) }}
                        @if(superAdmin())
                            <div class="form-group col-md-2">
                                {{ Form::select('franchise', ['All']+$franchise,
                                !empty($franchiseId) ? $franchiseId : null, ['class' => 'form-control']) }}
                            </div>
                        @else
                            <div class="form-group col-md-2 margintop8">
                                {{ $franchise[$franchiseId]  }}
                            </div>
                        @endif
                        <!-- develop by Parth Patel date:-08-11-2017 -->
                            <div class="form-group col-md-2">
                                {{ Form::select('payment_method', $payment_method,
                                !empty($payment_method_Id) ? $payment_method_Id : null, ['class' => 'form-control']) }}
                            </div>
                        <!-- end -->
                        <div class="form-group col-md-2">
                            {{ Form::text('form_date',  !empty($fromDate) ? $fromDate : null,
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
                            @if(!empty($franchiseId) && !empty($toDate))
                                <?php $route = route('saleReportExcel')."?franchise=". $franchiseId."&to_date=".$toDate;
                                $setPath = 'report?franchise'. $franchiseId."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($fromDate) && !empty($franchiseId))
                                <?php $route = route('saleReportExcel')."?form_date=". $fromDate."&franchise=". $franchiseId;
                                $setPath = 'report?form_date='. $fromDate."&franchise=". $franchiseId;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('saleReportExcel')."?form_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?form_date='. $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($franchiseId) && !empty($payment_method_Id))
                                <?php $route = route('saleReportExcel')."?franchise=". $franchiseId."&payment_method=".$payment_method_Id;
                                $setPath = 'report?franchise='. $franchiseId."&payment_method=".$payment_method_Id;
                                ?>
                            @elseif(!empty($fromDate) && !empty($payment_method_Id))
                                <?php $route = route('saleReportExcel')."?form_date=". $fromDate."&payment_method=".$payment_method_Id;
                                $setPath = 'report?form_date='. $fromDate."&payment_method=".$payment_method_Id;
                                ?>
                            @elseif(!empty($payment_method_Id) && !empty($toDate))
                                <?php $route = route('saleReportExcel')."?payment_method=".$payment_method_Id."&to_date=".$toDate;
                                $setPath = 'report?payment_method='.$payment_method_Id."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($franchiseId))
                                <?php $route = route('saleReportExcel')."?franchise=". $franchiseId ;
                                $setPath = 'report?franchise='. $franchiseId;
                                ?>
                            @elseif(!empty($fromDate))
                                <?php $route = route('saleReportExcel')."?form_date=". $fromDate ;
                                $setPath = 'report?form_date='. $fromDate;
                                ?>
                            @elseif(!empty($toDate))
                                <?php $route = route('saleReportExcel')."?to_date=". $toDate ;
                                $setPath = 'report?to_date='. $toDate;
                                ?>
                            @elseif(!empty($payment_method_Id))
                                <?php $route = route('saleReportExcel')."?payment_method=".$payment_method_Id ;
                                $setPath = 'report?payment_method='.$payment_method_Id;
                                ?>
                            @else
                                <?php $route = route('saleReportExcel') ;
                                $setPath = 'report';
                                ?>
                            @endif
                            @if(!empty($franchiseId) && !empty($fromDate) && !empty($toDate) && !empty($payment_method_Id))
                                @if (superAdmin())
                                    <?php $route = route('saleReportExcel')."?franchise=". $franchiseId."&form_date=". $fromDate."&to_date=".$toDate. "&payment_method=".$payment_method_Id;
                                    $setPath = 'report?franchise='. $franchiseId."&form_date=". $fromDate."&to_date=".$toDate. "&payment_method=".$payment_method_Id
                                    ?>
                                @else
                                    <?php $route = route('saleReportExcel')."?form_date=". $fromDate."&to_date=".$toDate. "&payment_method=".$payment_method_Id ;
                                    $setPath = 'report?form_date='. $fromDate.'&to_date='.$toDate. "&payment_method=".$payment_method_Id
                                    ?>
                                @endif
                            @endif
                            <a href="{{$route}}"
                               class="btn btn-primary">
                                Export
                            </a>
                        </div>
                        <div class="col-md-1">
                            <button class="col-md-offset-4 btn btn-primary print">
                                Print
                            </button>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="row margintop8 marginleft0 marginright0">
                        <div class="col-xs-3">
                            <div class="info-tiles tiles-success">
                                <div class="tiles-heading">{{ Lang::get('views.total_transaction') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        {{ !empty($total['total_transaction'])?$total['total_transaction']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">{{ Lang::get('views.total_collected') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <span class="text-top">₹</span>
                                        {{ !empty($total['grand_total'])?$total['grand_total']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="info-tiles tiles-success">
                                <div class="tiles-heading">{{ Lang::get('views.net_sales') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <span class="text-top">₹</span>
                                        {{ !empty($total['sub_total'])?$total['sub_total']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">{{ Lang::get('views.tex_collected') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <span class="text-top">₹</span>
                                        {{ !empty($total['tax_collected'])?$total['tax_collected']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="printSectionId">
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
                            @if(empty($ordersPrice->toArray()['data']))
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
                                        @if(!empty($order['nc_order']))
                                            <?php $payment = $order['payment_method'].' (NC)'; ?>
                                        @else
                                            <?php $payment = $order['payment_method']; ?>
                                        @endif    
                                               {{$payment}} 
                                        </td>
                                        @if (!empty($payment_method_Id) && $payment_method_Id == 5) 
                                         <td>{{isset($order['nc_order'])? $order['nc_order']['non_chargeable_people']['name']: 'N/A'}}</td>
                                          <td>{{isset($order['nc_order'])? $order['nc_order']['comment']: 'N/A'}}</td>
                                        @endif
                                        <td>
                                            {{ $date }}
                                        </td>
                                        {{--<td>
                                            {{isset($order['discount'])? $order['discount']: 0}}
                                        </td>
                                        <td>
                                            {{$order['grand_total']}}
                                        </td>--}}
                                        <td>
                                           &#x20B9; {{$order['grand_total']}}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row col-md-12 noprint">
                        <div class="pull-right">
                            {!!  $ordersPrice->setPath($setPath)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    @include('layouts.scripts.reports')
@endsection