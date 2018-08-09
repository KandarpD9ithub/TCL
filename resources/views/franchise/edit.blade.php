@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.edit_franchise') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($franchise, ['route' => ['franchise.update', 'id' => $franchise->id], 'method' => 'put', 'id' => 'edit-franchise']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', $franchise->name, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('gst_number') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('gst_number', Lang::get('views.gst_number'), ['class' => "control-label"]) }}
                                {{ Form::text('gst_number', $franchise->gst_number, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('address_line_one') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_one'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_one', $franchise->address_line_one, ['class' => 'form-control']) }}
                                @if ($errors->has('address_line_one'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_one') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_two'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_two', $franchise->address_line_two, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('city', Lang::get('views.city'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('city', $franchise->city, ['class' => 'form-control']) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('region', Lang::get('views.region'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('region', $franchise->region, ['class' => 'form-control']) }}
                                @if ($errors->has('region'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('region') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('country_id', Lang::get('views.country'),
                                ['class' => "control-label"]) }}
                                {{ Form::select('country_id', $countries, $franchise->country_id, ['class' => 'form-control']) }}
                                @if ($errors->has('country_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('country_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                       {{-- <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label" for="tax_name">
                                    {{ Lang::get('views.tax_name') }}
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label" for="tax_rate">
                                    {{ Lang::get('views.tax_rate') }}
                                </label>
                            </div>
                        </div>

                        <div class="row">--}}
                            {{--<div class="form-group col-md-6">
                                {{Form::text('tax[0][name]', \Config::get('constants.TAX_NAME.service_tax'),
                                        ['class' => "form-control", 'readonly'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <input type="text" name="tax[0][rate]" class="form-control keyFloat"
                                       value="{{ isset($taxRate[\Config::get('constants.TAX_NAME.service_tax')])?$taxRate[\Config::get('constants.TAX_NAME.service_tax')]: '' }}">
                            </div>
                            <div class="form-group col-sm-6">
                                {{Form::text('tax[1][name]', \Config::get('constants.TAX_NAME.service_charge'),
                                        ['class' => "form-control", 'readonly'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <input type="text" name="tax[1][rate]" class="form-control keyFloat"
                                       value="{{ isset($taxRate[\Config::get('constants.TAX_NAME.service_charge')])?$taxRate[\Config::get('constants.TAX_NAME.service_charge')]: '' }}">
                            </div>
                            <div class="form-group col-sm-6">
                                {{Form::text('tax[2][name]', \Config::get('constants.TAX_NAME.vat'),
                                        ['class' => "form-control", 'readonly'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <input type="text" name="tax[2][rate]" class="form-control keyFloat"
                                       value="{{ isset($taxRate[\Config::get('constants.TAX_NAME.vat')])?$taxRate[\Config::get('constants.TAX_NAME.vat')]: '' }}">
                            </div>--}}
                            {{--<div class="form-group col-md-6">
                                {{Form::text('tax[0][name]', \Config::get('constants.TAX_NAME.sgst'),
                                        ['class' => "form-control", 'readonly'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <input type="text" name="tax[0][rate]" class="form-control keyFloat"
                                       value="{{ isset($taxRate[\Config::get('constants.TAX_NAME.sgst')])?$taxRate[\Config::get('constants.TAX_NAME.sgst')]: '' }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                {{Form::text('tax[1][name]', \Config::get('constants.TAX_NAME.cgst'),
                                        ['class' => "form-control", 'readonly'])}}
                            </div>
                            <div class="form-group col-sm-6">
                                <input type="text" name="tax[1][rate]" class="form-control keyFloat"
                                       value="{{ isset($taxRate[\Config::get('constants.TAX_NAME.cgst')])?$taxRate[\Config::get('constants.TAX_NAME.cgst')]: '' }}" required>
                            </div>
                        </div>--}}
                    <div class="form-group">
                        <div class="pull-right">
                            {{ Html::link('/franchise', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                            {{ Form::submit(Lang::get('views.update'), ['class' => 'btn btn-primary']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\FranchiseRequest', '#edit-franchise') !!}
@endsection