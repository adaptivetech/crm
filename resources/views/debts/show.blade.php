@extends('layouts.master')

@section('heading')

@stop

@section('content')

@push('css_scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.5.5/datepicker.min.css">
    <style type="text/css">
        .calc-box-totals {
            font-size: 100% !important;
        }
        .small-box p:last-child {
            margin-bottom: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script type="text/javascript" src="{{ URL::asset('js/later.min.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.5.5/datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/superagent/3.0.0/superagent.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/validator/6.0.0/validator.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/adobe-sign-sdk/1.1.0/adobe-sign-sdk.min.js"></script>
    <script type="text/javascript" src="{{ URL::asset('js/accounting.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/calc.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/adobe.js') }}"></script>
    <script type="text/javascript">
    $(function() {
        // recalc click on page load
        $("#recalc").click();

        // prep library docs
        getLibraryDocs();

        // create the agreement
        var debtInfo = {debt: {!! $debt !!}, client: {!! $client !!}};
        //console.log(debtInfo);
        //console.log(getPaymentAmt());
        $('#agreementForm').submit(function(ev) {
            ev.preventDefault();
            createAgreement(debtInfo, this);
        });
    });
    </script>
@endpush
    <div class="row">
        @include('partials.clientheader')
        @include('partials.userheader')
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="col-md-3 removeleft">
                <div class="sidebarheader">
                    <p> {{ __('Debt information') }}</p>
                </div>
                <div class="sidebarbox">
                    <p>{{ __('Assigned to') }}:
                        <a href="{{route('debts.show', $debt->user->id)}}">
                            {{$debt->user->name}}</a></p>
                    <p>{{ __('Created at') }}: {{ date('d F, Y, H:i', strtotime($debt->created_at))}} </p>
                    @if($debt->status == 1)
                        {{ __('Status') }}: {{ __('Contact') }}
                    @elseif($debt->status == 2)
                        {{ __('Status') }}: {{ __('Completed') }}
                    @elseif($debt->status == 3)
                        {{ __('Status') }}: {{ __('Not interested') }}
                    @endif

                </div>
                @if($debt->status == 1)
                    {!! Form::model($debt, [
                'method' => 'PATCH',
                    'url' => ['debts/updateassign', $debt->id],
                    ]) !!}
                    {!! Form::select('user_assigned_id', $users, null, ['class' => 'form-control ui search selection top right pointing search-select', 'id' => 'search-select']) !!}
                    {!! Form::submit(__('Assign new user'), ['class' => 'btn btn-primary form-control closebtn']) !!}
                    {!! Form::close() !!}

                    {!! Form::model($debt, [
                        'method' => 'PATCH',
                        'url' => ['debts/updateagreement', $debt->id],
                        'id' => 'agreementForm',
                    ]) !!}
                    {!! Form::hidden('agreementId', null, array('id' => 'agreementId')) !!}
                    {!! Form::submit(__('Create Agreement'), ['class' => 'btn btn-success form-control movedown', 'id' => 'submitAgreement']) !!}
                    {!! Form::close() !!}

                    {!! Form::model($debt, [
                'method' => 'PATCH',
                'url' => ['debts/updatestatus', $debt->id],
                ]) !!}

                    {!! Form::submit(__('Complete Debt'), ['class' => 'btn btn-info form-control closebtn movedown']) !!}
                    {!! Form::close() !!}
                @endif

            </div>
            {!! Form::model($debt, [
                    'method' => 'PATCH',
                    'route' => ['debts.update', $debt->id],
                    'files' => false,
                    'enctype' => 'multipart/form-data'
                    ]) !!}
            <div class="col-md-9 removeleft">

                {!! Form::hidden('title', null, array('id' => 'title')) !!}
                {!! Form::hidden('description', null, array('id' => 'description')) !!}
                <div class="form-group">
                    <div class="form-group col-sm-2 removeleft">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 class="calc-box-totals" id="total-debt-text">0</h3>
                                <p>Total Debt</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 removeleft">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 class="calc-box-totals" id="total-cost-text">0</h3>
                                <p>Total Program Cost</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2 removeleft">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 class="calc-box-totals" id="admin-fee-text">0</h3>
                                <p>Admin Fee</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-2 removeleft">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 class="calc-box-totals" id="service-fee-text">0</h3>
                                <p>Service Fee</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 removeleft">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3 class="calc-box-totals" id="total-savings-text">0</h3>
                                <p>Total Customer Savings</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group col-lg-8 removeleft">
                        <table class="table table-hover" id="debts-table">
                            <thead>
                            <tr>

                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Recurring Payment') }}</th>
                                <th>{{ __('Admin Fee') }}</th>
                                <th>{{ __('Customer Savings') }}</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="form-group col-lg-4 removeleft removeright">
                        <span><b>Debt Load and Program Details</b></span><br>
                        {!! Form::label('original_debt', __('Original Debt'), ['class' => 'control-label']) !!}
                        {!! Form::text('original_debt', null, ['class' => 'form-control']) !!}
                        <br>
                        {!! Form::label('program_length', __('Program Length'), ['class' => 'control-label']) !!}
                        {!! Form::select('program_length', array(
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                            '11' => '11',
                            '12' => '12',
                            '13' => '13',
                            '14' => '14',
                            '15' => '15',
                            '16' => '16',
                            '17' => '17',
                            '18' => '18',
                            '19' => '19',
                            '20' => '20',
                            '21' => '21',
                            '22' => '22',
                            '23' => '23',
                            '24' => '24',
                            '25' => '25',
                            '26' => '26',
                            '27' => '27',
                            '28' => '28',
                            '29' => '29',
                            '30' => '30',
                            '31' => '31',
                            '32' => '32',
                            '33' => '33',
                            '34' => '34',
                            '35' => '35',
                            '36' => '36',
                            '37' => '37',
                            '38' => '38',
                            '39' => '39',
                            '40' => '40',
                            '41' => '41',
                            '42' => '42',
                            '43' => '43',
                            '44' => '44',
                            '45' => '45',
                            '46' => '46',
                            '47' => '47',
                            '48' => '48',
                            '49' => '49',
                            '50' => '50',
                            '51' => '51',
                            '52' => '52',
                            '53' => '53',
                            '54' => '54',
                            '55' => '55',
                            '56' => '56',
                            '57' => '57',
                            '58' => '58',
                            '59' => '59',
                            '60' => '60',
                            '61' => '61',
                            '62' => '62',
                            '63' => '63',
                            '64' => '64',
                            '65' => '65',
                            '66' => '66',
                            '67' => '67',
                            '68' => '68',
                            '69' => '69',
                            '70' => '70',
                            '71' => '71',
                            '72' => '72',
                            '73' => '73',
                            '74' => '74',
                            '75' => '75',
                            '76' => '76',
                            '77' => '77',
                            '78' => '78',
                            '79' => '79',
                            '80' => '80',
                            '81' => '81',
                            '82' => '82',
                            '83' => '83',
                            '84' => '84',
                            '85' => '85',
                            '86' => '86',
                            '87' => '87',
                            '88' => '88',
                            '89' => '89',
                            '90' => '90',
                            '91' => '91',
                            '92' => '92',
                            '93' => '93',
                            '94' => '94',
                            '95' => '95',
                            '96' => '96',
                            '97' => '97',
                            '98' => '98',
                            '99' => '99',
                            '100' => '100'),
                            12, ['class' => 'form-control'] )
                        !!}
                        <br>
                        {!! Form::label('repayment_percent', __('Repayment Percentage'), ['class' => 'control-label']) !!}
                        {!! Form::select('repayment_percent', array(
                            '20' => '20',
                            '21' => '21',
                            '22' => '22',
                            '23' => '23',
                            '24' => '24',
                            '25' => '25',
                            '26' => '26',
                            '27' => '27',
                            '28' => '28',
                            '29' => '29',
                            '30' => '30',
                            '31' => '31',
                            '32' => '32',
                            '33' => '33',
                            '34' => '34',
                            '35' => '35',
                            '36' => '36',
                            '37' => '37',
                            '38' => '38',
                            '39' => '39',
                            '40' => '40',
                            '41' => '41',
                            '42' => '42',
                            '43' => '43',
                            '44' => '44',
                            '45' => '45',
                            '46' => '46',
                            '47' => '47',
                            '48' => '48',
                            '49' => '49',
                            '50' => '50',
                            '51' => '51',
                            '52' => '52',
                            '53' => '53',
                            '54' => '54',
                            '55' => '55',
                            '56' => '56',
                            '57' => '57',
                            '58' => '58',
                            '59' => '59',
                            '60' => '60',
                            '61' => '61',
                            '62' => '62',
                            '63' => '63',
                            '64' => '64',
                            '65' => '65',
                            '66' => '66',
                            '67' => '67',
                            '68' => '68',
                            '69' => '69',
                            '70' => '70',
                            '71' => '71',
                            '72' => '72',
                            '73' => '73',
                            '74' => '74',
                            '75' => '75',
                            '76' => '76',
                            '77' => '77',
                            '77.5' => '77.5',
                            '78' => '78',
                            '79' => '79',
                            '80' => '80',
                            '81' => '81',
                            '82' => '82',
                            '83' => '83',
                            '84' => '84',
                            '85' => '85',
                            '86' => '86',
                            '87' => '87',
                            '88' => '88',
                            '89' => '89',
                            '90' => '90',
                            '91' => '91',
                            '92' => '92',
                            '93' => '93',
                            '94' => '94',
                            '95' => '95',
                            '96' => '96',
                            '97' => '97',
                            '98' => '98',
                            '99' => '99',
                            '100' => '100'),
                            50, ['class' => 'form-control'] )
                        !!}
                        <br><br>
                        <span><b>Payment Dates</b></span><br>
                        {!! Form::label('payment_date', __('Recurring Payment Date'), ['class' => 'control-label']) !!}
                        {!! Form::select('payment_date', array(
                            'Day of the Month' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                                '7' => '7',
                                '8' => '8',
                                '9' => '9',
                                '10' => '10',
                                '11' => '11',
                                '12' => '12',
                                '13' => '13',
                                '14' => '14',
                                '15' => '15',
                                '16' => '16',
                                '17' => '17',
                                '18' => '18',
                                '19' => '19',
                                '20' => '20',
                                '21' => '21',
                                '22' => '22',
                                '23' => '23',
                                '24' => '24',
                                '25' => '25',
                                '26' => '26',
                                '27' => '27',
                                '28' => '28',
                                '29' => '29',
                                '30' => '30',
                                '31' => '31'),
                            'Day of the Week' => array(
                                '2w' => 'Monday',
                                '3w' => 'Tuesday',
                                '4w' => 'Wednesday',
                                '5w' => 'Thursday',
                                '6w' => 'Friday')
                            ),
                            1, ['class' => 'form-control'] )
                        !!}
                        <br>
                        <label class="control-label">Enrollment Date: <span id="enrollment_date_text"></span></label>
                        {!! Form::hidden('enrollment_date', null, array('id' => 'enrollment_date')) !!}
                        <br>
                        @php
                        if($debt->first_payment_date == '0000-00-00 00:00:00') { $debt->first_payment_date = ""; }
                        @endphp
                        {!! Form::label('first_payment_date', __('First Payment Date'), ['class' => 'control-label']) !!}
                        {!! Form::text('first_payment_date', null, ['class' => 'form-control']) !!}
                        <br>
                        {!! Form::label('payment_sched_multiple-1', __('Monthly'), ['class' => 'control-label']) !!}
                        {!! Form::radio('payment_sched_multiple', '1', false, ['id' => 'payment_sched_multiple-1']) !!}&nbsp;
                        {!! Form::label('payment_sched_multiple-2', __('Bi-Weekly'), ['class' => 'control-label']) !!}
                        {!! Form::radio('payment_sched_multiple', '2', false, ['id' => 'payment_sched_multiple-2']) !!}&nbsp;
                        {!! Form::label('payment_sched_multiple-3', __('Weekly'), ['class' => 'control-label']) !!}
                        {!! Form::radio('payment_sched_multiple', '3', false, ['id' => 'payment_sched_multiple-3']) !!}&nbsp;
                        <br><br>
                        <span class=""><b>Program Fees</b></span><br>
                        {!! Form::label('admin_fee_months', 'Admin Fee (months)', ['class' => 'control-label']) !!}
                        {!! Form::select('admin_fee_months', array(
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10'),
                            3, ['class' => 'form-control'] )
                        !!}
                        <br>
                        {!! Form::label('admin_fee_percent', 'Admin Fee (percent)', ['class' => 'control-label']) !!}
                        {!! Form::select('admin_fee_percent', array(
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10'),
                            10, ['class' => 'form-control'] )
                        !!}
                        <br>
                        {!! Form::label('service_fee_percent', 'Service Fee (percent)', ['class' => 'control-label']) !!}
                        {!! Form::select('service_fee_percent', array(
                            '20' => '20',
                            '21' => '21',
                            '22' => '22',
                            '23' => '23',
                            '24' => '24',
                            '25' => '25',
                            '26' => '26',
                            '27' => '27',
                            '28' => '28',
                            '29' => '29',
                            '30' => '30',
                            '31' => '31',
                            '32' => '32',
                            '33' => '33',
                            '34' => '34',
                            '35' => '35'),
                            35, ['class' => 'form-control'] )
                        !!}
                        <br>
                        {!! Form::button(__('Recalculate Debt'), ['class' => 'btn btn-success', 'id' => 'recalc']) !!}
                    </div>



                    {!! Form::hidden('payment_schedule', null, array('id' => 'payment_schedule')) !!}
                </div>
            </div>
            <div class="col-lg-12 clearfix">
                <div class="form-group col-md-3 removeleft" style="min-height:70px">&nbsp;</div>
                <div class="form-group col-md-3 removeleft">
                    {!! Form::label('creditor_list', __('Creditor Names'), ['class' => 'control-label']) !!}
                    {!! Form::textarea('creditor_list', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group col-md-3 removeleft">
                    {!! Form::label('creditor_list_amt', __('Creditor List Amounts'), ['class' => 'control-label']) !!}
                    {!! Form::textarea('creditor_list_amt', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-lg-12 clearfix">
                <div class="form-group col-md-3 removeleft" style="min-height:70px">&nbsp;</div>
                <div class="form-group col-md-6 removeleft">
                    {!! Form::submit(__('Update debt'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    </div>


@stop
       

   
