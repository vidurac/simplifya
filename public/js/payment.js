/**
 * Created by Harsha on 5/26/2016.
 */

$(document).on('click','#payment_subscription', function(){
    var company_id = $('#company_id').val();
    var subscription_fee = $('#payment_subscription_fee').val();
    var payment_type = 'subscription';

    if($("#payment_form").valid()){
        $.ajax({
            url: "/company/subscription/",
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                $(".splash").show();
            },
            data:{company_id:company_id,subscription_fee:subscription_fee, payment_type:payment_type},
            success: function(result){
                var url      = window.location.host;
                if(result.success == 'true') {
                    //window.location = 'http://'+url+'/change/company/info';
                    window.location = 'http://'+url+'/dashboard';
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $(".splash").hide();
                    return false;
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    $(".splash").hide();
                    msgAlert(msg, msg_type);
                }
            },
            error: function(result) {
                $(".splash").hide();
            }
        });
    }
})


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



});

$(document).on('click','#mjb_subscription', function(){
    var foc                     = $('#foc').val();
    var company_id = $('#company_id').val();
    var coupon_id = $('#coupon_id').val();
    var referral_code = $('#referral_code').val();
    var is_referral = $('#is_referral').val();

    if(is_referral == 1)
    {
        var coupon_id = $('#coupon_id').val();
    }
    var no_of_license = $('#no_of_license').html();

    var payment_type = 'subscription';

    var card_number = "";
    var ccv_number = "";
    var exp_month = "";
    var exp_year = "";

    if(foc == 0)
    {
        card_number             = $('#card_number').val();
        ccv_number              = $('#ccv_number').val();
        exp_month               = $('#exp_month').val();
        exp_year                = $('#exp_year').val();
    }

    var subscrib_fee            = $('#payment_subscription_fee').val();
    var mjb_company_status      = $('#mjb_company_status').val();
    var entity_type             = $('#entity_type').val();
    var cc_added                = $('#have_card_detail').val();
    var coupon_check_status        = $('#coupon_check_status').val();

    if ((coupon_id != undefined && coupon_id != '')) {
        console.log("coupon id " + coupon_id);
        if( (foc == 0 && coupon_check_status != "valid") )
        {
            msgAlert("Please enter valid referral code", 'error');
            return false;
        }
    }
    if( (foc == 0 && coupon_check_status != "valid" && coupon_check_status != '') )
    {
        msgAlert("Please enter valid referral code", 'error');
        return false;
    }

    //console.log(mjb_company_status);
    // var radioSelectedId = $(this).attr('id');
    var radioSelectedId = $('input[name=subscription_package]:checked').val();
    if(mjb_company_status == 0){
        paymentForm =$('#payment_form');

        paymentForm.validate({
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
        var valid_data = false;
        if(foc == 0 && paymentForm.valid())
        {
            valid_data = true;
        }
        if(foc == 1)//no need to check validation here
        {
            valid_data = true;
        }
        if(valid_data){
            $.ajax({
                url: "/company/commonCompanyPayment",
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $(".splash").show();
                },
                data:{entity_type:entity_type,card_number: card_number , ccv_number:ccv_number, exp_month:exp_month ,exp_year:exp_year ,subscription_fee:subscrib_fee, payment_type:payment_type,subscription_plan: radioSelectedId, foc: foc,coupon_id:coupon_id,no_of_license:no_of_license,is_referral:is_referral},
                success: function(result){
                    var url      = window.location.host;
                    if(result.success == 'true') {
                        //window.location = 'http://'+url+'/change/company/info';
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        setTimeout(function(){
                            window.location = 'http://'+url+'/dashboard';
                            //location.reload();
                            $(".splash").hide();
                            return false;
                        }, 3000);
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        $(".splash").hide();
                        msgAlert(msg, msg_type);
                    }
                },
                error: function(result) {
                    console.log(result);
                    $(".splash").hide();
                }
            });
        }


    } else if(mjb_company_status == 4 || mjb_company_status == 5) {
        paymentForm =$('#payment_form');

        paymentForm.validate({
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
                    required: "The expire month is required"
                },
                terms : {
                    required: "Please agree with terms of Service"
                }
            }

        });
        if(cc_added == 'no'){
            if(paymentForm.valid()){
                $.ajax({
                    url: "/add/default/card/details",
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        $(".splash").show();
                    },
                    data:{entity_type:entity_type,card_number: card_number , ccv_number:ccv_number, exp_month:exp_month ,exp_year:exp_year ,subscription_fee:subscrib_fee, payment_type:payment_type,subscription_plan: radioSelectedId, foc: foc},
                    success: function(result){
                        var url      = window.location.host;
                        if(result.success == 'true') {

                            if(result.success == 'true'){
                                //window.location = 'http://'+url+'/change/company/info';
                                window.location = 'http://'+url+'/dashboard';
                                var msg = result.message;
                                var msg_type = 'success';
                                msgAlert(msg, msg_type);
                                //location.reload();
                                $(".splash").hide();
                                return false;
                            }
                        } else {
                            var msg = result.message;
                            var msg_type = 'error';
                            $(".splash").hide();
                            msgAlert(msg, msg_type);
                        }
                    },
                    error: function(result) {
                        console.log(result);
                        $(".splash").hide();
                    }
                });
            }
        } else {
            $.ajax({
                url: "/company/subscription",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $(".splash").show();
                },
                data:{company_id:company_id,subscription_fee:subscrib_fee, payment_type:payment_type,subscription_plan: radioSelectedId,  foc: foc,coupon_id:coupon_id},
                success: function(result){
                    var url      = window.location.host;
                    if(result.success == 'true') {
                        //window.location = 'http://'+url+'/change/company/info';
                        window.location = 'http://'+url+'/dashboard';
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        $(".splash").hide();
                        return false;
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        $(".splash").hide();
                        msgAlert(msg, msg_type);
                    }
                },
                error: function(result) {
                    $(".splash").hide();
                }
            });
        }

    } else{
        //if($("#payment_form").valid()){
            $.ajax({
                url: "/company/subscription",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $(".splash").show();
                },
                data:{company_id:company_id,subscription_fee:subscrib_fee, payment_type:payment_type,subscription_plan: radioSelectedId,  foc: foc,coupon_id:coupon_id},
                success: function(result){
                    var url      = window.location.host;
                    if(result.success == 'true') {
                        //window.location = 'http://'+url+'/change/company/info';
                        window.location = 'http://'+url+'/dashboard';
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        $(".splash").hide();
                        return false;
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        $(".splash").hide();
                        msgAlert(msg, msg_type);
                    }
                },
                error: function(result) {
                    $(".splash").hide();
                }
            });
        }
  //  }

});
