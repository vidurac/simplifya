app.controller('createReferral', function ($scope,$http, $window, $q, $log,$location,$filter ,$timeout,$uibModal,ReferralService,ngClipboard) {
    $scope.form={};
    $scope.form.plan_details=[];
    $scope.subscriptionPlan={};
    $scope.types=[];
    $scope.referrerId='';
    $scope.commissions=[];

    $scope.sortTypeCommission = 'referral_payment_id'; // set the default sort type
    $scope.sortReverseCommission = false;  // set the default sort order
    $scope.sortTypePayment     = 'created_at'; // set the default sort type
    $scope.sortReversePayment  = true;  // set the default sort order


    $scope.init = function() {

        $scope.getPlans();
        var promise=$scope.getTypes();
        promise.then(function() {
            // $scope.form.type = $scope.types[0].value;
        })

    };

    $scope.getTypes=function () {
        var deferred = $q.defer();
        $scope.types = [
            { name: 'Sales Person', value: 'salesperson' },
            { name: 'Partner', value: 'partner' },
            { name: 'Contractor', value: 'contractor' },
            { name: 'Business', value: 'business' }
        ];
        deferred.resolve();
        return deferred.promise;

    }



    $scope.getPlans=function () {
        ReferralService.getSubscriptionPlans($scope.form.mjbEntityType).success(function (getData) {
            angular.forEach(getData.data, function(planData){
                var subscriptions=[];
                subscriptions={
                    name:planData.name,
                    value:planData.validity_period_id,
                    id:planData.id,
                    type:'fixed',
                    plan_amount:planData.amount
                };

                $scope.form.plan_details.push(subscriptions);
            });
        });
    }

    $scope.saveReferrer=function (valid) {
        if (!valid) {
            return;
        }

        var data=angular.copy($scope.form)
        if($scope.referrerId!=''){
            data.id=$scope.referrerId;

        }
        $scope.savingReferrer=true;
        // Save Coupon entry
        var referrerCreate = ReferralService.saveReferrer(data);

        // success response
        referrerCreate.success(function (getData) {
            if (getData.success == 'true') {
                msgAlert(getData.message, 'success');
                $window.location.href = '/configuration/referrals'
            }
            if (getData.success == 'false') {
                msgAlert(getData.message, 'error');
            }
            $scope.savingReferrer=false;
        });

        // // failure response
        referrerCreate.error(function (error) {
            msgAlert(error.message, 'error');
            $scope.savingReferrer=false;
        });

    }

    // Get referrer Data
    $scope.getReferrer=function (id) {
        ReferralService.getReferrerDetails(id).success(function (getData) {
            var referrerData=getData.data.referrer;

            var promise=$scope.getTypes();
            promise.then(function() {
                $scope.referrerId=referrerData.id;
                $scope.form={
                    name:referrerData.name,
                    email:referrerData.email,
                    code:referrerData.code,
                    token:referrerData.token,
                    plan_details:referrerData.plan_details,
                    code_details:referrerData.referrer_code_details

                }
                angular.forEach($scope.types, function (item) {
                    if (referrerData.type == item.value) {
                        $scope.form.type = item.value;
                    }
                });

            });


        });

    }

    //Get referrer commissions

    $scope.getCommissions=function(id){

        ReferralService.getReferrerCommissions(id).success(function (getData) {
            $scope.hidePay=false;
            var commissionData=getData.data.commissions;

            $scope.commissions=commissionData;
            var totalCount=$scope.commissions.length;
            var checkedCount = $filter('filter')($scope.commissions, { status: '1' }, true).length;
            if(totalCount==checkedCount){$scope.hidePay=true}

        });





    }

    //Get referrer Payments

    $scope.getPayments=function (id) {
        ReferralService.getReferrerPayments(id).success(function (getData) {
            var paymentData=getData.data.commission_payments;

            $scope.payments=paymentData;

        });
    }

    $scope.saveCommissions=function (commissionsToPay,id) {
        ReferralService.saveReferrerCommissions(commissionsToPay).success(function (getData) {
            var msg = getData.message;
            var msg_type=(getData.success=='true')?'success':'error';
            msgAlert(msg, msg_type);
        }).error(function (error) {
            msgAlert('Could not Create the Payment Note', 'error');
        });
            $scope.getCommissions(id);
            $scope.getPayments(id);

    }
    $scope.startPayment = function (id) {

        var modalInstance;
        var modalScope = $scope.$new();
        modalScope.paymentDetails = {note:''};
        $scope.commissionsToPay = {};
        $scope.commissionsToPay.master_referral_id=id;
        $scope.commissionsToPay.commissions = [];

        var total=0;
        angular.forEach($scope.commissions, function(commission){
            if(commission.referral_payment_id==0 && commission.status==1 ){
                referrerCommissionData={
                    company_subscription_id:commission.company_subscription_id,
                    commission_amount:commission.commission,
                    mjb_name:commission.mjb_name
                }
                total+=parseFloat(commission.commission);
                $scope.commissionsToPay.commissions.push(referrerCommissionData);
            }

        });

        if($scope.commissionsToPay.commissions.length>0){
            modalInstance = $uibModal.open({
                    template: '<model-payments></model-payments>',
                    scope: modalScope
                }
            );

            modalInstance.result.then(function (selectedItem) {
                $scope.selected = selectedItem;
            }, function () {
                $log.info('Modal dismissed at: ' + new Date());
            });
        }else {
            swal("Check at least one Due commission")
        }

        $scope.commissionsToPay.amount = total.toFixed(2);


        modalScope.complete=function () {
            $scope.commissionsToPay.comment = modalScope.paymentDetails.note;
            $scope.saveCommissions($scope.commissionsToPay,id);
            modalInstance.close('closed');
        }
        modalScope.cancel = function () {
            modalInstance.dismiss('cancel');
        };







    };


    $scope.toClipboard=function(token){
        var url=window.location.origin + '/company/mjb-register/'+token;

        ngClipboard.toClipboard(url);
    }

    $scope.dynamicPopover = {
        templateUrl: 'myPopoverTemplate.html',
        title: 'Get Link'
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
}).directive('popover', function($compile){
    return {
        restrict : 'A',
        link : function(scope, elem){

            var content = $("#popover-content").html();
            var compileContent = $compile(content)(scope);
            var title = $("#popover-head").html();
            var options = {
                content: compileContent,
                html: true,
                title: title

            };


            $(elem).popover(options);
        },
        controller:function($scope) {
        },
    }
}).directive('popoverClose', function($timeout){
    return{
        scope: {
            excludeClass: '@'
        },
        link: function(scope, element, attrs) {
            var trigger = document.getElementsByClassName('trigger');

            function closeTrigger(i) {
                $timeout(function(){
                    angular.element(trigger[0]).trigger('click').removeClass('trigger');
                });
            }

            element.on('click', function(event){
                var etarget = angular.element(event.target);
                var tlength = trigger.length;

                if(!etarget.hasClass('trigger') && !etarget.hasClass(scope.excludeClass)) {
                    for(var i=0; i<tlength; i++) {
                        closeTrigger(i)
                    }
                }
            });
        }
    };
}).directive('popoverElem', function(){
    return{
        link: function(scope, element, attrs) {
            element.on('click', function(){
                element.addClass('trigger');
            });
        }
    };
}).directive("discount", function() {
    return {
        restrict: "A",

        require: "ngModel",

        link: function(scope, element, attributes, ngModel) {
            var subscription_amount=parseFloat(attributes.discount);
            ngModel.$validators.discount = function(modelValue) {
                if (modelValue) {
                    return subscription_amount >= modelValue
                }
                return true;
            }
        }
    };
}).directive('modelPayments', function() {
    return {
        restrict: 'E',
        templateUrl: 'modelPayments.html',
        controller: function ($scope) {
        }
    };
});


app.service('ReferralService', function($http,config) {
    return {
        // get all subscriptions for MJB
        getSubscriptionPlans : function(mjbEntityType) {
            return $http.get('/company/subscriptionPlans?entity_type='+mjbEntityType);
        },
        // save Coupons
        saveReferrer : function(data) {
            return $http.post('/configuration/referrals/create', data);
        },
        // get all the tasks
        getAllCoupons : function() {
            return $http.get('/configuration/coupons/all');
        },
        getReferrerDetails:function (id) {
            return $http.get('/configuration/referrals/referrer/'+id);
        },
        //get add referrer commission
        getReferrerCommissions:function (id) {
            return $http.get('/configuration/referrals/commissions/'+id);
        },
        //save referrer commisions
        saveReferrerCommissions:function(commissionData){
            return $http.post('/configuration/referrals/commissions/save', commissionData);
        },
        getReferrerPayments:function (id) {
            return $http.get('/configuration/referrals/commission/payments/'+id);

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