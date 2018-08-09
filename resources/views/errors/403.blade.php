@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1 class="text-center">403</h1>
                        <div class="text-center">
                            <h4><i class="glyphicon glyphicon-warning-sign"></i>
                                {{ Lang::get('errors.forbidden_error') }}</h4>
                            <p>
                                {{ Lang::get('errors.403_line1') }}
                            </p>
                            <p>
                                {{ Lang::get('errors.403_line2') }}
                                <a href="{{ URL::to('/') }}">
                                    {{ Lang::get('errors.home_page') }}
                                </a>?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection