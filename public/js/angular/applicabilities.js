
app.controller('createApplicability', function ($scope,$http, $window, config,$q, $log,$location,ApplicabilityService,$uibModal) {

    $scope.applicabilityId='';

	$scope.init = function() {

        $scope.group=[
            { name: 'Federal', value: '1' },
            { name: 'State', value: '2' },
        ]

        ApplicabilityService.getCountries()
            .success(function(getData) {
                $scope.countries = getData;
            });
        ApplicabilityService.getApplicabilityTypesAndGroups()
            .success(function(getData) {
                console.log(getData)
                $scope.types = getData.types;
                $scope.groups = getData.groups;
            });

	};

    $scope.saveApplicability=function (valid) {
        if (!valid) {
            return;
        }

        console.log($scope.applicabilityId);
        var data=angular.copy($scope.form)
        if($scope.applicabilityId!=''){
            data.id=$scope.applicabilityId;

        }
        $scope.savingApplicability=true;

        // Save Applicability entry
        var applicabilityCreate = ApplicabilityService.saveApplicability(data);

        // success response
        applicabilityCreate.success(function (getData) {
            if (getData.success == 'true') {
                msgAlert(getData.message, 'success');
                $window.location.href = '/configuration/applicability'
            }
            if (getData.success == 'false') {
                msgAlert(getData.message, 'error');
            }
            $scope.savingApplicability=false;
        });

        // // failure response
        applicabilityCreate.error(function (error) {
            msgAlert(error.message, 'error');
            $scope.savingApplicability=false;
        });


    }

    $scope.getApplicability = function getApplicability(id) {
        ApplicabilityService.getCountries()
            .success(function(getData) {
                $scope.countries = getData;
            });
        ApplicabilityService.getApplicabilityTypesAndGroups()
            .success(function(getData) {
                console.log(getData)
                $scope.types = getData.types;
                $scope.groups = getData.groups;
                ApplicabilityService.getApplicabilityById(id).success(function (getData) {
                    var applicabilityData=getData.data;
                    $scope.applicabilityId=applicabilityData.id;
                    $scope.form={
                        name:applicabilityData.name,
                        type:applicabilityData.type.toString(),
                        country:applicabilityData.country_id,
                        group:applicabilityData.group_id.toString()

                    };
                });
            });
    };







}).directive('rosterAssignment', function() {
	return {
		restrict: 'E',
		templateUrl: 'rosterAssignment.html',
		controller: function ($scope) {
			$scope.selected = {
				item: $scope.test,
				name:$scope.name
			};
		}
	};
}).directive('rosterJobs', function() {
	return {
		restrict: 'E',
		templateUrl: 'modelJobs.html',
		controller: function ($scope) {
			$scope.selected = {
				item: $scope.test,
				name:$scope.name
			};
		}
	};
}).filter('pagination', function()
{
	return function(input, start) {
		start = parseInt(start, 10);
		return input.slice(start);
	};
});

app.controller('ApplicabilityCtrl', function ($scope,$http, $window, $q, $log,$location, $timeout,ApplicabilityService) {


    $scope.init = function() {
        ApplicabilityService.getAllApplicabilities()
            .success(function(getData) {
               console.log(getData)
                $scope.applicabilities = getData.data;
            });

    };


    $scope.deleteApplicability = function deleteApplicability(applicability_id){
            console.log(applicability_id)
        swal({
                title: "Are you sure?",
                text: "Sure you want to delete?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if(isConfirm == true)
                {
                    ApplicabilityService.deleteApplicability(applicability_id)
                        .success(function(getData) {

                            if(getData.success == "false")
                            {
                                msgAlert(getData.message, 'error');
                            }
                            else
                            {
                                msgAlert(getData.message, 'success');
                                $scope.init();
                            }
                        });
                }
                if (isConfirm != true) {
                    return false;
                }
            });
    };


    $scope.changeStatus = function changeStatus(applicability_id,status){
        var data={
            'id':applicability_id,
            'status':status
        }

        ApplicabilityService.changeApplicabilityStatus(data)
            .success(function(getData) {

                if(getData.success == "false")
                {
                    msgAlert(getData.message, 'error');
                }
                else
                {
                    msgAlert(getData.message, 'success');
                    $scope.init();
                }
            });

    };

    $scope.init();
});


app.service('ApplicabilityService', function($http,config) {
    return {
        getApplicabilityTypesAndGroups:function () {
            return $http.get('/get/applicability/types');
        },

        getCountries:function () {
            return $http.get('/get/countryList');
        },
        saveApplicability : function(data) {
            return $http.post('/configuration/applicability/create', data);
        },
        getAllApplicabilities:function () {
            return $http.get('/configuration/applicabilities');

        },
        getApplicabilityById:function (id) {
            return $http.get('/configuration/applicabilities/applicability-id/'+id);
        },
        //delete applicability
        deleteApplicability : function(id){
            return $http.post('/configuration/applicability/delete/'+id);
        },

        //change status of applicability
        changeApplicabilityStatus : function(data){
            return $http.post('/configuration/applicability/status/change',data);
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