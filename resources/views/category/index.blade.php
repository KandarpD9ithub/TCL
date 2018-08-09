@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.categories') }}
                            <a href="{{ route('category.create') }}" class="col-md-offset-10 btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th>{{ Lang::get('views.name') }}</th>
                                <th>{{ Lang::get('views.parent') }}</th>
                                <th>{{ Lang::get('views.tax_id') }}</th>
                                <th>{{ Lang::get('views.status') }}</th>
                                <th>{{ Lang::get('views.action') }}</th>
                            </tr>
                            @if(count($categories) > 0)
                                @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            {{ ucfirst($category->name) }}
                                        </td><td>
                                            @if($category->parent_id == '0')
                                                {{ Lang::get('views.no_parent') }}
                                            @else
                                                {{ $parentCategory[$category->parent_id] }}
                                            @endif
                                        </td>
                                        <td> {{ ucfirst(isset($taxes[$category->tax_id])?$taxes[$category->tax_id]:'N/A') }}
                                        </td>
                                        <td>
                                            @if($category->is_active == 1)
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
                                            <a href="{{ route('category.edit', ['id' => $category->id]) }}"
                                               class="btn btn-default btn-sm">{{ Lang::get('views.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="3" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection