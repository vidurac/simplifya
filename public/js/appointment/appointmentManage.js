$(function () {

   jQuery('#startDatePicker').datetimepicker({
   });


    var clasRows = $("#reqClassifictionTable > tbody > tr").length;
    if(clasRows == 0){
       $("#appointmentClassificationRow").hide();
    }


    if($("#company_location").val() != ""){
        getLicenses();
    }

    $('#license_types_edit').select2({
        placeholder: "Select License"
    });

   $('#license_types').select2({
       placeholder: "Select License"
   });

    $("#paynow_btn").click(function(e){
        e.preventDefault();
        var currentDateTime = new Date();
        var inspecDateTime = new Date($("#startDate").val());

        if(inspecDateTime.getTime() > currentDateTime.getTime()){
            createAppointment(true,false);
        }
        else{
            swal({
                title: "Error!",
                text: "Audit Date and Time Should be Greater Than the Current Date and Time",
                type: "error"
            });
        }
    });

    $("#company_name").change(function(){
        var companyId = $(this).val();
        clearData("business");
        if(companyId != ""){
            getMJBLocation(companyId);
        }

    });

    $("#company_location").change(function(){
        clearData("");
        if($(this).val() != ""){
            getAssignTo();
            getLicenses();

        }
    });


    $(document).on('change', '#license_types', function(){
        var cc_ge_foc=$("#cc_ge_foc").val();
        var license_id = $('#license_types').val();
        var type = $("#audit_type").val();
        $("#licence_fee_div").removeClass("hidden");
        if(license_id == null){

            $('#cost_amount').html("0");
            $('#amount_cost').val("0");

            $( "#licence_fee li" ).each(function( index ) {
                if(index != 0){
                    $(this).remove();
                }
            });
            $("#licence_fee_div").addClass("hidden");

        }
        else{
            $.ajax({
                type: 'POST',
                url: '/get/licenses/amount',
                beforeSend: function() {
                    $(".splash").show();
                },
                data: { location_id : license_id, type: type },
                success: function(response) {
                    var fee=0;
                    var licence_name = "";
                    var licence_fee = 0;
                    var licenceId = 0;
                    var html = "";
                    for (i = 0; i < response.data.length; i++) {
                        if(type == 1){
                            fee += parseFloat(response.data[i].checklist_fee_inhouse);
                        }
                        else{
                            //console.log(license_id[i]);
                            licenceId = response.data[i].id;
                            fee += parseFloat(response.data[i].checklist_fee);
                            licence_name = response.data[i].name;
                            licence_fee = response.data[i].checklist_fee;

                            html+= '<li id="licenceID_'+licenceId+'"><div class="col-xs-7 col-sm-9 control-label text-left">'+licence_name+'</div>'+
                                '<div class="col-xs-5 col-sm-3 text-right"> $'+ licence_fee+'</div></li>';

                               /* if(i == (response.data.length -1)){
                                    $('#licence_fee').append(html);

                            }*/
                        }

                    }

                    $('#licence_fee').html(html);
                    var amount = parseInt(fee);
                    $('#amount_cost').val(fee.toFixed(2));
                    // $('#cost_amount').html(fee.toFixed(2));
                    if(cc_ge_foc==1){
                    $('#free_label').html('Free');
                    $('#cost_amount').html('<strike>'+fee.toFixed(2)+'</strike>');

                    }else{
                    $('#cost_amount').html(fee.toFixed(2));

                    }
                    $(".splash").hide();
                },
                error: function(xhr){
                    $(".splash").hide();
                }
            });
        }
    });

    $("#edit_appointment").click(function(){
        var appointmentId = $("#appointmentID").val();
        updateAppointment(appointmentId);
    });

    $("span ._remove").on('click',function(){
        console.log("close");
        $( "#licence_fee li" ).each(function( index ) {
            if(index > 0){
                $(this).remove();
                /*var lic_id =  $(this).attr('id');
                if( lic_id != 'undefined'){
                    if(lic_id != 'licenceID_'+license_id[index]){
                        console.log(lic_id);
                        //$('#licenceID_'+license_id[index]).remove();
                    }

                    //$('#licence_fee').append(html);
                }*/
            }

        });

    });

    $("#companyAppForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="terms"){
                error.insertAfter(document.getElementById('err-terms'));
            } else if(element.attr('id')=="startDate"){
                error.insertAfter(document.getElementById('err-date'));
            } else if(element.attr('name')=="license_types"){
                error.insertAfter(document.getElementById('err_license'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "company_name":{
                required: true
            },
            "company_location":{
                required: true
            },
            "assign_to" : {
                required: true
            },
            "startDate" : {
                required: true
            },
            "license_types" : {
                required: true
            },
            "terms" : {
                required: true
            },
            "card_number": {
                required: true,
                creditcardtypes:"#card_number"
            },
            "ccv_number": {
                required: true,
                val_ccv_number: "#ccv_number"

            },
            "exp_month": {
                required: true,
                creditcardexpiry:"#exp_month"
            }
        },
        messages: {
            "assign_to": {
                required: "Please select assigner"
            },
            "startDate" : {
                required: "Please specify a date & time"
            },
            "license_types" : {
                required: "Please specify one or more license types"
            },
            "terms" : {
                required: "Please agree with terms of Service"
            }
        },
        /*submitHandler:function(form){
         $(form).ajaxSubmit({
         dataType: "json",
         type: 'POST',
         success: function(data){

         },
         error: function(){
         console.error('Ajax Error');
         },
         beforeSend: function() {

         },
         complete: function(xhr) {
         }
         });
         }*/
    });


    // Create Appointment click function (in-house)
    $("#create_appointment").click(function(e){

        e.preventDefault();
        var currentDateTime = new Date();
        var inspecDateTime = new Date($("#startDate").val());

        if(inspecDateTime.getTime() > currentDateTime.getTime()){
                createAppointment(false,false);
        }
        else{
            swal({
                title: "Error!",
                text: "Audit Date and Time Should be Greater Than the Current Date and Time",
                type: "error"
            });
        }

    });

    // Create Appointment for Free of charge (in-house)
    $("#generate_checkist").click(function(e){

        e.preventDefault();
        var currentDateTime = new Date();
        var inspecDateTime = new Date($("#startDate").val());

        if(inspecDateTime.getTime() > currentDateTime.getTime()){
                createAppointment(false,true);
        }
        else{
            swal({
                title: "Error!",
                text: "Audit Date and Time Should be Greater Than the Current Date and Time",
                type: "error"
            });
        }

    });

    $("#cancel_appointment").click(function(e){
        e.preventDefault();
        swal({
            title: "",
            text: "Are you sure you want to cancel this appointment?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                var appointmentId = $("#appointmentID").val();
                // console.log('appointment id : ' + appointmentId);
                $.ajax({
                    url: "/appointment/cancel",
                    type: 'POST',
                    dataType: 'json',
                    data:{appointmentId:appointmentId},
                    beforeSend: function() {
                        $(".splash").show();
                    },
                    success: function(result){
                        $(".splash").hide();
                        window.location.assign("/appointment");
                    },
                    error: function(result) {
                        $(".splash").hide();
                    }
                });
            }
        });
    });


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


// update appointment function
function updateAppointment(appointmentId){
    var isValid = $("#companyAppForm").valid();
    var assignTo = $("#assign_person").val();
    var dateTime = $("#startDate").val();


    if(isValid){
        $.ajax({
            url: '/appointment/update',
            type: 'POST',
            data: { appointmentId : appointmentId, inspectorId: assignTo, date: dateTime},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(response) {
                $(".splash").hide();
                if(response.success == "false"){
                    swal({
                        title: "Error!",
                        text: result.message
                    });
                }
                else{
                    window.location.assign("/appointment");
                }
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }

}


//clear location and license
function clearData(type){

    if(type == "business"){
        $('#company_location').html('');
        $("#license_types").empty();
        $('#license_types').select2('val', '');
        $('#cost_amount').html("0");
        $('#amount_cost').val("0");
        $('#assign_person').html('');
        $('#assign_person').append('<option value=""> Select</option>');

    }
    else{
        $("#license_types").empty();
        $('#license_types').select2('val', '');
        $('#cost_amount').html("0");
        $('#amount_cost').val("0");
        $('#assign_person').html('');
    }



}


// get Licence
function getLicenses() {
    var company_id = $("#company_name").val();
    var location = $('#company_location').val();
    $.ajax({
        url: '/get/company/licenses',
        type: 'POST',
        data: { location_id : location, company_id: company_id },
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(response) {
            $('#license_types').html('');
            $.each(response.data, function($key, $value){
                if($value.master_license.status == "1"){
                    $('#license_types').append('<option value="'+ $value.master_license.id +'">'+ $value.master_license.name +'</option>');
                }

            });

            $(".splash").hide();
        },
        error: function(xhr){
            $(".splash").hide();
        }
    });
}


// get assign to users
function getAssignTo(){
    var company_id = $("#company_name").val();
    var location = $('#company_location').val();
    $.ajax({
        url: '/get/company/assignTo',
        type: 'GET',
        data: { location_id : location, company_id: company_id },
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(response) {
            $('#assign_person').html('');
            $('#assign_person').append('<option value=""> Select</option>');
            $.each(response.data, function($key, $value){
                // if mj business
                if(response.entity_type == 2){
                    if($value.company_user.length > 0 && $value.master_user_group_id == 3){
                        $('#assign_person').append('<option value="'+ $value.id +'">'+ $value.name +'</option>');
                    }
                    else if($value.master_user_group_id == 2){
                        $('#assign_person').append('<option value="'+ $value.id +'">'+ $value.name +'</option>');
                    }
                }
                else{
                    $('#assign_person').append('<option value="'+ $value.id +'">'+ $value.name +'</option>');
                }


            });

            $(".splash").hide();
        },
        error: function(xhr){
            $(".splash").hide();
        }
    });

}


//create an appointment
function createAppointment(isPayment,isFOC){

    var isValid = $("#companyAppForm").valid();
    var licence = $("#license_types").val();

  if(isValid){
        var appointment_type =  getURLParameter('manage');
        var audit_type = $("#audit_type").val();
        var company_location = $("#company_location").val();
        var to_company_id = $("#company_name").val();
        var comment = $("#comment").val();
        var assign_to = $("#assign_person").val();
        var startDate = new Date($("#startDate").val());
        var startDate = $("#startDate").val();
        var license_types = $("#license_types").val();
        var from_company_id = $("#from_company_id").val();
        var amount_cost = (isFOC)?0:$("#amount_cost").val();
        var classifications = findNonReqClassifictions();


        var data = {appointment_type: appointment_type, audit_type: audit_type, to_company_id: to_company_id, company_location: company_location, comment: comment, assign_to: assign_to, startDate: startDate, license_types: license_types, from_company_id: from_company_id, amount_cost: amount_cost, classifications: classifications, isPayment: isPayment,isFOC:isFOC}

        $.ajax({
            url: "/appointment/store",
            type: 'POST',
            dataType: 'json',
            data : data,
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                $(".splash").hide();
                if(result.type == "not_found"){
                    swal({
                        title: "Error!",
                        text: result.message
                    });
                }
                else if(result.success == "false"){
                    swal({
                        title: "Error!",
                        text: result.message
                    });
                }
                else{
                   window.location.assign("/appointment");
                }

            },
            error: function(result){
                $(".splash").hide();
                console.log(result);
                swal({
                    title: "Error!",
                    text: result.message
                });
            }

        });
    }
}


// Get url parameters
function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}


// get MJB Location
function getMJBLocation(company_id)
{
    $.ajax({
        type: 'POST',
        url: '/company/location/'+company_id,
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(response) {
            $('#company_location').html('');
            if(response.message.length > 0){
                $('#company_location').append('<option value="">Select</option>');
                for (i = 0; i < response.message.length; i++)
                {
                    $('#company_location').append('<option value="'+ response.message[i].id +'">'+ response.message[i].name +'</option>');
                }
                $(".splash").hide();

            }
            else{
                $('#company_location').html('');
                $('#company_location').append('<option value="">Select</option>');
                $(".splash").hide();
            }
        }
    });
}

//find classifications
function findNonReqClassifictions(){
    var classifications = [];
    $("#reqClassifictionTable").find('select').each (function() {
        var attr = $(this).attr('id');
        var val = $("#" + attr).val();
        var classificationId = $(this).attr('classification-id');

        if(val != null && val != ""){
            classifications.push({classificationId: classificationId, value: val});
        }
    });

    return classifications;
}



//if cc and gov does not added card details to system
$("#appointment_payment").on('click',function(){

    //e.preventDefault();
    var currentDateTime = new Date();
    var inspecDateTime = new Date($("#startDate").val());

    var company_id = $('#company_id').val();
    var payment_type = 'subscription';

    var card_number             = $('#card_number').val();
    var ccv_number              = $('#ccv_number').val();
    var exp_month               = $('#exp_month').val();
    var exp_year                = $('#exp_year').val();
    var appointment_fee            = $('#amount_cost').val();
    var mjb_company_status      = $('#mjb_company_status').val();
    var entity_type             = $('#entity_type').val();

    if(inspecDateTime.getTime() > currentDateTime.getTime()){
        //paymentForm =$('#companyAppForm');

        $('#companyAppForm').validate({
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

        if( $('#companyAppForm').valid()){
            $.ajax({
                url: "/company/addCompanyPaymentCard",
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $(".splash").show();
                },
                data:{entity_type:entity_type,card_number: card_number , ccv_number:ccv_number, exp_month:exp_month ,exp_year:exp_year ,subscription_fee:appointment_fee, payment_type:payment_type},
                success: function(result){
                    if(result.success == 'true') {
                        createAppointment(false,false);
                    }
                },
                error: function(result) {
                    $(".splash").hide();
                }
            });
        }

    } else{
        swal({
            title: "Error!",
            text: "Audit Date and Time Should be Greater Than the Current Date and Time",
            type: "error"
        });
    }


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


// jquery validation plugin
function validateAppointmentFrom(){
    $("#companyAppForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="terms"){
                error.insertAfter(document.getElementById('err-terms'));
            } else if(element.attr('id')=="startDate"){
                error.insertAfter(document.getElementById('err-date'));
            } else if(element.attr('name')=="license_types"){
                error.insertAfter(document.getElementById('err_license'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "company_name":{
                required: true
            },
            "company_location":{
                required: true
            },
            "assign_to" : {
                required: true
            },
            "startDate" : {
                required: true
            },
            "license_types" : {
                required: true
            },
            "terms" : {
                required: true
            }
        },
        messages: {
            "assign_to": {
                required: "Please select assigner"
            },
            "startDate" : {
                required: "Please specify a date & time"
            },
            "license_types" : {
                required: "Please specify one or more license types"
            },
            "terms" : {
                required: "Please agree with terms of Service"
            }
        },
        /*submitHandler:function(form){
         $(form).ajaxSubmit({
         dataType: "json",
         type: 'POST',
         success: function(data){

         },
         error: function(){
         console.error('Ajax Error');
         },
         beforeSend: function() {

         },
         complete: function(xhr) {
         }
         });
         }*/
    });

}




