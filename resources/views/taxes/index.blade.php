@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title  ">
                          {{ Lang::get('views.taxes') }}
                            <p class="pull-right align">
                            <a href="{{ route('taxes.create') }}"
                               class=" btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                            </p>
                        </h3>
                    </div>

                    <table class="table table-responsive panel-body">
                        <tbody>
                        <tr>
                            <th>{{ Lang::get('views.tax_name') }}</th>
                            <th>{{ Lang::get('views.tax_type') }}</th>
                            <th>{{ Lang::get('views.tax_rate') }}</th>
                            <th>{{ Lang::get('views.status') }}</th>
                            <th>{{ Lang::get('views.action') }}</th>
                        </tr>
                        @if(count($taxes) > 0)
                        @foreach ($taxes as $tax)
                            <tr>
                                <td>{{ $tax->tax_name}}</td>
                                <td>
                                    {{ !empty($tax->tax_type)? $taxType[$tax->tax_type]: 'N/A'}}
                                </td>
                                <td>{{ $tax->tax_rate}}</td>
                                <td>
                                    @if($tax->is_active == 1)
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
                                    <a class="btn btn-default btn-sm" href="{{ route('taxes.show',$tax->id) }}">View</a>
                                    <a class="btn btn-default btn-sm" href="{{ route('taxes.edit',$tax->id) }}">Edit</a>
                                </td>

                            </tr>
                        @endforeach
                        @else
                            <tr><td colspan="5" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                    {!! $taxes->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
