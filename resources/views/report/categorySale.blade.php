@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.category_sale') }}
                        </h3>
                    </div>
                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'category-filter']) }}
                        @if (superAdmin())
                            <div class="form-group col-md-4">
                                {{ Form::select('franchise', ['All']+$franchise, !empty($franchiseId)?$franchiseId:null,
                                ['class' => 'form-control']) }}
                            </div>
                        @else
                            <div class="form-group col-md-4 margintop8">
                                {{ $franchise[$franchiseId] }}
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
                                <?php $route = route('categoryWiseExcel')."?franchise=". $franchiseId."&to_date=".$toDate;
                                $setPath = 'sale?franchise'. $franchiseId."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($fromDate) && !empty($franchiseId))
                                <?php $route = route('categoryWiseExcel')."?form_date=". $fromDate."&franchise=". $franchiseId;
                                $setPath = 'sale?form_date='. $fromDate."&franchise=". $franchiseId;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('categoryWiseExcel')."?form_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'sale?form_date='. $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($franchiseId))
                                <?php $route = route('categoryWiseExcel')."?franchise=". $franchiseId ;
                                $setPath = 'sale?franchise='. $franchiseId;
                                ?>
                            @elseif(!empty($fromDate))
                                <?php $route = route('categoryWiseExcel')."?form_date=". $fromDate ;
                                $setPath = 'sale?form_date='. $fromDate;
                                ?>
                            @elseif(!empty($toDate))
                                <?php $route = route('categoryWiseExcel')."?to_date=". $toDate ;
                                $setPath = 'sale?to_date='. $toDate;
                                ?>
                            @else
                                <?php $route = route('categoryWiseExcel') ;
                                $setPath = 'sale';
                                ?>
                            @endif
                            @if(!empty($franchiseId) && !empty($fromDate) && !empty($toDate))
                                @if(superAdmin())
                                    <?php $route = route('categoryWiseExcel')."?franchise=". $franchiseId."&form_date=". $fromDate."&to_date=".$toDate ;
                                    $setPath = 'sale?franchise='. $franchiseId."&form_date=". $fromDate."&to_date=".$toDate
                                    ?>
                                @else
                                    <?php $route = route('categoryWiseExcel')."?form_date=". $fromDate."&to_date=".$toDate ;
                                    $setPath = "sale?form_date=". $fromDate."&to_date=".$toDate
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
                    <div id="printSectionId">
                        <table class="table table-responsive panel-body">
                            <tbody>
                            <tr>
                                <th>{{ Lang::get('views.category') }}</th>
                                <th>{{ Lang::get('views.total_items') }}</th>
                                <th>{{ Lang::get('views.quantity') }}</th>
                                <th>{{ Lang::get('views.gross_sale') }}</th>
                                <th>{{ Lang::get('views.percentage') }}</th>
                            </tr>
                                @if(empty($viewOrder))
                                    <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                                @else
                                    <?php $grandTotal = [];?>
                                    @foreach($viewOrder as $order)
                                        <?php $grandTotal[] = isset($order['grand_total'])?$order['grand_total']:0; ?>
                                        <tr>
                                            <td>
                                                {{$order['category_name']}}
                                            </td>
                                            <td>
                                                {{$order['count']}}
                                            </td>
                                            <td>
                                                {{$order['quantity']}}
                                            </td>
                                            <td>
                                               &#x20B9; {{$order['grand_total']}}
                                            </td>
                                            <td>
                                                {{$order['percent']}} %
                                            </td>
                                        </tr>
                                    @endforeach
                                        <?php $grandTotal = array_sum($grandTotal);?>
                                        @if($grandTotal > 0)
                                            <tr>
                                                <td colspan="3" align="right">{{ Lang::get('views.total') }}</td>
                                                <td colspan="2">
                                                     &#x20B9; {{ $grandTotal }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    <div class="row col-md-12">
                        <div class="pull-right">
                            {!!  $viewOrder->setPath($setPath)->render() !!}
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
