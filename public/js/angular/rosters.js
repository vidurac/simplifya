
app.controller('rosterTaskList', function ($scope,$http, $window, config,$q, $log,$location,rosterService,$uibModal) {

	$scope.sortTypeAssignee     = 'name'; // set the default sort type
	$scope.sortReverseAssignee  = false;  // set the default sort order
	$scope.sortTypeJobs     = 'name'; // set the default sort type
	$scope.sortReverseJobs  = false;  // set the default sort order

	$scope.init = function() {
		$scope.rosterId=$location.absUrl().split('/')[6];
		rosterService.getTask({rosterId:$scope.rosterId})
			.success(function(getData) {

				$scope.tasks = getData.data;
				$scope.getAssigneeJobs($location.absUrl().split('/')[6])
				$scope.loading = false;
			});

	};
	$scope.init();

	$scope.getAssigneeJobs=function (rosterId) {
		rosterService.getRosterAssignee({rosterId:rosterId}).success(function (getData) {
			$scope.rosterAssignees=getData.data;
		});
		rosterService.getRosterJobs({rosterId:rosterId}).success(function (getData) {
			$scope.rosterJobs=getData.data;
		});

	}

	// function to handle submitting the form
	// SAVE A Roster Task ================
	$scope.submitTask = function(valid) {
		if (!valid) {
			return;
		}

		// flag used to handle saving operation
		// idea is to `disable` button click soon after user clicks submit.
		$scope.savingRosterTask = true;

		$scope.taskData={
			rosterId:$scope.rosterId,
			taskName:$scope.taskName
		}
		var rosterTaskCreate = rosterService.saveTask($scope.taskData);
		
		rosterTaskCreate.success(function(data) {
			$scope.taskName='';
			$scope.rosterTaskAssignForm.$setPristine();
			rosterService.getTask({rosterId:$scope.rosterId})
				.success(function(getData) {
					$scope.tasks = getData.data;
					$scope.loading = false;
				});
			var msg = data.message;
			var msg_type=(data.success=='true')?'success':'error';
			msgAlert(msg, msg_type);
			$scope.savingRosterTask = false;

		});

		// failure response
		rosterTaskCreate.error(function (error) {
			msgAlert(error.message, 'error');
			$scope.savingRosterTask=false;
		});
	};



	// function to handle deleting a task
	// DELETE A Task ====================================================
	$scope.deleteTask = function(id) {
		$scope.taskData={
			rosterTaskId:id,
		}
		// use the function we created in our service
		rosterService.destroyTask($scope.taskData)
			.success(function(data) {
				// if successful, we'll need to refresh the comment list
				rosterService.getTask({rosterId:$scope.rosterId})
					.success(function(getData) {
						$scope.tasks = getData.data;
						$scope.loading = false;
					});
				var msg = data.message;
				var msg_type=(data.success=='true')?'success':'error';
				msgAlert(msg, msg_type);

			});
	};


	$scope.showAssignee = function (id) {
		console.log(id)
		$scope.form = {};
		$scope.form.rosterId = id;
		$scope.frequecies = [
			{ name: 'Daily', value: '1' },
			{ name: 'Weekly', value: '7' },
			{ name: 'Bi-weekly', value: '14' },
			{ name: 'Semi-monthly', value: '15' },
			{ name: 'Monthly', value: '30' }
		];
		rosterService.getAssignment({assignId:id}).success(function (getData) {
			$scope.users=getData.data;
			$scope.form={
				id:$scope.users[0].id,
				userName:$scope.users[0].name,
				selectedFrequency:$scope.users[0].frequency.toString(),
				dtStart:$scope.users[0].start_date,
				dtEnd:$scope.users[0].end_date
			}
		})

		var modalInstance;
		var modalScope = $scope.$new();
		modalScope.update = function () {
			rosterService.updateTask($scope.form).success(function (getData) {
				var msg = getData.message;
				var msg_type=(getData.success=='true')?'success':'error';
				msgAlert(msg, msg_type);
				$scope.init();
			});
			modalInstance.close(modalScope.selected);
		};
		modalScope.cancel = function () {
			modalInstance.dismiss('cancel');
		};

		modalInstance = $uibModal.open({
				template: '<roster-assignment></roster-assignment>',
				scope: modalScope
			}
		);

		modalInstance.result.then(function (selectedItem) {
			$scope.selected = selectedItem;
		}, function () {
			$log.info('Modal dismissed at: ' + new Date());
		});
	};

	$scope.showJobs = function (id,roster_id,job_status) {

		$http.get(config._base_url+'get/rosters/job/task', {
			params:{
				jobId:id,
				rosterId:roster_id
			}
		}).success(function(data) {
			$scope.userTasks=data.data;
			$scope.jobStatus=job_status;
			var modalInstance;
			var modalScope = $scope.$new();
			modalScope.save = function () {

				$scope.albumNameArray = {};
				$scope.albumNameArray.taskResults = [];
				angular.forEach($scope.userTasks, function(userTask){
					var userData=[];
					var status=0;
					userData={
						rosterTaskId:userTask.rosters_task_id,
						jobId:userTask.job_id,
						status:userTask.status
					}
					$scope.albumNameArray.taskResults.push(userData);
				});
				rosterService.saveTaskResults($scope.albumNameArray).success(function (getData) {
					var msg = getData.message;
					var msg_type=(getData.success=='true')?'success':'error';
					msgAlert(msg, msg_type);
				});
				modalInstance.close(modalScope.selected);
			};

			modalScope.complete=function () {
				$scope.albumNameArray = {};
				$scope.albumNameArray.type = 'complete';
				$scope.albumNameArray.jobId = id;
				$scope.albumNameArray.taskResults = [];
				angular.forEach($scope.userTasks, function(userTask){
					var userData=[];
					var status=0;
					userData={
						rosterTaskId:userTask.rosters_task_id,
						jobId:userTask.job_id,
						status:userTask.status
					}
					$scope.albumNameArray.taskResults.push(userData);
				});
				var totalCount=$scope.albumNameArray.taskResults.length;
				var checkedCount = $filter('filter')($scope.albumNameArray.taskResults, { status: 1 }, true).length;
				if(totalCount==checkedCount){
					$scope.saveTasks($scope.albumNameArray);
					modalInstance.close(modalScope.selected);
				}else {
					swal({
							title: "Please confirm",
							text: "You are going to Finish the task before completing",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#F8BB86",
							confirmButtonText: "Confirm!",
							cancelButtonText: "Cancel!",
							closeOnConfirm: true,
							closeOnCancel: true },
						function (isConfirm) {
							if (isConfirm) {
								$scope.saveTasks($scope.albumNameArray);
								modalInstance.close(modalScope.selected);
							}
						});
				}

			}
			modalScope.cancel = function () {
				modalInstance.dismiss('cancel');
			};

			modalInstance = $uibModal.open({
					template: '<model-jobs></model-jobs>',
					scope: modalScope
				}
			);

			modalInstance.result.then(function (selectedItem) {
				$scope.selected = selectedItem;
			}, function () {
				$log.info('Modal dismissed at: ' + new Date());
			});

		});


	};


	$scope.dateOptions = {
		startingDay: 1
	};
	$scope.toggleMin = function() {
		var tomorrow = new Date($scope.form.dtStart);
		tomorrow.setDate(tomorrow.getDate() + 1);
		$scope.minDate =  tomorrow;

	};
	$scope.open1 = function() {
		$scope.popup1.opened = true;
	};

	$scope.open2 = function() {
		$scope.popup2.opened = true;
		$scope.toggleMin();
	};

	$scope.open3 = function() {
		$scope.popup3.opened = true;
	};
	$scope.open4 = function() {
		$scope.popup4.opened = true;
	};


	$scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'yyyy-MM-dd'];
	$scope.format = $scope.formats[4];
	$scope.altInputFormats = ['M!/d!/yyyy'];

	$scope.popup1 = {
		opened: false
	};

	$scope.popup2 = {
		opened: false
	};
	$scope.popup3 = {
		opened: false
	};
	$scope.popup4 = {
		opened: false
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

app.controller('rosterList', function ($scope,$http, $window, $q, $log,$location,rosterService,$uibModal) {
	$scope.init = function() {
		rosterService.get()
			.success(function(getData) {
				$scope.rosters = getData.data;
			});
		rosterService.getAssignees()
			.success(function(getData) {
				$scope.rosterAssignees = getData.data;
			});

		rosterService.getJobs()
			.success(function(getData) {
				$scope.rosterJobs = getData.data;
				$scope.userGroup=getData.userGroup;

			});
	};
	$scope.init();

	$scope.assignRoster = function (id,name) {

		$scope.name = name;
		$scope.form = {};
		$scope.form.rosterId = id;
		$scope.frequecies = [
			{ name: 'Daily', value: '1' },
			{ name: 'Weekly', value: '7' },
			{ name: 'Bi-weekly', value: '14' },
			{ name: 'Semi-monthly', value: '15' },
			{ name: 'Monthly', value: '30' }
		];
		$scope.form.selectedFrequency = $scope.frequecies[0].value;
		rosterService.getCompanyUser().success(function (getData) {
			$scope.users=getData.data;
			$scope.form.userId = $scope.users[0].value;
		})

		var modalInstance;
		var modalScope = $scope.$new();
		modalScope.ok = function (valid) {
			if (!valid) {
				return;
			}

			// flag used to handle saving operation
			// idea is to `disable` button click soon after user clicks submit.
			$scope.savingRosterAssign = true;
			var rosterAssignTask=rosterService.assignTask($scope.form);
			console.log($scope.form.dtEnd)
			rosterAssignTask.success(function (getData) {
				var msg = getData.message;
				var msg_type=(getData.success=='true')?'success':'error';
				msgAlert(msg, msg_type);
				$scope.savingRosterAssign = false;
			});

			// failure response
			rosterAssignTask.error(function (error) {
				msgAlert(error.message, 'error');
				$scope.savingRosterAssign = false;
			});

			modalInstance.close(modalScope.selected);
		};
		modalScope.cancel = function () {
			modalInstance.dismiss('cancel');
		};

		modalInstance = $uibModal.open({
				template: '<my-modal></my-modal>',
				scope: modalScope
			}
		);

		modalInstance.result.then(function (selectedItem) {
			$scope.selected = selectedItem;
		}, function () {
			$log.info('Modal dismissed at: ' + new Date());
		});
	};
	$scope.deleteRoster = function (id) {
        $scope.rosterData={
            rosterId:id,
        }

        var rosterCount=rosterService.getRosterCount($scope.rosterData);

		rosterCount.success(function (getCount) {
			if (getCount.data > 0) {
				swal({
						title: "Please confirm",
						text: "You are going to Remove an already assign checklist",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#F8BB86",
						confirmButtonText: "Confirm!",
						cancelButtonText: "Cancel!",
						closeOnConfirm: true,
						closeOnCancel: true },
					function (isConfirm) {
						if (isConfirm) {

							rosterService.destroyRoster($scope.rosterData)
								.success(function(data) {
									var msg = data.message;
									var msg_type=(data.success=='true')?'success':'error';
									msgAlert(msg, msg_type);
									$scope.init();
								});
						}
					});

			}else{
				// use the function we created in our service
				rosterService.destroyRoster($scope.rosterData)
					.success(function(data) {
						var msg = data.message;
						var msg_type=(data.success=='true')?'success':'error';
						msgAlert(msg, msg_type);
						$scope.init();
					});
			}
		});

	};

	// $scope.dateOptions = {
	// 	dateDisabled: disabled,
	// 	formatYear: 'yy',
	// 	maxDate: new Date(2020, 5, 22),
	// 	minDate: new Date(),
	// 	startingDay: 1
	// };
	$scope.toggleMin = function() {
		// $scope.minDate = $scope.minDate ? null : new Date();
		var tomorrow = new Date();
		tomorrow.setDate(tomorrow.getDate() + 1);
		$scope.minDate = tomorrow;

	};
	$scope.open1 = function() {
		$scope.popup1.opened = true;
		$scope.toggleMin();
	};

	$scope.open2 = function() {
		$scope.popup2.opened = true;
		$scope.toggleMinPop2();
	};


	$scope.toggleMinPop2 = function() {
		var tomorrow = new Date($scope.form.dtStart);
		tomorrow.setDate(tomorrow.getDate() + 1);
		$scope.minDatePop2 = tomorrow;
	};

	$scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'yyyy-MM-dd'];
	$scope.format = $scope.formats[4];
	$scope.altInputFormats = ['M!/d!/yyyy'];

	$scope.popup1 = {
		opened: false
	};

	$scope.popup2 = {
		opened: false
	};

	$scope.submitRoster = function submitRoster(valid) {

		if (!valid) {
			return;
		}

		// flag used to handle saving operation
		// idea is to `disable` button click soon after user clicks submit.
		$scope.savingRoster = true;

		// Save roster entity
		var rosterCreate = rosterService.saveRoster({name: $scope.rosterName});

		// success response
		rosterCreate.success(function (getData) {
			if (getData.success == 'true') {
				msgAlert(getData.message, 'success');
				$scope.rosterName = '';
				$scope.rosterTaskForm.$setPristine();
				$scope.init();
			}
			$scope.savingRoster=false;
		});

		// failure response
		rosterCreate.error(function (error) {
			msgAlert(error.message, 'error');
			$scope.savingRoster=false;
		});
	};

    $scope.createRoster = function () {
        $scope.form = {};
        $scope.form.rosterName = '';

        var modalInstance;
        var modalScope = $scope.$new();
        modalScope.submitRoster = function (valid) {
            if (!valid) {
                return;
            }

            // flag used to handle saving operation
            // idea is to `disable` button click soon after user clicks submit.
            $scope.form.savingRoster = true;

            // Save roster entity
            var rosterCreate = rosterService.saveRoster({name: $scope.form.rosterName});

            // success response
            rosterCreate.success(function (getData) {
                if (getData.success == 'true') {
                    msgAlert(getData.message, 'success');
					$scope.form.rosterName = '';
					modalInstance.dismiss('cancel');
                    $scope.init();
                }
                $scope.form.savingRoster=false;
            });

            // failure response
            rosterCreate.error(function (error) {
                msgAlert(error.message, 'error');
                $scope.form.savingRoster=false;
            });
            console.log($scope.form.rosterName);
        };
        modalScope.cancel = function () {
            modalInstance.dismiss('cancel');
        };

        modalInstance = $uibModal.open({
                template: '<roster-modal></roster-modal>',
                scope: modalScope
            }
        );

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });
    };

}).directive('myModal', function() {
	return {
		restrict: 'E',
		templateUrl: 'myModalContent.html',
		controller: function ($scope) {
			$scope.selected = {
				item: $scope.test,
				name:$scope.name
			};
		}
	};
}).directive('rosterModal', function() {
    return {
        restrict: 'E',
        templateUrl: 'rosterModalContent.html',
        controller: function ($scope) {
            $scope.selected = {
                // item: $scope.test,
                // name:$scope.name
            };
        }
    };
}).directive("csDateToIso", function () {

	var linkFunction = function (scope, element, attrs, ngModelCtrl) {

		ngModelCtrl.$parsers.push(function (datepickerValue) {
			return moment(datepickerValue).format("YYYY-MM-DD");
		});
	};

	return {
		restrict: "A",
		require: "ngModel",
		link: linkFunction
	};
});

