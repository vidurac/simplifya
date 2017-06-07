/**
 * Created by Harsha on 5/6/2016.
 */
var register_form = {};
var business_location_form = {};
var business_location_dataTable = {};
var employee_dataTable = {};
var invite_users = {};
var edit_invite_users = {};
var business_location = {};
var active_tab;
var id;
var next_id;
var active_id;
var location_id;
var license_id;
var company_licenses =0;
var company_users =0;

jQuery(function($){
    $("#edit_phone_no").mask("(999) 999-9999")
});

$(function() {

    $(document).on('change', '#permission_level', function(){
        if($(this).val() == "2"){
            $('#permissionDescription_invite').text("Can add/remove staff, add business locations, add licenses, request 3rd Party audits, initiate self-audits, conduct self-audits, view all audit reports, and assign Action Items.")
        }
        else if($(this).val() == "3"){
            $('#permissionDescription_invite').text("Can add/remove staff, view audit reports for their location, conduct self-audits, respond to Action Items, and assign Action Items.");
        }
        else if($(this).val() == "4"){
            $('#permissionDescription_invite').text("Can respond to Action Items.");
        }
        else{
            $('#permissionDescription_invite').text("");
        }
    });


    $(document).on('change', '#edit_permission_level', function(){
        if($(this).val() == "2"){
            $('#editPermissionDescription_invite').text("Can add/remove staff, add business locations, add licenses, request 3rd Party audits, initiate self-audits, conduct self-audits, view all audit reports, and assign Action Items.")
        }
        else if($(this).val() == "3"){
            $('#editPermissionDescription_invite').text("Can add/remove staff, view audit reports for their location, conduct self-audits, respond to Action Items, and assign Action Items.");
        }
        else if($(this).val() == "4"){
            $('#editPermissionDescription_invite').text("Can respond to Action Items.");
        }
        else{
            $('#editPermissionDescription_invite').text("");
        }
    });


    $.validator.addMethod('edit_allow_location', function(value, element) {
        var permission_level = $('#edit_permission_level').val();
        if((permission_level  != 1) && (permission_level  != 2) && (permission_level  != 5) && (permission_level  != 7)) {
            if(value !==null && value.length > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, 'The business location is required.');

    $.validator.addMethod('allow_location', function(value, element) {
        var permission_level = $('#permission_level').val();

        if((permission_level  != 1) && (permission_level  != 2) && (permission_level  != 5) && (permission_level  != 7)) {
            if(value !==null && value.length > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, 'The business location is required.');

    $.validator.addMethod('phone_numbers', function(value, element) {
        var phone = $('#phone_no').val(),
        intRegex = /[0-9 -()+]+$/;
        if((phone.length < 6) || (!intRegex.test(phone)))
        {
            return false;
        } else {
            return true;
        }
    }, 'Please enter a valid phone number.');

    $.validator.addMethod('edit_phone_numbers', function(value, element) {
        var phone = $('#edit_phone_no').val(),
            intRegex = /[0-9 -()+]+$/;
        if((phone.length < 6) || (!intRegex.test(phone)))
        {
            return false;
        } else {
            return true;
        }
    }, 'Please enter a valid phone number.');

    $.validator.addMethod("zipcode", function(value, element) {
        return this.optional(element) || /^\d{5}(?:-\d{4})?$/.test(value);
    }, "The value is not valid zipcode");

     business_location = $("#business_location_form").validate({
        rules: {
            name_of_location: {
                required: true,
                maxlength: 50
            },
            add_line_1: {
                required: true,
                maxlength: 50
            },
            add_line_2: {
                maxlength: 50
            },
            country:{
                required: true
            },
            state:{
                required: true
            },
            cities:{
                required: true
            },
            zip_code:{
                required: true,
                zipcode: true,
            },
            phone_no:{
                required: true
            }
        },
        messages: {
            name_of_location:{
                required: "The name field is required"
            },
            add_line_1:{
                required: "The address line 1 is required"
            },
            country:{
                required: "The country field is required"
            },
            state:{
                required: "The state is required"
            },
            cities:{
                required: "The city is required"
            },
            zip_code:{
                required: "The zip code is required"
            },
            phone_no:{
                required: "The phone number is required"
            }
        }
    });

    $('#business_location_form_edit').validate({
        rules: {
            edit_name_of_location: {
                required: true
            },
            edit_add_line_1: {
                required: true
            },
            edit_country:{
                required: true
            },
            edit_state:{
                required: true
            },
            edit_cities:{
                required: true
            },
            edit_zip_code:{
                required: true
            },
            edit_phone_no:{
                required: true
            }

        },
        messages: {
            edit_name_of_location:{
                required: "The name field is required"
            },
            edit_add_line_1:{
                required: "The address line 1 is required"
            },
            edit_country:{
                required: "The country field is required"
            },
            edit_state:{
                required: "The state is required"
            },
            edit_cities:{
                required: "The city is required"
            },
            edit_zip_code:{
                required: "The zip code is required"
            },
            edit_phone_no:{
                required: "The phone number is required"
            }
        }
    });

    $('#license_location_form').validate({
        rules: {
            company_location: {
                required: true
            },
            licen_type: {
                required: true
            },
            licen_no:{
                required: true
            }
        },
        messages: {
            company_location:{
                required: "The location field is required"
            },
            licen_type:{
                required: "The license type is required"
            },
            licen_no:{
                required: "The license number  is required"
            }
        }
    });

    invite_users = $('#invite_employ_form').validate({
        rules: {
            name: {
                required: true
            },
            email_address:{
                required: true,
                email: true
            },
            location:{
                allow_location: '#location'
            },
            permission_level:{
                required: true
            }
        },
        messages: {
            name:{
                required: "The name is required"
            },
            email_address:{
                required: "The email is required"
            },
            location:{
                required: "The location is required"
            },
            permission_level:{
                required: "The permission level is required"
            }
        }
    });

    edit_invite_users = $('#user_details_form_edit').validate({
        rules: {
            edit_name: {
                required: true
            },
            edit_email_address:{
                required: true,
                email: true
            },
            edit_locations:{
                edit_allow_location: '#edit_locations'
            },
            edit_permission_level:{
                required: true
            }
        },
        messages: {
            edit_name:{
                required: "The name is required"
            },
            edit_email_address:{
                required: "The email is required"
            },
            edit_locations:{
                required: "The location is required"
            },
            edit_permission_level:{
                required: "The permission level is required"
            }
        }
    });

    /*$('#payment_form').validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="terms"){
                error.insertAfter(document.getElementById('err-terms'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            payment_subscription_fee: {
                required: true
            },
            terms: {
                required: true
            }
        },
        messages: {
            payment_subscription_fee: {
                required: "The subscription fee is required"
            },
            terms:{
                required: "Please agree with terms of Service"
            }
        }
    });*/
    $('#edit_license_location_form').validate({
        rules: {
            edit_company_location: {
                required: true
            },
            edit_licen_type: {
                required: true
            },
            edit_licen_no:{
                required: true
            }
        },
        messages: {
            edit_company_location:{
                required: "The location is required"
            },
            edit_licen_type:{
                required: "The license type is required"
            },
            edit_licen_no:{
                required: "The license number is required"
            }
        }
    });

    $(document).on('click', '#invite-emp', function(){
        var company_id = $('#company_id').val();
        getPermissionLevelById(company_id);

    });

    countryChange($("#country").val());

    // country change function
    $("#country").change(function(){
        var countryId = $(this).val();
        countryChange(countryId);
    });

    // state change function
    $("#state").change(function(){
        var stateId = $(this).val();

        jQuery.ajax({
            type: 'GET',
            url: "/question/getCities",
            async: false,
            data: { stateId: stateId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (result) {

                $("#cities").empty();
                $("#cities").append('<option value=""> Select City </option>');
                if(result.data != null){
                    $.each(result.data.master_city, function(incex, value){
                        $("#cities").append($('<option>', {value: value.id, text: value.name}));
                    });
                }

            },
            error: function (result) {

            }
        });
    });

    // country change function
    $("#edit_country").change(function(){
        var countryId = $(this).val();
        jQuery.ajax({
            type: 'GET',
            url: "/question/getStates",
            async: false,
            data: { countryId: countryId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (result) {

                $("#edit_state").empty();
                $("#edit_state").append('<option value="">Select State</option>');

                $("#edit_cities").empty();
                $("#edit_cities").append('<option value="">Select City</option>');

                $("#cities").empty();
                if(result.data != null){
                    $.each(result.data.master_states, function(incex, value){
                        if(value.status == 1) {
                            $("#edit_state").append('<option value="' + value.id + '"> ' + value.name + '</option>');
                        }
                    });
                }

            },
            error: function (result) {

            }
        });
    });

    // state change function
    $("#edit_state").change(function(){
        var stateId = $(this).val();

        jQuery.ajax({
            type: 'GET',
            url: "/question/getCities",
            async: false,
            data: { stateId: stateId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (result) {

                $("#edit_cities").empty();

                if(result.data != null){
                    $.each(result.data.master_city, function(incex, value){
                        $("#edit_cities").append($('<option>', {value: value.id, text: value.name}));
                    });
                }

            },
            error: function (result) {

            }
        });
    });

    // $("#subscription_plan_accordion").accordion({ event: "mouseup" });
});

/*
 * Add Business Location
 */
$(document).on('click', '#add-location', function(){
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
                if(result.success == 'true') {
                    $(".splash").hide();
                    clearLocationForm();
                    $('#business_location_tbl').dataTable().fnDestroy();
                    countryChange(county);
                    business_location_tbl(company_id);
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                } else {
                    $(".splash").hide();
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
});

/*
 * Edit Business Location
 */
$(document).on('click', '.save-business-changes', function(){
    var name = $('#edit_name_of_location').val();
    var edit_location_id = $('#edit_location_id').val();
    var address_01 = $('#edit_add_line_1').val();
    var address_02 = $('#edit_add_line_2').val();
    var county = $('#edit_country').val();
    var state = $('#edit_state').val();
    var city = $('#edit_cities').val();
    var zip_code = $('#edit_zip_code').val();
    var phone_no = $('#edit_phone_no').val();
    var company_id = $('#company_id').val();

    if($("#business_location_form_edit").valid()){
        $.ajax({
            url: "/edit/company/location",
            type: 'POST',
            dataType: 'json',
            data : {name_of_location:name,cities:city,state:state,address_line_1:address_01,address_line_2:address_02,phone_number:phone_no,'edit_location_id':edit_location_id,zip_code:zip_code},
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#edit-business-location').modal('hide');
                    $('#business_location_tbl').dataTable().fnDestroy();
                    business_location_tbl_new(company_id);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
})


function countryChange(countryId) {
    jQuery.ajax({
        type: 'GET',
        url: "/question/getStates",
        async: false,
        data: { countryId: countryId},
        dataType: "json",
        beforeSend: function () {
        },
        success: function (result) {

            $("#state").empty();
            $("#state").append('<option value=""> Select State </option>');

            $("#cities").empty();
            $("#cities").append('<option value=""> Select City </option>');
            if(result.data != null){
                $.each(result.data.master_states, function(incex, value){
                    if(value.status == 1) {
                        $("#state").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    }
                });
            }

        },
        error: function (result) {

        }
    });
}


/*
 * Change Business Location View From Display
 */
function changeBusinessLocation(location_id)
{
    location_id = location_id
    $.ajax({
        url: "/get/business/location/"+location_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            $('#edit_location_id').val(location_id);
            $('#edit_name_of_location').val(result.data.name);
            $('#edit_add_line_1').val(result.data.address_1);
            $('#edit_add_line_2').val(result.data.address_2);
            $('#edit_country').val(result.data.country_id);
            $('#edit_zip_code').val(result.data.zip_code);
            $('#edit_phone_no').val(result.data.phone_no);
            if(result != null){
                $("#edit_state").empty().append('<option value="">Select State</option>');
                $("#edit_cities").empty().append('<option value="">Select City</option>');
                $.each(result.data.states, function(index, value){
                    $("#edit_state").append($('<option>', {value: value.id, text: value.name}));
                });

                $.each(result.data.cities, function(index, value){
                    $("#edit_cities").append($('<option>', {value: value.id, text: value.name}));
                });

                $('#edit_state').val(result.data.states_id);
                $('#edit_cities').val(result.data.city_id);
            }

            $('#edit-business-location').modal('show');
        }
    });
}

/*
 * Delete Business location Form
 */
function changeBusinessLocationStatus(location_id, status)
{
    var company_id = $('#company_id').val();

        if(status == 0) {
            $.ajax({
                url: "/checkUsersInLocations/"+location_id,
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.success == 'true') {
                        swal({
                                title: "Are you sure?",
                                text: "Your will not be able to recover this business location!",
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
                                    swal("Deleted!", "Your business location has been deleted.", "success");
                                    $.ajax({
                                        url: "/change/business/location/status",
                                        type: 'GET',
                                        dataType: 'json',
                                        data:{'status':status, 'location_id':location_id},
                                        success: function(result){
                                            if(result.success == "true") {
                                                $("#business_location_tbl").dataTable().fnDestroy();
                                                business_location_tbl_new(company_id);
                                            }

                                        }
                                    });
                                } else {
                                    swal("Cancelled", "Your business location is safe :)", "error");
                                }
                            });

                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    }
                }
            });
        } else {
            $.ajax({
                url: "/checkUsersInLocations/"+location_id,
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.success == 'true') {
                        $.ajax({
                            url: "/change/business/location/status",
                            type: 'GET',
                            dataType: 'json',
                            data:{'status':status, 'location_id':location_id},
                            success: function(result){
                                if(result.success == 'true') {
                                    $("#business_location_tbl").dataTable().fnDestroy();
                                    business_location_tbl(company_id);
                                    var msg = result.message;
                                    var msg_type = 'success';
                                    msgAlert(msg, msg_type);
                                }
                            }
                        });
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    }
                }
            });
        }
}

/**
 *
 */
function checkUserInLocation(location_id)
{
    var response;
    $.ajax({
        url: "/checkUsersInLocations/"+location_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            response = result;
        }
    });
    return response;
}
/*
 * Edit Business Location From validation Reset
 */
$(document).on('click', '.cls-edit-busin-locat-form', function(){
    var validator = $( "#business_location_form_edit" ).validate();
    validator.resetForm();
});

/*
 * Invite to Employees
 */
$(document).on('click', '#invite_to_emp', function(){
    var name = $('#name').val();
    var email_address = $('#email_address').val();
    var locations = $('#location').val();
    var permission = $('#permission_level').val();
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    
    if($('#invite_employ_form').valid()) {
        $.ajax({
            url: "/invite/to/employees",
            type: 'POST',
            dataType: 'json',
            data : {name:name,email_address:email_address,locations:locations,permission:permission,company_id:company_id, entity_type:entity_type},
            success: function(result){
                if(result.success == 'true') {
                    clearEmployeeForm();
                    $("#employe-table").dataTable().fnDestroy();
                    invite_employee_tbl(company_id);
                    var msg = result.message;
                    var msg_type = 'success';
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

$(document).on('click', '#add-license', function(){
    var location_id = $('#company_location').val();
    var license_id = $('#licen_type').val();
    var license_no = $('#licen_no').val();
    var company_id = $('#company_id').val();

    if($('#license_location_form').valid()) {
        $.ajax({
            url: "/add/licenses",
            type: 'POST',
            dataType: 'json',
            data : {company_id:company_id,license_id:license_id, location_id:location_id,license_no:license_no},
            success: function(result){
                if(result.success == 'true') {
                    clearLicensesForm();
                    $("#licenses_table").dataTable().fnDestroy();
                    licenses_tbl(company_id);
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);

                } else {
                    clearLicensesForm();
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
});

/**
 * Clear and Validation Reset in Add Business Location Form
 */
$(document).on('click', '#clear-location-form', function(){
    clearLocationForm();
});
function clearLocationForm()
{
    $('#country').val(0);
    $('#state').find('option').remove().end().append('<option value="">Select State</option>').val('');
    $('#cities').find('option').remove().end().append('<option value="">Select Cities</option>').val('');
    $('#business_location_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#business_location_form" ).validate();
    validator.resetForm();
    var countryId = $('#country').val();
    countryChange(countryId)
}

/**
 *  Clear and Validation Reset in Add license Form
 *
 */
$(document).on('click', '#clear-license', function(){
    clearLicensesForm();
})

/**
 * cls-location-form
 */
$(document).on('click', '#cls-location-form', function () {
    clearLocationForm();
    $('#add-new-business-location').modal('hide');
})

/**
 * cls-emp-form
 */
$(document).on('click', '#cls-emp-form', function(){
    $('#invite-employees').modal('hide');
    clearEmployeeForm();
})

function clearLicensesForm()
{
    $('#company_location').val('');
    $('#licen_type').val('');
    $('#license_location_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#license_location_form" ).validate();
    validator.resetForm();
}



/**
 *  Wizard next button
 */
$(document).on('click', '.next', function () {
    active_tab = $('#wizardControl a.btn-primary').attr('id');

    var entity_status = $('#entity_status').val();
    id = active_tab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

    if(parseInt(id[1]) == 1) {

        if(cc_ge_subscription == 0 && entity_type != 2) {
            $('#term_condition').css({'display':'none'});
            $('#subscription_row').css({'display':'none'});
            $('#payment_subscription').css({'display':'none'});
            $('#active_account').css({'display':'block'});
        }

        if(entity_status == 5) {
            getSubscriptionPackage(entity_type);
            calculateSubscribtionFee(company_id, entity_type);
            //calculateSubscribtionFeeOnDisableUser(company_id, entity_type);
            cssClassManager(active_tab, 'tab_5');
            // calculateSubscribtionFee(company_id, entity_type);

            $('#step1').css('display','none');
            $('#step5').css('display','block');
        } else if(entity_status == 4) {
            getSubscriptionPackage(entity_type);
            calculateSubscribtionFee(company_id, entity_type);
            cssClassManager(active_tab, 'tab_5');
            $('#step1').css('display', 'none');
            $('#step5').css('display', 'block');

        } else {
            next_id = parseInt(id[1])+1;
            active_id = 'tab_'+next_id;
            $("#business_location_tbl").dataTable().fnDestroy();
            business_location_tbl(company_id);
            cssClassManager(active_tab, active_id);
            $('#step1').css('display','none');
            $('#step2').css('display','block');
        }


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
            cssClassManager(active_tab, active_id);
            getCompanyLocationById(company_id);
            $("#licenses_table").dataTable().fnDestroy();
            licenses_tbl(company_id);
            if(entity_type == 2) {
                $('#step2').css('display','none');
                $('#step3').css('display','block');
            } else if(entity_type == 3 || entity_type == 4){
                $('#location').html();
                $('#location').select2({
                    placeholder: "Select Business Location"
                });
                getCompanyLocationsById(company_id);
                getPermissionLevelById(company_id);
                $("#employe-table").dataTable().fnDestroy();
                invite_employee_tbl(company_id);
                $('#step2').css('display','none');
                $('#step3').css('display','block');
            }

        }
    } else if(parseInt(id[1]) == 3) {
        next_id = parseInt(id[1])+1;
        active_id = 'tab_'+next_id;

        if($( "#licenses_table > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $( "#license_location_form" ).valid();
        } else {
            $('#location').html();
            $('#location').select2({
                placeholder: "Select Business Location"
            });

            getCompanyLocationsById(company_id);
            getPermissionLevelById(company_id);
            $("#employe-table").dataTable().fnDestroy();
            invite_employee_tbl(company_id);
            cssClassManager(active_tab, active_id);
            $('#step3').css('display','none');
            $('#step4').css('display','block');
            $('#payment_subscription').css({'display':'block'});
            //console.log("step ")
        }
    } else if(parseInt(id[1]) == 4) {

        next_id = parseInt(id[1])+1;
        active_id = 'tab_'+next_id;
        if($( "#employe-table > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $( "#invite_employ_form" ).valid();

        } else {
           // if(entity_type != 2){
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
           // }
            cssClassManager(active_tab, active_id);
            $('#step4').css('display','none');
            $('#step5').css('display','block');
        }
    }else if(parseInt(id[1]) == 5){

    }

})

function calculateSubscribtionFee(company_id, entity_type)
{
    var radioSelectedId = $('input[name=subscription_package]:checked').val();
    //console.log("selected plan " + radioSelectedId);
    $.ajax({
        url: "/get/company/subscription",
        type: 'POST',
        dataType: 'json',
        data:{company_id:company_id, entity_type:entity_type},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();

                if(entity_type == 2) {
                    if(result.data.card_detail != null){
                        $('#card_number').val('XXXX XXXX XXXX '+result.data.card_detail.CardNumber);
                        $('#exp_month').val(result.data.card_detail.exp_month);
                        $('#exp_year').val(result.data.card_detail.exp_year);
                        $('#have_card_detail').val('yes');
                        $('#mjb_subscription').text('PAY NOW');
                    } else {
                        $('#have_card_detail').val('no');
                        $("#card_number").attr("readonly", false);
                        //$("#exp_month").attr("readonly", false);
                        //$("#exp_year").attr("readonly", false);
                    }


                    $('#payment_subscription_fee').val(result.data.subscription_fee);
                    $('#payment_company_name').html(result.data.name);

                    var month_fee = '$'+result.data.monthly_fee;
                    //console.log(month_fee);
                    $('.fee').html(month_fee);
                    $('#month_fee').html(month_fee);
                    var totalMonthlyFee = (result.data.monthly_fee*result.data.no_license);
                    $('#total_monthly_fee').html('$'+totalMonthlyFee.toFixed(2));
                    $('#no_of_license').html(result.data.no_license);
                    $('#payment_sub_fee_for_usage').html('$'+result.data.subscription_fee);


                    $('#payment_no_of_license').val(result.data.no_license);
                    $('#payment_business_name').val(result.data.name);
                    $('#payment_entity_type').val(result.data.entity_type);
                } else if(entity_type == 3 || entity_type == 4) {
                    if(result.card_detail != null){
                        $('#card_number').val('XXXX XXXX XXXX '+result.card_detail.CardNumber);
                        $('#exp_month').val(result.card_detail.exp_month);
                        $('#exp_year').val(result.card_detail.exp_year);
                    }
                    $('#payment_subscription_fee').val(result.subscription_fee);
                    $('#no_of_license').hide();
                    $('#payment_business_name').val(result.name);
                    $('#payment_entity_type').val(result.entity_type);
                }

            }
        }
    });
}

function calculateSubscribtionFeeOnDisableUser(company_id, entity_type)
{
    $.ajax({
        url: "/get/company/subscription",
        type: 'POST',
        dataType: 'json',
        data:{company_id:company_id, entity_type:entity_type},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();

                if(entity_type == 2) {
                    if(result.data.card_detail != ''){
                        $('#card_number').val('XXXX XXXX XXXX '+result.data.card_detail.CardNumber);
                        $('#exp_month').val(result.data.card_detail.exp_month);
                        $('#exp_year').val(result.data.card_detail.exp_year);
                    }
                }

            }
        }
    });
}

$(document).on('click', '.prev', function () {
    var active_tab = $('#wizardControl a.btn-primary').attr('id');
    var entity_type = $('#entity_type').val();
    var entity_status = $('#entity_status').val();
    var id = active_tab.split('_');
    var prev_id = '';
    var active_id = '';
    prev_id = parseInt(id[1])-1;
    active_id = 'tab_'+prev_id;
    if(entity_type == 2) {
        if( entity_status == 5) {
            cssClassManager('tab_5', 'tab_1');
            $('#step5').css('display','none');
            $('#step1').css('display','block');
        } else if(entity_status == 4) {
            cssClassManager('tab_5', 'tab_1');
            $('#step5').css('display','none');
            $('#step1').css('display','block');
        } else {
            cssClassManager(active_tab, active_id);
            $('#step'+parseInt(id[1])).css('display','none');
            $('#step'+prev_id).css('display','block');
        }
    } else if(entity_type == 3 || entity_type == 4) {
        if((entity_status == 0 )|| (entity_status == 5) || (entity_status == 4)) {
            cssClassManager('tab_5', 'tab_1');
            if(parseInt(id[1]) == 3) {
                $('#step5').css('display','none');
                $('#step1').css('display','block');
            } else {
                $('#step5').css('display','none');
                $('#step1').css('display','block');
            }
        } else {
            cssClassManager(active_tab, active_id);
            if(parseInt(id[1]) == 3) {
                $('#step3').css('display','none');
                $('#step'+prev_id).css('display','block');
            } else {
                $('#step'+parseInt(id[1])).css('display','none');
                $('#step'+prev_id).css('display','block');
            }
        }

    }
})

function cssClassManager(active_tab, active_id)
{
    $('#'+active_tab).removeClass('btn-primary');
    $('#'+active_tab).addClass('btn-default');
    $('#'+active_id).removeClass('btn-default');
    $('#'+active_id).addClass('btn-primary');
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

function business_location_tbl(company_id)
{
    business_location_dataTable = $('#business_location_tbl').dataTable( {
        "ajax": {
            "url": "/company/locations/"+company_id,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
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

function invite_employee_tbl(company_id)
{
    employee_dataTable = $('#employe-table').dataTable({
        "ajax": {
            "url": "/get/employees/"+company_id,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 3},
            {"mData": 4}
        ]
    });
    $('#employe-table thead > tr > th').removeClass('sorting_asc');
}

/**
 * @param company_id
 */
function changeUserDetails(user_id)
{
    var entity_type = $('#entity_type').val();
    var user_role = $('#master_user_group_id').val();
    $.ajax({
        url: "/get/invite/user/details/"+user_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            getCompanyLocationsById(result.data[0].company_id, 'edit', entity_type);
            getPermissionLevelById(result.data[0].company_id, 'edit', result.data[0].master_user_group_id);

            var location = [];
            $.each(result.data[0].company_user, function(index, value){
                $("#edit_locations option[value='"+value.location_id+"']").attr("selected","selected")
            });

            if(entity_type == 1) {
                $('#edit_locations').prop( "disabled", true );
                $('.edit_location_enable').css("display", "none");
            } else if(entity_type == 2) {
                if(user_role == result.data[0].master_user_group_id) {
                    $('#edit_permission_level').prop( "disabled", true );
                    if(user_role == 2) {
                        $('.edit_location_enable').css( "display",'none');
                    } else {
                        $('.edit_location_enable').css( "display",'block');
                    }
                } else if(user_role == 3) {
                    if(result.data[0].master_user_group_id == 2) {
                        $('#edit_permission_level').prop( "disabled", true );
                        $('.edit_location_enable').css("display", "none");
                    } else {
                        $('#edit_permission_level').prop( "disabled", false );
                        $('.edit_location_enable').css("display", "block");
                    }
                } else if(user_role == 2) {
                    if((result.data[0].master_user_group_id == 3) || (result.data[0].master_user_group_id == 4)) {
                        $('#edit_permission_level').prop( "disabled", false );
                        $('.edit_location_enable').css("display", "block");
                    } else {
                        $('.edit_location_enable').css("display", "block");
                    }
                }
            } else if((entity_type == 3) || (entity_type == 4)) {
                if(user_role == result.data[0].master_user_group_id) {
                    $('#edit_permission_level').prop( "disabled", true );
                    $('.edit_location_enable').css( "display",'none');
                } else {
                    $('#edit_permission_level').prop( "disabled", false );
                    $('.edit_location_enable').css( "display",'block');
                }
            } else {
                $('#edit_permission_level').prop( "disabled", false );
            }
            $('#edit_title').val(result.data[0].title);
            $('#edit_name').val(result.data[0].name);
            $('#edit_email_address').val(result.data[0].email);

            $('#edit-user-details').modal('show');

        }
    });
}

/**
 * Delete Invited Employee
 * @param user_id
 */
function deleteInviteUser(user_id)
{
    var company_id = $('#company_id').val();
    $.ajax({
        url: "/delete/users/"+user_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success == 'true') {
                $("#employe-table").dataTable().fnDestroy();
                invite_employee_tbl(company_id);
                var msg = result.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);

            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        }
    });
}


function licenses_tbl(company_id)
{
    $('#licenses_table').dataTable({
        "ajax": {
            "url": "/company/license/"+company_id,
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
            {"sWidth": "20%", "mData": 3}
        ]
    });
    $('#licenses_table thead > tr > th').removeClass('sorting_asc');
}

$(document).on('click', '.save-user-changes', function(){
    var name = $('#edit_name').val();
    var email_address = $('#edit_email_address').val();
    var locations = $('#edit_locations').val();
    var permission = $('#edit_permission_level').val();
    var company_id = $('#company_id').val();

    if($('#user_details_form_edit').valid()) {
        $.ajax({
            url: "/edit/invite/employees",
            type: 'POST',
            dataType: 'json',
            data : {name:name,email_address:email_address,locations:locations,permission:permission,company_id:company_id},
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#edit-user-details').modal('hide');
                    $("#employe-table").dataTable().fnDestroy();
                    invite_employee_tbl(company_id);
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

function getCompanyLocationsById(company_id, type, entity_type)
{
    $("#location").html('');
    $("#edit_locations").html('');
    if(type == 'edit') {
        $("#edit_locations").select2({
            placeholder: "Business Location",
            allowClear: true
        });
    } else {
        $("#location").select2({
            placeholder: "Business Location",
            allowClear: true
        });
    }
    $.ajax({
        url: "/get/company/locations/"+company_id,
        type: 'GET',
        async: false,
        dataType: 'json',
        success: function(result){
            if(result != null){
                if(type == 'edit') {
                    $.each(result.locations, function(index, value){
                        $("#edit_locations").append($('<option>', {value: value.id, text: value.name}));
                    });
                } else {
                    $.each(result.locations, function(index, value){
                        $("#location").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
            }
        }
    });
}

function getCompanyLocationById(company_id)
{
    $.ajax({
        url: "/get/company/locations/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result != null){
                $('#company_location').empty().append('<option value="">Select Location</option>');
                $.each(result.locations, function(index, value){
                    $("#company_location").append($('<option>', {value: value.id, text: value.name}));
                });
            }
        }
    });
}

function getCompanyLocationByCompanyId(company_id)
{
    $.ajax({
        url: "/get/company/locations/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result != null){
                $("#edit_company_location").empty().append('<option value="">Select Location</option>');
                $.each(result.locations, function(index, value){
                    $("#edit_company_location").append('<option value="'+value.id+'"> '+value.name+'</option>');
                });
            }
        }
    });
}


// Location change function
$("#company_location").change(function(){
    var location_id = $(this).val();

    if(location_id != "" ) {
        jQuery.ajax({
            type: 'GET',
            url: "/get/licensetypes",
            async: false,
            data: { location_id: location_id},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (result) {
                if(result.data.length != 0){
                    $("#licen_type").empty().append('<option value="">Select Licenses Type</option>');
                    $.each(result.data, function(incex, value){
                        $("#licen_type").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                } else {
                    $("#licen_type").empty().append('<option value="">Select Licenses Type</option>');
                }

            },
            error: function (result) {

            }
        });
    } else {
        $("#licen_type").empty().append('<option value="">Select Licenses Type</option>');
    }

});

// Location change function
$("#edit_company_location").change(function(){
    var location_id = $(this).val();

    jQuery.ajax({
        type: 'GET',
        url: "/get/licensetypes",
        async: false,
        data: { location_id: location_id},
        dataType: "json",
        beforeSend: function () {
        },
        success: function (result) {

            if(result.data != null){
                $("#edit_licen_type").empty().append('<option value=""> Select Entity Type </option>');
                $.each(result.data, function(incex, value){
                    $("#edit_licen_type").append('<option value="'+value.id+'"> '+value.name+'</option>');
                });
            }

        },
        error: function (result) {

        }
    });
});

function getPermissionLevelById(company_id, type, select_id)
{
    $.ajax({
        url: "/get/company/permission/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){ console.log(result);
            if(result != null){
                $("#edit_permission_level").empty().append('<option value="">Select Permission Level</option>');
                if(type == 'edit') {
                    $.each(result.permissions, function(index, value){
                        if(select_id == 2) {
                            $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                        } else {
                            if(value.name !='Master Admin') {
                                $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                            }
                        }

                    });
                    $("#edit_permission_level").val(select_id);
                } else {

                    $("#permission_level").empty().append('<option value="">Select Permission Level</option>');
                    $.each(result.permissions, function(index, value){
                        if(value.name != 'Master Admin') {
                            $("#permission_level").append($('<option>', {value: value.id, text: value.name}));
                        }
                    });
                    $("#permissionDescription_invite").html('');
                }
            }
        }
    });
}

$(document).on('click', '#clear-emp-form', function(){
    clearEmployeeForm();
});

function clearEmployeeForm()
{
    $('#title').val('');
    $("#location").val("").trigger("change");
    $('#permission_level').val('');
    $('#invite_employ_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#invite_employ_form" ).validate();
    validator.resetForm();
}

$('#edit-business-location').on('hidden.bs.modal', function (e) {
    var validator = $( "#business_location_form_edit" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$('#invite-employees').on('hidden.bs.modal', function (e) {
    var validator = $( "#invite_employ_form" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$('#edit-user-details').on('hidden.bs.modal', function (e) {
    var validator = $( "#user_details_form_edit" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$(document).on('click', '.cls-edit-user-form', function(){
    var validator = $('#user_details_form_edit').validate();
    validator.resetForm();
});

$(document).on('click', '#cls-license', function(){
    var validator = $('#edit_license_location_form').validate();
    validator.resetForm();
    $('#edit-license-location').modal('hide');
});

$(document).on('click', '#change-license', function(){
    var location_id = $('#hide_edit_company_location').val();
    var license_id = $('#hide_edit_licen_type').val();
    var license_no = $('#edit_licen_no').val();
    var company_id = $('#company_id').val();

    if($("#edit_license_location_form").valid()){
        $.ajax({
            url: "/change/licenses",
            type: 'POST',
            dataType: 'json',
            data : {company_id:company_id,license_id:license_id, location_id:location_id,license_no:license_no, location_license_id:license_id},
            success: function(result){
                if(result.success == 'true') {
                    $('#edit-license-location').modal('hide');
                    $("#licenses_table").dataTable().fnDestroy();
                    licenses_tbl(company_id);
                    var msg = result.message;
                    var msg_type = 'success';
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

function changeLocationLicense(license_id)
{
    var company_id = $('#company_id').val();
    license_id = license_id;
    getCompanyLocationByCompanyId(company_id);
    setTimeout(function(){
        $.ajax({
            url: "/get/license/details/"+license_id,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                $('#edit_company_location').val(result.data.license_details[0]['company_loc_id']);
                $('#hide_edit_company_location').val(result.data.license_details[0]['company_loc_id']);
                $('#edit_licen_no').val(result.data.license_details[0]['license_number']);
                $('#edit_licen_type').empty().append('<option value="">Select Entity Type</option>');
                $.each(result.data.master_license_type, function(index, value){
                    $('#edit_licen_type').append($('<option>', {value: value.id, text: value.name}));
                });
                setTimeout(function(){
                    $('#edit_licen_type').val(result.data.license_details[0]['master_license_id']);
                }, 1000);
                $('#hide_edit_licen_type').val(result.data.license_details[0]['master_license_id']);
                $('#edit_company_location').prop("disabled", true);
                $('#edit_licen_type').prop("disabled", true);
                $('#edit-license-location').modal('show');
            }
        });
    }, 1000);
}

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
                cancelButtonText: "No, cancel plx!",
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
                        data : {user_id:user_id,status:status},
                        success: function(result){
                            if(result.success == 'true') {
                                //var msg = result.message;
                                //var msg_type = 'success';
                                $("#employe-table").dataTable().fnDestroy();
                                invite_employee_tbl(company_id);
                                //msgAlert(msg, msg_type);
                            } else {
                               // var msg = result.message;
                               // var msg_type = 'error';
                                //msgAlert(msg, msg_type);
                            }

                        }
                    });
                } else {
                    swal("Cancelled", "User details are safe :)", "error");
                }
        });

    }
}

$(document).on('change', '#permission_level', function(){
    var role_id = $(this).val();
    if((role_id == 1) || (role_id == 2) || (role_id == 5) || (role_id == 7)) {
        $('#locations-enable').css("display", "none");
    } else {
        $('#locations-enable').css("display", "block");
    }
});

$(document).on('change', '#location', function() {
    invite_users.element( "#location" );
});

$(document).on('change', '#edit_locations', function(){
    edit_invite_users.element( "#edit_locations" );
});

$(document).on('change', '#edit_permission_level', function() {
    var role_id = $(this).val();
    if((role_id== 1) || (role_id== 2) || (role_id== 5) || (role_id== 7)) {
        $('.edit_location_enable').css("display", "none");
    } else {
        $('.edit_location_enable').css("display", "block");
    }
});

$("#phone_no").mask("(999) 999-9999");
$("#phone_no").on("blur", function() {
    var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );

    if( last.length == 3 ) {
        var move = $(this).val().substr( $(this).val().indexOf("-") - 1, 1 );
        var lastfour = move + last;

        var first = $(this).val().substr( 0, 9 );

        $(this).val( first + '-' + lastfour );
    }
});

$(document).on('click', '#active_account', function() {
     var company_id = $('#company_id').val();

    $.ajax({
        url: "/active/company",
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            $(".splash").show();
        },
        data:{company_id:company_id},
        success: function(result){ console.log(result);
            var url      = window.location.host;
            if(result.success == 'true') {
                window.location = 'http://'+url+'/change/company/info';
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
});


$(document).on('click', '#get_start_view', function() {

    var company_id = $('#company_id').val();
    business_location_tbl(company_id);

    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

        $('#step3').css('display','none');
        $('#step4').css('display','none');
        $('#step5').css('display','none');

    $('#get_start').addClass("info_hidden");
    $('#company_info').removeClass("info_hidden");
    $('#tab_2').removeClass("btn-default").addClass("btn-primary");


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

$(document).on('change', '#coupon_code', function () {
    var coupon_code = $(this).val();
    var sub_fee = $('#payment_sub_fee_for_usageH').val();
    var no_of_license = $('#no_of_license').html();
    var subscription_plan = $('input[name=subscription_package]:checked').val();

    sub_fee = sub_fee.split('$');
    sub_fee = sub_fee[1];

    $.ajax({
        url: "/company/validateCoupon",
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
            $(".splash").show();
        },
        data:{coupon_code:coupon_code,subscription_plan:subscription_plan},
        success: function(result)
        {
            if(coupon_code != "")
            {
                if(result.success)
                {
                    $("#coupon_check_msg").html(result.msg);
                    $("#coupon_check_status").val(result.msg);
                    $("#coupon_check_msg").addClass("valid");
                    $("#coupon_check_msg").removeClass("invalid");

                    getDiscount(coupon_code,sub_fee,subscription_plan,no_of_license);
                }
                else
                {
                    $("#coupon_check_msg").html(result.msg);
                    $("#coupon_check_status").val(result.msg);
                    $("#coupon_check_msg").addClass("invalid");
                    $("#coupon_check_msg").removeClass("valid");

                    //$('#discount').html('$0.00');
                    $('#discount_div').html('');
                    $('#payment_sub_fee_for_usage').html('$' + Number(sub_fee).toFixed(2) );
                    $('#payment_subscription_fee').val(Number(sub_fee).toFixed(2) );
                    $('#coupon_id').val('');
                }
            }
            else
            {
                //$('#discount').html('$0.00');
                $('#discount_div').html('');
                $('#payment_sub_fee_for_usage').html('$' + Number(sub_fee).toFixed(2) );
                $('#payment_subscription_fee').val(Number(sub_fee).toFixed(2) );
                $('#coupon_id').val('');

                $("#coupon_check_msg").html("");
                $("#coupon_check_status").val("");
                $("#coupon_check_msg").removeClass("invalid");
                $("#coupon_check_msg").removeClass("valid");
            }
            $(".splash").hide();
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
});

function getDiscount(coupon_code,sub_fee,subscription_plan,no_of_license)
{
    $.ajax({
        url: "/company/getDiscount",
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
            $(".splash").show();
        },
        async:false,
        data:{coupon_code:coupon_code, sub_fee:sub_fee, subscription_plan:subscription_plan,no_of_license:no_of_license,order:1},
        success: function(result){

            if(result != "")
            {
                var discount_html = '<div class="col-md-8 col-xs-8">Discount:</div> <div class="col-md-4 col-xs-4 text-right value" id="discount"></div>';
                $('#discount_div').html(discount_html);
                $('#discount').html('$' + Number(result.discount).toFixed(2));
                $('#payment_sub_fee_for_usage').html('$' + (sub_fee - result.discount).toFixed(2) );
                $('#payment_subscription_fee').val((sub_fee - result.discount).toFixed(2) );
                $('#coupon_id').val(result.coupon_id);

            }
            else
            {
                $('#discount').html('$0.00');
                $('#payment_sub_fee_for_usage').html('$' + Number(sub_fee).toFixed(2) );
                $('#payment_subscription_fee').val(Number(sub_fee).toFixed(2) );
                $('#coupon_id').val('');
            }

            $(".splash").hide();
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
}

$(document).on('click','#payment_subscription', function(){
    var company_id = $('#company_id').val();
    var payment_type = 'subscription';

    var card_number             = $('#card_number').val();
    var ccv_number              = $('#ccv_number').val();
    var exp_month               = $('#exp_month').val();
    var exp_year                = $('#exp_year').val();
    var subscrib_fee            = $('#payment_subscription_fee').val();
    var entity_type             = $('#entity_type').val();

    paymentForm =$('#payment_form');

    paymentForm.validate({
        rules: {
            card_number: {
                required: true,
                //card_number:"#card_number"
            },
            ccv_number: {
                required: true,
                //val_ccv_number: "#ccv_number"
            },
            exp_month: {
                required: true,
                //exp_month:"#exp_month"
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

    if(paymentForm.valid()){
        $.ajax({
            url: "/company/commonCompanyPayment",
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $(".splash").show();
            },
            data:{entity_type:entity_type,card_number: card_number , ccv_number:ccv_number, exp_month:exp_month ,exp_year:exp_year ,subscription_fee:subscrib_fee, payment_type:payment_type},
            success: function(result){
                var url      = window.location.host;
                if(result.success == 'true') {
                    //window.location = 'http://'+url+'/change/company/info';
                    window.location = 'http://'+url+'/dashboard';
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    //location.reload();
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
});


function selectPaymentPlan() {

    var radioSelectedId = $('input[name=subscription_package]:checked').val();
    //console.log("radioSelectedId value : " + radioSelectedId);
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var is_referral = $('#is_referral').val();

    $("#payment_summary").removeClass("hidden");
    $.ajax({
        url: "/get/subscription/package",
        type: 'POST',
        dataType: 'json',
        data:{company_id:company_id, entity_type:entity_type,package_id:radioSelectedId},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success) {
                $(".splash").hide();
                $('#payment_subscription_fee').val(result.data.subscription_fee);
                $('#payment_company_name').html(result.data.name);

                var month_fee = '$'+result.data.monthly_fee;
                //console.log(month_fee);
                $('.fee').html(month_fee);
                $('#month_fee').html(month_fee);
                var totalMonthlyFee = (result.data.monthly_fee*result.data.no_license);
                $('#total_monthly_fee').html('$'+totalMonthlyFee.toFixed(2));
                $('#no_of_license').html(result.data.no_license);

                if(is_referral == 1)
                {
                    var coupon_amount = $('#coupon_amount').val();
                    //coupon_amount = coupon_amount.split('$');
                    //coupon_amount = coupon_amount[1];

                    coupon_amount = parseFloat(coupon_amount);
                    var amount_type = $('#amount_type').val();
                    var discount = 0;
                    //alert(amount_type)
                    if(amount_type == "percentage")
                    {
                        subscription_fee = (100 - coupon_amount) * result.data.subscription_fee / 100;
                    }
                    if(amount_type == "fixed")
                    {
                        subscription_fee = result.data.subscription_fee - (coupon_amount * result.data.no_license);
                    }

                    discount = result.data.subscription_fee - subscription_fee;

                    $('#payment_sub_fee_for_usage').html('$'+subscription_fee);
                    $('#payment_sub_fee_for_usageH').val('$'+subscription_fee);
                    $('#discount').html('$'+Number(discount).toFixed(2));
                }
                if(is_referral == 0)
                {
                    $('#payment_sub_fee_for_usage').html('$'+result.data.subscription_fee);
                    $('#payment_sub_fee_for_usageH').val('$'+result.data.subscription_fee);
                    $('#discount').html('$0.00');
                }

                $('#payment_no_of_license').val(result.data.no_license);
                $('#payment_business_name').val(result.data.name);
                $('#payment_entity_type').val(result.data.entity_type);
                $('#coupon_code').change();
            }
        }
    });
}

function getSubscriptionPackage(entity_type){

    if(entity_type != ""){
        var html ="";
        $.ajax({
            url: "/company/subscriptionPlans",
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                $(".splash").show();
            },
            data:{entity_type:entity_type},
            success: function(result){
                if(result.success == 'true') {
                    for (i = 0; i < result.data.length; i++) {
                        var tempChecked = '';
                        if (i == 0) {
                            tempChecked = 'checked="checked"';
                        }
                        // html+= '<li >' +
                        //             '<div class="col-md-6 text-left">'+result.data[i].name+'</div>'+
                        //             '<div class="col-md-3 text-right"> $'+ result.data[i].amount+'</div>'+
                        //             '<div class="col-md-2 text-right"><input type="radio" name="subscription_package" id="'+result.data[i].id+'" class="subscription_plan"' + tempChecked +'></div>'+
                        //             '<input type="hidden" value="'+result.data[i].id+'" id="plan_'+result.data[i].id+'" name="validity_id">'+
                        //             '<input type="hidden" value="'+result.data[i].amount+'" name="package_amount">'+
                        //         '</li>';
                        if(result.foc == 0)
                        {
                            html+= '' +
                                '<h3>'+
                                '<label class="accordion-full-width-lbl">' +
                                '<input type="radio" name="subscription_package" id="'+result.data[i].id+'" class="subscription_plan"' + tempChecked +' value="' + result.data[i].id +'">'+
                                '' + result.data[i].name + ' ($' + result.data[i].amount +' per license)'+
                                '</label>'+
                                '</h3>'+
                                '<div>' +
                                '<p>' +result.data[i].description+'</p>'+
                                '<input type="hidden" value="'+result.data[i].id+'" id="plan_'+result.data[i].id+'" name="validity_id">'+
                                '<input type="hidden" value="'+result.data[i].amount+'" name="package_amount">'+
                                '</div>'+
                                '';
                        }

                        if(result.foc == 1)
                        {
                            html+= '' +
                                '<h3>'+
                                '<label class="accordion-full-width-lbl">' +
                                '<input type="radio" name="subscription_package" id="'+result.data[i].id+'" class="subscription_plan"' + tempChecked +' value="' + result.data[i].id +'">'+
                                '' + result.data[i].name + ' <span style="color: #ff0000">Free</span> ' +
                                '</label>'+
                                '</h3>'+
                                '<div>' +
                                '<p>' +result.data[i].description+'</p>'+
                                '<input type="hidden" value="'+result.data[i].id+'" id="plan_'+result.data[i].id+'" name="validity_id">'+
                                '<input type="hidden" value="'+result.data[i].amount+'" name="package_amount">'+
                                '</div>'+
                                '';
                        }


                    }

                    $('#subscription_plan_accordion').html(html);
                    $("#subscription_plan_accordion").accordion({ event: "mouseup" });
                    $("#subscription_plan_accordion").accordion("refresh");
                    $("#1").prop("checked", true);
                    $(".splash").hide();
                    selectPaymentPlan();
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

}


$(document).on('change', '.subscription_plan', function () {

    selectPaymentPlan();

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
            {"sWidth": "10%", "mData": 2},
            {"sWidth": "10%", "mData": 3},
            {"sWidth": "10%", "mData": 4},
            {"sWidth": "5%", "mData": 5},
            {"sWidth": "15%", "mData": 6}
        ]
    });
    $('#business_location_tbl thead > tr > th').removeClass('sorting_asc');
}

$(document).on('click', '#tab_1', function () {
    var activeTab = $('#wizardControl a.btn-primary').attr('id');
    if(activeTab == 'tab_2') {
        cssClassManager(activeTab, 'tab_1');
        $('#step1').css('display', 'block');
        $('#step2').css('display', 'none');
    } else if(activeTab == 'tab_3') {
        cssClassManager(activeTab, 'tab_1');
        $('#step1').css('display', 'block');
        $('#step3').css('display', 'none');
    }
});

$(document).on('click', '#tab_2', function () {

    var activeTab = $('#wizardControl a.btn-primary').attr('id');
    var entity_status = $('#entity_status').val();
    id = activeTab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

    if(activeTab != 'tab_2') {
        if(activeTab == 'tab_1') {
            if(entity_status == 5) {
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
                //calculateSubscribtionFeeOnDisableUser(company_id, entity_type);
                cssClassManager(activeTab, 'tab_5');
                // calculateSubscribtionFee(company_id, entity_type);

                $('#step1').css('display','none');
                $('#step5').css('display','block');
            } else if(entity_status == 4) {
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
                cssClassManager(activeTab, 'tab_5');
                $('#step1').css('display', 'none');
                $('#step5').css('display', 'block');

            } else {
                next_id = parseInt(id[1]);
                active_id = 'tab_2';
                if(activeTab == 'tab_1') {
                    cssClassManager(activeTab, active_id);
                    $('#step1').css('display','none');
                }
                else if(activeTab == 'tab_3') {
                    $("#licenses_table").dataTable().fnDestroy();
                    $('#step3').css('display','none');
                }else if(activeTab == 'tab_4'){
                    cssClassManager(activeTab, active_id);
                    $('#step4').css('display','none');
                } else if(activeTab == 'tab_5'){
                    cssClassManager(activeTab, active_id);
                    $('#step5').css('display','none');
                }
                $("#business_location_tbl").dataTable().fnDestroy();
                business_location_tbl(company_id);
                cssClassManager(activeTab, active_id);
                $('#step2').css('display','block');
            }
        } else {
            if(cc_ge_subscription == 0 && entity_type != 2) {
                $('#term_condition').css({'display':'none'});
                $('#subscription_row').css({'display':'none'});
                $('#payment_subscription').css({'display':'none'});
                $('#active_account').css({'display':'block'});
            }

            if(entity_status == 5) {
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
                //calculateSubscribtionFeeOnDisableUser(company_id, entity_type);
                cssClassManager(activeTab, 'tab_5');
                // calculateSubscribtionFee(company_id, entity_type);

                $('#step1').css('display','none');
                $('#step5').css('display','block');
            } else if(entity_status == 4) {
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
                cssClassManager(activeTab, 'tab_5');
                $('#step1').css('display', 'none');
                $('#step5').css('display', 'block');

            } else {
                next_id = parseInt(id[1]);
                active_id = 'tab_2';
                if(activeTab == 'tab_3') {
                    $("#licenses_table").dataTable().fnDestroy();
                    $('#step3').css('display','none');
                }else if(activeTab == 'tab_4'){
                    cssClassManager(activeTab, active_id);
                    $('#step4').css('display','none');
                } else if(activeTab == 'tab_5'){
                    cssClassManager(activeTab, active_id);
                    $('#step5').css('display','none');
                }
                $("#business_location_tbl").dataTable().fnDestroy();
                business_location_tbl(company_id);
                cssClassManager(activeTab, active_id);
                $('#step2').css('display','block');
            }
        }

    }
})

$(document).on('click', '#tab_3', function () {
    var activeTab = $('#wizardControl a.btn-primary').attr('id');

    id = activeTab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

    if(entity_type != 1) {
        next_id = parseInt(id[1]);
    }else {
        next_id = parseInt(id[1]);
    }
    if(activeTab == 'tab_2') {
        if($( "#business_location_tbl > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $('#business_location_form').valid();
        } else {
            cssClassManager(activeTab, 'tab_3');
            getCompanyLocationById(company_id);
            $("#licenses_table").dataTable().fnDestroy();
            licenses_tbl(company_id);
            if(entity_type == 2) {
                $('#step2').css('display','none');
                $('#step3').css('display','block');
            } else if(entity_type == 3 || entity_type == 4) {
                $('#location').html();
                $('#location').select2({
                    placeholder: "Select Business Location"
                });
                getCompanyLocationsById(company_id);
                getPermissionLevelById(company_id);
                $("#employe-table").dataTable().fnDestroy();
                invite_employee_tbl(company_id);
                $('#step2').css('display','none');
                $('#step3').css('display','block');
            }
        }
    } else if(activeTab == 'tab_4'){
        getCompanyLocationById(company_id);
        $("#licenses_table").dataTable().fnDestroy();
        licenses_tbl(company_id);
        cssClassManager(activeTab, 'tab_3');
        $('#step4').css('display','none');
        $('#step3').css('display','block');
    } else if(activeTab == 'tab_5'){
        cssClassManager(activeTab, 'tab_3');
        getCompanyLocationById(company_id);
        $("#licenses_table").dataTable().fnDestroy();
        licenses_tbl(company_id);
        $('#step5').css('display','none');
        $('#step3').css('display','block');
    } else if(activeTab == 'tab_1'){
        cssClassManager(activeTab, 'tab_3');
        getCompanyLocationsById(company_id);
        getPermissionLevelById(company_id);
        $("#employe-table").dataTable().fnDestroy();
        invite_employee_tbl(company_id);
        $('#step1').css('display','none');
        $('#step3').css('display','block');
    }
});

$(document).on('click', '#tab_4', function () {
    var activeTab = $('#wizardControl a.btn-primary').attr('id');

    id = activeTab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();
    if(activeTab == 'tab_2') {
        if($( "#business_location_tbl > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $('#business_location_form').valid();
        }
        else {
            getLicenseCount(company_id, function( cal_license_count ){
                company_licenses = cal_license_count.data.length
            });
            if(company_licenses == 0) {
                    $( "#license_location_form" ).valid();
                    getCompanyLocationById(company_id);
                    licenses_tbl(company_id);
                    cssClassManager(activeTab, 'tab_3');
                    $('#step2').css('display','none');
                    $('#step3').css('display','block');
            } else {
                getCompanyLocationsById(company_id);
                getPermissionLevelById(company_id);
                $("#employe-table").dataTable().fnDestroy();
                invite_employee_tbl(company_id);
                cssClassManager(activeTab, 'tab_4');
                $('#step2').css('display','none');
                $('#step4').css('display','block');
                $('#payment_subscription').css({'display':'block'});
            }
        }
    } else if(activeTab == 'tab_3') {

        if($( "#licenses_table > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $( "#license_location_form" ).valid();
        } else {
            $('#location').html();
            $('#location').select2({
                placeholder: "Select Business Location"
            });

            getCompanyLocationsById(company_id);
            getPermissionLevelById(company_id);
            $("#employe-table").dataTable().fnDestroy();
            invite_employee_tbl(company_id);
            cssClassManager(activeTab, 'tab_4');
            $('#step3').css('display','none');
            $('#step4').css('display','block');
            $('#payment_subscription').css({'display':'block'});
            //console.log("step ")
        }

    } else if(activeTab == 'tab_5') {
        getCompanyLocationsById(company_id);
        getPermissionLevelById(company_id);
        $("#employe-table").dataTable().fnDestroy();
        invite_employee_tbl(company_id);
        cssClassManager(activeTab, 'tab_4');
        $('#step5').css('display','none');
        $('#step4').css('display','block');
    }
});

$(document).on('click', '#tab_5', function () {
    var activeTab = $('#wizardControl a.btn-primary').attr('id');

    id = activeTab.split('_');
    var company_id = $('#company_id').val();
    var entity_type = $('#entity_type').val();
    var cc_ge_subscription = $('#cc_ge_subscription').val();

    if(activeTab == 'tab_2') {
        if($( "#business_location_tbl > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $('#business_location_form').valid();
        } else {

            getLicenseCount(company_id, function( cal_license_count ){
                company_licenses = cal_license_count.data.length
            });

            if(company_licenses == 0) {
                $( "#license_location_form" ).valid();
                getCompanyLocationById(company_id);
                licenses_tbl(company_id);
                cssClassManager(activeTab, 'tab_3');
                $('#step2').css('display','none');
                $('#step3').css('display','block');

            } else {
                getInviteUserCount(company_id, function (cal_invt_user_count) {
                    company_users =  cal_invt_user_count.data.length;
                })

                if(company_users == 0) {
                    $( "#invite_employ_form" ).valid();

                    getCompanyLocationsById(company_id);
                    getPermissionLevelById(company_id);
                    $("#employe-table").dataTable().fnDestroy();
                    invite_employee_tbl(company_id);
                    cssClassManager(activeTab, 'tab_4');
                    $('#step2').css('display','none');
                    $('#step4').css('display','block');
                } else {
                    // if(entity_type != 2){
                    getSubscriptionPackage(entity_type);
                    calculateSubscribtionFee(company_id, entity_type);
                    // }
                    cssClassManager(activeTab, 'tab_5');
                    $('#step2').css('display','none');
                    $('#step5').css('display','block');
                }
            }
        }
    } else if(activeTab == 'tab_3') {
        if($( "#licenses_table > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $( "#license_location_form" ).valid();
        } else {
            getInviteUserCount(company_id, function (cal_invt_user_count) {
                company_users =  cal_invt_user_count.data.length;
            })

            if(company_users == 0) {
                $( "#invite_employ_form" ).valid();

                getCompanyLocationsById(company_id);
                getPermissionLevelById(company_id);
                $("#employe-table").dataTable().fnDestroy();
                invite_employee_tbl(company_id);
                cssClassManager(activeTab, 'tab_4');
                $('#step3').css('display','none');
                $('#step4').css('display','block');
            } else {
                // if(entity_type != 2){
                getSubscriptionPackage(entity_type);
                calculateSubscribtionFee(company_id, entity_type);
                // }
                cssClassManager(activeTab, 'tab_5');
                $('#step3').css('display','none');
                $('#step5').css('display','block');
            }
        }

    } else if(activeTab == 'tab_4') {
        if($( "#employe-table > tbody > tr > td" ).hasClass( "dataTables_empty" )) {
            $( "#invite_employ_form" ).valid();

        } else {
            // if(entity_type != 2){
            getSubscriptionPackage(entity_type);
            calculateSubscribtionFee(company_id, entity_type);
            // }
            cssClassManager(activeTab, 'tab_5');
            $('#step4').css('display','none');
            $('#step5').css('display','block');
        }

    }
});

function getLicenseCount(company_id, cb_func){

    $.ajax({
        url: "/company/license/"+company_id,
        type: 'GET',
        async:false,
        success: cb_func
    });
}

function getInviteUserCount(company_id, cb_func)
{
    $.ajax({
        url: "/get/employees/"+company_id,
        type: 'GET',
        async:false,
        success: cb_func
    });
}

