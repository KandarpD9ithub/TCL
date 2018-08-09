@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Category wise Sale
                                <?php $default = [''=>'Please Select'];  
                                $franchise_result = $default + $franchise_result->toArray();?>
                                {{ Form::select('franchise' ,$franchise_result, null, ['class' => 'col-md-offset-2','id'=>'franchise'])}}
                                {{ Form::select('filterby', [
                                    '' => 'Select Filter',
                                   'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'], 'null', ['id'=>'filterby']) 
                                }}
                                <input type="submit" class="btn btn-primary btn-sm" id="export" value="{{ Lang::get('views.export-btn') }}">                                       
                        </h3>                            
                    </div>
                    <table class="table table-responsive panel-body">
                    <thead><tr> <th>Category</th> <th>Total Products</th> <th>Total Quantity</th><th>Gross Sale</th> </tr></thead>
                        <tbody id="cat">                            
                            @if(count($category_result)>0)
                                @foreach($category_result as $value)
                                    <tr><td> {{$value->name}} </td> <td> {{$value->cnt}} </td> <td>{{$value->quantity}} </td><td>N/A</td> </tr>
                                @endforeach                                
                            @else
                            <tr><td>N/A</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>
                            @endif
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
    </div>
@endsection