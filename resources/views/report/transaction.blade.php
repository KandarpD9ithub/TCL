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
                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'time-track-filter']) }}
                        <div class="form-group col-md-4">
                            {{ Form::select('franchise', ['All']+$franchise, !empty($franchiseId) ? $franchiseId : null,
                            ['class' => 'form-control', 'id' => 'franchiseId']) }}
                        </div>
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
                            @if(!empty($franchiseId))
                                <?php $route = route('transactionExcel')."?franchise=". $franchiseId ;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('transactionExcel')."?form_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @else
                                <?php $route = route('transactionExcel') ;
                                ?>
                            @endif
                            @if(!empty($franchiseId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('transactionExcel')."?franchise=". $franchiseId."&form_date=". $fromDate."&to_date=".$toDate ;
                                ?>
                            @endif
                            <a href="{{$route}}"
                               class="col-md-offset-10 btn btn-primary">
                                Export
                            </a>
                        </div>
                    </div>
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
                                <div class="tiles-heading">Total Collected</div>
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
                                <div class="tiles-heading">Net Sales</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <span class="text-top">₹</span>
                                        {{ !empty($total['subtotal'])?$total['subtotal']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">Tax Collected</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <span class="text-top">₹</span>
                                        {{ !empty($total['total_tax'])?$total['total_tax']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
