@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.rules') }}
                            <a href="{{ route('rules.create') }}" class="col-md-offset-10 btn btn-primary btn-xs">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th>{{ Lang::get('views.name') }}</th>
                                <th>{{ Lang::get('views.rule_type') }}</th>
                                <th>{{ Lang::get('views.from_date') }}</th>
                                <th>{{ Lang::get('views.to_date') }}</th>
                                <th>{{ Lang::get('views.status') }}</th>
                                <th>{{ Lang::get('views.action') }}</th>
                            </tr>
                            @if(count($rules) > 0)
                                @foreach($rules as $rule)
                                    <tr>
                                        <td>{{ ucfirst($rule->name) }}</td>
                                        <td>{{ $rule->rule_type }}</td>
                                        <td>{{ date('d M, Y', strtotime($rule->from_date)) }}</td>
                                        <td>{{ date('d M, Y', strtotime($rule->to_date)) }}</td>
                                        <td>
                                            @if($rule->is_active == 1)
                                                <?php $label = 'Active';
                                                $class = 'btn-success ';?>
                                            @else
                                                <?php $label = 'In-active';
                                                $class = 'btn-warning';?>
                                            @endif
                                            <span class="btn {{ $class }} btn-xs">{{ $label }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('rules.edit', ['id' => $rule->id]) }}"
                                               class="btn btn-default btn-xs">{{ Lang::get('views.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" align="center">{{ Lang::get('views.no_records_found') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $rules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection