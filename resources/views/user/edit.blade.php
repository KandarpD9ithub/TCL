@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.edit_employee') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($user, ['route' => ['employee.update', 'id' => $user->id], 'method' => 'put', 'id' =>'edit-user']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('franchise_id', Lang::get('views.franchise'), ['class' => "control-label"]) }}
                                {{ Form::select('franchise_id', $franchise, $user->employee->franchise_id, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('role_name', Lang::get('views.role_name'), ['class' => "control-label"]) }}
                                {{ Form::select('role_name', \Config::get('constants.ROLE_NAME'), $user->role_name, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-12">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', $user->name, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('email', Lang::get('views.email'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('email', $user->email, ['class' => 'form-control']) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('mobile', Lang::get('views.mobile'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('mobile', $user->mobile, ['class' => 'form-control']) }}
                                @if ($errors->has('mobile'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('mobile') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('address_line_one') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_one'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_one', $user->employee->address_line_one, ['class' => 'form-control']) }}
                                @if ($errors->has('address_line_one'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_one') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('address_line_two') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_two'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('address_line_two', $user->employee->address_line_two, ['class' => 'form-control']) }}
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
                                {{ Form::text('city', $user->employee->city, ['class' => 'form-control']) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('region', Lang::get('views.region'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('region', $user->employee->region, ['class' => 'form-control']) }}
                                @if ($errors->has('region'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('region') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('country_id', Lang::get('views.country'),
                                ['class' => "control-label"]) }}
                                {{ Form::select('country_id', $countries, $user->employee->country_id, ['class' => 'form-control']) }}
                                @if ($errors->has('country_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('country_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/employee', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    {!! JsValidator::formRequest('App\Http\Requests\UserRequest', '#edit-user') !!}
@endsection