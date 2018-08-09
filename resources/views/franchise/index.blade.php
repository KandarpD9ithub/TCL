@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.franchise') }}
                            <a href="{{ route('franchise.create') }}"
                               class="col-md-offset-10 btn btn-primary btn-xs">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive panel-body">
                        <tbody>
                            <tr>
                                <th>{{ Lang::get('views.name') }}</th>
                                <th>{{ Lang::get('views.address') }}</th>
                                <th>{{ Lang::get('views.status') }}</th>
                                <th>{{ Lang::get('views.action') }}</th>
                            </tr>
                            @if(count($franchises) > 0)
                                @foreach($franchises as $franchise)
                                    <tr>
                                        <td>
                                            {{ ucfirst($franchise->name) }}
                                        </td>
                                        <td>
                                            {{ $franchise->address_line_one }}, {{ $franchise->address_line_two }}<br>
                                            {{ $franchise->city }} {{ $franchise->region }} <br>
                                            {{ $countries[$franchise->country_id] }}
                                        </td>
                                        <td>
                                            @if($franchise->is_active == 1)
                                                <?php $label = 'Active';
                                                $class = 'btn-success ';?>
                                            @else
                                                <?php $label = 'In-active';
                                                $class = 'btn-warning';?>
                                            @endif
                                            <span class="btn {{ $class }} btn-xs">
                                                    {{ $label }}
                                                </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('franchise.edit', ['id' => $franchise->id]) }}"
                                               class="btn btn-default btn-xs pull-left">{{ Lang::get('views.edit') }}
                                            </a>
                                            {!! deleteForm('/franchise/'. $franchise->id ) !!}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $franchises->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection