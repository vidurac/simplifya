// Reports -> Questions Tab
app.controller('questionsList', function($scope, $http, config, $filter, $rootScope, $timeout, $sanitize){

   $scope.questions = [];
   $scope.questionCategories = [];
   var responseData = [];
   var responseData2 = [];
   $scope.activeMenu = '';
   $scope.mainQuestionsList = [];
   
   $scope.compliantCount = [];
   $scope.nonCompliantCount = [];
   $scope.unknownCompliantCount = [];
   
   $scope.setActive = function(menuItem) {
      $scope.activeMenu = menuItem;
   };
   
   $scope.setCompliantCount = function (dataSet, cat_id){
      if(cat_id === ''){
         $scope.compliantCount = $filter('filter')(dataSet, { answer_value_id: '1' }, true);
      }else{
         $scope.compliantCount = $filter('filter')(dataSet, { answer_value_id: '1', category_id: cat_id }, true);
      }
   };
   
   $scope.setNonCompliantCount = function (dataSet, cat_id){
      if(cat_id === ''){
         $scope.nonCompliantCount = $filter('filter')(dataSet, { answer_value_id: '2' }, true);
      }else{
         $scope.nonCompliantCount = $filter('filter')(dataSet, { answer_value_id: '2', category_id: cat_id }, true);
      }
   };
   
   $scope.setUnknownCompliantCount = function (dataSet, cat_id){
      if(cat_id === ''){
         $scope.unknownCompliantCount = $filter('filter')(dataSet, { answer_value_id: '3' }, true);
      }else{
         $scope.unknownCompliantCount = $filter('filter')(dataSet, { answer_value_id: '3', category_id: cat_id }, true);
      }
   };
   
   $scope.$watch('appmnt_id', function () {
      $http({
         method: 'GET',
         url: config._base_url+'report/getAppointmentReportData/'+$scope.appmnt_id+'/'+1
      }).then(function successCallback(response) {
         $scope.questionCategories = response.data.categories;
         $scope.questions = response.data.questions;
         responseData = response.data.questions;
      },
      function errorCallback(response) {

      });

       $http({
           method: 'GET',
           url: config._base_url+'report/getAppointmentReportData/'+$scope.appmnt_id+'/'
       }).then(function successCallback(response) {
               responseData2 = response.data.questions;
           },
           function errorCallback(response) {

           });
   });

   // Filter by category
   $scope.categoryFilter = function(cat_id){
      
      this.setActive(cat_id);
      if(cat_id === ''){
         $scope.questions = responseData;
         this.setCompliantCount($scope.mainQuestionsList, '');
         this.setNonCompliantCount($scope.mainQuestionsList, '');
         this.setUnknownCompliantCount($scope.mainQuestionsList, '');
      }else{
         $scope.questions = $filter('filter')(responseData, { category_id: cat_id }, true);
         this.setCompliantCount($scope.mainQuestionsList, cat_id);
         this.setNonCompliantCount($scope.mainQuestionsList, cat_id);
         this.setUnknownCompliantCount($scope.mainQuestionsList, cat_id);
      }
   };
   
   $rootScope.$watch('mainQuestionsList', function () {
      $scope.mainQuestionsList = $rootScope.mainQuestionsList;
      $scope.setCompliantCount($rootScope.mainQuestionsList, '');
      $scope.setNonCompliantCount($rootScope.mainQuestionsList, '');
      $scope.setUnknownCompliantCount($rootScope.mainQuestionsList, '');
   });
   // Add comment to question
   $scope.addQuestionComment = function (question_id, appointment_id){
       var validator = $( "#addQuestionCommentForm" ).validate();
       validator.resetForm();

       $('#qComment').removeClass('error');

       if($('#qComment').val() == "" || $('#qComment').val() == null){
           setTimeout($('#remove_qcomment_btn').addClass('show_remove_btn'), 10);
           $('#remove_qcomment_btn').remove();
       }else{
           setTimeout($('#remove_qcomment_btn').addClass('remove_remove_btn'), 10);
       }

      $('#addQuestionCommentModel').modal('show');
      //assign values to elements
      $('#question_id').val(question_id);
      $('#appointment_id').val(appointment_id);
      var question_comment = $('.answer-'+question_id).html();
      $('#qComment').val('');
      $('#qComment').val(question_comment);
      $('#add-edit-comment').text((question_comment != '') ? 'Edit':'Add');
   };

   $scope.getComplianceRate = function getComplianceRate(cat_id) {
       var dataSet = responseData2;
       var compliantCount = $filter('filter')(dataSet, { answer_value_id: '1', category_id: cat_id }, true);
       var nonCompliantCount = $filter('filter')(dataSet, { answer_value_id: '2', category_id: cat_id }, true);
       var unknownCompliantCount = $filter('filter')(dataSet, { answer_value_id: '3', category_id: cat_id }, true);
       var pct = (compliantCount.length/(compliantCount.length+nonCompliantCount.length+unknownCompliantCount.length))*100;
       if (isNaN(pct)) {
           pct = 0;
       }else {
           // console.log("pct: " + pct);
       }
       return pct;
   };

   $scope.getAllComplianceRate = function getAllComplianceRate() {
            var dataSet = responseData2;
            var compliantCount = $filter('filter')(dataSet, { answer_value_id: '1' }, true);
            var nonCompliantCount = $filter('filter')(dataSet, { answer_value_id: '2' }, true);
            var unknownCompliantCount = $filter('filter')(dataSet, { answer_value_id: '3' }, true);
            var pct = (compliantCount.length/(compliantCount.length+nonCompliantCount.length+unknownCompliantCount.length))*100;
            if (isNaN(pct)) {
                pct = 0;
            }
            return pct;
    };

    $scope.getCategoryWiseComplianceRate = function getCategoryWiseComplianceRate(compliantCountLength,
                                                                                  compliantCountLength,
                                                                                  nonCompliantCountLength,
                                                                                  unknownCompliantCountLength
    ) {
        var pct = (compliantCountLength/(compliantCountLength+nonCompliantCountLength+unknownCompliantCountLength))*100
        if (isNaN(pct)) {
            pct = 0;
        }
        return pct;
    }

    /**
     * Expand/Collapse question accordion
     * @param expand
     */
    $scope.changeExpand = function changeExpand(expand) {
        console.log(expand);
        $scope.questions.map(function(item){
            item.open = expand;
        });
    }

    $scope.saveNoteListener = function saveNoteListener(newValue) {
        $timeout(function(){
            var questionData = angular.copy(newValue);
            var objectData = {
                comment: questionData.tempComment,
                question_id: newValue.question_id,
                appointment_id: newValue.appointment_id
            };
            $(".splash").show();
            $http.get(config._base_url+'/report/edit/questions/comment/store',{params: objectData}
            ).then(function successCallback(response) {
                // Request completed successfully
                $(".splash").hide();

                angular.forEach( $scope.questions, function (item) {
                    if (item.question_id == newValue.question_id) {
                        item.comment = questionData.tempComment;
                    }
                });

            }, function errorCallback(error) {
                // Request error
                $(".splash").hide();
            });

        },1000);
    };
});

