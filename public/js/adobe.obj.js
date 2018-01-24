var AdobeTJS = new function() {
    this.token = "3AAABLblqZhAyKTgK9RbWOKx9vaFa-7gB9eu_y75BpYjHW2fkvjPm2chupxYK6DEnpv7se6gPzMoM7ibN08LfHZ7NnuoRWFRv";
    this.context = null;
    this.agreementsApi = null;
    this.headerParams = {"accessToken": this.token};
    this.getContext = function () {
        if (this.context == null) {
            this.context = new AdobeSignSdk.Context();
        }
        return this.context;
    };
    this.getAgreementsApi = function() {
        if (this.agreementsApi == null) {
            this.agreementsApi = new AdobeSignSdk.AgreementsApi(this.getContext);
        }
        console.log(this.agreementsApi);
        return this.agreementsApi;
    };
    this.getAgreements = function(agreementsApi) {
        agreementsApi.getAgreements(this.headerParams)
            .then(function(userAgreements) {
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
    };
}
