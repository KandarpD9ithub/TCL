@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.employees') }}
                            <a href="{{ route('employee.create') }}"
                               class="col-md-offset-10 btn btn-primary btn-xs">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive panel-body">
                        <tbody>
                        <tr>
                            <th>{{ Lang::get('views.name') }}</th>
                            <th>{{ Lang::get('views.email') }}</th>
                            <th>{{ Lang::get('views.mobile') }}</th>
                            <th>{{ Lang::get('views.role_name') }}</th>
                            <th>{{ Lang::get('views.franchise') }}</th>
                            <th>{{ Lang::get('views.action') }}</th>
                        </tr>
                        @if(count($users) > 0)
                            @foreach($users as $user)
                               @if ($user->employee->franchise != null)

                                    <tr>
                                        <td>
                                            {{ ucfirst($user->name) }}
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                        </td>
                                        <td>
                                           {{ $user->mobile }}
                                        </td>
                                        <td>
                                            {{ \Config::get('constants.ROLE_NAME.'.$user->role_name) }}
                                        </td>
                                        <td>
                                            {{ $user->employee->franchise->name }}
                                        </td>
                                        <td>
                                            <a href="{{ route('employee.edit', ['id' => $user->id]) }}"
                                               class="btn btn-default btn-xs pull-left">{{ Lang::get('views.edit') }}
                                            </a>
                                            {!! deleteForm('/employee/'. $user->id ) !!}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr><td colspan="6" align="center">{{ Lang::get('views.no_records_found') }}</td></tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection