/**
 * Created by Harsha on 5/24/2016.
 */

$(function() {
    $.validator.addMethod("expireMonth",
        function(value, element, params){
            var y = $('#exp_year').val();
            // Return today's date and time
            var currentTime = new Date()

            // returns the month (from 0 to 11)
            var month = currentTime.getMonth() + 1

            // returns the year (four digits)
            var year = currentTime.getFullYear()
            if(y <= year) {
                if(value <= month) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        },'expire month should not be equal or less than current month');

    $.validator.addMethod("expireYear",
        function(value, element, params){

            // Return today's date and time
            var currentTime = new Date()

            // returns the year (four digits)
            var year = currentTime.getFullYear()

            return (value >= year);
        },'expire year should not be less than current year');

    $("#edit_company_details").validate({
        rules: {
            name_of_business: {
                required: true
            },
            /*company_registration_no: {
                required: true
            }*/
        },
        messages: {
            name_of_business:{
                required: "The name field is required"
            },
            /*company_registration_no:{
                required: "The company registration number is required"
            }*/
        }
    });

    $("#edit_card_details").validate({

        rules: {
            exp_month: {
                required: true,
                expireMonth:"#exp_month"
            },
            exp_year: {
                required: true,
                expireYear:"#exp_year"
            }
        },
        messages: {
            exp_month:{
                required: "The Expire month field is required"
            },
            exp_year:{
                required: "The Expire year field is required"
            }
        }
    });

    $('.dropzone a').click(function(event) {
        event.preventDefault();
        $(this).parents('.dropzone').find('input[type="file"]').trigger('click');
    });


    $('.dropzone input[type="file"]').change(function(e) {
        var file = e.target.files[0];
        file.preview = URL.createObjectURL(file);
        $(this).parents('.dropzone').find('img').attr('src', file.preview).end().show();

    });
});

$(document).on('click', '#tab_1', function() {
    var tabNo = $('#tab_1').data('tab_no');
    tabHeadingEvent(tabNo);
});
$(document).on('click', '#tab_2', function() {
    var tabNo = $('#tab_2').data('tab_no');
    tabHeadingEvent(tabNo);
});
$(document).on('click', '#tab_3', function() {
    var tabNo = $('#tab_3').data('tab_no');
    tabHeadingEvent(tabNo);
});
$(document).on('click', '#tab_4', function() {
    var tabNo = $('#tab_4').data('tab_no');
    tabHeadingEvent(tabNo);
});

$("#profilePicture").change(function(){
    var imageSize = this.files[0].size / 1024;

    if(imageSize > 1024* 5){
        $(".img-responsive").attr('src', '');
        $(".img-responsive").attr('style', 'height:220px');
        swal({
            title: "Error!",
            text: "File size should be less than 5MB."
        });
    }
});
$(function() {
    var company_id = $('#company_id').val();
    var step_id ='';

    step_id =  'step2';
    next_id = 2;
    active_id = 'tab_'+next_id;
    $("#business_location_tbl").dataTable().fnDestroy();
    business_location_tbl_new(company_id);
});

function business_location_tbl_new(company_id)
{
    business_location_dataTable = $('#business_location_tbl').dataTable( {
        "ajax": {
            "url": "/company/locations/"+company_id,
        },
        "searching": true,
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            // {"sWidth": "20%", "mData": 2},
            {"sWidth": "10%", "mData": 3},
            {"sWidth": "10%", "mData": 4},
            {"sWidth": "10%", "mData": 5},
            {"sWidth": "5%", "mData": 6},
            {"sWidth": "15%", "mData": 7}
        ]
    });
    $('#business_location_tbl thead > tr > th').removeClass('sorting_asc');
}

