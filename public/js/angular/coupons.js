app.controller('ReferralCtrl', function ($scope,$http, $window, $q, $log,$location, $timeout,ReferralService) {

    $scope.sortType     = 'name'; // set the default sort type
    $scope.sortReverse  = false;  // set the default sort order
    $scope.init = function() {
        ReferralService.getAllReferrals()
            .success(function(getData) {
                $scope.referrals = getData.data;
            });

    };


    $scope.deleteReferral = function deleteReferral(referral_id){

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
                        ReferralService.deleteReferral(referral_id)
                            .success(function(getData) {

                                if(getData.success == "false")
                                {
                                    msgAlert(getData.message, 'error');
                                }
                                else
                                {
                                    $scope.init();
                                }
                            });
                    }
                    if (isConfirm != true) {
                        return false;
                    }
                });
    };

    $scope.init();

    $scope.checkPlanIdSelection = function checkPlanIdSelection() {

        if($scope.search.master_subscription_id == null)
        {
            delete $scope.search.master_subscription_id;
        }
    };

});

app.controller('CouponCtrl', function ($scope,$http, $window, $q, $log,$location, $timeout,CouponService) {

    $scope.sortType     = 'code'; // set the default sort type
    $scope.sortReverse  = false;  // set the default sort order
    $scope.init = function() {
        CouponService.getAllCoupons()
            .success(function(getData) {
                $scope.coupons = getData.data;
                console.log($scope.coupons)
            });

        CouponService.getSubscriptionPlans($scope.mjbEntityType)

            .success(function(getData) {
                $scope.subscription_plans = getData.data;
            });

    };

    $scope.init();

    $scope.checkPlanIdSelection = function checkPlanIdSelection() {

        if($scope.search.master_subscription_id == null)
        {
            delete $scope.search.master_subscription_id;
        }
    }
});
app.controller('createCoupon', function ($scope,$http, $window, $q, $log,$location, $timeout,$uibModal,CouponService) {
    $scope.form={};
    $scope.plans=[];
    $scope.couponId='';
    $scope.form.coupon_details=[];
    $scope.subscriptionPlan={};

    $scope.init = function() {
        while ($scope.plans.length > 0) {
            $scope.plans.pop();
        }
        var promise=$scope.getPlans();
        promise.then(function() {

            $scope.getfields();
        })

    };

    $scope.getPlans=function () {
        var deferred = $q.defer();
        CouponService.getSubscriptionPlans($scope.form.mjbEntityType).success(function (getData) {
            console.log(getData.data);
            angular.forEach(getData.data, function(planData){
                var subscriptions=[];
                subscriptions={
                    name:planData.name,
                    value:planData.validity_period_id,
                    id:planData.id,
                    amount:planData.amount
                };

                $scope.plans.push(subscriptions);
            });
            deferred.resolve();
        });
            return deferred.promise;
    }

    $scope.getfields=function(){
        console.log($scope.form.master_subscription_id.amount)
        var promise=$scope.emptyFieldList();
        promise.then(function() {
            var fieldCount=$scope.form.master_subscription_id.value;
            for (var i=1;i<=fieldCount;i++){
                var arrayFields=[];
                arrayFields={
                    'order':i,
                    'type':'fixed',
                    'amount':0
                };
                $scope.form.coupon_details.push(arrayFields);
            }
        });

    };
    
    $scope.emptyFieldList=function () {
        var deferred = $q.defer();

        $timeout(function() {
            while ($scope.form.coupon_details.length > 0) {
                $scope.form.coupon_details.pop();
            }
            deferred.resolve();

        },0);

        return deferred.promise;
    };
    $scope.saveCoupon=function (valid) {
        if (!valid) {
            return;
        }

        var data=angular.copy($scope.form)
        delete data.mjbEntityType;
        delete data.master_subscription_id;
        delete data.used;

        var start_date=moment(data.start_date).format("YYYY-MM-DD");
        var end_date=moment(data.end_date).format("YYYY-MM-DD");

        delete data.start_date;
        delete data.end_date;
        data.start_date=start_date;
        data.end_date=end_date;

        data.master_subscription_id=$scope.form.master_subscription_id.id;
        if($scope.couponId!=''){
            data.id=$scope.couponId;

        }
        $scope.savingCoupon=true;
        // Save Coupon entry
        var couponCreate = CouponService.saveCoupon(data);

        // success response
        couponCreate.success(function (getData) {
            if (getData.success == 'true') {
                msgAlert(getData.message, 'success');
                $window.location.href = '/configuration/coupons'
            }
            if (getData.success == 'false') {
                msgAlert(getData.message, 'error');
            }
            $scope.savingCoupon=false;
        });

        // // failure response
        couponCreate.error(function (error) {
            msgAlert(error.message, 'error');
            $scope.savingCoupon=false;
        });

    }

        // Get coupon Data
    $scope.getCoupon=function (id) {
        CouponService.getCouponDetails(id).success(function (getData) {

            var couponData=getData.data.coupon;
            var selectedPlan={
                name:couponData.master_subscription_name,
                value:couponData.validity_period_id,
                id:couponData.master_subscription_id
            }

            var promise=$scope.getPlans();
            promise.then(function() {
                $scope.couponId=couponData.id;
                $scope.form={
                    code:couponData.code,
                    description:couponData.description,
                    start_date:couponData.start_date,
                    end_date:couponData.end_date,
                    master_subscription_id:selectedPlan,
                    coupon_details:couponData.coupon_details,
                    used:couponData.used
                }

                angular.forEach($scope.plans, function (item) {
                    console.log('sss')
                    if (couponData.master_subscription_id == item.id) {
                        $scope.form.master_subscription_id = item;
                    }
                });

            });

        });
    }
    $scope.toggleMin = function() {
        var tomorrow = new Date();
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
        var tomorrow = new Date($scope.form.start_date);
        tomorrow.setDate(tomorrow.getDate() + 1);
        $scope.minDatePop2 = tomorrow;
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'yyyy-MM-dd','MM/dd/yyyy'];
    $scope.format = $scope.formats[5];
    $scope.altInputFormats = ['M!/d!/yyyy'];

    $scope.popup1 = {
        opened: false
    };

    $scope.popup2 = {
        opened: false
    };

}).directive("csDateToIso", function () {

    var linkFunction = function (scope, element, attrs, ngModelCtrl) {

        ngModelCtrl.$parsers.push(function (datepickerValue) {
            return moment(datepickerValue).format("MM/DD/YYYY");
        });
    };

    return {
        restrict: "A",
        require: "ngModel",
        link: linkFunction
    };
}).directive("discount", function() {
    return {
        restrict: "A",

        require: "ngModel",

        link: function(scope, element, attributes, ngModel) {
            var subscription_amount=parseFloat(scope.form.master_subscription_id.amount);
            ngModel.$validators.discount = function(modelValue) {
                if(modelValue){
                    return subscription_amount >= modelValue
                }
                return true;

            }
        }
    };
});


