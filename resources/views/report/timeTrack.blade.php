@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.time_track') }}
                        </h3>
                    </div>
                    <div class="row margintop8 marginleft0">
                        {{ Form::open(['method' => 'get', 'id' => 'time-track-filter']) }}
                        @if(superAdmin())
                            <div class="form-group col-md-4">
                                {{ Form::select('franchise', ['All']+$franchise,
                                !empty($franchiseId) ? $franchiseId : null, ['class' => 'form-control']) }}
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
                                <?php $route = route('trackOrderTimeExcel')."?franchise=". $franchiseId."&to_date=".$toDate;
                                $setPath = 'track-time?franchise'. $franchiseId."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($fromDate) && !empty($franchiseId))
                                <?php $route = route('trackOrderTimeExcel')."?form_date=". $fromDate."&franchise=". $franchiseId;
                                $setPath = 'track-time?form_date='. $fromDate."&franchise=". $franchiseId;
                                ?>
                            @elseif(!empty($fromDate) && !empty($toDate))
                                <?php $route = route('trackOrderTimeExcel')."?form_date=". $fromDate."&to_date=".$toDate;
                                $setPath = 'track-time?form_date='. $fromDate."&to_date=".$toDate;
                                ?>
                            @elseif(!empty($franchiseId))
                                <?php $route = route('trackOrderTimeExcel')."?franchise=". $franchiseId ;
                                $setPath = 'track-time?franchise='. $franchiseId;
                                ?>
                            @elseif(!empty($fromDate))
                                <?php $route = route('trackOrderTimeExcel')."?form_date=". $fromDate ;
                                $setPath = 'track-time?form_date='. $fromDate;
                                ?>
                            @elseif(!empty($toDate))
                                <?php $route = route('trackOrderTimeExcel')."?to_date=". $toDate ;
                                $setPath = 'track-time?to_date='. $toDate;
                                ?>
                            @else
                                <?php $route = route('trackOrderTimeExcel') ;
                                $setPath = 'track-time';
                                ?>
                            @endif
                            @if(!empty($franchiseId) && !empty($fromDate) && !empty($toDate))
                                @if (superAdmin())
                                    <?php $route = route('trackOrderTimeExcel')."?franchise=". $franchiseId."&form_date=". $fromDate."&to_date=".$toDate ;
                                    $setPath = 'track-time?franchise='. $franchiseId."&form_date=". $fromDate."&to_date=".$toDate
                                    ?>
                                @else
                                    <?php $route = route('trackOrderTimeExcel')."?form_date=". $fromDate."&to_date=".$toDate ;
                                    $setPath = 'track-time?form_date='. $fromDate.'&to_date='.$toDate
                                    ?>
                                @endif
                            @endif
                            <a href="{{$route}}"
                               class="col-md-offset-10 btn btn-primary">
                               Export
                            </a>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <table class="table table-responsive panel-body">
                        <tbody>
                        <tr>
                            <th>{{ Lang::get('views.order_number') }}</th>
                            <th>{{ Lang::get('views.order_taken_by') }}</th>
                            <th>{{ Lang::get('views.created_at') }}</th>
                            <th>{{ Lang::get('views.delivered_at') }}</th>
                            <th>{{ Lang::get('views.time_taken') }}</th>
                        </tr>
                        </tr>
                        @if(empty($orders->toArray()['data']))
                            <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @else
                            @foreach($orders as $order)
                                <?php
                                $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order['created_at'], 'UTC')
                                       ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                                $deliveredAt = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order['delivered_at'], 'UTC')
                                 ->setTimezone('Asia/Kolkata')->format('j-n-Y h:i:s A');
                                ?>
                                <tr>
                                    <td>
                                        {{$order['order_number']}}
                                    </td>
                                    <td>
                                        {{$order['order_taken']}}
                                    </td>
                                    <td>
                                        {{$date}}
                                    </td>
                                    <td>
                                        {{$deliveredAt}}
                                    </td>
                                    <td>
                                        {{$order['time']}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="row col-md-12">
                        <div class="pull-right">
                            {!!  $orders->setPath($setPath)->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
