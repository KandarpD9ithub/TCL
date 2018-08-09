@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.products') }}
                            <a href="{{ route('special-product.create') }}"
                               class="col-md-offset-10 btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive panel-body">
                        <tbody>
                        <tr>
                            <th>{{ Lang::get('views.name') }}</th>
                            <th>{{ Lang::get('views.price') }}</th>
                            <th>{{ Lang::get('views.action') }}</th>

                        </tr>
                        @if(empty($productsPrice))
                            <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @else
                            @foreach($productsPrice as $key => $value)
                                <tr>
                                    <td>
                                        {{$value['name']}}
                                    </td>
                                    <td>
                                        {{$value['price']}}
                                    </td>
                                    <td>
                                        {!! deleteForm('/special-product/'. $value['id'] ) !!}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
