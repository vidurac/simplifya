app.filter('isEmpty', function () {
    var bar;
    return function (obj) {
        for (bar in obj) {
            if (obj.hasOwnProperty(bar)) {
                return false;
            }
        }
        return true;
    };
});

app.filter("jsDate", function () {
    return function (x) {
        return new Date(x);
    };
});

app.controller('globalHeader', function($scope, $http, config, $interval, $window){
	// every 5 minutes check user has new notifications
	$window.sessionStorage.setItem("ntotal", 0);
	$http({
  		method: 'GET',
  		url: config._base_url+'get/notifications/count'
	}).then(function successCallback(response) {
   		if (response.data.status_code == 0){
   			if ($window.sessionStorage.getItem("ntotal") > 0 && $window.sessionStorage.getItem("ntotal") <= response.data.result){
   				$('#notification-alert').addClass('notification-alert');
   			} else {
   				$('#notification-alert').removeClass('notification-alert');
   			}
   			$window.sessionStorage.setItem("ntotal", response.data.result);
   		} else {

   		}
  	}, function errorCallback(response) {

  	});
});
// html filter (render text as html)
app.filter('html', ['$sce', function ($sce) {
	return function (text) {
		return $sce.trustAsHtml(text);
	};
}]);

app.directive('postsPagination', function(){
	return{
		restrict: 'E',
		template: '<ul class="pagination dataTables_paginate paging_simple_numbers" style="float: right">'+
		'<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getPosts(1)">&laquo;</a></li>'+
		'<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="getPosts(currentPage-1)">&lsaquo; Prev</a></li>'+
		'<li ng-repeat="i in range" ng-class="{active : currentPage == i}">'+
		'<a href="javascript:void(0)" ng-click="getPosts(i)">{{i}}</a>'+
		'</li>'+
		'<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getPosts(currentPage+1)">Next &rsaquo;</a></li>'+
		'<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="getPosts(totalPages)">&raquo;</a></li>'+
		'</ul>'
	};
});

/**
 * Compliance answer icon
 */
app.directive('complianceState', function(){
	return {
		restrict: 'EA',
		scope: {
			title: '=myText'
		},
		link: function(scope, element, attrs) {
			if (attrs.value == 'Non-Compliant') {
				element.addClass('comp non-comp bg-danger');
			}else if (attrs.value == 'Compliant') {
				element.addClass('comp comp-comp bg-success');
			}else if (attrs.value == 'Unknown Compliance ')  {
				element.addClass('comp unknown-comp bg-warning');
			}
		}
	}
});

