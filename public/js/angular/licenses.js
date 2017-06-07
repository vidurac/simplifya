app.controller('createLicense', function ($scope,$http, $window, config,$q, $log,$location,LicenseCreateService, $timeout, $filter) {

    /* license form object */
    $scope.licenseForm = {};
    $scope.types = [];
    /**
     * initialize
     */
    $scope.init = function() {
        $scope.licenseForm.license_id = 0;
        $scope.enableLoading();
        LicenseCreateService.getCountries()
            .success(function(getData) {
                $scope.countries = getData;
                $scope.licenseForm.country = 1;
                $scope.getStates();
                LicenseCreateService.getApplicabilityTypes()
                    .success(function(getData) {
                        $scope.types = getData.types;
                        delete $scope.types['1'];
                        $scope.disableLoading();
                    })
                    .error(function (error) {
                        $log.error("error loading types");
                        $scope.disableLoading();
                    });
            })
            .error(function (error) {
                $log.error("error loading countries");
                $scope.disableLoading();
            });
    };

    $scope.initWithLicense = function initWithLicense(id) {
        $scope.enableLoading();
        LicenseCreateService.getCountries()
            .success(function(getData) {
                $scope.countries = getData;
                LicenseCreateService.getApplicabilityTypes()
                    .success(function(getData) {
                        $scope.types = getData.types;
                        delete $scope.types['1'];
                        var license = LicenseCreateService.getLicense(id);
                        license.success(function(data) {
                            $log.info(data);
                            $scope.licenseForm.license_name = data.data.license_name;
                            $scope.licenseForm.country = data.data.country_id;
                            $scope.licenseForm.checklist_fee = parseFloat(data.data.checklist_fee);
                            $scope.states = data.data.states;
                            $scope.licenseForm.state = data.data.state_id;
                            $scope.licenseForm.license_id  = id;
                            $scope.licenseForm.type = data.data.type.toString();
                            $scope.applicabilities = data.data.applicabilties;
                            $scope.disableLoading();
                        });
                        license.error(function (error) {
                            $scope.disableLoading();
                            $log.error("error loading license details");
                        });
                    })
                    .error(function (error) {
                        $log.error("error loading types");
                        $scope.disableLoading();
                    });
            })
            .error(function (error) {
                $scope.disableLoading();
                $log.error("error loading countries");
            });
    };

    /*
     * Get states according to particular country
     */
    $scope.getStates = function getStates() {
        $(".splash").show();
        LicenseCreateService.getStates($scope.licenseForm.country)
            .success(function(getData) {
                $scope.states = getData.data.master_states;
                $(".splash").hide();
            })
            .error(function (error) {
                $log.error("error loading states");
                $(".splash").hide();
            });
    };

    /**
     * Save license function
     * @param valid
     */
    $scope.saveLicense = function saveLicense(valid) {
        $scope.isSubmitted = true;
        if (!valid) { $scope.isSubmitted = false; return; }
        $(".splash").show();

        var selecteedApplicabilitiesObjects = $filter('filter')($scope.applicabilities, { checked: true }, true);
        var selectedApplicabilityIds = [];
        if (selecteedApplicabilitiesObjects.length) {
            angular.forEach(selecteedApplicabilitiesObjects, function (item) {
                selectedApplicabilityIds.push(item.id)
            })

        }
        $log.info(selectedApplicabilityIds);
        $scope.licenseForm.applicability_ids = selectedApplicabilityIds;
        $log.info($scope.licenseForm);

        if ($scope.licenseForm.license_id == 0) {
            var saveLicense = LicenseCreateService.saveLicense($scope.licenseForm);
            saveLicense.success(function(getData) {
                $(".splash").hide();
                $scope.isSubmitted = false;
                msgAlert(getData.message, 'success');
                $timeout(function () {
                    $window.location.href = '/configuration/licenses'
                }, 1000);
            });
            saveLicense.error(function (error) {
                $log.error("error loading states");
                $(".splash").hide();
                $scope.isSubmitted = false;
            });
        }else {
            var updateLicense = LicenseCreateService.updateLicense($scope.licenseForm);
            updateLicense.success(function(getData) {
                $(".splash").hide();
                $scope.isSubmitted = false;
                msgAlert(getData.message, 'success');
                $timeout(function () {
                    $window.location.href = '/configuration/licenses'
                }, 1000);
            });
            updateLicense.error(function (error) {
                $log.error("error loading states");
                $(".splash").hide();
                $scope.isSubmitted = false;
            });
        }
    };

    $scope.getApplicabilityDetails = function getApplicabilityDetails() {
        var applicabilities = LicenseCreateService.getApplicabilityByStateAndCountries($scope.licenseForm.type, $scope.licenseForm.country);
        applicabilities.success(function(getData) {
            $log.info("applicabilities details");
            $log.info(getData.data);
            $scope.applicabilities = getData.data;
            angular.forEach($scope.applicabilities, function (item) {
               item.checked = false;
            });
            $(".splash").hide();
        });
        applicabilities.error(function (error) {
            $log.error("error loading states");
            $(".splash").hide();
        });

    };

    $scope.enableLoading = function enableLoading() {
        $(".splash").show();
        $scope.isLoading = true;
    };

    $scope.disableLoading = function disableLoading() {
        $(".splash").hide();
        $scope.isLoading = false;
    };

});

app.service('LicenseCreateService', function($http,config) {
    return {
        getCountries:function () {
            return $http.get('/get/countryList');
        },
        getStates: function(countryId) {
            return $http({
                url: '/question/getStates',
                method: "GET",
                params: {countryId: countryId}
            });
        },
        getLicense: function(id) {
            return $http({
                url: '/get/license/'+id,
                method: "GET",
                params: {}
            });
        },
        saveLicense: function (data) {
            return $http.post('/add/license/type', data);
        },
        updateLicense: function (data) {
            return $http.post('/change/license/type', data);
        },
        getApplicabilityTypes:function () {
            return $http.get('/get/applicability/types');
        },
        getApplicabilityByStateAndCountries:function (type_id, country_id) {
            return $http({
                url: '/license/getApplicabilityByStateAndCountries/'+type_id+'/'+country_id,
                method: "GET",
                params: {}
            });
        },
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