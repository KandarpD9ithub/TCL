@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('Update Tax profile') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($tax, ['route' => ['taxes.update', 'id' => $tax->id], 'method' => 'put', 'id' => 'edit-tax']) }}
                        {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group">
                                    {{ Form::label('is_active', Lang::get('views.status'), ['class' => "control-label col-md-1"]) }}
                                    <div class="col-md-3">
                                        {{ Form::checkbox('is_active', $tax->is_active, $tax->is_active ? true : false) }}
                                        Active
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group{{ $errors->has('tax_name') ? ' has-error' : '' }} col-md-6">
                                    {{ Form::label('tax_name', Lang::get('views.tax_name'), ['class' => "control-label"]) }}
                                    {{ Form::text('tax_name', $tax->tax_name, ['class' => 'form-control']) }}
                                    @if ($errors->has('tax_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tax_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('tax_type') ? ' has-error' : '' }} col-md-6">
                                    {{ Form::label('tax_type', Lang::get('views.tax_type'),
                                    ['class' => "control-label"]) }}
                                    {{ Form::select('tax_type', \Config::get('constants.TAX_TYPE'), $tax->tax_type,
                                    ['class' => 'form-control']) }}
                                    @if ($errors->has('tax_type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tax_type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('tax_rate') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('tax_rate', Lang::get('views.tax_rate'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('tax_rate', $tax->tax_rate, ['class' => 'form-control keyFloat']) }}
                                @if ($errors->has('tax_rate'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('tax_rate') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{ Form::label('tax_description', Lang::get('views.tax_description'), ['class' => "control-label"]) }}
                                    {{ Form::textarea('tax_description', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="pull-right">
                                    {{ Html::link('/taxes', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                                    {{ Form::submit(Lang::get('views.update'), ['class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\TaxRequest', '#edit-tax') !!}
@endsection


