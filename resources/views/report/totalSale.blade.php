@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'time-track-filter']) }}
                        @if(superAdmin())
                            <div class="form-group col-md-4">
                                {{ Form::select('franchise', ['All']+$franchise, !empty($franchiseId) ? $franchiseId : null,
                                ['class' => 'form-control', 'id' => 'franchiseId']) }}
                            </div>
                        @else
                            <div class="form-group col-md-4 margintop8 franchise">
                                {{ $franchise[$franchiseId]  }}
                            </div>
                        @endif
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
                                <?php $route = route('totalSaleExcel')."?franchise=". $franchiseId."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($fromDate) && !empty($franchiseId))
                                <?php $route = route('totalSaleExcel')."?form_date=". $fromDate."&franchise=". $franchiseId;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('totalSaleExcel')."?form_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($franchiseId))
                                <?php $route = route('totalSaleExcel')."?franchise=". $franchiseId ;
                                ?>
                            @elseif(!empty($fromDate))
                                <?php $route = route('totalSaleExcel')."?form_date=". $fromDate ;
                                ?>
                            @elseif(!empty($toDate))
                                <?php $route = route('totalSaleExcel')."?to_date=". $toDate ;
                                ?>
                            @else
                                <?php $route = route('totalSaleExcel') ;
                                $setPath = 'sale';
                                ?>
                            @endif
                            @if(!empty($franchiseId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('totalSaleExcel')."?franchise=". $franchiseId."&form_date=". $fromDate."&to_date=".$toDate ;
                                ?>
                            @endif
                            <a href="{{$route}}"
                               class="btn btn-primary">
                                Export
                            </a>
                        </div>
                        <div class="col-md-1">
                            <button class="col-md-offset-1 btn btn-primary print">
                                Print
                            </button>
                        </div>
                    </div>
                    <div id="container">
                        {{ Form::hidden('totalSale', $totalSale, ['id' => 'totalSale'] ) }}
                        {{ Form::hidden('createDate', $createDate, ['id' => 'createDate'] ) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" >
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default" id="printSectionId">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
                        <thead class="head-color">
                        <tr>
                            <th class="col-xs-6">&nbsp;</th>
                            <th class="col-xs-2 text-right">Sales</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(empty($total))
                            <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @else
                            <tr class="bold">
                                <td>Gross Sales</td>
                                <td class="text-right" id="divTotalSales">₹ {{ $total['sub_total'] }}</td>
                            </tr>
                            <tr>
                                <td>Discounts</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($total['discount'])?$total['discount']:0 }}</td>
                            </tr>
                            <tr>
                                <td>Offer</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($total['offer'])? $total['offer']:0 }}</td>
                            </tr>
                            <tr>
                                <td>Cash</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($cash)?$cash:0 }}</td>
                            </tr>
                            <tr>
                                <td>Card</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($card)?$card:0 }}</td>
                            </tr>
                            <tr>
                                <td>Paytm</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($other)?$other:0 }}</td>
                            </tr>
                            <tr>
                                <td>Wallet</td>
                                <td class="text-right" id="divTotalDiscount">₹ {{ !empty($wallet)?$wallet:0 }}</td>
                            </tr>
                            <tr class="bold">
                                <td>Net Sales</td>
                                <td class="text-right" id="divNetSaleTotal">₹ {{ $total['grand_total'] }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td class="text-right" id="divTaxSales">₹ {{ $total['tax_collected'] }}</td>
                            </tr>
                            <tr class="bold">
                                <td>Total Collected</td>
                                <td class="text-right" id="divCollectedSales">₹ {{ $total['grand_total'] }}</td>
                            </tr>
                         @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('layouts.scripts.total_sale')
    @include('layouts.scripts.reports')
@endsection