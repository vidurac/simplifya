/**
 * Created by harhsa on 3/3/17.
 */


app.controller('KeywordListCtrl', function ($scope,$http, $window, $q, $log,$location, $timeout,KeywordService, config) {

    $scope.pageSize = '10'
    $scope.sortType     = 'name'; // set the default sort type
    $scope.sortReverse  = false;  // set the default sort order
    $scope.entryOptions=['10','25','50','100'];
    $scope.pageSize= $scope.entryOptions[0];
    
    $scope.init = function() {
        KeywordService.getAllKeywords()
            .success(function(getData) {
                $scope.keywords = getData.data;
            });

    };

    $scope.keywordDelete = function (keyWordId) {


        swal({
                title: "Are you sure?",
                text: "This keyword no longer will be available in the system!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    var questionKeyword = KeywordService.getQuestionKeyword(keyWordId);

                    questionKeyword.success(function (getData) {
                        if(getData.success == 'true'){
                            swal("Deleted!", "keyword has been deleted.", "success");
                            KeywordService.deleteKeywordsById(keyWordId)
                                .success(function(result) {
                                    $scope.init();

                                })

                        }else if(getData.success == 'false'){
                            swal("Error", "Keyword is already added to the question", "error");
                        }
                    })

                } else {
                    swal("Cancelled", "keyword details are safe :)", "error");
                }
            });


    };

    $scope.saveKeywordListener = function (newValue) {

        $timeout(function(){
            var keywordData = angular.copy(newValue);
            var data = {'id': newValue.id, 'tempName': keywordData.tempName};
            var updatedKeyword = KeywordService.updateKeyword(data);

            // successy response
            updatedKeyword.success(function (getData) {
                if (getData.success == 'true') {
                    msgAlert(getData.message, 'success');
                }
                if (getData.success == 'false') {
                    msgAlert(getData.message, 'error');
                    $scope.init();
                }
            });

            // // failure response
            updatedKeyword.error(function (error) {
                msgAlert(error.message, 'error');
                $scope.init();
            });
        },1000)

    };

    $scope.init();
});

app.service('KeywordService', function($http,config) {
    return {

        // get all keywords
        getAllKeywords : function(data) {
            return $http.get('/configuration/get/keywords');
        },

        //delete keywords by id
        deleteKeywordsById : function(data) {
            return $http.get('/configuration/delete/keywords/'+data);
        },

        updateKeyword : function (data) {
            return $http.post('/configuration/edit/keywords', data);
        },

        getQuestionKeyword : function (data) {
            return $http.get('/configuration/get/question/keyword/'+data);
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