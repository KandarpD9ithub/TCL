@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ Lang::get('views.edit_price') }}</h3>
                </div>
                <div class="panel-body">
                    {{ Form::model($price, ['route' => ['product-price.update', 'id' => $price->id],
                    'method' => 'put', 'id' => 'edit-product-price']) }}
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group selected-value col-md-6">
                            {{ Form::label('name', Lang::get('views.product_name'), ['class' => "control-label"]) }}
                            {{ Form::text('name', $products[$price->product_id], ['class' => 'form-control', 'readOnly' => true]) }}
                        </div>
                        <div class="form-group selected-value col-md-6">
                            {{ Form::label('price', Lang::get('views.price'), ['class' => "control-label"]) }}
                            {{ Form::text('price', $price->price, ['class' => 'form-control keyFloat']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            {{ Html::link('/product-price', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    {!! JsValidator::formRequest('App\Http\Requests\ProductPriceRequest', '#edit-product-price') !!}
@endsection