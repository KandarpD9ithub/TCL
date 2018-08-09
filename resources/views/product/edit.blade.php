@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ Lang::get('views.edit_product') }}</h3>
                </div>
                <div class="panel-body">
                    {{ Form::model($product, ['route' => ['product.update', 'id' => $product->id], 'method' => 'put',
                    'id' => 'edit-product', 'files' => true, 'enctype'=>'multipart/form-data']) }}
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group">
                            {{ Form::label('is_active', Lang::get('views.status'), ['class' => "control-label col-md-1"]) }}
                            <div class="col-md-3">
                                {{ Form::checkbox('is_active', $product->is_active, $product->is_active ? true : false) }}
                                Active
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                            {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                            {{ Form::text('name', $product->name, ['class' => 'form-control']) }}
                            @if ($errors->has('name'))
                                <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }} col-md-6">
                            {{ Form::label('price', Lang::get('views.price'), ['class' => "control-label"]) }}
                            {{ Form::text('price', $product->price, ['class' => 'form-control keyFloat']) }}
                            @if ($errors->has('price'))
                                <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    {{--<div class="row">--}}
                        {{--<div class="form-group col-md-6">--}}
                            {{--{{ Form::label('effective_from', Lang::get('views.from_date'), ['class' => "control-label"]) }}--}}
                            {{--{{ Form::text('effective_from',--}}
                            {{--isset($product->effective_from) ? date('d-M-y', strtotime($product->effective_from)): null,--}}
                            {{--['class' => 'form-control from_date']) }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('product_code', Lang::get('views.sku'), ['class' => "control-label"]) }}
                            {{ Form::text('product_code', $product->product_code, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('tax_id', Lang::get('views.tax_profile'), ['class' => "control-label"]) }}
                            {{ Form::select('tax_id',[Lang::get('views.tax_profile')]+$taxes, $product->tax_id, ['class' => 'form-control']) }}
                            <span>(If left blank category/subcategory tax profile will be applied)</span>
                        </div>
                    </div>
                        <div class="row">
                        <!-- to add products images -->
                        <div class="form-group col-md-8">
                            {{ Form::label('image', Lang::get('views.file_name'), ['class' => "control-label"]) }}
                            <div>
                            @if (!empty($productsPhoto))
                                <?php $imageUrl = url('upload').'/'.$productsPhoto->file_name;?>
                                <img src="{{ $imageUrl }}" height="100" width="100">
                            @endif
                            </div>
                            <div class="margintop8">
                            <input type="file" name="image"/>
                            </div>
                            
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
                                    {{ Form::checkbox('tag_id[]',$key, in_array($key, $productTags), ['class'=>'ads_Checkbox'])}}
                                    {{ $value }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            {{ Html::link('/product', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    @include('layouts.scripts.discount')
    {!! JsValidator::formRequest('App\Http\Requests\ProductRequest', '#edit-product') !!}
@endsection