var token = "3AAABLblqZhCSR1Zbo7A4hDC6AKGeWwh1n7DXGIcQDxbrZcSb9RsfxdXO1YoZCMdUfWonrZW4Qm5SIaeJlK-4PLEb1uFY7tS0",
    context = null,
    agreementsApi = null,
    libDocsApi = null,
    headerParams = {"accessToken": token},
    agreementsModel = AdobeSignSdk.AgreementsModel;

var singlePageID = twoPageID = threePageID = fourPageID = undefined;
var agreementSubmitted = false;

function getContext() {
    if (context == null) {
        context = new AdobeSignSdk.Context();
    }
    return context;
}

function getAgreementsApi() {
    if (agreementsApi == null) {
        agreementsApi = new AdobeSignSdk.AgreementsApi(getContext());
    }

    return agreementsApi;
}

function getLibDocsApi() {
    if (libDocsApi == null) {
        libDocsApi = new AdobeSignSdk.LibraryDocumentsApi(getContext());
    }

    return libDocsApi;

}

function getAgreements() {
    getAgreementsApi().getAgreements(headerParams)
        .then(function (userAgreements) {
                var userAgreementList = userAgreements.getUserAgreementList();

                //Find the signing status of the agreement
                for (var i = 0; i < userAgreementList.length; i++) {
                    var userAgreement = userAgreementList[i];
                    console.log(userAgreement.getName());
                    console.log(userAgreement.getAgreementId());
                    console.log(userAgreement.getStatus());
                }
        })
        .catch(function (apiError) {
                console.log(apiError);
        });
}

function getLibraryDocs() {
    getLibDocsApi().getLibraryDocuments(headerParams)
        .then(function (libDocs) {
                var libDocsList = libDocs.getLibraryDocumentList();

                //Find the signing status of the agreement
                for (var i = 0; i < libDocsList.length; i++) {
                    var libDoc = libDocsList[i];
                    switch (libDoc.getName()) {
                        case "CRM_singlePageSched":
                            singlePageID = libDoc.getLibraryDocumentId();
                            break;
                        case "CRM_twoPageSched":
                            // temporarily use 2 pager for single page
                            singlePageID = libDoc.getLibraryDocumentId();
                            twoPageID = libDoc.getLibraryDocumentId();
                            break;
                        case "CRM_threePageSched":
                            threePageID = libDoc.getLibraryDocumentId();
                            break;
                        case "CRM_fourPageSched":
                            fourPageID = libDoc.getLibraryDocumentId();
                            break;
                    }
                }
        })
        .catch(function (apiError) {
                console.log(apiError);
        });

}

function tabCalc(num) {
    if (num != 0.00) {
        if (num.toString().length < 6) { 
            return "\t";
        } else if (num.toString().length < 8) {
            return "\t";
        } else {
            return "";
        }
    } else {
        return "";
    }
}

function renderSchedule(schedule) {
    var table11 = "Date\n",
        table12 = "Recurring Payment\n",
        table13 = "Admin Fee\n",
        table14 = "Effective Payment\n";
    var table21 = table22 = table23 = table24 = "",
        table31 = table32 = table33 = table34 = "",
        table41 = table42 = table43 = table44 = "";
    for (var i = 0; i < schedule.length; i++) {
        var recur = accounting.unformat(schedule[i][1]);
        var admin = accounting.unformat(schedule[i][2]);
        var effective = accounting.unformat(schedule[i][3]);
        if (i <= 46) {
                table11 = table11 + schedule[i][0] + "\n";
                table12 = table12 + accounting.formatMoney(recur) + "\n";
                table13 = table13 + accounting.formatMoney(admin) + "\n";
                table14 = table14 + accounting.formatMoney(effective) + "\n";
        } else if (i > 46 && i < 94) {
                table21 = table21 + schedule[i][0] + "\n";
                table22 = table22 + accounting.formatMoney(recur) + "\n";
                table23 = table23 + accounting.formatMoney(admin) + "\n";
                table24 = table24 + accounting.formatMoney(effective) + "\n";
        } else if (i > 93 && i < 141) {
                table31 = table31 + schedule[i][0] + "\n";
                table32 = table32 + accounting.formatMoney(recur) + "\n";
                table33 = table33 + accounting.formatMoney(admin) + "\n";
                table34 = table34 + accounting.formatMoney(effective) + "\n";
        } else {
                table41 = table41 + schedule[i][0] + "\n";
                table42 = table42 + accounting.formatMoney(recur) + "\n";
                table43 = table43 + accounting.formatMoney(admin) + "\n";
                table44 = table44 + accounting.formatMoney(effective) + "\n";
        }
    }
    return {
        'table11': table11,
        'table12': table12,
        'table13': table13,
        'table14': table14,
        'table21': table21,
        'table22': table22,
        'table23': table23,
        'table24': table24,
        'table31': table31,
        'table32': table32,
        'table33': table33,
        'table34': table34,
        'table41': table41,
        'table42': table42,
        'table43': table43,
        'table44': table44,
    };
}

