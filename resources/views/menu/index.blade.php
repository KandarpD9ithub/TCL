@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.menu') }}
                            @if(superAdmin())
                            <a href = "{{ route('menu.create') }}"
                               class="col-md-offset-10 btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                            @endif
                        </h3>
                    </div>
                    <div class="panel-body">
                        <ul class="col-md-12">
                            @if(empty($categories))
                                <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @else
                            <?php $franchiseProductPrice = $franchiseProductId =[]; ?>
                            @if(isset($productPrice))
                                @foreach($productPrice as $price)
                                    <?php $franchiseProductId[$price['product_id']] = $price['price'];
                                    $franchiseProductPrice[] = $price['product_id']
                                    ?>
                                @endforeach
                            @endif
                            @foreach($categories as $key => $category)
                            @if($category->is_active == 1)
                                <li>
                                    {{ $category->name }}
                                    @if (count($category->products) > 0)
                                    <ul>
                                        @foreach($category->products as $product)
                                            @if($product->is_active ==1)
                                                <ul>
                                                    @if(!superAdmin())
                                                        @if(!in_array($product->menu->id, $inactive) || storeManger())
                                                            <li class="product-name">{{ $product->name}}
                                                                <span class="pull-right">
                                                                    @if(in_array($product->id, $franchiseProductPrice))
                                                                        {{ $franchiseProductId[$product->id] }}
                                                                    @else
                                                                        {{ $product->price }}
                                                                    @endif
                                                                </span>
                                                                @if(superAdmin())
                                                                    {{ Form::open(['route' => ['menu.remove.product', $product->id],
                                                                    'method' => 'POST', 'class' => 'delete-icon hide']) }}
                                                                    <button class="colorRed"><i class="glyphicon glyphicon-minus-sign"></i></button>
                                                                    {{ Form::close() }}
                                                                @endif
                                                                @if(!superAdmin() && storeManger())
                                                                    {{ Form::open(['route' => ['menu.inactiveMenuItems', $product->id], 'method' => 'POST']) }}
                                                                    @if(in_array($product->menu->id, $inactive))
                                                                        <button class="btn btn-primary btn-xs">InActive</button>
                                                                    @else
                                                                        <button class="btn btn-primary btn-xs">Active</button>
                                                                    @endif
                                                                    {{ Form::close() }}
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @else
                                                        <li class="product-name">{{ $product->name}}
                                                            <span class="pull-right">
                                                                    @if(in_array($product->id, $franchiseProductPrice))
                                                                    {{ $franchiseProductId[$product->id] }}
                                                                @else
                                                                    {{ $product->price }}
                                                                @endif
                                                                </span>
                                                            @if(superAdmin())
                                                                {{ Form::open(['route' => ['menu.remove.product',
                                                                $product->id], 'method' => 'POST', 'class' => 'delete-icon hide']) }}
                                                                    <button class="colorRed"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                                                {{ Form::close() }}
                                                            @endif
                                                            @if(!superAdmin() && storeManger())
                                                                {{ Form::open(['route' => ['menu.inactiveMenuItems', $product->id], 'method' => 'POST']) }}
                                                                @if(in_array($product->menu->id, $inactive))
                                                                    <button class="btn btn-primary btn-xs">InActive</button>
                                                                @else
                                                                    <button class="btn btn-primary btn-xs">Active</button>
                                                                @endif
                                                                {{ Form::close() }}
                                                            @endif
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                    </ul>
                                    @endif
                                @if (count($category->child) > 0)
                                    <ul>
                                        @foreach($category->child as $key => $subcategory)
                                                @if($subcategory->is_active ==1)
                                                <li>
                                                    {{ $subcategory->name }}
                                                    @if (count($subcategory->products) > 0)
                                                        <ul>
                                                            @foreach($subcategory->products as $product)
                                                                @if($product->is_active ==1)
                                                                    @if(!superAdmin())
                                                                        @if(!in_array($product->menu->id, $inactive) || storeManger())
                                                                            <li class="product-name">{{ $product->name}}
                                                                                <span class="pull-right">
                                                                                    @if(in_array($product->id, $franchiseProductPrice))
                                                                                        {{ $franchiseProductId[$product->id] }}
                                                                                    @else
                                                                                        {{ $product->price }}
                                                                                    @endif
                                                                                </span>
                                                                                @if(superAdmin())
                                                                                    {{ Form::open(['route' =>
                                                                                    ['menu.remove.product', $product->id],
                                                                                    'method' => 'POST', 'class' => 'delete-icon hide']) }}
                                                                                    <button class="colorRed"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                                                                    {{ Form::close() }}
                                                                                @endif
                                                                                @if(!superAdmin() && storeManger())
                                                                                    {{ Form::open(['route' => ['menu.inactiveMenuItems', $product->id], 'method' => 'POST']) }}
                                                                                    @if(in_array($product->menu->id, $inactive))
                                                                                        <button class="btn btn-primary btn-xs">Inactive</button>
                                                                                    @else
                                                                                        <button class="btn btn-primary btn-xs">Active</button>
                                                                                    @endif
                                                                                    {{ Form::close() }}
                                                                                @endif
                                                                            </li>
                                                                        @endif
                                                                    @else
                                                                        <li class="product-name">{{ $product->name}}
                                                                            <span class="pull-right">
                                                                                    @if(in_array($product->id, $franchiseProductPrice))
                                                                                    {{ $franchiseProductId[$product->id] }}
                                                                                @else
                                                                                    {{ $product->price }}
                                                                                @endif
                                                                                </span>
                                                                            @if(superAdmin())
                                                                                {{ Form::open(['route' => ['menu.remove.product', $product->id],
                                                                                'method' => 'POST', 'class' => 'delete-icon hide']) }}
                                                                                <button class="colorRed"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                                                                {{ Form::close() }}
                                                                            @endif
                                                                            @if(!superAdmin() && storeManger())
                                                                                {{ Form::open(['route' => ['menu.inactiveMenuItems', $product->id], 'method' => 'POST']) }}
                                                                                @if(in_array($product->menu->id, $inactive))
                                                                                    <button class="btn btn-primary btn-xs">Inactive</button>
                                                                                @else
                                                                                    <button class="btn btn-primary btn-xs">Active</button>
                                                                                @endif
                                                                                {{ Form::close() }}
                                                                            @endif
                                                                        </li>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                                </li>
                            @endif
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('layouts.scripts.menu')
@endsection