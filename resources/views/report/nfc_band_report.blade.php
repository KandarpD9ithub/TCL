@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.nfc_band_report') }}
                        </h3>
                    </div>
                    <div class="row margintop8 marginleft0 marginright0">
                        <div class="col-xs-4">
                            <div class="info-tiles tiles-success">
                                <div class="tiles-heading">{{ Lang::get('views.total_bands') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        {{ !empty($totalBands['total_count'])?$totalBands['total_count']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">{{ Lang::get('views.new') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                        <!-- <span class="text-top">₹</span> -->
                                        {{ !empty($totalBands['new'])?$totalBands['new']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="info-tiles tiles-success">
                                <div class="tiles-heading">{{ Lang::get('views.in_use') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                       <!--  <span class="text-top">₹</span> -->
                                        {{ !empty($totalBands['in_use'])?$totalBands['in_use']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">{{ Lang::get('views.damaged') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                      <!--   <span class="text-top">₹</span> -->
                                        {{ !empty($totalBands['damaged'])?$totalBands['damaged']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="info-tiles tiles-primary">
                                <div class="tiles-heading">{{ Lang::get('views.lost') }}</div>
                                <div class="tiles-body-alt">
                                    <div class="text-center">
                                      <!--   <span class="text-top">₹</span> -->
                                        {{ !empty($totalBands['lost'])?$totalBands['lost']:0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'item-filter']) }}
                        @if (superAdmin())
                            <div class="form-group col-md-2 select-category">
                                <?php
                                $AddElement='<div class="copy menu">
                                <div class =" select-category _margin_ product">
                                '.Form::select('customer', ['All']+$customersList, null, ['class' =>'category', 'id' => 'category']).'
                                '.Form::hidden('productCount', count($customersList), ['id' => 'productCount'] ).'
                                </div></div>';
                                $template = str_replace(['_margin_','[0]'], ['margintop8','[_index_]'], $AddElement);
                                echo $AddElement;
                                ?>
                        {{--
                            {{ Form::select('customer', ['All']+$customersList, !empty($customerId) ? $customerId : null,
                            ['class' => 'form-control selectpicker','data-live-search'=>'true']) }} --}}
                             </div>
                        @else
                            <div class="form-group col-md-2 margintop8">
                                {{ $customer[$customerId]  }}
                            </div>
                        @endif

                        @if (superAdmin())
                            <div class="form-group col-md-2">
                            {{ Form::select('bands',$nfcBand, !empty($customerId) ? $customerId : null,
                            ['class' => 'form-control']) }}
                             </div>
                        @else
                            <div class="form-group col-md-2 margintop8">
                                {{ $customer[$customerId]  }}
                            </div>
                        @endif

                    
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

                            @if(!empty($bandId) && !empty($customerId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?bandId='. $bandId."&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($bandId) && empty($customerId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?bandId='. $bandId."&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($bandId) && !empty($customerId) && !empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&customerId=". $customerId."&from_date=". $fromDate;
                                $setPath = 'report?bandId='. $bandId."&customerId=". $customerId."&from_date=". $fromDate;
                                ?>
                            @elseif(!empty($bandId) && empty($customerId) && !empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&from_date=". $fromDate;
                                $setPath = 'report?bandId='. $bandId."&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(empty($bandId) && !empty($customerId) && !empty($fromDate) && empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?bandId='. $bandId."&customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($customerId) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?customerId=". $customerId."&to_date=".$toDate;
                                $setPath = 'report?customer'. $customerId."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($customerId) && !empty($from_Date))
                                <?php $route = route('CustoemrReportExcel')."?customerId=". $customerId."&from_Date=".$from_Date;
                                $setPath = 'report?customer'. $customerId."&from_Date=".$from_Date;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?from_date='. $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($customerId) && !empty($bandId))
                                <?php $route = route('CustoemrReportExcel')."?customerId=". $customerId."&bandId=".$bandId;
                                $setPath = 'report?customer'. $customerId."&bands=".$bandId;
                                ?>
                            @elseif(!empty($bandId) && !empty($fromDate) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."from_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'report?bandId='. $bandId."&from_date=". $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($bandId) && !empty($fromDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&fromDate=". $fromDate;
                                $setPath = 'report?fromDate='.$fromDate ."&bands=". $bandId;
                                ?>
                            @elseif(!empty($bandId) && !empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&customerId=". $customerId;
                                $setPath = 'report?customer='.$customerId ."&bands=". $bandId;
                                ?>
                            @elseif(!empty($customerId))
                                <?php $route = route('CustoemrReportExcel')."?customerId=". $customerId ;
                                $setPath = 'report?customer='. $customerId;
                                ?>
                            @elseif(!empty($fromDate))
                                <?php $route = route('CustoemrReportExcel')."?from_date=". $fromDate ;
                                $setPath = 'report?from_date='. $fromDate;
                                ?>
                            @elseif(!empty($toDate))
                                <?php $route = route('CustoemrReportExcel')."?to_date=". $toDate ;
                                $setPath = 'report?to_date='. $toDate;
                                ?>
                            @elseif(!empty($bandId) && empty($customerId))
                                <?php $route = route('CustoemrReportExcel')."?bandId=". $bandId."&customerId=". $customerId;
                                $setPath = 'report?customer='.$customerId ."&bands=". $bandId;
                                ?>
                            @else
                                <?php $route = route('CustoemrReportExcel') ;
                                $setPath = 'report';
                                ?>
                            @endif
                            @if(!empty($customerId) && !empty($fromDate) && !empty($toDate))
                                @if (superAdmin())
                                <?php $route = route('CustoemrReportExcel')."?customerId=". $customerId."&from_date=". $fromDate."&to_date=".$toDate ;
                                $setPath = 'report?customerId='. $customerId."&from_date=". $fromDate."&to_date=".$toDate
                                ?>
                                @else
                                    <?php $route = route('CustoemrReportExcel')."?from_date=". $fromDate."&to_date=".$toDate;
                                    $setPath = "report?from_date=". $fromDate."&to_date=".$toDate;
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
                                <th>{{ Lang::get('views.band_UUID') }}</th>
                                <th>{{ Lang::get('views.customer_name') }}</th>
                                <th>{{ Lang::get('views.customer_mobile') }}</th>
                                <th>{{ Lang::get('views.status') }}</th>
                                <th>{{ Lang::get('views.issued_data') }}</th>
                                <th>{{ Lang::get('views.active_inactive') }}</th>
                                
                            </tr>
                            @if(isset($paginatedItems) and count($paginatedItems) > 0)
                                @foreach($paginatedItems as $list)
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
                                            {{$list['status']}}
                                        </td>
                                        <td>
                                            {{$list['issued_at']}}
                                        </td>
                                        <td>
                                            {{$list['is_active']}}
                                        </td>
                                    </tr>
                                @endforeach
                                
                            @else
                                <tr><td colspan="6" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @endif
                            </tbody>
                        </table>
                        </div>
                    <div class="row col-md-12">
                        <div class="pull-right">
                            {!!  $paginatedItems->setPath($setPath)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('scripts')
    @include('layouts.scripts.reports')
    @include('layouts.scripts.menu')
@endsection
