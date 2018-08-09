@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class ="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.non-chargeable') }}
                            <a href="{{ route('non-chargeable.create') }}"
                               class="col-md-offset-10 btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive panel-body">
                        <tbody>
                        <tr>
                            <th>{{ Lang::get('views.name') }}</th>
                            <th>{{ Lang::get('views.status') }}</th>
                            <th>{{ Lang::get('views.action') }}</th>
                        </tr>
                        @if(empty($ncPeoples->toArray()['data']))
                            <tr><td colspan="3" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @else
                            @foreach($ncPeoples as $ncPeople)
                                <tr>
                                    <td>
                                        {{$ncPeople->name}}
                                    </td>
                                    <td>
                                        @if($ncPeople->is_active == 1)
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
                                    <td><a href="{{ route('non-chargeable.edit', ['id' => $ncPeople->id]) }}"
                                               class="btn btn-default btn-sm">{{ Lang::get('views.edit') }}
                                            </a></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $ncPeoples->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
