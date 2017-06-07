/**
 * Created by Harsha on 5/24/2016.
 */

$(function() {
    var message = '';
    jQuery.validator.addMethod("creditcardtypes", function(value, element, param) {
        if (/[^0-9-]+/.test(value)) {
            return false;
        } else {
            return true;
        }

       // if (value.length ==16) {
           // return true
        //}

    }, "Please enter a valid credit card number.");
    $.validator.addMethod('creditcardexpiry', function(value, element) {
            var cc_exp_year = $('#exp_year').val();
            var cc_exp_month = $('#exp_month').val();
            expiry = cc_exp_year + cc_exp_month,
            date = new Date(),
            month = date.getMonth() + 1,
            now = '' + date.getFullYear() + (month < 10 ? '0' + month : month);

            return expiry > now;
        }, 'Please enter valid expiration month');

    $.validator.addMethod("val_ccv_number",
        function(value, element, params){
            var ccv_number = $('#ccv_number').val();
            var ccvRule = /^[0-9]{3,4}$/;
            var ccvArray = ccvRule.exec(ccv_number);
            if(ccv_number != ccvArray)
            {
                message = "Invalid cvv number";
                return false;
            }else{
                return true;  //valid cvv number
            }

        }, function(){ return message;});

    $('#paymentRegForm').validate({
        //errorElement: 'span',
        //errorClass: 'help-block',
        //highlight: function (element, errorClass, validClass) {
        //    $(element).closest('.form-group').addClass("has-error");
        //},
        //unhighlight: function (element, errorClass, validClass) {
        //    $(element).closest('.form-group').removeClass("has-error");
        //},
        // Specify the validation rules
        rules: {
            card_number: {
                required: true,
                creditcardtypes:"#card_number"
            },
            ccv_number: {
                required: true,
                val_ccv_number: "#ccv_number"

            },
            exp_month: {
                required: true,
                creditcardexpiry:"#exp_month"
            },
            terms: {
                required: true
            }
        },
        // Specify the validation error messages
        messages: {
            card_number: {
                required: "The card number is required"
            },
            ccv_number: {
                required: "The ccv number is required"
            },
            exp_month: {
                required: "The your name is required"
            },
            terms : {
                required: "Please agree with terms of Service"
            }
        }

    });


    $('#next').click(function () {

        var entity_type = $('#entity_type').val();
        var cc_ge_subscription = $('#cc_ge_subscription').val();
        register_form = $("#companyRegForm");
        register_form.validate({
            //errorElement: 'span',
            //errorClass: 'help-block',
            //highlight: function (element, errorClass, validClass) {
            //    $(element).closest('.form-group').addClass("has-error");
            //},
            //unhighlight: function (element, errorClass, validClass) {
            //    $(element).closest('.form-group').removeClass("has-error");
            //},
            // Specify the validation rules
            rules: {
                entity_type: {
                    required: true
                },
                name_of_business: {
                    required: true
                },
                your_name: {
                    required: true
                },
                company_registration_no: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                conf_email: {
                    equalTo: "#email"
                },
                password: {
                    required: true
                },
                conf_password: {
                    equalTo: "#password"
                }
            },
            // Specify the validation error messages
            messages: {
                entity_type: {
                    required: "The entity type is required"
                },
                name_of_business: {
                    required: "The name of business is required"
                },
                your_name: {
                    required: "The your name is required"
                },
                company_registration_no: {
                    required: "The company registration number is required"
                },
                email: {
                    required: "The email is required"
                },
                conf_email: {
                    required: "The confirm email is required"
                },
                password: {
                    required: "The password is required"
                },
                conf_password: {
                    required: "The confirm password is required"
                }
            }
        });
        if (register_form.valid() == true) {
            current_fs = $('#step1');
            next_fs = $('#step2');
            next_fs.show();
            current_fs.hide();
            if(cc_ge_subscription == 1) {
                if(entity_type == '2') {
                    $('#reg-btn').css("display", "block");
                    $("#initial_pay").css("display", "block");
                    $('#pay-now-btn').css("display", "none");
                } else {
                    $('#pay-now-btn').css("display", "block");
                    $('#reg-btn').css("display", "none");
                    $("#initial_pay").css("display", "none");
                }
            } else {
                $('#pay-now-btn').css("display", "none");
                $('#reg-btn').css("display", "block");
                if(entity_type != '2') {
                    $("#initial_pay").css("display", "none");
                }
            }

            $('#tab1').removeClass("btn-primary");
            $('#tab1').addClass("btn-default");
            $('#tab2').addClass("btn-primary");
            $('#tab2').removeClass("btn-default");

        }
    });

    $('#previous').click(function(){
        current_fs = $('#step2');
        next_fs = $('#step1');
        next_fs.show();
        current_fs.hide();
        $('#tab2').removeClass("btn-primary");
        $('#tab2').addClass( "btn-default" );
        $('#tab1').addClass("btn-primary");
        $('#tab1').removeClass( "btn-default" );
        $("#initial_pay").css("display", "block");
    });

    $('#card_number').bind('keypress blur',function(){
        $('#card_number').validateCreditCard(function(result){
            if(result.card_type!=null){
                $('.cc_icons li img').stop().animate({
                    opacity : .2
                });
                $('#cc-'+result.card_type.name).stop().animate({
                    opacity: 1
                });
                if((result.length_valid==true) && (result.luhn_valid==true)){
                    $('#card_number').addClass('cc_valid');
                }else{
                    $('#card_number').removeClass('cc_valid');
                }
            }else{
                $('.cc_icons li img').stop().animate({
                    opacity : 1
                });
            }
        });
    });

    $( "#entity_type" ).change(function() {
        var entity_type = $('#entity_type').val();
        var subscription  = $('#cc_ge_subscription').val();
        if(subscription == 1) {
            if(entity_type != "2") {
                jQuery.ajax({
                    type: 'GET',
                    url: "/get/subscription/fee",
                    async: false,
                    data:{entity_type:entity_type},
                    dataType: "json",
                    beforeSend: function () {
                    },
                    success: function (result) {
                        if(result != null) {
                            $('#subscrib_fee').val(result.subscription_fee);
                        }
                    },
                    error: function (result) {

                    }
                });

                $("#initial_pay").css("display", "none");

                $("#display-only-cc-ge").css("display", "block");
            } else {
                $("#display-only-cc-ge").css("display", "none");
                $("#initial_pay").css("display", "block");
            }
        } else {

            $("#display-only-cc-ge").css("display", "none");
        }
    });


    //new registration


    var entity_type = $('#entity_type').val();
    var subscription  = $('#cc_ge_subscription').val();
    if(subscription == 1) {
        if(entity_type != "2") {
            jQuery.ajax({
                type: 'GET',
                url: "/get/subscription/fee",
                async: false,
                data:{entity_type:entity_type},
                dataType: "json",
                beforeSend: function () {
                },
                success: function (result) {
                    if(result != null) {
                        $('#subscrib_fee').val(result.subscription_fee);
                    }
                },
                error: function (result) {

                }
            });

            $("#initial_pay").css("display", "none");

            $("#display-only-cc-ge").css("display", "block");
        } else {
            $("#display-only-cc-ge").css("display", "none");
            $("#initial_pay").css("display", "block");
        }
    } else {

        $("#display-only-cc-ge").css("display", "none");
    }




});
showHideConditions();