app.controller('rosterJobs', function($scope, $http, config, $interval, $window,$log,$filter,$uibModal,rosterService){

	$scope.sortTypeJobs     = 'name'; // set the default sort type
	$scope.sortReverseJobs  = false;  // set the default sort order

	$scope.init = function() {
		rosterService.getJobs()
			.success(function(getData) {
				$scope.rosterJobs = getData.data;
				$scope.userGroup=getData.userGroup;

			});

	};
	$scope.init();

	$scope.saveTasks=function (albumNameArray) {
		rosterService.saveTaskResults(albumNameArray).success(function (getData) {
			var msg = getData.message;
			var msg_type=(getData.success=='true')?'success':'error';
			msgAlert(msg, msg_type);
			$scope.init();
		});

	}
	$scope.showJobs = function (id,roster_id,job_status) {

		$http.get(config._base_url+'get/rosters/job/task', {
			params:{
				jobId:id,
				rosterId:roster_id
			}
		}).success(function(data) {
			$scope.userTasks=data.data;
			$scope.jobStatus=job_status;
			var modalInstance;
			var modalScope = $scope.$new();
			modalScope.save = function () {

				$scope.albumNameArray = {};
				$scope.albumNameArray.taskResults = [];
				angular.forEach($scope.userTasks, function(userTask){
					var userData=[];
					var status=0;
					userData={
						rosterTaskId:userTask.rosters_task_id,
						jobId:userTask.job_id,
						status:userTask.status
					}
					$scope.albumNameArray.taskResults.push(userData);
				});
				rosterService.saveTaskResults($scope.albumNameArray).success(function (getData) {
					var msg = getData.message;
					var msg_type=(getData.success=='true')?'success':'error';
					msgAlert(msg, msg_type);
				});
				modalInstance.close(modalScope.selected);
			};
			
			modalScope.complete=function () {
				$scope.albumNameArray = {};
				$scope.albumNameArray.type = 'complete';
				$scope.albumNameArray.jobId = id;
				$scope.albumNameArray.taskResults = [];
				angular.forEach($scope.userTasks, function(userTask){
					var userData=[];
					var status=0;
					userData={
						rosterTaskId:userTask.rosters_task_id,
						jobId:userTask.job_id,
						status:userTask.status
					}
					$scope.albumNameArray.taskResults.push(userData);
				});
				var totalCount=$scope.albumNameArray.taskResults.length;
				var checkedCount = $filter('filter')($scope.albumNameArray.taskResults, { status: 1 }, true).length;
				if(totalCount==checkedCount){
						$scope.saveTasks($scope.albumNameArray);
						modalInstance.close(modalScope.selected);
				}else {
					swal({
							title: "Please confirm",
							text: "You are going to Finish the task before completing",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#F8BB86",
							confirmButtonText: "Confirm!",
							cancelButtonText: "Cancel!",
							closeOnConfirm: true,
							closeOnCancel: true },
						function (isConfirm) {
							if (isConfirm) {
								$scope.saveTasks($scope.albumNameArray);
								modalInstance.close(modalScope.selected);
							}
						});
				}

			}
			modalScope.cancel = function () {
				modalInstance.dismiss('cancel');
			};

			modalInstance = $uibModal.open({
					template: '<model-jobs></model-jobs>',
					scope: modalScope
				}
			);

			modalInstance.result.then(function (selectedItem) {
				$scope.selected = selectedItem;
			}, function () {
				$log.info('Modal dismissed at: ' + new Date());
			});

		});


	};
}).directive('modelJobs', function() {
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





app.controller('rosterAssignees', function ($scope,$http, $window, $q, $log,$location,rosterService,$uibModal) {
	$scope.init = function() {
		rosterService.getAssignees()
			.success(function(getData) {
				$scope.rosterAssignees = getData.data;
			});

	};
	$scope.init();
	$scope.sortType     = 'name'; // set the default sort type
	$scope.sortReverse  = false;  // set the default sort order

	$scope.showAssignee = function (id) {
		$scope.form = {};
		$scope.form.rosterId = id;
		$scope.frequecies = [
			{ name: 'Daily', value: '1' },
			{ name: 'Weekly', value: '7' },
			{ name: 'Bi-weekly', value: '14' },
			{ name: 'Semi-monthly', value: '15' },
			{ name: 'Monthly', value: '30' }
		];
		rosterService.getAssignment({assignId:id}).success(function (getData) {
			$scope.users=getData.data;
			$scope.form={
				id:$scope.users[0].id,
				userName:$scope.users[0].name,
				selectedFrequency:$scope.users[0].frequency.toString(),
				dtStart:$scope.users[0].start_date,
				dtEnd:$scope.users[0].end_date
			}
		})

		var modalInstance;
		var modalScope = $scope.$new();
		modalScope.update = function () {
			rosterService.updateTask($scope.form).success(function (getData) {
				var msg = getData.message;
				var msg_type=(getData.success=='true')?'success':'error';
				msgAlert(msg, msg_type);
				$scope.init();
			});
			modalInstance.close(modalScope.selected);
		};
		modalScope.cancel = function () {
			modalInstance.dismiss('cancel');
		};

		modalInstance = $uibModal.open({
				template: '<model-assignment></model-assignment>',
				scope: modalScope
			}
		);

		modalInstance.result.then(function (selectedItem) {
			$scope.selected = selectedItem;
		}, function () {
			$log.info('Modal dismissed at: ' + new Date());
		});
	};

	$scope.dateOptions = {
		startingDay: 1
	};
	$scope.toggleMin = function() {
		var tomorrow = new Date($scope.form.dtStart);
		tomorrow.setDate(tomorrow.getDate() + 1);
		$scope.minDate =  tomorrow;

	};
	$scope.open1 = function() {
		$scope.popup1.opened = true;
	};

	$scope.open2 = function() {
		$scope.popup2.opened = true;
	$scope.toggleMin();
	};

	$scope.open3 = function() {
			$scope.popup3.opened = true;
	};
	$scope.open4 = function() {
			$scope.popup4.opened = true;
	};


	$scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'yyyy-MM-dd'];
	$scope.format = $scope.formats[4];
	$scope.altInputFormats = ['M!/d!/yyyy'];

	$scope.popup1 = {
		opened: false
	};

	$scope.popup2 = {
		opened: false
	};
	$scope.popup3 = {
		opened: false
	};
	$scope.popup4 = {
		opened: false
	};



}).directive('modelAssignment', function() {
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
}).directive("csDateToIso", function () {

	var linkFunction = function (scope, element, attrs, ngModelCtrl) {

		ngModelCtrl.$parsers.push(function (datepickerValue) {
			return moment(datepickerValue).format("YYYY-MM-DD");
		});
	};

	return {
		restrict: "A",
		require: "ngModel",
		link: linkFunction
	};
}).filter('pagination', function()
{
	return function(input, start) {
		start = parseInt(start, 10);
		return input.slice(start);
	};
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

