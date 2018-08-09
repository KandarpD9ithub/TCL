@extends('layouts.app')

@section('content')
    <div class="container">

        <!-- Top sale items begin -->

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Top Sale Items        
                                <?php $default = [''=>'Please Select'];
                                $last = [count($sales['franchise'])+1 => 'Select date'];                                
                                $sales['franchise'] = $default + $sales['franchise']->toArray();
                                ?>                  
                                {{ Form::select('franchise' ,$sales['franchise'], null, ['class' => 'col-md-offset-3','id'=>'franchise_top'])}}
                                {{ Form::select('filterby', [
                                    '' => 'Select Filter',
                                   'daily' => 'Daily',
                                   'weekly' => 'Weekly',
                                   'monthly' => 'Monthly'], 'null', ['id'=>'filterby_top']) 
                                }}
                                <input type="submit" class="btn btn-primary btn-sm" id="export_top_sales" value="{{ Lang::get('views.export-btn') }}">                                       
                        </h3>  
                    </div>
                    <table class="table table-responsive panel-body">
                        <thead>
                                <th>Item Name</th>
                                <th>{{ Lang::get('views.sku') }}</th>
                                <th>Orders</th>                                                                
                                <th>Order Quantity</th>                                                                
                                <th>Gross Sale</th>                                                                
                            </thead>
                        <tbody id="topsales">                            
                            @if(count($sales['top_sales']) > 0)
                                @foreach($sales['top_sales'] as $result)
                                    <tr><td>{{ $result[0] }}</td><td>{{ $result[1] }}</td><td>{{ $result[2] }}</td><td>{{ $result[3] }}</td><td>{{ $result[4] }}</td></tr>
                                @endforeach                                                              
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top sale end -->

        <!-- Low sale items begin -->

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Low Sale Items                                
                                {{$franchise_result['0']=""}}
                                {{ Form::select('franchise' ,$sales['franchise'], null, ['class' => 'col-md-offset-3','id'=>'franchise_low'])}}
                                {{ Form::select('filterby', [
                                    '' => 'Select Filter',
                                   'daily' => 'Daily',
                                   'weekly' => 'Weekly',
                                   'monthly' => 'Monthly'], 'null', ['id'=>'filterby_low']) 
                                }}
                                <input type="submit" class="btn btn-primary btn-sm" id="export_low_sales" value="{{ Lang::get('views.export-btn') }}">
                                
                        </h3>  
                    </div>
                    <table class="table table-responsive panel-body">
                        <thead>
                            <th>Item Name</th>
                            <th>{{ Lang::get('views.sku') }}</th>
                            <th>Orders</th>                                                                
                            <th>Order Quantity</th>  
                            <th>Gross Sale</th>                                                                                                                              
                        </thead>
                        <tbody id="lowsales">                            
                            @if(count($sales['low_sales']) > 0)                                   
                                @for($i=count($sales['low_sales'])-1;$i>=0;$i--)               
                                    <tr><td>{{ $sales['low_sales'][$i][0] }}</td><td>{{ $sales['low_sales'][$i][1] }}</td><td>{{ $sales['low_sales'][$i][2] }}</td><td>{{ $sales['low_sales'][$i][3] }}</td><td>{{ $result[4] }}</td></tr>
                                @endfor                                                              
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Low sale end -->
    </div>
@endsection