function tabHeadingEvent(tabNo)
{
    active_tab = $('#wizardControl a.btn-primary').attr('id');
    id = active_tab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var step_id ='';

    if(tabNo == 2) {
        step_id =  'step'+id[1];
        next_id = tabNo;
        active_id = 'tab_'+next_id;
        $("#business_location_tbl").dataTable().fnDestroy();
        business_location_tbl(company_id);
        cssClassManager(active_tab, active_id);
        $('#'+step_id).css('display','none');
        $('#step2').css('display','block');
    } else if(tabNo == 3) {
        step_id =  'step'+id[1];
        if(entity_type != 1) {
            next_id = tabNo;
        }else {
            next_id = tabNo;
        }
        active_id = 'tab_'+next_id;
        if($( "#business_location_tbl > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $('#business_location_form').valid();
        } else {
            getCompanyLocationsById(company_id);
            getPermissionLevelById(company_id);
            $("#employe-table").dataTable().fnDestroy();
            invite_employee_tbl(company_id);
            cssClassManager(active_tab, active_id);
            $('#'+step_id).css('display','none');
            $('#step3').css('display','block');
        }
    } else if(tabNo == 1) {
        next_id = tabNo;
        step_id =  'step'+id[1];
        active_id = 'tab_'+next_id;
        cssClassManager(active_tab, active_id);
        $('#'+step_id).css('display','none');
        $('#step'+tabNo).css('display','block');
    } else if(tabNo == 4) {
        next_id = tabNo;
        step_id =  'step'+id[1];
        active_id = 'tab_'+next_id;
        getCreditCardDetail(company_id);
        cssClassManager(active_tab, active_id);
        $('#'+step_id).css('display','none');
        $('#step'+tabNo).css('display','block');
    }

}


/**
 *  Edit Wizard next button event
 */
$(document).on('click', '.edit-next', function () {
    active_tab = $('#wizardControl a.btn-primary').attr('id');
    id = active_tab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();

    if(parseInt(id[1]) == 1) {
        next_id = parseInt(id[1])+1;
        active_id = 'tab_'+next_id;
        $("#business_location_tbl").dataTable().fnDestroy();
        business_location_tbl(company_id);
        cssClassManager(active_tab, active_id);
        $('#step1').css('display','none');
        $('#step2').css('display','block');
    } else if(parseInt(id[1]) == 2) {
        if(entity_type != 1) {
            next_id = parseInt(id[1])+1;
        }else {
            next_id = parseInt(id[1])+1;
        }
        active_id = 'tab_'+next_id;
        if($( "#business_location_tbl > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $('#business_location_form').valid();
        } else {
            getCompanyLocationsById(company_id);
            getPermissionLevelById(company_id);
            $("#employe-table").dataTable().fnDestroy();
            invite_employee_tbl(company_id);
            cssClassManager(active_tab, active_id);
            $('#step2').css('display','none');
            $('#step3').css('display','block');
        }
    } else if(parseInt(id[1]) == 3) {
        next_id = parseInt(id[1])+1;
        active_id = 'tab_'+next_id;
        getCreditCardDetail(company_id);
        cssClassManager(active_tab, active_id);
        $('#step3').css('display','none');
        $('#step4').css('display','block');
    }  else if(parseInt(id[1]) == 4) {
        next_id = parseInt(id[1])+1;
        active_id = 'tab_'+next_id;
    }

})

/**
 *  Invite New Employees
 */
$(document).on('click', '#invite_employee_modl', function(){
    var name = $('#name').val();
    var email_address = $('#email_address').val();
    var locations = $('#location').val();
    var permission = $('#permission_level').val();
    var company_id = $('#company_id').val();

    if($('#invite_employ_form').valid()) {
        $.ajax({
            url: "/invite/to/employees",
            type: 'POST',
            dataType: 'json',
            data : {name:name,email_address:email_address,locations:locations,permission:permission,company_id:company_id},
            success: function(result){
                if(result.success == 'true') {
                    $("#location").val(location).trigger('change');
                    clearEmployeeForm();
                    $("#employe-table").dataTable().fnDestroy();
                    invite_employee_tbl(company_id);
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#invite-employees').modal('hide');
                    msgAlert(msg, msg_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
});
$('#new_card_number').bind('keypress blur',function(){
    $('#new_card_number').validateCreditCard(function(result){
        if(result.card_type!=null){
            $('.cc_icons li img').stop().animate({
                opacity : .2
            });
            $('#cc-'+result.card_type.name).stop().animate({
                opacity: 1
            });
            if((result.length_valid==true) && (result.luhn_valid==true)){
                $('#new_card_number').addClass('cc_valid');
            }else{
                $('#new_card_number').removeClass('cc_valid');
            }
        }else{
            $('.cc_icons li img').stop().animate({
                opacity : 1
            });
        }
    });
});
$(document).on('click', '#change-basic-info', function(){
    var name = $('#name_of_business').val();
    var reg_no = $('#company_registration_no').val();
    var reg_noH = $('#company_registration_noH').val();
    var is_active = $("input[type='radio']:checked").val();
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();
    if(is_active == undefined || is_active == '') {
        is_active = '';
    }
    if(reg_no == reg_noH)
    {
        reg_no = "";
    }

    if(is_active == 4) {
       if(cc_ge_subscription == 0 && entity_type == 2 ){
           swal({
                   title: "Are you sure?",
                   text: "Your account will be deactivated and you will no longer be able to use Simplifya. You can reactivate your account, but it will require payment again.",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes"
               },
               function () {
                   if($("#edit_company_details").valid()){

                       $.ajax({
                           url: "/change/company/info",
                           type: 'POST',
                           dataType: 'json',
                           data : {name_of_location:name, reg_no:reg_no, is_active:is_active},
                           success: function(result){
                               if(result.success == 'true') {
                                   var url      = window.location.host;
                                   var msg = result.message;
                                   var msg_type = 'success';
                                   msgAlert(msg, msg_type);
                                   setTimeout(function(){
                                       if(result.is_active == "4") {
                                           window.location = 'http://'+url+'/company/info';
                                           return false;
                                       }
                                   }, 1000);

                               } else {
                                   var msg = result.message;
                                   var msg_type = 'error';
                                   msgAlert(msg, msg_type);
                               }

                           }
                       });
                   }
               });
       }

        if(entity_type != 2 && cc_ge_subscription == 0){
            swal({
                    title: "Are you sure?",
                    text: "Your account will be deactivated and you will no longer be able to use Simplifya.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    if($("#edit_company_details").valid()){

                        $.ajax({
                            url: "/change/company/info",
                            type: 'POST',
                            dataType: 'json',
                            data : {name_of_location:name, reg_no:reg_no, is_active:is_active},
                            success: function(result){
                                if(result.success == 'true') {
                                    var url      = window.location.host;
                                    var msg = result.message;
                                    var msg_type = 'success';
                                    msgAlert(msg, msg_type);
                                    setTimeout(function(){
                                        if(result.is_active == "4") {
                                            window.location = 'http://'+url+'/company/info';
                                            return false;
                                        }
                                    }, 1000);

                                } else {
                                    var msg = result.message;
                                    var msg_type = 'error';
                                    msgAlert(msg, msg_type);
                                }

                            }
                        });
                    }
                });
        }

        if(cc_ge_subscription == 1){
            swal({
                    title: "Are you sure?",
                    text: "Your account will be deactivated and you will no longer be able to use Simplifya. You can reactivate your account, but it will require payment again.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    if($("#edit_company_details").valid()){

                        $.ajax({
                            url: "/change/company/info",
                            type: 'POST',
                            dataType: 'json',
                            data : {name_of_location:name, reg_no:reg_no, is_active:is_active},
                            success: function(result){
                                if(result.success == 'true') {
                                    var url      = window.location.host;
                                    var msg = result.message;
                                    var msg_type = 'success';
                                    msgAlert(msg, msg_type);
                                    setTimeout(function(){
                                        if(result.is_active == "4") {
                                            window.location = 'http://'+url+'/company/info';
                                            return false;
                                        }
                                    }, 1000);

                                } else {
                                    var msg = result.message;
                                    var msg_type = 'error';
                                    msgAlert(msg, msg_type);
                                }

                            }
                        });
                    }
                });
        }


    } else {
        if($("#edit_company_details").valid()){
            $.ajax({
                url: "/change/company/info",
                type: 'POST',
                dataType: 'json',
                data : {name_of_location:name, reg_no:reg_no, is_active:is_active},
                success: function(result){
                    if(result.success == 'true') {
                        var url      = window.location.host;
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        if(result.is_active == "4") {
                            window.location = 'http://'+url+'/company/info';
                            return false;
                        }
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    }

                }
            });
        }
    }

});


$(document).on('click', '#invite-emp', function(){
    $('#invite-employees').modal('show');
});

$(document).on('click', '#new-add-location', function(){

    var name = $('#name_of_location').val();
    var address_01 = $('#add_line_1').val();
    var address_02 = $('#add_line_2').val();
    var county = $('#country').val();
    var state = $('#state').val();
    var city = $('#cities').val();
    var zip_code = $('#zip_code').val();
    var phone_no = $('#phone_no').val();
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();

    if($("#business_location_form").valid()){
        $.ajax({
            url: "/add/company/location",
            type: 'POST',
            dataType: 'json',
            data : {name_of_location:name,cities:city,state:state,address_line_1:address_01,address_line_2:address_02,phone_number:phone_no,entity_type:entity_type,zip_code:zip_code},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                $(".splash").hide();
                if(result.success == 'true') {
                    clearLocationForm();
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#add-new-business-location').modal('hide');
                    $('#business_location_tbl').dataTable().fnDestroy();
                    countryChange(county);
                    business_location_tbl(company_id);
                    msgAlert(msg, msg_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
                
            }
        });
    }
});

$(document).on('click', '#invite_to_employee', function(){
    var name = $('#name').val();
    var email_address = $('#email_address').val();
    var locations = $('#location').val();
    var permission = $('#permission_level').val();
    var company_id = $('#company_id').val();

    if($('#invite_employ_form').valid()) {
        $.ajax({
            url: "/invite/to/employees",
            type: 'POST',
            dataType: 'json',
            data : {name:name,email_address:email_address,locations:locations,permission:permission,company_id:company_id},
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#invite-employees').modal('hide');
                    msgAlert(msg, msg_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
});

function changeUserStatus(user_id, status)
{
    var company_id = $('#company_id').val();
    if(status == 0) {
        swal({
                title: "Are you sure?",
                text: "Your will not be able to recover this user details!",
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
                    swal("Deleted!", "User has been deleted.", "success");
                    $.ajax({
                        url: "/change/users/status",
                        type: 'GET',
                        dataType: 'json',
                        data:{user_id:user_id, status:status},
                        success: function(result){
                            $("#employe-table").dataTable().fnDestroy();
                            invite_employee_tbl(company_id);

                        }
                    });
                } else {
                    swal("Cancelled", "User details are safe :)", "error");
                }
            });
    }
}

$('#invite-employees').on('hidden.bs.modal', function (e) {

    $('#invite-employees').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#invite-employees" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$('#add-new-business-location').on('hidden.bs.modal', function (e) {

    $('#business_location_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#business_location_form" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$(document).on('change', '#permission_level', function () {
    var role_id = $(this).val();
    if((role_id== 1) || (role_id== 2) || (role_id== 5) || (role_id== 7)) {
        $('#locations-enable').css("display", "none");
    } else {
        $('#locations-enable').css("display", "block");
    }
})

function getCreditCardDetail(company_id)
{

    $("#credit_card_table").dataTable().fnDestroy();
    $('#credit_card_table').dataTable({
        "ajax": {
            "url": "/get/card/all/"+company_id,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "20%", "mData": 2},
            {"sWidth": "20%", "mData": 3},
            {"sWidth": "20%", "mData": 4}
        ]
    });
    $('#credit_card_table thead > tr > th').removeClass('sorting_asc');

   /* $.ajax({
        url: "/get/card/detail/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            $('#card_number').val('XXXX XXXX XXXX '+result.CardNumber);
            $('#exp_month').val(result.exp_month);
            $('#exp_year').val(result.exp_year);
        }
    });*/
}

$(document).on('click', '#change-card-info', function(){

    var exp_month = $('#exp_month').val();
    var exp_year = $('#exp_year').val();
    var company_id = $('#company_id').val();
    if($("#edit_card_details").valid()){
        $.ajax({
            url: "/update/card/detail",
            type: 'POST',
            dataType: 'json',
            data : {exp_month:exp_month,exp_year:exp_year},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                if(result.success == 'true') {
                    $(".splash").hide();

                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#edit-card-details').modal('hide');
                    getCreditCardDetail(company_id);
                }
            }
        });
    }
});

function setDefaultCard(cardId, companyId){

    swal({
            title: "Are you sure?",
            text: "Are you sure do you want to set this card as a default!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes,",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "/active/company/card",
                    type: 'POST',
                    dataType: 'json',
                    data:{'status':status, 'card_id':cardId},
                    success: function(result){
                        if(result.success == "true") {
                            //$("#license-table-manager").dataTable().fnDestroy();
                            swal("Card Active!", "Set as a default.", "success");
                        }
                        getCreditCardDetail(companyId);
                    }
                });
            } else {
                swal("Cancelled", "Your card details are safe :)", "error");
            }
        });

}


function editCardDetails(company_id){
    $.ajax({
        url: "/get/card/detail/"+company_id,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            $(".splash").hide();
            $('#edit-card-details').modal('show');
            $('#card_number').val('XXXX XXXX XXXX '+result.CardNumber);
            $('#exp_month').val(result.exp_month);
            $('#exp_year').val(result.exp_year);
        }
    });
}


$(document).on('click', '#new-card-btn', function(){
    $('#new-card-modal').modal('show');
});


$(document).on('click', '#addCompanyCard', function(){

    var company_id = $('#company_id').val();

    var card_number             = $('#new_card_number').val();
    var ccv_number              = $('#new_ccv_number').val();
    var exp_month               = $('#new_exp_month').val();
    var exp_year                = $('#new_exp_year').val();

    paymentForm =$('#payment_form');

    paymentForm.validate({
        rules: {
            new_card_number: {
                required: true,
                newCreditcardtypes:"#new_card_number"
            },
            new_ccv_number: {
                required: true,
                val_ccv_number: "#new_ccv_number"

            },
            new_ccv_number: {
                required: true,
                newCreditcardexpiry:"#new_ccv_number"
            },
            terms:{
                required:true
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
            terms: {
                required: "Please agree with terms of Service"
            }
        }

    });

    if(paymentForm.valid()){
        //console.log(exp_month);
        $.ajax({
            url: "/company/addCompanyPaymentCard",
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $(".splash").show();
            },
            data:{company_id:company_id,card_number: card_number , ccv_number:ccv_number, exp_month:exp_month ,exp_year:exp_year},
            success: function(result){
                //console.log(exp_month);
                var url      = window.location.host;
                if(result.success == 'true') {
                    //window.location = 'http://'+url+'/change/company/info';
                    //window.location = 'http://'+url+'/dashboard';
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#new-card-modal').modal('hide');
                    $(".splash").hide();
                    getCreditCardDetail(company_id);
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

});

$(function() {

    var message = '';
    jQuery.validator.addMethod("newCreditcardtypes", function(value, element, param) {
        if (/[^0-9-]+/.test(value)) {
            return false;
        } else {
            return true;
        }

        // if (value.length ==16) {
        // return true
        //}

    }, "Please enter a valid credit card number.");
    $.validator.addMethod('newCreditcardexpiry', function(value, element) {
        var cc_exp_year = $('#new_exp_year').val();
        var cc_exp_month = $('#new_exp_month').val();
        expiry = cc_exp_year + cc_exp_month,
            date = new Date(),
            month = date.getMonth() + 1,
            now = '' + date.getFullYear() + (month < 10 ? '0' + month : month);

        return expiry > now;
    }, 'Please enter valid expiration month');

    $.validator.addMethod("val_ccv_number",
        function(value, element, params){
            var ccv_number = $('#new_ccv_number').val();
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


