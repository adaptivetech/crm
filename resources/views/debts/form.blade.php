            <table class="table table-hover" id="debts-table">
                <thead>
                <tr>

                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Recurring Payment') }}</th>
                    <th>{{ __('Admin Fee') }}</th>
                    <th>{{ __('Effective Payment') }}</th>

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
                '36' => '36'),
                12, ['class' => 'form-control'] )
            !!}
            <br>
            {!! Form::label('repayment_percent', __('Repayment Percentage'), ['class' => 'control-label']) !!}
            {!! Form::select('repayment_percent', array(
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

    </div>
