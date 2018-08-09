@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.create_franchise') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::open(['route' => 'franchise.store', 'method' => 'post', 'id' => 'create-franchise']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', null, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('gst_number') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('gst_number', Lang::get('views.gst_number'), ['class' => "control-label"]) }}
                                {{ Form::text('gst_number', null, ['class' => 'form-control']) }}
                                @if ($errors->has('gst_number'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('gst_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('address_line_one') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_one'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_one', null, ['class' => 'form-control']) }}
                                @if ($errors->has('address_line_one'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_one') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('address_line_two') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_two'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_two', null, ['class' => 'form-control']) }}
                                @if ($errors->has('address_line_two'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_two') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('city', Lang::get('views.city'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('city', null, ['class' => 'form-control']) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('region', Lang::get('views.region'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('region', null, ['class' => 'form-control']) }}
                                @if ($errors->has('region'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('region') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('country_id', Lang::get('views.country'),
                                ['class' => "control-label"]) }}
                                {{ Form::select('country_id', $countries, null, ['class' => 'form-control']) }}
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
                        </div>--}}

                            {{--<div class="row">--}}
                                {{--<div class="form-group col-md-6">
                                    {{Form::text('tax[0][name]', \Config::get('constants.TAX_NAME.service_tax'),
                                            ['class' => "form-control", 'readonly'])}}
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" name="tax[0][rate]" class="form-control keyFloat" required>
                                </div>
                                <div class="form-group col-sm-6">
                                        {{Form::text('tax[1][name]', \Config::get('constants.TAX_NAME.service_charge'),
                                                ['class' => "form-control", 'readonly'])}}
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <input type="text" name="tax[1][rate]" class="form-control keyFloat" required>
                                    </div>
                                <div class="form-group col-sm-6">
                                        {{Form::text('tax[2][name]', \Config::get('constants.TAX_NAME.vat'),
                                                ['class' => "form-control", 'readonly'])}}
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" name="tax[2][rate]" class="form-control keyFloat" required>
                                </div>--}}
                               {{-- <div class="form-group col-md-6">
                                    {{Form::text('tax[0][name]', \Config::get('constants.TAX_NAME.sgst'),
                                            ['class' => "form-control", 'readonly'])}}
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" name="tax[0][rate]" class="form-control keyFloat" required>
                                </div>
                                <div class="form-group col-md-6">
                                    {{Form::text('tax[1][name]', \Config::get('constants.TAX_NAME.cgst'),
                                            ['class' => "form-control", 'readonly'])}}
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="text" name="tax[1][rate]" class="form-control keyFloat" required>
                                </div>
                            </div>--}}
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/franchise', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\FranchiseRequest', '#create-franchise') !!}
@endsection
