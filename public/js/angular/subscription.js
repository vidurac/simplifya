app.controller('SubscriptionPlanCtrl', function ($scope,$http, $window, $q, $log,$location,SubscriptionService, $timeout) {

    $scope.subscriptionForm = {
        selectedPlan: ''
    };

    $scope.init = function() {
        $(".splash").show();
        SubscriptionService.get()
            .success(function(getData) {
                $scope.isSubscriptionDataLoaded = true;
                $(".splash").hide();
                $scope.currentPlan = getData.data.current_plan;
                $scope.nextPlan = getData.data.next_plan;
                $scope.plans = getData.data.plans;
                $scope.cancelFee = getData.data.cancel_fee;
                $scope.hideCancelPlan = getData.data.hideCancel;
                $scope.couponReferralId = getData.data.current_plan.coupon_referral_id;
                $scope.is_valid_coupon = 1;
                if (undefined != getData.data.current_plan) {
                    $scope.subscriptionForm.selectedPlan = getData.data.current_plan.plan_id;
                }
            })
            .error(function (data) {
                $scope.isSubscriptionDataLoaded = true;
                $(".splash").hide();
            });

    };

    $scope.init();

    $scope.validateCoupon = function validateCoupon() {
        var coupon_code = $scope.subscriptionForm.coupon_code;

        if(coupon_code != "" && coupon_code != undefined)
        {
            $(".splash").show();

            var formData = {
                subscription_plan: $scope.subscriptionForm.selectedPlan,
                coupon_code: $scope.subscriptionForm.coupon_code
            };

            SubscriptionService.validateCoupon(formData)
                .success(function(data) {

                    if(coupon_code != "")
                    {
                        if(data.success)
                        {
                            $("#coupon_check_msg").html(data.msg);
                            $("#coupon_check_msg").addClass("valid");
                            $("#coupon_check_msg").removeClass("invalid");
                            $scope.is_valid_coupon = 1;

                        }
                        else
                        {
                            $("#coupon_check_msg").html(data.msg);
                            $("#coupon_check_msg").addClass("invalid");
                            $("#coupon_check_msg").removeClass("valid");
                            $scope.is_valid_coupon = 0;
                        }
                    }
                    $(".splash").hide();
                    //console.log(data);
                    //msgAlert(data.message, 'success');
                })
                .error(function (data) {
                    $(".splash").hide();
                    msgAlert(data.message, 'error');
                });
        }
        else
        {
            $("#coupon_check_msg").html('');
            $("#coupon_check_msg").removeClass("invalid");
            $("#coupon_check_msg").removeClass("valid");
            $scope.is_valid_coupon = 1;
        }
    }

    $scope.updatePlan = function updatePlan(valid) {

        if($scope.is_valid_coupon == 0)
        {
            msgAlert('Please enter valid coupon code', 'error');
        }
        if (valid && $scope.is_valid_coupon == 1) {
            swal({
                    title: "Are you sure?",
                    text: "You want to update the subscription plan?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Update!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: true,
                    closeOnCancel: true },
                function (isConfirm) {
                    if (isConfirm) {
                        var formData = {
                            plan_id: $scope.subscriptionForm.selectedPlan,
                            coupon_code: $scope.subscriptionForm.coupon_code
                        };
                        if (undefined != $scope.currentPlan) {
                            formData.current_subscription_plan_id = $scope.currentPlan.current_subscription_plan_id;
                        }
                        if (undefined != $scope.nextPlan) {
                            formData.next_subscription_plan_id = $scope.nextPlan.subscription_plan_id;
                        }
                        $(".splash").show();
                        SubscriptionService.updatePlan(formData)
                            .success(function(data) {
                                $(".splash").hide();
                                $scope.init();
                                msgAlert(data.message, 'success');
                            })
                            .error(function (data) {
                                $(".splash").hide();
                                $scope.init();
                                msgAlert(data.message, 'error');
                            });
                    }
                });
        }
    }

    $scope.cancelPlan = function updatePlan() {
        swal({
                title: "Are you sure?",
                text: "You want to cancel the subscription plan?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Update!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: true,
                closeOnCancel: true },
            function (isConfirm) {
                if (isConfirm) {
                    $(".splash").show();
                    var formData = {};
                    SubscriptionService.cancelPlan(formData)
                        .success(function(data) {
                            $(".splash").hide();
                            msgAlert(data.message, 'success');
                            //todo logout user
                            $timeout(function () {
                                var landingUrl = "/auth/logout";
                                $window.location.href = landingUrl;
                            }, 3000);

                        })
                        .error(function (data) {
                            $(".splash").hide();
                            msgAlert(data.message, 'error');
                        });
                }

            });
    }
});

app.service('SubscriptionService', function($http,config) {
    return {
        // get all the Rosters
        get : function() {
            return $http.get('/subscription/available-plans');
        },
        updatePlan:function (data) {
            return $http.post('/subscription/update', data);
        },
        cancelPlan:function (data) {
            return $http.post('/subscription/cancel', data);
        },
        validateCoupon:function (data) {
            return $http.post('/company/validateCoupon', data);
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