app.controller('questionIndex', function ($scope, $http, $window, $q, $log,ngTreetableParams,$timeout) {


	var data = [];
	$scope.questionSearch = {
		questionName: '',
		keywords: '',
		status: '',
		display: '',
		sort:'desc',
		sortType:'created_at'
	};
	$scope.entryOptions=['10','25','50','100','250','500'];
	$scope.questionListLoaded = false;
	$scope.sortReverseName=true;
	$scope.sortReverseId=false;
	$scope.sortReverseCreated=false;
	$scope.sortReverseUpdated=false;
	$scope.sortReverseCreatedAt=false;
	$scope.sortReverseIsDraft=false;
	$scope.sortType='created_at';
	$scope.sort=='desc';

	$scope.init = function() {
		$(".splash").show();
		userHistory();

	};

	// runs once per controller instantiation
	$scope.init();

	var getList=function getList(pageNumber,entries) {
		var paramData = {};
		$(".splash").show();
		if ($scope.questionSearch.keywords.length > 0) {
			paramData = {
				questionName: $scope.questionSearch.questionName,
				'keywords[]': (undefined != $scope.questionSearch.keywords)? $scope.questionSearch.keywords : '',
				status: $scope.questionSearch.status,
				display: $scope.questionSearch.display,
				sort:$scope.questionSearch.sort,
				sortType:$scope.questionSearch.sortType
			};
		}else {
			paramData = {
				questionName: $scope.questionSearch.questionName,
				keywords: '',
				status: $scope.questionSearch.status,
				display: $scope.questionSearch.display,
				sort:$scope.questionSearch.sort,
				sortType:$scope.questionSearch.sortType
			};
		}
		if(pageNumber==false){
			$url='/questions/all?entries='+entries
		}else{
			$url='/questions/all?page='+pageNumber
		}
		$http.get($url, {
			params:  paramData
		}).success(function(data) {
			$scope.dataTree = data.data.data;
			$scope.expanded_params.refresh();

			$scope.questionListLoaded = true;
			$scope.total = data.data.total;
			$scope.totalPages   = data.data.last_page;
			$scope.currentPage  = data.data.current_page;
			$scope.to  = data.data.to;
			$scope.from  = data.data.from;
			// Pagination Range
			var pages = [];

			for(var i=1;i<=data.data.last_page;i++) {
				pages.push(i);
			}

			$scope.range = pages;
			$(".splash").hide();

		});
	};


	var getSearchedList=function getSearchedList(pageNumber) {
		var paramData = {};
		$log.debug($scope.questionSearch.keywords);
		if ($scope.questionSearch.keywords.length > 0) {
			 paramData = {
				questionName: $scope.questionSearch.questionName,
				'keywords[]': (undefined != $scope.questionSearch.keywords)? $scope.questionSearch.keywords : '',
				status: $scope.questionSearch.status,
				display: $scope.questionSearch.display,
				 sort:$scope.questionSearch.sort,
				 sortType:$scope.questionSearch.sortType
			};
		}else {
			paramData = {
				questionName: $scope.questionSearch.questionName,
				keywords: '',
				status: $scope.questionSearch.status,
				display: $scope.questionSearch.display,
				sort:$scope.questionSearch.sort,
				sortType:$scope.questionSearch.sortType
			};
		}
		$(".splash").show();
		$http.get('/questions/all', {
			params: paramData
		}).success(function(data) {
			$scope.dataTree = data.data.data;
			$scope.expanded_params.refresh();
			$scope.questionListLoaded = true;
			$scope.total = data.data.total;
			$scope.totalPages   = data.data.last_page;
			$scope.currentPage  = data.data.current_page;
			$scope.to  = data.data.to;
			$scope.from  = data.data.from;
			// Pagination Range
			var pages = [];

			for(var i=1;i<=data.data.last_page;i++) {
				pages.push(i);
			}

			$scope.range = pages;
			$(".splash").hide();

		});
	}

	function userHistory() {
		$(".splash").show();
		var paramData={};
		$http.get('/question/getUserHistory').success(function(data) {
			var lastQuestion=data.data[0];
			var page=data.data[1];
			var entries=data.data[2];
			if(data.data!=''){
				searchHistoryFields(data.data);
				$scope.sortQuestionsHistory(((null != data.data[8])?data.data[8]:'created_at'),((null != data.data[7])?data.data[7]:'asc'));
				$url='/questions/all?entries='+entries+'&page='+page;
				if (null != data.data[4]) {
					paramData = {
						questionName: (null != data.data[3])?data.data[3] : '',
						'keywords[]': (null != data.data[4])? data.data[4].split(',') : '',
						status: (null != data.data[5])?data.data[5] : '',
						display: (null != data.data[6])?data.data[6] : '',
						sort:(null != data.data[7])?data.data[7]:'desc',
						sortType:(null != data.data[8])?data.data[8]:'created_at'
					};
				}else {
					paramData = {
						questionName: (null != data.data[3])?data.data[3] : '',
						keywords: '',
						status: (null != data.data[5])?data.data[5] : '',
						display: (null != data.data[6])?data.data[6] : '',
						sort:(null != data.data[7])?data.data[7]:'desc',
						sortType:(null != data.data[8])?data.data[8]:'created_at'
					};
				}
			}else{
				$url='/questions/all';
				paramData=$scope.questionSearch;
				console.log(paramData)
			}
				$http.get($url, {
					params:  paramData
				}).success(function(data) {
					$scope.dataTree = data.data.data;
					$scope.questionListLoaded = true;
					if ($scope.expanded_params == undefined) {
						$scope.expanded_params = new ngTreetableParams({
							getNodes: function(parent) {
								return parent ? parent.children : $scope.dataTree;
							},
							getTemplate: function(node) {
								return 'tree_node';
							}
						});

					}
					$scope.expanded_params.refresh();
					$scope.total = data.data.total;
					$scope.totalPages   = data.data.last_page;
					$scope.entries=(data.data.per_page).toString();
					$scope.currentPage  = data.data.current_page;
					$scope.to  = data.data.to;
					$scope.from  = data.data.from;
					// Pagination Range
					var pages = [];

					for(var i=1;i<=data.data.last_page;i++) {
						pages.push(i);
					}

					$scope.range = pages;
					$("#questionKeywords").select2();
					$(".splash").hide();
					if(lastQuestion!=null){
						scrollToLastQuestion(lastQuestion)
					}

				});

		});
	}
	$scope.sortQuestions=function (type,val) {
		$scope.questionSearch.sort=val;
		$scope.questionSearch.sortType=type;
		console.log(type)
		console.log(val)
		switch (type) {
			case 'question':
					$scope.sortReverseName=(val=='desc')?false:true;
					$scope.sortType='question';
				break;
			case 'created_by':
					$scope.sortReverseCreated=(val=='desc')?true:false;
					$scope.sortType='created_by';
				break;
			case 'updated_by':
					$scope.sortReverseUpdated=(val=='desc')?true:false;
					$scope.sortType='updated_by';
				break;
			case 'created_at':
					$scope.sortReverseCreatedAt=(val=='desc')?true:false;
					$scope.sortType='created_at';
				break;
			case 'is_draft':
					$scope.sortReverseIsDraft=(val=='desc')?true:false;
					$scope.sortType='is_draft';
				break;
            case 'id':
					$scope.sortReverseId=(val=='desc')?true:false;
					$scope.sortType='id';
				break;
			default:
		}

		userHistory();
	}

	$scope.sortQuestionsHistory=function (type,val) {
		$scope.questionSearch.sort=val;
		$scope.questionSearch.sortType=type;
		switch (type) {
			case 'question':
				$scope.sortReverseName=(val=='desc')?false:true;
				$scope.sortType='question';
				break;
			case 'created_by':
				$scope.sortReverseCreated=(val=='desc')?true:false;
				$scope.sortType='created_by';
				break;
			case 'updated_by':
				$scope.sortReverseUpdated=(val=='desc')?true:false;
				$scope.sortType='updated_by';
				break;
			case 'created_at':
				$scope.sortReverseCreatedAt=(val=='desc')?true:false;
				$scope.sortType='created_at';
				break;
			case 'is_draft':
				$scope.sortReverseIsDraft=(val=='desc')?true:false;
				$scope.sortType='is_draft';
				break;
            case 'id':
                $scope.sortReverseId=(val=='desc')?true:false;
                $scope.sortType='id';
                break;
			default:
		}

	}
	function searchHistoryFields(userData) {
		if(null != userData[3]){
			$scope.questionSearch.questionName=userData[3];
		}
		if(null != userData[4]){
			$scope.questionSearch.keywords=userData[4].split(',');


		}if(null != userData[5]){
			$scope.questionSearch.status=userData[5];

		}
		if(null != userData[6]){
			$scope.questionSearch.display=userData[6];

		}
	}


	$scope.getPosts=function getPosts(pageNumber) {
		$scope.questions='';
		getList(pageNumber,false);
	};
	$scope.getSearchedPosts=function getSearchedPosts() {

		$scope.questions='';
		$log.debug($scope.questionSearch.keywords)
		getSearchedList();
	};

	$scope.getQuestions=function () {
		$log.debug($scope.entries);
		$http.post('/questions/updateUserPagination', {
				entries:$scope.entries
		}).success(function(data) {
			$log.debug(data);
			getList(false,$scope.entries);
		});
	};



})

