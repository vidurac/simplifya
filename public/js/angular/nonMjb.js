app.controller('createNonMjb', function ($scope,$http, $window, $q, $log,$location, $timeout,NonMjbService) {
    $scope.form={};
    $scope.companyId='';
    $scope.companyLocationId='';
    $scope.init = function() {

        $scope.getCountries();
        $scope.disableAddmore=true;
        $("#phone_no").mask("(999) 999-9999")
    };

    $scope.getCountries=function () {
        var deferred = $q.defer();
        NonMjbService.getCountries()
            .success(function(getData) {
                $scope.countries = getData.data;
                deferred.resolve();
            });
        return deferred.promise;
    }

    $scope.getStates=function () {
        $scope.form.choices = [{licenses:[]}];
        var deferred = $q.defer();
        if($scope.form.country!=undefined){
            NonMjbService.getStates({countryId:$scope.form.country}).success(function (getData) {
                $scope.states=getData.data.master_states;
                deferred.resolve();
            });
        }else {
                $scope.states=[];
                $scope.cities=[];

        }
        return deferred.promise;

    }

    $scope.getStatesMain=function () {
        $scope.getStates();
    }
    
    $scope.getCitiesAndLicenses=function () {
        var deferred = $q.defer();
        var data={stateId:$scope.form.state}
        if($scope.form.state!=undefined){
            NonMjbService.getCities(data).success(function (getData) {
                $scope.cities=getData.data.master_city;
                NonMjbService.getLicences(data).success(function (getData) {
                    $scope.licenses=getData.data.master_license;
                    $scope.form.choices[0].licenses=$scope.licenses;
                    deferred.resolve();
                });
            });
            

        }else {
            $scope.cities=[];
            $scope.licenses=[];

        }

        return deferred.promise;
    }

    $scope.getCitiesAndLicensesMain=function () {
        $scope.getCitiesAndLicenses();
    }
    $scope.enableAddmore=function () {
        angular.forEach($scope.form.choices, function(planData){
            if(planData.license!=undefined){
                $scope.disableAddmore=false;
            }else {
                $scope.disableAddmore=true;
            }



        });
    }
    $scope.form.choices = [{licenses:[]}];

    $scope.addNewChoice = function() {
        var newItemNo = $scope.form.choices.length+1;
        $scope.disableAddmore=true;

        $scope.selectedLicense=[];

        angular.forEach($scope.form.choices, function(planData){
            if(planData.license!=undefined){
                $scope.selectedLicense.push(planData.license);
            }


        });

        var tempLicenses = angular.copy($scope.licenses);

        if(tempLicenses!=undefined){
            for (var i = tempLicenses.length - 1; i >= 0; i--) {

                for (var j = $scope.selectedLicense.length - 1; j >= 0; j--) {

                    if (tempLicenses[i] != undefined && tempLicenses[i].id == $scope.selectedLicense[j]) {
                        tempLicenses.splice(i,1)
                    }
                }
            }
        }
        $scope.form.choices.push({licenses:tempLicenses,license:undefined});
    };

    $scope.removeChoice = function(i,choice) {
        var lastItem = $scope.form.choices.length-1;
        $scope.form.choices.splice(i,1);
        if(choice.license!=undefined){

            $scope.selectedLicense.splice($.inArray(choice.license, $scope.selectedLicense),1);

        }
        $scope.disableAddmore=false;
    };

    $scope.saveNonMjb=function (valid) {
        if (!valid) {
            return;
        }
        if($scope.companyId!='') {
            $scope.form.company_id = $scope.companyId;
        }
        if($scope.companyLocationId!='') {
            $scope.form.company_location_id = $scope.companyLocationId;
        }

        var saveNonMjb = NonMjbService.saveNonMjb($scope.form);

        saveNonMjb.success(function (data) {
            if(data.success=='true'){
                $window.location.href = '/appointment/create?manage=4&id='+data.company_id;
            }else {
                msgAlert('Could not add Details',false);
            }

        });
    }

    $scope.getTempMjb=function (company_id) {
        NonMjbService.gettempMjbDetails(company_id).success(function (getData) {

            var companyData=getData.data.company;
            $scope.companyId=companyData.company_id;
            $scope.companyLocationId=companyData.company_location_id;


            var promise_country=$scope.getCountries();
            promise_country.then(function() {
                $scope.form.country=companyData.country;
                var promise_state=$scope.getStates();
                promise_state.then(function () {
                    $scope.form.state=companyData.state;
                    var promise_city=$scope.getCitiesAndLicenses();
                    promise_city.then(function () {
                        $scope.form={
                            name_of_business:companyData.name_of_business,
                            add_line_1:companyData.address_line_1,
                            add_line_2:companyData.address_line_2,
                            country:companyData.country,
                            state:companyData.state,
                            city:companyData.city,
                            zip_code:companyData.zip_code,
                            phone_no:companyData.phone_no,
                            contact_person:companyData.contact_person,
                            contact_email:companyData.contact_email,
                            choices:companyData.choices
                        }
                        angular.forEach($scope.form.choices, function(planData){
                            if(planData.license!=undefined){
                                planData.licenses=$scope.licenses;
                            }else {
                            }

                        });
                    });

                });


            });

        });
    }
});



app.service('NonMjbService', function($http,config) {
    return {
        getCountries:function () {
            return $http.get('/appointment/nonmjb/countries');

        },
        getStates:function (country_id) {
            return $http.get('/appointment/nonmjb/states',{params:country_id});

        },
        getCities:function (state_id) {
            return $http.get('/appointment/nonmjb/cities',{params:state_id});

        },
        getLicences:function (state_id) {
            return $http.get('/appointment/nonmjb/licenses',{params:state_id});

        },
        saveNonMjb:function (nonMjbData) {
            return $http.post('/appointment/nonmjb/create',nonMjbData);

        },
        gettempMjbDetails:function (companyid) {
            return $http.get('/appointment/nonmjb/get/'+companyid);
        }
    }
});


/**
 * Show notification message function
 * @param msg
 * @param msg_type
 */
function msgAlert(msg, msg_type) {
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown"
    };
    if(msg_type == 'success') {
        toastr.success(msg);
    } else if(msg_type == 'error') {
        toastr.error(msg);
    }

}