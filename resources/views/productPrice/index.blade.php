@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.product_prices') }}
                            <a href="{{ route('product-price.create') }}"
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
                            @if(empty($data))
                                <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @else
                                @foreach($data as $key => $value)
                                <tr>
                                    <td>
                                        {{$product[$value['product_id']]}}
                                    </td>
                                    <td>
                                        {{$value['price']}}
                                    </td>
                                    <td>
                                        <a href="{{ route('product-price.edit', ['id' => $value['id']]) }}"
                                           class="btn btn-primary btn-sm">{{ Lang::get('views.edit') }}
                                        </a>
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
