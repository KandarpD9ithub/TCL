@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ Lang::get('views.create_product') }}</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['route' => 'product.store', 'method' => 'post', 'id' => 'product',
                    'files' => true, 'enctype'=>'multipart/form-data']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', null, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('price', Lang::get('views.price'), ['class' => "control-label"]) }}
                                {{ Form::text('price', null, ['class' => 'form-control keyFloat']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('product_code', Lang::get('views.sku'), ['class' => "control-label"]) }}
                                {{ Form::text('product_code', null, ['class' => 'form-control']) }}
                            </div>
                           {{-- <div class="form-group col-md-6 ">
                                {{ Form::label('parent_id', Lang::get('views.parent'), ['class' => "control-label"]) }}
                                {{ Form::select('parent_id',[Lang::get('views.parent')]+['same_as_category' => 'Category', 'other'=>'Other'] ,null, ['class' => 'form-control other ']) }}
                            </div>--}}
                            <div class="form-group col-md-6">
                                {{ Form::label('tax_id', Lang::get('views.tax_profile'), ['class' => "control-label"]) }}
                                {{ Form::select('tax_id',[Lang::get('views.tax_profile')]+$taxes, null, ['class' => 'form-control']) }}
                                <span>(If left blank category/subcategory tax profile will be applied)</span>
                            </div>
                         </div>
                         <div class="row">
                            <!-- to add products images -->
                            <div class="form-group col-md-8">
                                {{ Form::label('image', Lang::get('views.file_name'), ['class' => "control-label"]) }}
                                <input type="file" name="image"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                {{ Form::label('description', Lang::get('views.description'), ['class' => "control-label"]) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                            <label class="control-label">Product Tags</label>
                            </div>
                            <div class="form-group" id="save_value">
                                @foreach($tags as $key => $value)
                                    <label for="tag_id" class="control-label col-md-3">
                                        {{ Form::checkbox('tag_id[]',$key,  null, ['class'=>'ads_Checkbox'])}}
                                        {{ $value }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/product', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    {!! JsValidator::formRequest('App\Http\Requests\ProductRequest', '#product') !!}
@endsection