function createAgreement(debtInfo, agreementForm) {
    //Create recipient set info
    var recipientInfo = new agreementsModel.RecipientInfo();
    var recipientSetInfo = new agreementsModel.RecipientSetInfo();
    var recipientSetMemberInfos = [];
    recipientInfo.setEmail(debtInfo.client.email);
    recipientSetMemberInfos.push(recipientInfo);

    recipientSetInfo.setRecipientSetMemberInfos(recipientSetMemberInfos);
    recipientSetInfo.setRecipientSetRole(agreementsModel.RecipientSetInfo.RecipientSetRoleEnum.SIGNER);
    recipientSetInfo.setRecipientSetName(debtInfo.client.name);
    
    var recipientSetInfos = [];
    recipientSetInfos.push(recipientSetInfo);

    // Get file info and create a list of file info
    var fileInfo = new agreementsModel.FileInfo();
    var fileInfos = [];
    var scheduleLength = getPaymentAmt().schedule.length;
    if (scheduleLength <= 47) {
        fileInfo.setLibraryDocumentId(singlePageID);
    } else if (scheduleLength > 47 && scheduleLength < 95) {
        fileInfo.setLibraryDocumentId(twoPageID);
    } else if (scheduleLength > 94 && scheduleLength < 142) {
        fileInfo.setLibraryDocumentId(threePageID);
    } else {
        fileInfo.setLibraryDocumentId(fourPageID);
    }
    fileInfos.push(fileInfo);

    // Create mergeFieldInfo array
    var mergeFieldInfos = [];
    // Client related fields
    mergeFieldInfos.push({'fieldName': 'clientName', 'defaultValue': debtInfo.client.name});
    mergeFieldInfos.push({'fieldName': 'clientName2', 'defaultValue': debtInfo.client.name});
    mergeFieldInfos.push({'fieldName': 'clientName3', 'defaultValue': debtInfo.client.name});
    mergeFieldInfos.push({'fieldName': 'clientName4', 'defaultValue': debtInfo.client.name});
    mergeFieldInfos.push({'fieldName': 'clientEmail1', 'defaultValue': debtInfo.client.email});
    mergeFieldInfos.push({'fieldName': 'companyName1', 'defaultValue': debtInfo.client.company_name});
    mergeFieldInfos.push({'fieldName': 'companyName2', 'defaultValue': debtInfo.client.company_name});
    mergeFieldInfos.push({'fieldName': 'clientAddress', 'defaultValue': debtInfo.client.address});
    mergeFieldInfos.push({'fieldName': 'clientPhone', 'defaultValue': debtInfo.client.primary_number});
    mergeFieldInfos.push({'fieldName': 'city_state_zip1', 'defaultValue': debtInfo.client.city + ", " + debtInfo.client.state + " " + debtInfo.client.zipcode});
    
    // Debt related fields
    mergeFieldInfos.push({'fieldName': 'creditorListName', 'defaultValue': debtInfo.debt.creditor_list});
    mergeFieldInfos.push({'fieldName': 'creditorListAmt', 'defaultValue': debtInfo.debt.creditor_list_amt});
    mergeFieldInfos.push({'fieldName': 'adminFee', 'defaultValue': debtInfo.debt.admin_fee_percent + "%"});
    mergeFieldInfos.push({'fieldName': 'serviceFee1', 'defaultValue': debtInfo.debt.service_fee_percent + "%"});
    mergeFieldInfos.push({'fieldName': 'serviceFee2', 'defaultValue': debtInfo.debt.service_fee_percent + "%"});
    mergeFieldInfos.push({'fieldName': 'originalDebt', 'defaultValue': accounting.formatMoney(parseFloat(debtInfo.debt.original_debt))});
    mergeFieldInfos.push({'fieldName': 'repayAmt', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().settlementAmt))});
    mergeFieldInfos.push({'fieldName': 'adminFeeAmt', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().adminFeeAmt))});
    mergeFieldInfos.push({'fieldName': 'settlementFeeAmt', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().serviceFeeAmt).toFixed(2))});
    mergeFieldInfos.push({'fieldName': 'progLength', 'defaultValue': debtInfo.debt.program_length + " months"});
    mergeFieldInfos.push({'fieldName': 'totalProgCost', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().totalProgAmt))});
    mergeFieldInfos.push({'fieldName': 'totalClientSavingsAmt', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().totalCustSavingAmt))});
    mergeFieldInfos.push({'fieldName': 'firstPaymentAmt', 'defaultValue': accounting.formatMoney(parseFloat(getPaymentAmt().firstPaymentAmt))});
    mergeFieldInfos.push({'fieldName': 'firstPaymentDate', 'defaultValue': debtInfo.debt.first_payment_date});
    mergeFieldInfos.push({'fieldName': 'paymentSched1-1', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table11});
    mergeFieldInfos.push({'fieldName': 'paymentSched1-2', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table12});
    mergeFieldInfos.push({'fieldName': 'paymentSched1-3', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table13});
    mergeFieldInfos.push({'fieldName': 'paymentSched1-4', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table14});
    mergeFieldInfos.push({'fieldName': 'paymentSched2-1', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table21});
    mergeFieldInfos.push({'fieldName': 'paymentSched2-2', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table22});
    mergeFieldInfos.push({'fieldName': 'paymentSched2-3', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table23});
    mergeFieldInfos.push({'fieldName': 'paymentSched2-4', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table24});
    mergeFieldInfos.push({'fieldName': 'paymentSched3-1', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table31});
    mergeFieldInfos.push({'fieldName': 'paymentSched3-2', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table32});
    mergeFieldInfos.push({'fieldName': 'paymentSched3-3', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table33});
    mergeFieldInfos.push({'fieldName': 'paymentSched3-4', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table34});
    mergeFieldInfos.push({'fieldName': 'paymentSched4-1', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table41});
    mergeFieldInfos.push({'fieldName': 'paymentSched4-2', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table42});
    mergeFieldInfos.push({'fieldName': 'paymentSched4-3', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table43});
    mergeFieldInfos.push({'fieldName': 'paymentSched4-4', 'defaultValue': renderSchedule(getPaymentAmt().schedule).table44});
    console.log(mergeFieldInfos);

    // Get document creation info        
    // Create document creation info from the file info object
    var documentCreationInfo = new agreementsModel.DocumentCreationInfo();
    documentCreationInfo.setName("AGREEMENT TRIPLE JET: " + debtInfo.client.company_name);
    documentCreationInfo.setFileInfos(fileInfos);
    documentCreationInfo.setRecipientSetInfos(recipientSetInfos);
    documentCreationInfo.setMergeFieldInfo(mergeFieldInfos);
    documentCreationInfo.setSignatureType(agreementsModel.DocumentCreationInfo.SignatureTypeEnum.ESIGN);
    documentCreationInfo.setSignatureFlow(agreementsModel.DocumentCreationInfo.SignatureFlowEnum.SENDER_SIGNATURE_NOT_REQUIRED);

    //Get agreement creation info        
    var agreementCreationInfo = new agreementsModel.AgreementCreationInfo();        
    agreementCreationInfo.setDocumentCreationInfo(documentCreationInfo);
    
    getAgreementsApi().createAgreement(headerParams, agreementCreationInfo)
        .then(function (agreementCreationResponse) {
            console.log("Agreement created for Id " + agreementCreationResponse.getAgreementId());
            $('#agreementId').val(agreementCreationResponse.getAgreementId());
            agreementForm.submit();
        })
        .catch(function (apiError) {
            console.log(apiError);
            alert("AdobeSign API Failed with message: " + apiError);
        });
}
