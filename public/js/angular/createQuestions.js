app.controller('childQuestion', function ($scope,$http, $window, $q, $log,$location,$filter ,$timeout,$uibModal,ngClipboard,QuestionService) {
    $scope.form={};
    $scope.laws=[];
    var pathname = window.location.pathname;
    var res = pathname.split("/");
    var parent_id=res[4];
    var answer_id=res[5];
    $scope.form.licenseChoises = [];
    $scope.form.citations = [];
    $scope.form.actionItems = [];

    $scope.init = function init() {

        var promise_patent=$scope.getParent(parent_id);
        promise_patent.then(function(data) {
            $scope.parentLaw=data.parentLawType;
            $scope.countries=data.countries;
            $scope.licenses=data.masterLicenses;
            $scope.form.licenseChoises.push({licenses:$scope.licenses});
            $scope.form.citations.push({});
            $scope.form.actionItems.push({});
            $scope.states=data.states;
            $scope.cities=data.cities;
            $scope.form.category=data.parentCategory;
            $scope.auditTypes=data.auditTypes;

            angular.forEach($scope.auditTypes, function(auditTypeData){
                if(auditTypeData.name == 'In-house') {
                    auditTypeData.name = 'Self-audit';
                }
            });

            $scope.otherClassifications=data.otherClassifications;
            $scope.otherClassificationsNotRequired=data.otherClassificationsNotRequired;
            $scope.getLawTypes();
            $scope.form.law = $scope.laws[0].value;
            $scope.form.country = $scope.countries[0].id;
            $scope.form.state = $scope.states[0].id;
            $scope.form.masterAnswers = data.masterAnswers;
            $scope.form.superParentQuestionId  = data.superParentQuestionId;
            questionAnswerMapping(data.masterAnswerValue);
        })


    };

    $scope.getLawTypes = function getLawTypes() {
        $scope.laws=[];
        if($scope.parentLaw==1){
            $scope.laws = [
                { name: 'Federal', value: '1' },
            ];
        }else if ($scope.parentLaw==2){
            $scope.laws = [
                { name: 'State', value: '2' },
                { name: 'Local Jurisdiction', value: '3' }
            ];
        }else if($scope.parentLaw==3){
            $scope.laws = [
                { name: 'Local Jurisdiction', value: '3' }
            ];
        }else{
            $scope.laws = [
                { name: 'Federal', value: '1' },
                { name: 'State', value: '2' },
                { name: 'Local Jurisdiction', value: '3' }
            ];
        }
    }

    $scope.getParent=function getParent(parentId) {
        var deferred = $q.defer();
        var parent=QuestionService.getParent(parentId);
        parent.success(function (data) {
            deferred.resolve(data);
        });

        return deferred.promise;

    }

    // $scope.init();

    $scope.addNewChoice = function addNewChoice() {
        var newItemNo = $scope.form.licenseChoises.length+1;

        $scope.selectedLicense=[];

        $scope.form.licenseChoises.push({licenses:$scope.licenses});
    };

    $scope.removeChoice = function removeChoice(i) {
        var lastItem = $scope.form.licenseChoises.length-1;
        $scope.form.licenseChoises.splice(i,1);
    };
    $scope.addNewCitation = function addNewCitation() {
        $scope.form.citations.push({});


    };

    $scope.removeCitation = function removeCitation(i) {
        var lastItem = $scope.form.citations.length-1;
        $scope.form.citations.splice(i,1);
    };
    $scope.addNewActionItem = function addNewActionItem() {
        $scope.form.actionItems.push({id:'',name:''});


    };

    $scope.removeActionItem = function removeActionItem (i) {
        $scope.form.actionItems.splice(i,1);
    };

    $scope.saveChildQuestion = function saveChildQuestion(questionId) {

        $scope.data={};
        console.log($scope.form)
        var law=$scope.form.law;
        $scope.data.law=law;
        $scope.data.visibility=1;
        $scope.data.mandatory=1;
        $scope.data.country=$scope.form.country;
        $scope.data.state=$scope.form.state;
        $scope.data.mainCategory=$scope.form.category;
        $scope.data.classificationId=1;
        $scope.data.question=$scope.form.question;
        if(questionId!=undefined){
            $scope.data.questionId=questionId;
            $scope.data.answerId=$scope.answerId;
            $scope.data.parentQuestionId=$scope.parentQuestionId;
        }else {

            $scope.data.parentQuestionId=parent_id;
            $scope.data.answerId=answer_id;
        }
        $scope.data.explanation=$scope.form.explanation;
        $scope.data.citations_child=$scope.form.citations;

        $scope.data.cities=[];
        var allTag="ALL";
        if(law==3 ){
            angular.forEach($scope.form.city, function(cityData){
                if(cityData!=undefined){
                    $scope.data.cities.push(cityData.id);
                }
            });
        }else {
            $scope.data.cities.push(allTag);
        }


        $scope.data.license_type=[];
        var selectedLicense=[];

        angular.forEach($scope.form.licenseChoises, function(multiLicense){
            if(multiLicense.multi!=undefined){
                angular.forEach(multiLicense.multi, function(planData){
                    selectedLicense.push(planData.id);
                });
                $scope.data.license_type.push({val:selectedLicense})
                selectedLicense=[];
            }
        });

        $scope.data.actionItems=[];
        angular.forEach($scope.form.actionItems, function(actionItemData){
            if(actionItemData!=undefined){
                $scope.data.actionItems.push(actionItemData.name);

            }
        });


        $scope.data.audit_types=[];
        angular.forEach($scope.form.auditTypes, function(auditTypeData){
            if(auditTypeData.name == 'In-house') {
                auditTypeData.name = 'Self-audit';
            }
            if(auditTypeData!=undefined){
                $scope.data.audit_types.push(auditTypeData.id);
            }
        });

        $scope.data.req_classification=[];
        var selectedClassifications=[];
        angular.forEach($scope.otherClassifications, function(otherClassificationData){
            if(otherClassificationData.selected!=undefined){
                angular.forEach(otherClassificationData.selected, function(classificationData){
                    selectedClassifications.push(classificationData.id);
                });
                $scope.data.req_classification.push({classificationId:otherClassificationData.id,value:selectedClassifications})
                selectedClassifications=[];
            }
        });

        $scope.data.not_req_classification=[];
        var selectedNonClassifications=[];
        angular.forEach($scope.otherClassificationsNotRequired, function(otherNonClassificationData){
            if(otherNonClassificationData.selected!=undefined){
                angular.forEach(otherNonClassificationData.selected, function(classificationData){
                    selectedNonClassifications.push(classificationData.id);
                });
                $scope.data.not_req_classification.push({classificationId:otherNonClassificationData.id,value:selectedNonClassifications})
                selectedNonClassifications=[];
            }
        });


        $scope.data.answers = [];
        angular.forEach($scope.form.masterAnswers, function (item) {
            if (item.checked) {
                var tempObject = {};
                tempObject.answerOptionId = item.answerOptionId;
                tempObject.answerId = item.answerId;
                $scope.data.answers.push(tempObject);
            }
        });

        $scope.data.superParentQuestionId = $scope.form.superParentQuestionId;


        var isValidLicenceCombination = validateLicenseCombination($scope.data.license_type);
        if (isValidLicenceCombination && $scope.form.law != 1) {
            swal({
                title: "Error!",
                text: "You can't use the same license combination."
            });
            return;
        }

        var isValidAnswerValueCombination = validateAnswerValueCombination();
        if (isValidAnswerValueCombination) {
            swal({
                title: "Error!",
                text: "You can't use the same answer value."
            });
            return;
        }

        var isValidAnswerCount = isAnswersCountValidOnSubmit();
        if (!isValidAnswerCount) {
            swal({
                title: "Error!",
                text: "You should select at least two answers."
            });
            return;
        }

        if(questionId!=undefined){
            var updateChildQuestion = QuestionService.updateChildQuestion($scope.data);
            $(".splash").show();

            updateChildQuestion.success(function (data) {
                $(".splash").hide();
                if(data.success=='true'){
                    msgAlert('Child question has been successfully updated', 'success');
                    $timeout(function () {
                        var landingUrl = "/question/editQuestion/" + $scope.form.superParentQuestionId;
                        $window.location.href = landingUrl;
                    }, 1000);
                }else {
                    msgAlert(data.msg,'error');
                }

            });
            updateChildQuestion.error(function () {
                $(".splash").hide();
                msgAlert('Could not update Details',false);
            });
        }else {
            var saveChildQuestion = QuestionService.saveChildQuestion($scope.data);
            $(".splash").show();

            saveChildQuestion.success(function (data) {
                $(".splash").hide();
                if(data.success=='true'){
                    msgAlert('Child question has been successfully saved', 'success');
                    $timeout(function () {
                        var landingUrl = "/question/editQuestion/" + $scope.form.superParentQuestionId;
                        $window.location.href = landingUrl;
                    }, 1000);
                }else {
                    msgAlert(data.msg,'error');
                }

            });
            saveChildQuestion.error(function () {
                $(".splash").hide();
                msgAlert('Could not add Details','error');
            });
        }

    };


    function questionAnswerMapping(masterAnswerValue) {
        angular.forEach($scope.form.masterAnswers, function (item) {
            item.checked = true;
            item.answerId = item.id;
            var questionAnswerValues = [];
            if (item.id == '1') {
                questionAnswerValues = []
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 1 || answer.id == 2) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[0].id;

            } else if (item.id == '2') {
                questionAnswerValues = [];
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 1 || answer.id == 2) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[1].id;
            } else if (item.id == '3') {
                questionAnswerValues = [];
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 3) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[2].id;
            }
        });
    }

    function questionAnswerMappingOnEdit(masterAnswerValue) {
        angular.forEach($scope.form.masterAnswers, function (item) {
            item.answerId = item.id;
            item.checked = true;
            if (item.count != '1') {
                item.checked = false;
            }
            var questionAnswerValues = [];
            if (item.id == '1') {
                questionAnswerValues = []
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 1 || answer.id == 2) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[0].id;

            } else if (item.id == '2') {
                questionAnswerValues = [];
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 1 || answer.id == 2) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[1].id;
            } else if (item.id == '3') {
                questionAnswerValues = [];
                angular.forEach(masterAnswerValue, function (answer) {
                    if (answer.id == 3) {
                        questionAnswerValues.push(answer);
                    }
                });
                item.questionAnswerValues = questionAnswerValues;
                item.answerOptionId = masterAnswerValue[2].id;
            }

            if (item.answer_value_id != null) {
                item.answerOptionId = item.answer_value_id;
            }
        });
    }
    /**
     * Validate answers count, there should be at least 2 answers selected
     */
    $scope.validateAnswersCount = function validateAnswersCount(item) {
        var countValues = 0;
        for( var i=0; i < $scope.form.masterAnswers.length; i++ ) {
            if ($scope.form.masterAnswers[i].checked == true) {
                countValues++;
            }
        }
        if (countValues < 2) {
            swal({
                title: "Error!",
                text: "You should select at least two answers."
            });
            item.checked = true;
        }
    }

    /**
     * Validate answers count, there should be at least 2 answers selected
     */
    function isAnswersCountValidOnSubmit() {
        var countValues = 0;
        for( var i=0; i < $scope.form.masterAnswers.length; i++ ) {
            if ($scope.form.masterAnswers[i].checked == true) {
                countValues++;
            }
        }
        if (countValues < 2) {
            return false;
        }else {
            return true;
        }
    }

    /**
     * Check license combination is duplicating
     * if it duplicates more that two times it treats as validation error
     * @param allLicense
     * @returns {boolean}
     */
    function validateLicenseCombination(allLicense) {
        var licenseDuplicatingCount = 0;
        for( var i=0; i < allLicense.length; i++ ) {
            var item = allLicense[i].val;
            licenseDuplicatingCount = 0;
            for( var j=0; j < allLicense.length; j++ ) {
                var tempItem = allLicense[j].val;
                if (item.equals(tempItem, false)) {
                    licenseDuplicatingCount++;
                }
                if (licenseDuplicatingCount > 1) {
                    break;
                }
            }
            if (licenseDuplicatingCount > 1) {
                break;
            }
        }
        if (licenseDuplicatingCount > 1) {
            return true;
        }else {
            return false;
        }
    }

    function validateAnswerValueCombination() {
        var answerValueDuplicatingCount = 0;
        for (var i = 0; i < $scope.data.answers.length; i++) {
            var item = $scope.data.answers[i];
            answerValueDuplicatingCount = 0;
            for (var j = 0; j < $scope.data.answers.length; j++) {
                var tempItem = $scope.data.answers[j];
                if (tempItem.answerOptionId == item.answerOptionId) {
                    answerValueDuplicatingCount++;
                }
                if (answerValueDuplicatingCount > 1) {
                    break;
                }
            }
            if (answerValueDuplicatingCount > 1) {
                break;
            }
        }
        console.log(answerValueDuplicatingCount);
        if (answerValueDuplicatingCount > 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retreiving Child question data for edit view
     */
    $scope.getChildQuestion=function (question_id,parent_id) {
        var promise_patent=$scope.getParent(parent_id);
        promise_patent.then(function(data) {
            $scope.parentLaw=data.parentLawType;
            $scope.countries=data.countries;
            $scope.licenses=data.masterLicenses;
            $scope.states=data.states;
            $scope.cities=data.cities;
            $scope.auditTypes=data.auditTypes;
            angular.forEach($scope.auditTypes, function(auditTypeData){
                if(auditTypeData.name == 'In-house') {
                    auditTypeData.name = 'Self-audit';
                }
            });
            $scope.otherClassifications=data.otherClassifications;
            $scope.otherClassificationsNotRequired=data.otherClassificationsNotRequired;
            $scope.getLawTypes();
            $scope.form.superParentQuestionId  = data.superParentQuestionId;


            QuestionService.getChildQuestionDetails(question_id).success(function (getData) {
                $scope.form.law=getData.childLawType;
                $scope.form.state=getData.states[0].id;
                $scope.form.country=getData.countries[0].id;
                $scope.form.city=getData.cities;
                $scope.form.category=getData.parentCategory;
                $scope.form.actionItems=getData.actionItems;
                $scope.form.auditTypes=getData.auditTypes;
                angular.forEach($scope.form.auditTypes, function(auditTypeData){
                    if(auditTypeData.name == 'In-house') {
                        auditTypeData.name = 'Self-audit';
                    }
                });
                $scope.form.question=getData.question;
                $scope.form.explanation=getData.explanation;
                $scope.otherClassifications=getData.otherClassifications;
                $scope.otherClassificationsNotRequired=getData.otherClassificationsNotRequired;
                $scope.parentQuestionId=parent_id;
                $scope.answerId=getData.questionAnswerId;

                if(getData.citations_saved.length){
                    $scope.form.citations=getData.citations_saved;
                }else {
                    $scope.form.citations.push({})
                }

                angular.forEach(getData.selectedLices, function(selectedLicenseData){
                    $scope.form.licenseChoises.push({licenses:$scope.licenses,multi:selectedLicenseData});
                });
                $scope.form.masterAnswers = getData.masterAnswersSelected;
                questionAnswerMappingOnEdit(getData.masterAnswerValue);


            });
        });

    }

    $scope.removeSelectedCities=function () {
        if($scope.form.law=='3'){
            $scope.form.city=[];
        }
    }
});


app.service('QuestionService', function($http,config) {
    return {
        // get Parent Question Law type
        getParent : function(parentId) {
            return $http.get('/question/parent?parent_id='+parentId);
        },
        saveChildQuestion:function (childQuestionData) {
            return $http.post('/question/createChildQuestion',childQuestionData);

        },
        getChildQuestionDetails:function (questionId) {
            return $http.get('/question/child?question_id='+questionId);
        },
        updateChildQuestion:function (childQuestionData) {
            return $http.post('/question/updateChildQuestion',childQuestionData);

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

Array.prototype.equals = function (array, strict) {
    if (!array)
        return false;

    if (arguments.length == 1)
        strict = true;

    if (this.length != array.length)
        return false;

    for (var i = 0; i < this.length; i++) {
        if (this[i] instanceof Array && array[i] instanceof Array) {
            if (!this[i].equals(array[i], strict))
                return false;
        }
        else if (strict && this[i] != array[i]) {
            return false;
        }
        else if (!strict) {
            return this.sort().equals(array.sort(), true);
        }
    }
    return true;
};