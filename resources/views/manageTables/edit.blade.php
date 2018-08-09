@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.manage-tables') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($manageTablesData,['route' => ['manage-tables.update', $manageTablesData->id], 'method' => 'put', 'id' => 'update']) }}
                        <div class="row">
                            <div class ="col-md-12">
                                <div class="col-md-2">
                                    {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                                </div>
                                <div class="form-group col-md-2">
                                    @if($manageTablesData->is_active == 1)
                                        {{ Html::link(URL::to('manage-tables/activeInactive',$manageTablesData->id), Lang::get('views.inactive_btn'),['onclick'=>'return confirm("Are you sure you want to InActive?")','class' => 'btn btn-danger']) }}
                                    @else
                                        {{ Html::link(URL::to('manage-tables/activeInactive',$manageTablesData->id), Lang::get('views.active_btn'),['onclick'=>'return confirm("Are you sure you want to Active?")','class' => 'btn btn-success']) }}

                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/manage-tables', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                                {{ Form::submit(Lang::get('views.submit'), ['class' => 'btn btn-primary']) }}
                            </div>
                        </div>

                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection