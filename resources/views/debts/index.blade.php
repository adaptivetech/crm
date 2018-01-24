@extends('layouts.master')
@section('heading')
    <h1>{{__('All debts')}}</h1>
@stop

@section('content')
    <table class="table table-hover" id="debts-table">
        <thead>
        <tr>

            <th>{{ __('Title') }}</th>
            <th>{{ __('Created by') }}</th>
            <th>{{ __('Assigned') }}</th>

        </tr>
        </thead>
    </table>
@stop

@push('scripts')
<script>
    $(function () {
        $('#debts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('debts.data') !!}',
            columns: [

                {data: 'titlelink', name: 'title'},
                {data: 'user_created_id', name: 'user_created_id'},
                {data: 'user_assigned_id', name: 'user_assigned_id'},


            ]
        });
    });
</script>
@endpush
