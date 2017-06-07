app.controller('notifications', function($scope, $http, config, $interval, $window,$uibModal,$log,rosterService,$filter){
	// every 5 minutes check user has new notifications
	$interval(function (){
		$http({
	  		method: 'GET',
	  		url: config._base_url+'get/notifications/count'
		}).then(function successCallback(response) {
	   		if (response.data.status_code == 0){
	   			if ($window.sessionStorage.getItem("ntotal") > 0 && $window.sessionStorage.getItem("ntotal") <= response.data.result){
	   				$scope.todo();
	   				$('#notification-alert').addClass('notification-alert');
	   			} else {
	   				$('#notification-alert').removeClass('notification-alert');
	   			}
	   			$window.sessionStorage.setItem("ntotal",response.data.result);
	   		} else {

	   		}
	  	}, function errorCallback(response) {
	    
	  	});

	  	$scope.reports();
	}, 50000)

	// notification load angular function
	$scope.todo = function (){
		$http({
	  		method: 'GET',
	  		url: config._base_url+'get/notifications'
		}).then(function successCallback(response) {
	   		if (response.data.status_code == 0){
	   			$scope.notifications = response.data.result;
	   		} else {
	   			$scope.notifications = [];
	   		}

			if (response.data.action_items_status_code == 0){
				$scope.action_items = response.data.action_items;
			} else {
				$scope.action_items = [];
			}
	  	}, function errorCallback(response) {
	    
	  	});	
	}

	$scope.reports = function (){
		$http({
	  		method: 'GET',
	  		url: config._base_url+'get/report/notification'
		}).then(function successCallback(response) {
	   		if (response.data.status_code == 0){
	   			$scope.reportNotofication = response.data.result;
	   		} else {
	   			$scope.reportNotofication = [];
	   		}
	  	}, function errorCallback(response) {
	    
	  	});	
	}
	$scope.reports();
	$scope.todo();

	// update read notifications and remove it from the list
	$scope.readNotification = function(id, appointment_id){
		$http({
	  		method: 'GET',
	  		url: config._base_url+'notifications/'+id+'/update'
		}).then(function successCallback(response) {
	   		if (response.data.status_code == 0){
	   			$scope.todo();
	   			$('#notification-alert').removeClass('notification-alert');
				window.location.assign("/report/edit/"+appointment_id+"#/step3");
	   		} else {

	   		}
	  	}, function errorCallback(response) {
	    
	  	});	
	}

	$scope.readReportNotification = function(id){
		$http({
	  		method: 'GET',
	  		url: config._base_url+'notifications/'+id+'/update'
		}).then(function successCallback(response) {
	   		if (response.data.status_code == 0){
	   			$scope.reports();
	   		} else {

	   		}
	  	}, function errorCallback(response) {
	    
	  	});	
	}

	$scope.getRosters=function(){
		$http({
			method: 'GET',
			url: config._base_url+'get/rosters'
		}).then(function successCallback(response) {
			console.log(response)
			if (response.data.status == 1){
				$scope.rosters=response.data.result;
			} else {

			}
		}, function errorCallback(response) {

		});
	}
	$scope.getRosters();
	$http({
  		method: 'GET',
  		url: config._base_url+'appointment/list'
	}).then(function successCallback(response) {
   		if (response.data.status_code == 0){
   			$scope.appointments = response.data.result; 
   		} else {

   		}
  	}, function errorCallback(response) {
    
  	});

  	$http({
  		method: 'GET',
  		url: config._base_url+'requests/list'
	}).then(function successCallback(response) {
   		if (response.data.status_code == 0){
   			$scope.requests = response.data.result; 
   		} else {

   		}
  	}, function errorCallback(response) {
    
  	});	

  	$http({
  		method: 'GET',
  		url: config._base_url+'licenses/list'
	}).then(function successCallback(response) {
   		if (response.data.status_code == 0){
   			$scope.licenses = response.data.result; 
   		} else {

   		}
  	}, function errorCallback(response) {
    
  	});
	$scope.saveTasks=function (albumNameArray) {
		rosterService.saveTaskResults(albumNameArray).success(function (getData) {
			var msg = getData.message;
			var msg_type=(getData.success=='true')?'success':'error';
			msgAlert(msg, msg_type);
			$scope.getRosters();
		});

	}
	$scope.startTask = function (id,roster_id) {
		$http.get('get/rosters/job/task', {
				params:{
					jobId:id,
					rosterId:roster_id
				}
		}).success(function(data) {
			$scope.userTasks=data.data;
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
				var checkedCount = $filter('filter')($scope.albumNameArray.taskResults, { status: '1' }, true).length;
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
					template: '<model-task></model-task>',
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
}).directive('modelTask', function() {
	return {
		restrict: 'E',
		templateUrl: 'modelTasks.html',
		controller: function ($scope) {
			$scope.selected = {
				item: $scope.test,
				name:$scope.name
			};
		}
	};
});