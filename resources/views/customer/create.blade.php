@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.customers') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::open(['route' => 'customer.store', 'method' => 'post', 'id' => 'create-customer','files'=>true]) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', null, ['class' => 'form-control','placeholder'=>Lang::get('views.name')]) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('contact_number') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('contact_number', Lang::get('views.contact_number'), ['class' => "control-label"]) }}
                                {{ Form::text('contact_number', null, ['class' => 'form-control keyNumSingle','placeholder'=>Lang::get('views.contact_number')]) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('contact_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('email', Lang::get('views.email'), ['class' => "control-label"]) }}
                                {{ Form::email('email', null, ['class' => 'form-control','placeholder'=>Lang::get('views.email')]) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif                                
                            </div>
                            <div class="form-group{{ $errors->has('profile_picture') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('profile_picture', Lang::get('views.profile_picture'), ['class' => "control-label"]) }}
                                {{ Form::file('profile_picture', ['class' => 'form-control','placeholder'=>Lang::get('views.profile_picture')]) }}
                                @if ($errors->has('profile_picture'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('profile_picture') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group{{ $errors->has('address_line_one') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_one', Lang::get('views.address_line_one'), ['class' => "control-label"]) }}
                                {{ Form::text('address_line_one', null, ['class' => 'form-control','placeholder'=>Lang::get('views.address_line_one')]) }}
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address_line_one') }}</strong>
                                </span>
                                @endif                                
                            </div>
                            <div class="form-group{{ $errors->has('address_line_two') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('address_line_two', Lang::get('views.address_line_two'), ['class' => "control-label"]) }}
                                {{ Form::text('address_line_two', null, ['class' => 'form-control','placeholder'=>Lang::get('views.address_line_two')]) }}
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
                                {{ Form::text('city', null, ['class' => 'form-control','placeholder'=>Lang::get('views.city')]) }}
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('region', Lang::get('views.region'),
                                ['class' => "control-label"]) }}
                                {{ Form::text('region', null, ['class' => 'form-control','placeholder'=>Lang::get('views.region')]) }}
                                @if ($errors->has('region'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('region') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }} col-md-4">
                                {{ Form::label('country_id', Lang::get('views.country'),
                                ['class' => "control-label"]) }}
                                {{ Form::select('country_id', $countries, null, ['class' => 'form-control','placeholder'=>Lang::get('views.select_country')]) }}
                                @if ($errors->has('country_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('country_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/customer', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                                {{ Form::submit(Lang::get('views.submit'), ['class' => 'btn btn-primary']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div> <!-- End class panel-body -->
                </div> <!-- End class panel panel-default -->
            </div> <!-- End class col-md-8 col-md-offset-2 -->
        </div> <!-- End class row -->
    </div> <!-- End class container -->
@endsection
@section('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\CategoryRequest', '#create-category') !!}
@endsection