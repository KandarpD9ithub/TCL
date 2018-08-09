@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ Lang::get('views.create_price') }}</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['route' => 'product-price.store', 'method' => 'post', 'id' => 'create-product-price']) }}
                    {{ csrf_field() }}
                        <div class="form-group addMoreCollectionElementContainer">
                            <div class="col-md-6">
                                {{ Form::label('product_id[0]', Lang::get('views.product_name'), ['class' => "control-label"]) }}
                            </div>
                            <div class="col-md-6">
                                {{ Form::label('price[0]', Lang::get('views.price'), ['class' => "control-label"]) }}
                            </div>
                            <?php
                            $element = '
                                <div class="copy">
                                <div class="col-md-6 _margin_ product">
                                '.Form::select('product_price[0][product_id]', $products, null,
                                            ['class' => 'form-control category products', 'unique' => 'products']).'
                                '.Form::hidden('productCount', count($products), ['id' => 'productCount'] ).'
                                </div>

                                <div class="col-md-5 _margin_">
                                    <input type="text" name="product_price[0][price]" class="form-control keyFloat price">
                                 </div>
                                </div>';
                            $template = str_replace(['_margin_','[0]'], ['margintop8','[_index_]'], $element);
                            echo $element;
                            ?>
                            <div class="row addMore">
                                <div class="col-sm-12 margintop8">
                                    <a href="javascript:void(0);" class="addCollectionElement  colorGreen">
                                        <span class="glyphicon glyphicon-plus-sign"></span> {{ Lang::get('views.update_more') }}	</a>
                                </div>
                            </div>
                            <span class="hide template" data-template='<?php echo $template; ?>'></span>
                        </div>
                    <div class="form-group">
                        <div class="pull-right margintop8">
                            {{ Html::link('/product-price', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    @include('layouts.scripts.validations')
@endsection