app.controller('createReferralCode', function ($scope,$http, $window, $q, $log,$location, $timeout,$uibModal,CouponService, ReferralService) {

    // Initializations
    $scope.form={id: 0};
    $scope.plans = {};
    $scope.form.coupon_details=[
        {
            'order': 1,
            'type':'fixed',
            'amount':0
        }
    ];

    $scope.commissionPeriod = [
        { name: '1 Year', id: 12 },
        { name: '2 Years', id: 24 }
    ];

    $scope.init = function init() {
        ReferralService.getAllReferrals()
            .success(function(getData) {
                $scope.referrals = getData.data;
            });
        var promise=$scope.getPlans();
        promise.then(function(data) {
            $scope.plans = data;
        });
    };

    $scope.initWithCoupon = function initWithCoupon(id) {
        ReferralService.getAllReferrals()
            .success(function(getData) {
                $scope.referrals = getData.data;
                var promise=$scope.getPlans();
                promise.then(function(data) {
                    $scope.plans = data;
                });
                
                CouponService.getReferralCouponDetails(id).success(function (getData) {
                    var couponData=getData.data.coupon;
                    $scope.couponId=couponData.id;
                    $scope.form={
                        id: couponData.id,
                        code:couponData.code,
                        description:couponData.description,
                        start_date:couponData.start_date,
                        end_date:couponData.end_date,
                        coupon_details:couponData.coupon_details,
                        used:couponData.used,
                        master_referral_id: couponData.master_referral_id,
                        commission_period: couponData.commission_period
                    };
                });
            });

    };

    /**
     * Save referral code
     * @param valid
     */
    $scope.saveReferralCode = function saveReferralCode(valid) {

        if (!valid) {
            return;
        }

        var data=angular.copy($scope.form);
        data.type = 'referral';
        data.master_subscription_id = '0';
        console.log(data)
        var start_date=moment(data.start_date).format("YYYY-MM-DD");
        var end_date=moment(data.end_date).format("YYYY-MM-DD");

        delete data.start_date;
        delete data.end_date;
        data.start_date=start_date;
        data.end_date=end_date;

        $scope.savingCoupon=true;

        // Save Coupon entry
        var couponCreate = CouponService.saveCoupon(data);
        $(".splash").show();

        // success response
        couponCreate.success(function (getData) {
            $(".splash").hide();
            if (getData.success == 'true') {
                msgAlert(getData.message, 'success');
                $timeout(function () {
                    $window.location.href = '/configuration/referrals/codes'
                }, 1000);
            }
            if (getData.success == 'false') {
                msgAlert(getData.message, 'error');
            }
            $scope.savingCoupon=false;
        });

        // // failure response
        couponCreate.error(function (error) {
            $(".splash").hide();
            msgAlert(error.message, 'error');
            $scope.savingCoupon=false;
        });

    };

    $scope.toggleMin = function() {
        var tomorrow = new Date();
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
        var tomorrow = new Date($scope.form.start_date);
        tomorrow.setDate(tomorrow.getDate() + 1);
        $scope.minDatePop2 = tomorrow;
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate', 'yyyy-MM-dd','MM/dd/yyyy'];
    $scope.format = $scope.formats[5];
    $scope.altInputFormats = ['M!/d!/yyyy'];

    $scope.popup1 = {
        opened: false
    };

    $scope.popup2 = {
        opened: false
    };

    $scope.getPlans=function () {
        var deferred = $q.defer();
        CouponService.getSubscriptionPlans($scope.entityType).success(function (getData) {
            var subscriptions=[];
            angular.forEach(getData.data, function(planData){
                var subscription={
                    name:planData.name,
                    id:planData.id,
                    amount:planData.amount
                };
                subscriptions.push(subscription);
            });
            deferred.resolve(subscriptions);
        });
        return deferred.promise;
    }
});

app.controller('ReferralCodeListCtrl', function ($scope,$http, $window, $q, $log,$location, $timeout,ReferralService) {

    $scope.sortType     = 'code'; // set the default sort type
    $scope.sortReverse  = false;  // set the default sort order

    $scope.init = function() {
        ReferralService.getAllRefCodes()
            .success(function(getData) {
                $scope.referrals = getData.data;
            });

    };

    $scope.init();
});
app.service('CouponService', function($http,config) {
    return {
        // get all subscriptions for MJB
        getSubscriptionPlans : function(mjbEntityType) {
            return $http.get('/company/subscriptionPlans?entity_type='+mjbEntityType);
        },
        // save Coupons
        saveCoupon : function(data) {
            return $http.post('/configuration/coupons/create', data);
        },
        // get all the tasks
        getAllCoupons : function() {
            return $http.get('/configuration/coupons/all');
        },
        getCouponDetails:function (id) {
            return $http.get('/configuration/coupons/coupon/'+id);
        },
        getReferralCouponDetails:function (id) {
            return $http.get('/configuration/referrals/referrer-code/'+id);
        }
    }
});

app.service('ReferralService', function($http,config) {
    return {
        // get all the tasks
        getAllReferrals : function() {
            return $http.get('/configuration/referrals/all');
        },
        // get all ref codes
        getAllRefCodes : function(data) {
            return $http.get('/configuration/referralCodes');
        },
        //delete referral
        deleteReferral : function(data){
            //console.log(data);
            return $http.post('/configuration/referrals/delete', data);
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