$(document).on('click', '#reg-btn', function(){
    companyRegistration();
});

$(document).on('click', '#pay-now-btn', function(){
    companyRegistration();
});
$(document).on('click', '#reg-without-pay', function(){
    companyRegistration();
});

function companyRegistration()
{
    var entity_type             = $('#entity_type').val();
    var name_of_business        = $('#name_of_business').val();
    var company_registration_no = $('#company_registration_no').val();
    var your_name               = $('#your_name').val();
    var email                   = $('#email').val();
    var conf_email              = $('#conf_email').val();
    var password                = $('#password').val();
    var conf_password           = $('#conf_password').val();
    var card_number             = $('#card_number').val();
    var ccv_number              = $('#ccv_number').val();
    var exp_month               = $('#exp_month').val();
    var exp_year                = $('#exp_year').val();
    var subscrib_fee            = $('#subscrib_fee').val();
    var cc_ge_subscription      = $('#cc_ge_subscription').val();
    var ref_token      = $('#ref_token').val();

    register_form = $("#companyRegForm");
    register_form.validate({
        //errorElement: 'span',
        //errorClass: 'help-block',
        //highlight: function (element, errorClass, validClass) {
        //    $(element).closest('.form-group').addClass("has-error");
        //},
        //unhighlight: function (element, errorClass, validClass) {
        //    $(element).closest('.form-group').removeClass("has-error");
        //},
        // Specify the validation rules
        rules: {
            entity_type: {
                required: true
            },
            name_of_business: {
                required: true
            },
            your_name: {
                required: true
            },
            company_registration_no: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            conf_email: {
                equalTo: "#email"
            },
            password: {
                required: true
            },
            conf_password: {
                equalTo: "#password"
            }
        },
        // Specify the validation error messages
        messages: {
            entity_type: {
                required: "The entity type is required"
            },
            name_of_business: {
                required: "The name of business is required"
            },
            your_name: {
                required: "The your name is required"
            },
            company_registration_no: {
                required: "The company registration number is required"
            },
            email: {
                required: "The email is required"
            },
            conf_email: {
                required: "The confirm email is required"
            },
            password: {
                required: "The password is required"
            },
            conf_password: {
                required: "The confirm password is required"
            }
        }
    });

    if(entity_type == 2) {
        if(register_form.valid()){
            console.log("form validation");
            $.ajax({
                url: "/company/registration",
                type: 'POST',
                dataType: 'json',
                data : {entity_type:entity_type,name_of_business:name_of_business,company_registration_no:company_registration_no,
                    your_name:your_name,email:email,conf_email:conf_email,password:password,
                    conf_password:conf_password, cc_ge_subscription:cc_ge_subscription,ref_token:ref_token},
                beforeSend: function() {
                    $(".splash").show();
                },
                success: function(result){
                    var url      = window.location.host;
                    if(result.success == 'true') {
                        window.location = 'http://'+url+'/thanks';
                        return false;
                    } else {
                        if(result.is_redirect == 'false') {
                            var msg = result.message;
                            var msg_type = 'error';
                            msgAlert(msg, msg_type);
                            $(".splash").hide();
                        } else {
                            window.location = 'http://'+url+'/error';
                            var msg = result.message;
                        }
                    }

                },
                error: function(result) {
                    $(".splash").hide();
                }
            });
        }
    } else {
        if(cc_ge_subscription == 1) {
            if($("#paymentRegForm").valid()){

                if(subscrib_fee > 0.5) {
                    $.ajax({
                        url: "/company/registration",
                        type: 'POST',
                        dataType: 'json',
                        data : {entity_type:entity_type,name_of_business:name_of_business,company_registration_no:company_registration_no,
                            your_name:your_name,email:email,conf_email:conf_email,password:password,
                            conf_password:conf_password, card_number:card_number, ccv_number:ccv_number, exp_month:exp_month,
                            exp_year:exp_year, subscrib_fee:subscrib_fee, cc_ge_subscription:cc_ge_subscription},
                        beforeSend: function() {
                            $(".splash").show();
                        },
                        success: function(result){
                            var url      = window.location.host;
                            if(result.success == 'true') {
                                window.location = 'http://'+url+'/thanks';
                                return false;
                            } else {
                                if(result.is_redirect == 'false') {
                                    var msg = result.message;
                                    var msg_type = 'error';
                                    msgAlert(msg, msg_type);
                                    $(".splash").hide();
                                } else {
                                    window.location = 'http://'+url+'/error';
                                    var msg = result.message;
                                }
                            }

                        },
                        error: function(result) {
                            $(".splash").hide();
                        }
                    });
                } else {
                    var msg = 'Amount must be at least 50 cents';
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        } else {
            if(register_form.valid()){
                    $.ajax({
                        url: "/company/registration",
                        type: 'POST',
                        dataType: 'json',
                        data : {entity_type:entity_type,name_of_business:name_of_business,company_registration_no:company_registration_no,
                            your_name:your_name,email:email,conf_email:conf_email,password:password,
                            conf_password:conf_password, cc_ge_subscription:cc_ge_subscription},
                        beforeSend: function() {
                            $(".splash").show();
                        },
                        success: function(result){
                            var url      = window.location.host;
                            if(result.success == 'true') {
                                window.location = 'http://'+url+'/thanks';
                                return false;
                            } else {
                                if(result.is_redirect == 'false') {
                                    var msg = result.message;
                                    var msg_type = 'error';
                                    msgAlert(msg, msg_type);
                                    $(".splash").hide();
                                } else {
                                    window.location = 'http://'+url+'/error';
                                    var msg = result.message;
                                }
                            }

                        },
                        error: function(result) {
                            $(".splash").hide();
                        }
                    });
            }
        }
    }

}
function msgAlert(msg, msg_type) {
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown",
    };
    if(msg_type == 'success') {
        toastr.success(msg);
    } else if(msg_type == 'error') {
        toastr.error(msg);
    }

}
function showHideConditions() {
    var entity_type = $('#entity_type').val();
    var subscription  = $('#cc_ge_subscription').val();

    if(subscription == 1) {
        if(entity_type != "2") {

            $("#initial_pay").css("display", "none");

            $("#display-only-cc-ge").css("display", "block");
        } else {
            $("#display-only-cc-ge").css("display", "none");
            $("#initial_pay").css("display", "block");
        }
    } else {

        $("#display-only-cc-ge").css("display", "none");
    }
}

/** MJB Registration **/
function mjbCompanyRegistration()
{
    var entity_type             = $('#entity_type').val();
    var name_of_business        = $('#name_of_business').val();
    var company_registration_no = $('#company_registration_no').val();
    var your_name               = $('#your_name').val();
    var email                   = $('#email').val();
    var conf_email              = $('#conf_email').val();
    var password                = $('#password').val();
    var conf_password           = $('#conf_password').val();

        register_form = $("#companyRegForm");
        if(mjbRegistrationValidate(register_form) == true){

            $.ajax({
                url: "/company/registration",
                type: 'POST',
                dataType: 'json',
                data : {entity_type:entity_type,name_of_business:name_of_business,company_registration_no:company_registration_no,
                    your_name:your_name,email:email,conf_email:conf_email,password:password,
                    conf_password:conf_password,},
                beforeSend: function() {
                    $(".splash").show();
                },
                success: function(result){
                    var url      = window.location.host;
                    if(result.success == 'true') {
                        window.location = 'http://'+url+'/thanks';
                        return false;
                    } else {
                        if(result.is_redirect == 'false') {
                            var msg = result.message;
                            var msg_type = 'error';
                            msgAlert(msg, msg_type);
                            $(".splash").hide();
                        } else {
                            window.location = 'http://'+url+'/error';
                            var msg = result.message;
                        }
                    }
                },
                error: function(result) {
                    $(".splash").hide();
                }
            });
        }

}

function mjbRegistrationValidate(mjbregister_form) {

    //register_form = $("#companyRegForm");
    mjbregister_form.validate({
        rules: {
            name_of_business: {
                required: true
            },
            your_name: {
                required: true
            },
            company_registration_no: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            conf_email: {
                equalTo: "#email"
            },
            password: {
                required: true
            },
            conf_password: {
                equalTo: "#password"
            }
        },
        // Specify the validation error messages
        messages: {
            name_of_business: {
                required: "The name of business is required"
            },
            your_name: {
                required: "The your name is required"
            },
            company_registration_no: {
                required: "The company registration number is required"
            },
            email: {
                required: "The email is required"
            },
            conf_email: {
                required: "The confirm email is required"
            },
            password: {
                required: "The password is required"
            },
            conf_password: {
                required: "The confirm password is required"
            }
        }
    });

    return mjbregister_form.valid();

}


$(document).on('click', '#initial_sign_up', function(){
    companyRegistration();
});


/*
function companyRegistration() {
    var entity_type = $('#entity_type').val();
    console.log(entity_type);
    if(entity_type != null){
    $.ajax({
        url: "/company/companyType",
        type: 'POST',
        dataType: 'json',
        data: {entity_type: entity_type},
        beforeSend: function () {
            $(".splash").show();
        },
        success: function (result) {
            var url = window.location.host;
            if (result.success == 'true') {
                window.location = 'http://' + url + '/thanks';
                return false;
            } else {
                if (result.is_redirect == 'false') {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                    $(".splash").hide();
                } else {
                    window.location = 'http://' + url + '/error';
                    var msg = result.message;
                }
            }

        },
        error: function (result) {
            $(".splash").hide();
        }
    });
}
}
*/
$(document).on('click', '#tab2', function () {
    var activeTab = $('#wizardControl .btn-primary').attr('id');
    id = activeTab.split('_');
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

    if(activeTab == 'tab1') {
        register_form = $("#companyRegForm");
        register_form.validate({
            rules: {
                entity_type: {
                    required: true
                },
                name_of_business: {
                    required: true
                },
                your_name: {
                    required: true
                },
                company_registration_no: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                conf_email: {
                    equalTo: "#email"
                },
                password: {
                    required: true
                },
                conf_password: {
                    equalTo: "#password"
                }
            },
            // Specify the validation error messages
            messages: {
                entity_type: {
                    required: "The entity type is required"
                },
                name_of_business: {
                    required: "The name of business is required"
                },
                your_name: {
                    required: "The your name is required"
                },
                company_registration_no: {
                    required: "The company registration number is required"
                },
                email: {
                    required: "The email is required"
                },
                conf_email: {
                    required: "The confirm email is required"
                },
                password: {
                    required: "The password is required"
                },
                conf_password: {
                    required: "The confirm password is required"
                }
            }
        });

        if (register_form.valid() == true) {
            current_fs = $('#step1');
            next_fs = $('#step2');
            next_fs.show();
            current_fs.hide();
            if(cc_ge_subscription == 1) {
                if(entity_type == '2') {
                    $('#reg-btn').css("display", "block");
                    $("#initial_pay").css("display", "block");
                    $('#pay-now-btn').css("display", "none");
                } else {
                    $('#pay-now-btn').css("display", "block");
                    $('#reg-btn').css("display", "none");
                    $("#initial_pay").css("display", "none");
                }
            } else {
                $('#pay-now-btn').css("display", "none");
                $('#reg-btn').css("display", "block");
                if(entity_type != '2') {
                    $("#initial_pay").css("display", "none");
                }
            }

            $('#tab1').removeClass("btn-primary");
            $('#tab1').addClass("btn-default");
            $('#tab2').addClass("btn-primary");
            $('#tab2').removeClass("btn-default");

        }
    }

})

$(document).on('click', '#tab1', function () {
    current_fs = $('#step2');
    next_fs = $('#step1');
    next_fs.show();
    current_fs.hide();
    $('#tab2').removeClass("btn-primary");
    $('#tab2').addClass( "btn-default" );
    $('#tab1').addClass("btn-primary");
    $('#tab1').removeClass( "btn-default" );
    $("#initial_pay").css("display", "block");

})