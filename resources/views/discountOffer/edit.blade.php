@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.edit_rule') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($rule, ['route' => ['rules.update', 'id' => $rule->id], 'method' => 'put', 'id' => 'edit-discount']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group">
                                {{ Form::label('is_active', Lang::get('views.status'), ['class' => "control-label col-md-1"]) }}
                                <div class="col-md-3">
                                    {{ Form::checkbox('is_active', $rule->is_active, $rule->is_active ? true : false) }}
                                        Active
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('rule_type', Lang::get('views.rule_type'), ['class' => "control-label"]) }}
                                {{ Form::select('rule_type', \Config::get('constants.RULE_TYPE'), $rule->rule_type,
                                ['class' => 'form-control', 'id' => 'rule_type']) }}
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', $rule->name, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('from_date', Lang::get('views.from_date'), ['class' => "control-label"]) }}
                                {{ Form::text('from_date', date('d-M-y', strtotime($rule->from_date)), ['class' => 'form-control from_date']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('to_date', Lang::get('views.to_date'), ['class' => "control-label"]) }}
                                {{ Form::text('to_date', date('d-M-y', strtotime($rule->to_date)), ['class' => 'form-control to_date']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                {{ Form::label('description', Lang::get('views.description'), ['class' => "control-label"]) }}
                                {{ Form::textarea('description', $rule->description, ['class' => 'form-control', 'rows' => '6']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('amount_type', Lang::get('views.amount_type'), ['class' => "control-label"]) }}
                                @if($rule->rule_type == 'discount')
                                    {{ Form::select('amount_type_offer',\Config::get('constants.OFFER_AMOUNT_TYPE'), $rule->amount_type,
                                    ['class' => 'form-control offer hide']) }}
                                {{ Form::select('amount_type',\Config::get('constants.AMOUNT_TYPE'), $rule->amount_type,
                                    ['class' => 'form-control discount']) }}
                                @endif
                                @if($rule->rule_type == 'offer')
                                    {{ Form::select('amount_type_offer',\Config::get('constants.OFFER_AMOUNT_TYPE'), $rule->amount_type,
                                    ['class' => 'form-control offer']) }}
                                {{ Form::select('amount_type',\Config::get('constants.AMOUNT_TYPE'), $rule->amount_type,
                                    ['class' => 'form-control discount hide']) }}
                                @endif
                            </div>
                            @if($rule->rule_type == 'discount')
                            <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} col-md-6 amount">
                                {{ Form::label('amount', Lang::get('views.amount'), ['class' => "control-label"]) }}
                                {{ Form::text('amount', $rule->amount, ['class' => 'form-control keyFloat']) }}
                                @if ($errors->has('amount'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('amount') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} col-md-3 hide offer-amount">
                                {{ Form::label('amount', Lang::get('views.amount'), ['class' => "control-label"]) }}
                                {{ Form::text('amount_offer',null, ['class' => 'form-control keyFloat']) }}
                                @if ($errors->has('amount'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('amount') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('discount_qty_step') ? ' has-error' : '' }} col-md-3 hide amount-qty">
                                {{ Form::label('discount_qty_step', Lang::get('views.discount_qty_step'), ['class' => "control-label"]) }}
                                {{ Form::text('discount_qty_step', null, ['class' => 'form-control keyFloat']) }}
                                @if ($errors->has('discount_qty_step'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('discount_qty_step') }}</strong>
                                </span>
                                @endif
                            </div>
                            @else
                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} col-md-6 hide amount">
                                    {{ Form::label('amount', Lang::get('views.amount'), ['class' => "control-label"]) }}
                                    {{ Form::text('amount', null, ['class' => 'form-control keyFloat']) }}
                                    @if ($errors->has('amount'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('amount') }}</strong>
                                </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} col-md-3 offer-amount">
                                    {{ Form::label('amount', Lang::get('views.amount'), ['class' => "control-label"]) }}
                                    {{ Form::text('amount_offer', round($rule->amount), ['class' => 'form-control keyFloat']) }}
                                    @if ($errors->has('amount'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('amount') }}</strong>
                                </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('discount_qty_step') ? ' has-error' : '' }} col-md-3 amount-qty">
                                    {{ Form::label('discount_qty_step', Lang::get('views.discount_qty_step'), ['class' => "control-label"]) }}
                                    {{ Form::text('discount_qty_step', $rule->discount_qty_step, ['class' => 'form-control keyFloat']) }}
                                    @if ($errors->has('discount_qty_step'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('discount_qty_step') }}</strong>
                                </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('conditions', Lang::get('views.conditions'), ['class' => "control-label"]) }}
                                {{ Form::select('conditions[type]',\Config::get('constants.RULE_ON'), $condition['type'],
                                ['class' => 'form-control', 'id' => 'rule_on']) }}
                            </div>
                            @if($condition['type'] == 'products')
                                <div class="form-group col-md-6 products rule-on">
                                    <select name="conditions[ids][]" multiple>
                                        @foreach ($products as $key => $value)
                                            <option value="{{$key }}" {{ (in_array($key ,  $condition['ids'])) ? ' selected="selected"' : '' }}>{{$value}}</option>
                                        @endforeach
                                    </select>    
                                </div>
                            @else
                                <div class="form-group col-md-6 products hide rule-on">
                                    {{ Form::select('conditions[ids][]', $products, null, [ 'multiple'=>'multiple']) }}
                                </div>
                            @endif
                            @if($condition['type'] == 'categories')
                            <div class="form-group col-md-6 categories rule-on">
                                <select name="conditions[ids][]" multiple>
                                    @foreach ($parentCategory as $key => $value)
                                        <option value="{{$key }}" {{ (in_array($key ,  $condition['ids'])) ? ' selected="selected"' : '' }}>{{$value}}</option>
                                    @endforeach
                                </select>  

                            </div>
                            @else
                                <div class="form-group col-md-6 categories hide rule-on">
                                    {{ Form::select('conditions[ids][]', $parentCategory, null, ['multiple'=>'multiple']) }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/rules', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    @include('layouts.scripts.discount')
    {!! JsValidator::formRequest('App\Http\Requests\DiscountOfferRuleRequest', '#edit-discount') !!}
@endsection