// Reports -> 'I dont know' tab
app.controller('unknownComplianceCtrl', function($scope, $http, config, $filter, $rootScope){
    $scope.questions = [];
    $scope.questionCategories = [];
    $scope.activeMenu = '';
    var responseData = [];

    $scope.$watch('appmnt_id', function () {
      $http({
         method: 'GET',
         url: config._base_url+'report/getAppointmentReportData/'+$scope.appmnt_id
      }).then(function successCallback(response){
          $scope.questionCategories = response.data.categories;
          $rootScope.mainQuestionsList = response.data.questions;
          $scope.questions = $filter('filter')(response.data.questions, { answer_value_id: '3' }, true);
          responseData = $filter('filter')(response.data.questions, { answer_value_id: '3' }, true);
      },
      function errorCallback(response){
         console.error('Ajax Error!');
      });	
   });

   // Add comment to unknown compliant question
   $scope.addQuestionComment = function (question_id, appointment_id){

       var validator = $( "#addQuestionCommentForm" ).validate();
       validator.resetForm();

       $('#qComment').removeClass('error');

       if($('#qComment').val() == "" || $('#qComment').val() == null){
           setTimeout($('#remove_qcomment_btn').addClass('show_remove_btn'), 10);
           $('#remove_qcomment_btn').remove();
       }else{
           setTimeout($('#remove_qcomment_btn').addClass('remove_remove_btn'), 10);
       }

      $('#addQuestionCommentModel').modal('show');
      //assign values to elements
      $('#question_id').val(question_id);
      $('#appointment_id').val(appointment_id);
      var question_comment = $('.answer-'+question_id).html();
      $('#qComment').val('');
      $('#qComment').val(question_comment);
      $('#add-edit-comment').text((question_comment != '') ? 'Edit':'Add');

       $('#remove_qcomment_btn').css('display', 'none');

       if($('#qComment').val() == "" || $('#qComment').val() == null){
           setTimeout($('#remove_qcomment_btn').addClass('show_remove_btn'), 10);
           $('#remove_qcomment_btn').remove();
       }else{
           setTimeout($('#remove_qcomment_btn').addClass('remove_remove_btn'), 10);
       }
   };

    // Filter by category
    $scope.categoryFilter = function(cat_id){

        this.setActive(cat_id);
        if(cat_id === ''){
            $scope.questions = responseData;
        }else{
            $scope.questions = $filter('filter')(responseData, { category_id: cat_id }, true);
        }
    };

    $scope.setActive = function(menuItem) {
        $scope.activeMenu = menuItem;
    };

    /**
     * Expand/Collapse questions accordion
     * @param expand
     */
    $scope.changeExpand = function changeExpand(expand) {
        console.log(expand);
        $scope.questions.map(function(item){
            item.open = expand;
        });
    }

});