function debug(functionName, title, value) {
    var re = /function (.*?)\(/
    var functionName = re.exec( functionName )
    console.log("-- " + functionName[1] + " -- " + title);
    console.log(value);
}


var dataset = [];
var currentDate = new Date();
var day = currentDate.getDate();
var month = currentDate.getMonth() + 1;
var year = currentDate.getFullYear();
var settlementAmt = 0;
var adminFeeAmt = 100;
var serviceFeeAmt = 0;
var totalProgAmt = 0;
var totalCustSavingAmt = 0;
later.date.localTime();

$('#enrollment_date_text').text(month + '/' + day + '/' + year);
$('#enrollment_date').val(Date.today().toString('yyyy-MM-dd HH:mm:ss'));

var debtTable = $('#debts-table').DataTable({
    "pageLength": 30,
    "lengthChange": false, 
    "searching": false,
    "ordering": false,
    columns: [
        {title: 'Date'},
        {title: 'Recurring Payment'},
        {title: 'Admin Fee'},
        {title: 'Effective Payment'},
    ]
});

if($("#payment_schedule").val()) {
    debtTable.clear().rows.add(JSON.parse($("#payment_schedule").val())).draw();
}

$('#first_payment_date').datepicker({
    changeMonth: true,
    changeYear: false,
    autoHide: true
});

function getPaymentAmt() {
    var firstPay = accounting.unformat(JSON.parse($("#payment_schedule").val())[0][1]);
    return {'settlementAmt': settlementAmt, 'adminFeeAmt': adminFeeAmt, 'serviceFeeAmt': serviceFeeAmt, 'totalProgAmt': totalProgAmt, 'totalCustSavingAmt': totalCustSavingAmt, 'firstPaymentAmt': firstPay, 'schedule': JSON.parse($("#payment_schedule").val())};
}

function isEven(n) {
    return n % 2 == 0;
}

function exceptUsHolidays(schedule) {
    return schedule.except()
        // new year's and MLK day
        .on(1).month().on([1, 20]).dayOfMonth()
        // presidents day - 3rd monday in feb
        .and().on(2).month().on(2).dayOfWeek().on(3).dayOfWeekCount()
        // memorial day - last monday in may
        .and().on(5).month().on(2).dayOfWeek().last().dayOfWeekCount()
        // independence day
        .and().on(7).month().on(4).dayOfMonth()
        // labor day - 1st monday in sept
        .and().on(9).month().on(2).dayOfWeek().on(1).dayOfWeekCount()
        // columbus day - 2nd monday in oct
        .and().on(10).month().on(2).dayOfWeek().on(2).dayOfWeekCount()
        // veterans day
        .and().on(11).month().on(11).dayOfMonth()
        // thanksgiving day - 4th thursday in nov
        .and().on(11).month().on(5).dayOfWeek().on(4).dayOfWeekCount()
        // christmas day
        .and().on(12).month().on(25).dayOfMonth();
}

function checkWeekend(schedule, progLength) {
    for (var i = 0; i < progLength; i++) {
        var curDate = schedule[i];
        var prevDay = curDate.getDate() - 1;
        var prevDay2 = curDate.getDate() - 2;
        var nextDay = curDate.getDate() + 1;
        var nextDay2 = curDate.getDate() + 2;
        var curMonth = curDate.getMonth();
        var prevMonth = curMonth - 1;
        //debug(arguments.callee.toString(), "curDate", curDate);
        //debug(arguments.callee.toString(), "prevDay", prevDay);
        //debug(arguments.callee.toString(), "prevDay2", prevDay2);
        //debug(arguments.callee.toString(), "nextDay", nextDay);
        //debug(arguments.callee.toString(), "nextDay2", nextDay2);
        //debug(arguments.callee.toString(), "curMonth", curMonth);
        //debug(arguments.callee.toString(), "prevMonth", prevMonth);
        //debug(arguments.callee.toString(), "", );
        if(later.dayOfWeek.isValid(curDate,1)) {
            schedule[i] = later.day.prev(curDate,prevDay2);
        }
        if(later.dayOfWeek.isValid(curDate,7)) {
            schedule[i] = later.day.prev(curDate,prevDay);
        }
    }
    
    return schedule;
}


function weeklySched(recurPayDate, progLength) {
    var todayDate = Date.today();
    var endDate = Date.today().addMonths(parseInt(progLength));
    var dayOfWeek = recurPayDate.replace(/w/g,'');

    //debug(arguments.callee.toString(), "todayDate", todayDate);
    //debug(arguments.callee.toString(), "endDate", endDate);
    //debug(arguments.callee.toString(), "recurPayDate", recurPayDate);
    //debug(arguments.callee.toString(), "firstPayDate", firstPayDate);
    //debug(arguments.callee.toString(), "dayOfWeek", dayOfWeek);
    var schedule = later.parse.recur().every().weekOfYear().on(parseInt(dayOfWeek)).dayOfWeek();
    var next = later.schedule(schedule).next(5000, todayDate, endDate);
    //debug(arguments.callee.toString(), "Weekly Schedule Raw", next);
    return checkWeekend(next, progLength); 
            
}

function biweeklySched(recurPayDate, progLength, firstPayDate, prevWeek) {
    var todayDate = Date.parse(firstPayDate);
    var endDate = Date.parse(firstPayDate).addMonths(parseInt(progLength));
    var dayOfWeek = recurPayDate.replace(/w/g,'');
    if(isEven(later.weekOfYear.val(new Date(firstPayDate)))) {
        //todayDate = todayDate.addWeeks(-1);
    } 
    /*if(prevWeek) {
        var firstDate = (later.weekOfYear.val(new Date(firstPayDate))-1);
    } else {
        var firstDate = later.weekOfYear.val(new Date(firstPayDate));
    }*/

    //debug(arguments.callee.toString(), "progLength", progLength);
    //debug(arguments.callee.toString(), "todayDate", todayDate);
    //debug(arguments.callee.toString(), "endDate", endDate);
    //debug(arguments.callee.toString(), "recurPayDate", recurPayDate);
    //debug(arguments.callee.toString(), "firstPayDate", firstPayDate);
    //debug(arguments.callee.toString(), "dayOfWeek", dayOfWeek);
    var schedule = later.parse.recur().every(2).weekOfYear().on(parseInt(dayOfWeek)).dayOfWeek();
    var next = later.schedule(schedule).next(progLength, todayDate, endDate);

    //debug(arguments.callee.toString(), "Bi-Weekly Schedule Raw", next);
    return checkWeekend(next, progLength); 
    
}

function monthlySched(recurPayDate, progLength, firstPayDate) {
    var todayDate = Date.parse(firstPayDate).addDays(-1);
    var dayConvert = new Date("Jan " + recurPayDate + ", 2017");
    var dayOfMonth = later.day.val(dayConvert);
    //debug(arguments.callee.toString(), "todayDate", todayDate);
    //debug(arguments.callee.toString(), "firstPayDate", firstPayDate);
    //debug(arguments.callee.toString(), "recurPayDate", recurPayDate);
    //debug(arguments.callee.toString(), "dayOfMonth", dayOfMonth);
    var schedule = later.parse.recur().every().month().on(dayOfMonth).dayOfMonth();
    var next = later.schedule(schedule).next(progLength, todayDate);
    
    //debug(arguments.callee.toString(), "Monthly Schedule Raw", next);
    return checkWeekend(next, progLength); 
    
}

function replaceFirstPayment(schedule, firstPayDate) {
    schedule[0] = firstPayDate;
    return schedule; 

}

function adminFeeSched(adminFeeMonths, adminFeeAmt, paymentInterval) {
    paymentInterval = parseInt(paymentInterval);
    switch(paymentInterval) {
        case 1:
            var amount = adminFeeAmt / adminFeeMonths;
            return {paymentLength: adminFeeMonths, paymentAmount: amount};
            break;
        case 2:
            var todayDate = Date.today();
            var endDate = Date.today().addMonths(parseInt(adminFeeMonths));

            var schedule = later.parse.recur().every(2).weekOfYear();
            var next = later.schedule(schedule).next(5000, todayDate, endDate);

            var amount = (adminFeeAmt / next.length);

            return {paymentLength: next.length, paymentAmount: amount};
            break;
        case 3:
            var todayDate = Date.today();
            var endDate = Date.today().addMonths(parseInt(adminFeeMonths));

            var schedule = later.parse.recur().every().weekOfYear();
            var next = later.schedule(schedule).next(5000, todayDate, endDate);
            var amount = (adminFeeAmt / next.length);

            return {paymentLength: next.length, paymentAmount: amount};
            break;
    }
}

function moneyfi(i) {
    return i.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

function monthlyCap(progLength, schedule, settlementAmt, adminFeeAmt, serviceFeeAmt, recurPayment, paymentInterval) {
    console.log("-- InMonthly Cap -- settlementAmt");
    console.log(settlementAmt);
    console.log("-- InMonthly Cap -- adminFeeAmt");
    console.log(adminFeeAmt);
    console.log("-- InMonthly Cap -- serviceFeeAmt");
    console.log(serviceFeeAmt);
    console.log("-- InMonthly Cap -- progLength");
    console.log(progLength);

    if (paymentInterval == 3) {
        paymentInterval = 4;
    }

    console.log("-- InMonthly Cap -- paymentInterval PostAlter");
    console.log(paymentInterval);

    //var monthCapFee = parseFloat(((settlementAmt + serviceFeeAmt + adminFeeAmt) / progLength));
    var monthCapFee = parseFloat(recurPayment * paymentInterval);
    var excessAmt = 0;
    var previousMonth = 0;
    var contMonths = [];
    var newSched = schedule;
    var firstMonth = lastMonth = null;
    var numFirstLastPeriods = 0;
    var numFirstLastPeriodsAmt = 0;
    var splitMonths = false;

    //console.log("-- InMonthly Cap -- Monthly Cap Fee");
    //console.log(monthCapFee);

    // Find First Month
    for (var i = 0; i < schedule.length; i++) {
        var d = new Date(schedule[i][0]);
        var currentMonth = later.month.val(d);

        if (firstMonth == null) {
            firstMonth = currentMonth;
            numFirstLastPeriods++;
        }
        if (currentMonth != firstMonth) {
            break;
        }
    }

    // Find Last Month
    for (var i = (schedule.length - 1); i > 0; i--) {
        var d = new Date(schedule[i][0]);
        var currentMonth = later.month.val(d);

        if (lastMonth == null) {
            lastMonth = currentMonth;
        }
        if (currentMonth != firstMonth) {
            break;
        } else {
            numFirstLastPeriods++;
        }
    }

    if (firstMonth == lastMonth) {
        splitMonths = true;
    }

    numFirstLastPeriodsAmt = numFirstLastPeriods;

    //console.log('num of pay periods on each end: ' + numFirstLastPeriods);

    for (var i = 0; i < schedule.length; i++) {
        var d = new Date(schedule[i][0]);
        var currentMonth = later.month.val(d);


        if(i == (schedule.length - numFirstLastPeriods)) {
            numFirstLastPeriods--;
        }

        if (previousMonth == 0 && currentMonth == firstMonth && splitMonths == false) {
            previousMonth = currentMonth;
        }

        if (currentMonth != previousMonth) {
            var periodFee = 0;
            if (previousMonth == 0 && currentMonth == firstMonth && i < 2 && splitMonths == true) { 
                periodFee = (monthCapFee / numFirstLastPeriodsAmt);
                numFirstLastPeriods--;
                contMonths.push({ schedKey: i, schedVal: schedule[i] });
            } else {
                if (numFirstLastPeriods == 0 && contMonths.length < 3 && splitMonths == true) {
                    periodFee = (monthCapFee / numFirstLastPeriodsAmt);
                } else {
                    periodFee = (monthCapFee / contMonths.length);
                }
            }


            for (var h = 0; h < contMonths.length; h++) {
                var adminFeeAmt = contMonths[h].schedVal[2].replace(/[$,]/g, '');
                var savingsSplit = parseFloat(periodFee - adminFeeAmt);
                contMonths[h].schedVal = [contMonths[h].schedVal[0], '$' + moneyfi(periodFee), adminFeeAmt, parseFloat(savingsSplit).toFixed(2)];
                newSched[parseInt(contMonths[h].schedKey)] = contMonths[h].schedVal;
            }

            if (previousMonth == 0 && currentMonth == firstMonth && i < 2) { 
                contMonths = [];
            } else {
                contMonths = [{ schedKey: i, schedVal: schedule[i] }];
            }

            if (numFirstLastPeriods == 0 && splitMonths == true) {
                periodFee = (monthCapFee / numFirstLastPeriodsAmt);
                for (var h = 0; h < contMonths.length; h++) {
                    var adminFeeAmt = contMonths[h].schedVal[2].replace(/[$,]/g, '');
                    var savingsSplit = parseFloat(periodFee - adminFeeAmt);
                    contMonths[h].schedVal = [contMonths[h].schedVal[0], '$' + moneyfi(periodFee), adminFeeAmt, parseFloat(savingsSplit).toFixed(2)];
                    newSched[parseInt(contMonths[h].schedKey)] = contMonths[h].schedVal;
                }
            }
            previousMonth = currentMonth;
        } else {
            contMonths.push({ schedKey: i, schedVal: schedule[i] });
            if (numFirstLastPeriods == 0 && splitMonths == true) {
                periodFee = (monthCapFee / numFirstLastPeriodsAmt);
                for (var h = 0; h < contMonths.length; h++) {
                    var adminFeeAmt = contMonths[h].schedVal[2].replace(/[$,]/g, '');
                    var savingsSplit = parseFloat(periodFee - adminFeeAmt);
                    contMonths[h].schedVal = [contMonths[h].schedVal[0], '$' + moneyfi(periodFee), adminFeeAmt, parseFloat(savingsSplit).toFixed(2)];
                    newSched[parseInt(contMonths[h].schedKey)] = contMonths[h].schedVal;
                }
            } else if (i == (schedule.length - 1)) {
                periodFee = (monthCapFee / contMonths.length);
                for (var h = 0; h < contMonths.length; h++) {
                    var adminFeeAmt = contMonths[h].schedVal[2].replace(/[$,]/g, '');
                    var savingsSplit = parseFloat(periodFee - adminFeeAmt);
                    contMonths[h].schedVal = [contMonths[h].schedVal[0], '$' + moneyfi(periodFee), adminFeeAmt, parseFloat(savingsSplit).toFixed(2)];
                    newSched[parseInt(contMonths[h].schedKey)] = contMonths[h].schedVal;
                }
            }
        }
    }

    return newSched;

}

function sanitizeDatesandAddPayments(schedule, settlementAmt, progLength, adminFeeMonths, adminFeeAmt, serviceFeeAmt, totalCustSavingAmt, paymentInterval) {
    var recurPayment = ((settlementAmt + serviceFeeAmt + adminFeeAmt) / schedule.length);
    var recurAdmFee = adminFeeSched(adminFeeMonths, adminFeeAmt, paymentInterval); 
    //console.log(recurPayment);
    //console.log(recurAdmFee);
    var adminFeeMonthsCount = recurAdmFee.paymentLength;
    var accountedAdmFee = savingsAmt = parseFloat(0);
    for (var i = 0; i < schedule.length; i++) {
        var curDate = new Date(schedule[i]);
            var day = curDate.getDate();
            var month = curDate.getMonth() + 1;
            var year = curDate.getFullYear();

            if(day < 10) {
                day = "0" + day;
            }

            if(month < 10) {
                month = "0" + month;
            }

        if(adminFeeMonthsCount <= 0) {
            recurAdmFee2 = "";
            savingsAmt = moneyfi(recurPayment);
        } else {
            accountedAdmFee = parseFloat(accountedAdmFee) + parseFloat(recurAdmFee.paymentAmount.toFixed(2));
            if(adminFeeMonthsCount == 1 && (parseFloat(adminFeeAmt)) != accountedAdmFee) {
                recurAdmFee.paymentAmount = parseFloat(recurAdmFee.paymentAmount) + (parseFloat(adminFeeAmt) - accountedAdmFee);
            }
            recurAdmFee2 = "$" + moneyfi(recurAdmFee.paymentAmount);
            savingsAmt = moneyfi(recurPayment - recurAdmFee.paymentAmount);
        }


        schedule[i] = [month + '/' + day + '/' + year, "$" + moneyfi(recurPayment), recurAdmFee2, "$" + savingsAmt];

        adminFeeMonthsCount = adminFeeMonthsCount - 1;
    }

    schedule = monthlyCap(progLength, schedule, settlementAmt, adminFeeAmt, serviceFeeAmt, recurPayment, paymentInterval);
    //console.log("-- PostMonthly Cap");
    //console.log(schedule);

    return schedule;

}

$(function() {

    $("#recalc").click(function() {
        var origDebt = $("#original_debt").val();
        var progLength = $("#program_length").val();
        var repayPercent = $("#repayment_percent").val();
        var recurPayDate = $("#payment_date").val();
        var firstPayDate = $("#first_payment_date").val();
        var paymentInterval = $("input[name=payment_sched_multiple]:checked").val();
        var adminFeeMonths = $("#admin_fee_months").val();
        var adminFeePercent = $("#admin_fee_percent").val();
        var serviceFeePercent = $("#service_fee_percent").val();

        settlementAmt = origDebt * (repayPercent / 100);
        adminFeeAmt = origDebt * (adminFeePercent / 100);
        serviceFeeAmt = settlementAmt * (serviceFeePercent / 100);
        totalProgAmt = (settlementAmt + adminFeeAmt + serviceFeeAmt).toFixed(2);
        totalCustSavingAmt = (origDebt - totalProgAmt).toFixed(2);
        
        var schedule;

        $("#total-debt-text").text(parseFloat(origDebt).toFixed(2));
        $("#total-cost-text").text(parseFloat(totalProgAmt).toFixed(2));
        $("#admin-fee-text").text(parseFloat(adminFeeAmt).toFixed(2));
        $("#service-fee-text").text(parseFloat(serviceFeeAmt).toFixed(2));
        $("#total-savings-text").text(parseFloat(totalCustSavingAmt).toFixed(2));

        if(!paymentInterval) {
            alert("Check a Payment Interval!");
        }

        switch(paymentInterval) {
            case '1':
                schedule = monthlySched(recurPayDate, progLength, firstPayDate);
                break;
            case '2':
                schedule = biweeklySched(recurPayDate, progLength, firstPayDate, false);
                break;
            case '3':
                schedule = weeklySched(recurPayDate, progLength);
                break;
        }

        if(firstPayDate != "") {
            schedule = replaceFirstPayment(schedule, firstPayDate);
        }


        dataset = sanitizeDatesandAddPayments(schedule, settlementAmt, progLength, adminFeeMonths, adminFeeAmt, serviceFeeAmt, totalCustSavingAmt, paymentInterval);
        debtTable.clear().rows.add(dataset).draw();
        var payment_schedule = JSON.stringify(dataset);
        $("#payment_schedule").val(payment_schedule);
    });
});