app.service('couponService', function($http,config) {
	return {
		// get all the tasks
		getCoupon : function() {
			return $http.get('/roster/list/task/');
		}
	}
});

app.service('rosterService', function($http,config) {
	return {
		// get all the Rosters
		get : function() {
			return $http.get('/roster/list/all');
		},

		// get all the tasks
		getTask : function(rosterId) {
			return $http.get('/roster/list/task/',{params:rosterId});
		},

		// save a task
		saveTask : function(taskData) {
			return $http.post('/roster/list/task/add',taskData)
		},
		// destroy a task
		destroyTask : function(id) {
			return $http.post('/roster/list/task/delete', id);
		},

		getCompanyUser:function(){
			return $http.get('/roster/users');
		},
		assignTask:function (taskData) {
			return $http.post('/roster/list/task/assign', taskData);
		},
		saveTaskResults:function (taskData) {
			return $http.post(config._base_url+'rosters/job/task/save', taskData);
		},
		//get all jobs
		getJobs:function() {
			return $http.get('/roster/job/all');
		},
		getAssignees:function () {
			return $http.get('/roster/assignees/all');

		},
		getAssignment:function (assignmentId) {
			return $http.get(config._base_url+'get/roster/assignee',{params:assignmentId});

		},
		updateTask:function (taskData) {
			return $http.post('/roster/assignee/update', taskData);
		},
		saveRoster:function (data) {
			return $http.post('/roster/create', data);
		},
		getRosterCount:function (id) {
			return $http.get(config._base_url+'get/roster/count',{params:id});

		},
		destroyRoster:function (id) {
			return $http.post('/roster/delete', id);

		},
        getRosterAssignee:function (rosterId) {
			return $http.get('/roster/assignee',{params:rosterId});

        },
        getRosterJobs:function (rosterId) {
			return $http.get('/roster/job',{params:rosterId});

        }
	}
});

app.directive('uiSelectRequired', function () {
	return {
		require: 'ngModel',
		link: function (scope, element, attr, ctrl) {
			ctrl.$validators.uiSelectRequired = function (modelValue, viewValue) {
				if (attr.uiSelectRequired) {
					var isRequired = scope.$eval(attr.uiSelectRequired)
					if (isRequired == false)
						return true;
				}
				var determineVal;
				if (angular.isArray(modelValue)) {
					determineVal = modelValue;
				} else if (angular.isArray(viewValue)) {
					determineVal = viewValue;
				} else {
					return false;
				}
				return determineVal.length > 0;
			};
		}
	};
});


