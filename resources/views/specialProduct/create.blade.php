@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.special-product') }}</h3>
                    </div>
                    <div class="panel-body">
                            {{ Form::open(['route' => 'special-product.store', 'method' => 'post']) }}
                        <div class="row">
                            <div class ="col-md-12">
                                <div class="col-md-6 select-product _margin_">
                                    {{ Form::select('product[]', $products, null, ['multiple'=>'multiple']) }}
                                </div>
                            </div>
                        </div>
                            <div class="form-group">
                                <div class="pull-right">
                                    {{ Html::link('/special-product', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    @include('layouts.scripts.menu')
@endsection