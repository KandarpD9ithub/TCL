@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{ Lang::get('views.customers') }}
                            <a href="{{ route('customer.create') }}" class="col-md-offset-10 btn btn-primary btn-sm">
                                {{ Lang::get('views.add') }}
                            </a>
                        </h3>
                    </div>
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th>{{ Lang::get('views.name') }}</th>
                                <th>{{ Lang::get('views.contact_number') }}</th>
                               {{-- <th>{{ Lang::get('views.status') }}</th> --}}
                                <th>{{ Lang::get('views.action') }}</th>
                            </tr>
                            @if(isset($customers) and (count($customers) > 0))
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            {{ ucfirst($customer->name) }}
                                        </td><td>
                                            {{$customer->contact_number}}
                                        </td>
                                        
                                        <td>
                                            <a href="{{ route('customer.edit', ['id' => $customer->id]) }}"
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
                        {{ $customers->links() }}
                    </div>
                </div> <!-- End class panel panel-default -->
            </div> <!-- End class col-md-8 col-md-offset-2 -->
        </div> <!-- End class row -->
    </div> <!-- End class container -->
@endsection
