@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.change_password') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::open(['route' => 'change-password.store', 'method' => 'post', 'id' => 'change-password']) }}
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('old_password', Lang::get('views.old_password'), ['class' => 'control-label']) }}
                                {{ Form::password('old_password', ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('new_password', Lang::get('views.new_password'), ['class' => 'control-label']) }}
                                {{ Form::password('new_password', ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                {{ Form::label('confirm_password', Lang::get('views.confirm_password'), ['class' => 'control-label']) }}
                                {{ Form::password('confirm_password', ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/home', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
        {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest', '#change-password') !!}
    @endsection
