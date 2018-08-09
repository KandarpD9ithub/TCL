@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ Lang::get('views.edit_category') }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ Form::model($category, ['route' => ['category.update', 'id' => $category->id], 'method' => 'put', 'id' => 'edit-category']) }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group">
                                {{ Form::label('is_active', Lang::get('views.status'), ['class' => "control-label col-md-1"]) }}
                                <div class="col-md-3">
                                    {{ Form::checkbox('is_active', $category->is_active, $category->is_active ? true : false) }}
                                    Active
                                </div>
                            </div>
                        </div>
                        <div class="row selected-value">
                            <div class="form-group col-md-6">
                                {{ Form::label('parent_id', Lang::get('views.parent'), ['class' => "control-label"]) }}
                                {{ Form::select('parent_id',['0'=>'Choose Parent Category']+$parentCategory, $category->parent_id, ['class' => 'form-control' ]) }}
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                {{ Form::label('name', Lang::get('views.name'), ['class' => "control-label"]) }}
                                {{ Form::text('name', $category->name, ['class' => 'form-control']) }}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('tax_id', Lang::get('views.tax_profile'), ['class' => "control-label"]) }}
                                {{ Form::select('tax_id',['' =>  Lang::get('views.tax_profile')]+$taxes,  null, ['class' => 'form-control']) }}
                            </div>


                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                {{ Html::link('/category', Lang::get('views.cancel'),['class' => 'btn btn-danger']) }}
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
    {!! JsValidator::formRequest('App\Http\Requests\CategoryRequest', '#edit-category') !!}
@endsection