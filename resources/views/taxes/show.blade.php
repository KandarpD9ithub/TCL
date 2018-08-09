
@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 ">
                <div class="panel panel-default ">
                    <div class="panel-heading">
                        <h3 class="panel-title ">
                            {{ Lang::get('views.tax_detail') }}
                              <p class="pull-right align">
                                  <a class="  btn btn-primary btn-sm " href="{{ route('taxes.edit', $tax->id) }} ">
                                      {{ Lang::get('views.edit') }}</a>
                            <a class="  btn btn-danger btn-sm " href="{{ route('taxes.index') }}">
                                {{ Lang::get('views.cancel') }}
                            </a>
                               </p>
                        </h3>
                    </div>

                    <div class="panel-body ">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong> {{ Lang::get('views.status') }}:
                                </strong>

                                Active
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong> {{ Lang::get('views.tax_profile') }}:
                                </strong>

                                {{ $tax->tax_name}}
                            </div>
                            <div class="form-group col-md-6">

                                <strong>{{ Lang::get('views.tax_rate') }}:</strong>

                                {{ $tax->tax_rate}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <strong>{{ Lang::get('views.tax_description') }}:</strong>

                                {{ $tax->tax_description}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection