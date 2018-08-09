@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.products') }}
                            <a href="{{ route('product.create') }}"
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
                                <th>{{ Lang::get('views.sku') }}</th>

                                <th>{{ Lang::get('views.status') }}</th>
                                <th>{{ Lang::get('views.action') }}</th>
                            </tr>
                            @if(count($products) > 0)
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            {{ ucfirst($product->name) }}
                                        </td>
                                        <td>
                                            {{ $product->price }}
                                        </td>
                                        <td>
                                            {{ $product->product_code }}
                                        </td>
                                        <td>
                                            @if($product->is_active == 1)
                                                <?php $label = 'Active';
                                                      $class = 'btn-success ';?>
                                            @else
                                                <?php $label = 'In-active';
                                                $class = 'btn-warning';?>
                                            @endif
                                            <span class="btn {{ $class }} btn-sm">
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('product.edit', ['id' => $product->id]) }}"
                                               class="btn btn-default btn-sm">{{ Lang::get('views.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection