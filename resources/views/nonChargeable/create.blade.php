@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.non-chargeable') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::open(['route' => 'non-chargeable.store', 'method' => 'post', 'id' => 'create']) }}
                        <div class="row">
                            <div class ="col-md-12">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/non-chargeable', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    {!! JsValidator::formRequest('App\Http\Requests\NonChargeableRequset', '#create') !!}
@endsection