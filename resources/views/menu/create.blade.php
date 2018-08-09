@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.menu') }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        @if(!empty($parentCategory))
                        {{ Form::open(['route' => 'menu.store', 'method' => 'post']) }}
                            <div class="row form-group addMoreCollectionElementContainer">
                                <div class ="col-md-6">
                                   {{ Form::label('menu[][category_id]', Lang::get('views.categories'),['class' => 'control-label'] ) }}
                                </div>
                                <div class ="col-md-6">
                                    {{ Form::label('menu[][product_id]', Lang::get('views.products'),['class' => 'control-label'] ) }}
                                </div>
                                <?php
                                $AddElement='<div class="copy menu product">
                                <div class ="col-md-6 select-category _margin_ product">
                                '.Form::select('menu[0][category_id]', $parentCategory, null, ['class' =>'category', 'id' => 'category']).'
                                '.Form::hidden('productCount', count($parentCategory), ['id' => 'productCount'] ).'
                                </div>
                                <div class ="col-md-5 select-product _margin_">
                                '.Form::select('menu[0][product_id][]', $product, null,
                                                ['multiple'=>'multiple', 'class' => 'products']).'
                                </div>
                            </div>';
                                $template = str_replace(['_margin_','[0]'], ['margintop8','[_index_]'], $AddElement);
                                echo $AddElement;
                                ?>
                                <div class="row addMore">
                                    <div class="col-sm-12 margintop8">
                                        <a href="javascript:void(0);" class="addCollectionElement colorGreen">
                                            <span class="glyphicon glyphicon-plus-sign"></span>
                                            {{ Lang::get('views.add_more') }}
                                        </a>
                                    </div>
                                </div>
                                <span class="hide template" data-template='<?php echo $template; ?>'></span>
                            </div>
                            <div class="form-group">
                                <div class="pull-right">
                                    {{ Html::link('/menu', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
                                    {{ Form::submit(Lang::get('views.submit'), ['class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('layouts.scripts.menu')
@endsection