/**
 * Created by Harsha on 6/28/2016.
 */
var dataTable = {}
var license_Table = {}
var license_location_id = '';
var license_count = '';
var dateToday = new Date();

    $(document).ready(function() {
    $.validator.addMethod("time_differ",
        function(value, element, params){
            var license_date = $('#new_license_date').val();
            var licenseDate = new Date(license_date);

            var renewal_date =  $('#new_renewal_date').val();
            var renewalDate = new Date(renewal_date);

            return (renewalDate.getTime() > licenseDate.getTime());
        },'renewal date should not be less than the license time');

    $.validator.addMethod("exp_date", function (value, element, params) {
        var exp_license_date = $('#new_license_date').val();
        var expLicenseDate = new Date(exp_license_date);

        var currentDate = new Date();
        return (expLicenseDate.getTime() > currentDate.getTime());
    },'Expiration date should not be less than the current date');

    $.validator.addMethod("renew_date", function (value, element, params) {
        var renewal_date = $('#new_renewal_date').val();
        var renewalDate = new Date(renewal_date);

        var exp_license_date = $('#new_license_date').val();
        var expLicenseDate = new Date(exp_license_date);

        return (renewalDate.getTime() < expLicenseDate.getTime());
    },'Renew By Date should not be greater than the expiration date');

    $('#license_add_form').validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="terms"){
                error.insertAfter(document.getElementById('err-terms'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            new_license_type: {
                required: true
            },
            new_license_number: {
                required: true
            },
            new_license_location:{
                required: true
            },
            new_dba_name:{
                required: true
            },
            new_license_date:{
                required: true,
                exp_date : "#new_license_date"
            },
            new_renewal_date:{
                required: true,
                renew_date : "#new_renewal_date"
                //time_differ:"#new_license_date"
            },
            new_reminder:{
                required: true
            },
            terms: {
                required: true
            }

        },
        messages: {
            new_license_type:{
                required: "The license type is required"
            },
            new_license_number:{
                required: "The license number is required"
            },
            new_license_location:{
                required: "The location is required"
            },
            new_dba_name:{
                required: "The name is required"
            },
            new_license_date:{
                required: "The license date is required"
            },
            new_renewal_date:{
                required: "The renewal date is required"
            },
            new_reminder:{
                required: "The renewal reminder is required"
            },
            "terms" : {
                required: "Please agree with terms of Service"
            }
        }
    });

    $('#license_details_form_edit').validate({
        rules: {
            edit_license_number: {
                required: true
            },
            // dba_name: {
            //     required: true
            // },
            edit_license_date:{
                required: true
            },
            edit_renewal_date:{
                required: true
            },
            reminder:{
                required: true
            }
        },
        messages: {
            edit_license_number:{
                required: "The license number is required"
            },
            // dba_name:{
            //     required: "The name is required"
            // },
            edit_license_date:{
                required: "The license date is required"
            },
            edit_renewal_date:{
                required: "The renewal date is required"
            },
            reminder:{
                required: "The renewal reminder is required"
            }
        }
    });

    jQuery('#newLicenseDatePicker').datetimepicker({
        useCurrent: false,
        minDate: dateToday,
        format: 'MM/DD/YYYY'
    });
    jQuery('#newRenewalDatePicker').datetimepicker({
        useCurrent: false,
        minDate: dateToday,
        format: 'MM/DD/YYYY'
    });
    licenseTableManager();
});


function licenseTableManager()
{
    dataTable =  $('#license-table-manager').dataTable( {
        "ajax": {
            "url": "/get/license",
            "type": "GET",
        },
        "searching": false,
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "aoColumns": [
            // {"sWidth": "10%", "mData": 0},
            {"sWidth": "10%", "mData": 1},
            {"sWidth": "25%", "mData": 2},
            {"sWidth": "25%", "mData": 3},
            {"sWidth": "10%", "mData": 4},
            {"sWidth": "10%", "mData": 5},
            {"sWidth": "10%", "mData": 6},
            {"sWidth": "10%", "mData": 7}
        ]
    } );
    activeLicensesCount();
}

$(document).on('click', '#license_search', function(){
    var license_number = $('#license_number').val();
    var license_type = $('#license_type').val();
    var license_location = $('#license_location').val();

    if(license_number =='' && license_type =='' && license_location == '') {
        $("#license-table-manager").dataTable().fnDestroy();
        licenseTableManager();
    } else {
        $("#license-table-manager").dataTable().fnDestroy();
        license_Table =  $('#license-table-manager').dataTable( {
            "ajax": {
                "url": "/search/license",
                "type": "POST",
                data:{license_number:license_number, license_type:license_type, license_location:license_location},
            },
            "searching": false,
            "paging": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "aoColumns": [
                {"sWidth": "10%", "mData": 0},
                {"sWidth": "20%", "mData": 1},
                {"sWidth": "20%", "mData": 2},
                {"sWidth": "20%", "mData": 3},
                {"sWidth": "10%", "mData": 4},
                {"sWidth": "10%", "mData": 5},
                {"sWidth": "10%", "mData": 6}
            ]
        } );
    }
});

function editLicenseDetails(license_id)
{
    var reminders = [];
    $('#reminder').select2({
        placeholder: "Select Reminders",
        tags: true
    });

    jQuery('#licenseDatePicker').datetimepicker({
        useCurrent: false,
        format: 'MM/DD/YYYY'
    });
    jQuery('#renewalDatePicker').datetimepicker({
        useCurrent: false,
        format: 'MM/DD/YYYY'
    });
    license_location_id = license_id;
    $.ajax({
        url: "/get/license/details/"+license_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success == 'true') {
                $('#license_type_edit').val(result.data.license_details[0].master_license_name);
                $('#edit_license_type').val(result.data.license_details[0].master_license_id);
                $('#edit_license_number').val(result.data.license_details[0].license_number);
                $('#edit_license_location').val(result.data.license_details[0].company_loc_name);
                $('#license_location_edit').val(result.data.license_details[0].company_loc_id);
                // $('#dba_name').val(result.data.license_details[0].dba_name);
                $('#edit_license_date').val(result.data.license_date);
                $('#edit_renewal_date').val(result.data.renewal_date);
                $.each(result.data.reminder, function(key, value){
                    reminders.push(value);
                });
                $("#reminder").val(reminders).trigger("change");
                $('#edit-license-details').modal('show');
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }

        }
    });
}

$(document).on('click', '.save-license-changes', function(){

    var license_id = $('#edit_license_type').val();
    var license_number = $('#edit_license_number').val();
    var location_id = $('#license_location_edit').val();
    var license_date = $('#edit_license_date').val();
    var renewal_date = $('#edit_renewal_date').val();
    var dba_name = '';//$('#dba_name').val();
    var reminder = $('#reminder').val();

    if($('#license_details_form_edit').valid()) {
        $.ajax({
            url: "/update/license",
            type: 'POST',
            dataType: 'json',
            data : {license_location_id:license_location_id, license_id:license_id,location_id:location_id,license_no:license_number,license_date:license_date,renewal_date:renewal_date, reminder:reminder, name:dba_name},
            success: function(result){
                if(result.success == 'true') {
                    $('#license-table-manager').dataTable().fnDestroy();
                    licenseTableManager();
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#edit-license-details').modal('hide');
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

$(document).on('click', '#new-license-btn', function(){

    jQuery('#newLicenseDatePicker').datetimepicker({
        useCurrent: false,
        format: 'D/MM/YYYY'
    });
    jQuery('#newRenewalDatePicker').datetimepicker({
        useCurrent: false,
        format: 'D/MM/YYYY'
    });
    $('#new_reminder').select2({
        placeholder: "Select Reminders",
        allowClear: true
    });

    $.ajax({
        url: "/license/locations",
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success = 'true') {
                $('#new_license_type').empty().append('<option value="">Select License Type</option>');
                $('#new_license_location').empty().append('<option value="">Select Location</option>');
                $.each(result.data.license, function(key, value){
                    $('#new_license_type').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
            }
        }
    });

    $('#new-license-modal').modal('show');
});

$(document).on('change', '#new_license_type', function(){
    var license_id = $('#new_license_type').val();

    $.ajax({
        url: "/license/locations/"+license_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success == 'true') {
                $('#new_license_location').empty().append('<option value="">Select Location</option>');
                $.each(result.data, function(key, value){
                    $('#new_license_location').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
            } else {

            }
        }
    });

    $.ajax({
        url: "/license/fee/"+license_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success == 'true') {
                $("#cost_amount").text(result.data);
                $('#amount_cost').val(result.data);

            } else {

            }
        }
    });
});

$(document).on('click', '#paynow_btn', function(){
    var new_license_type = $('#new_license_type').val();
    var new_license_number = $('#new_license_number').val();
    var new_license_location = $('#new_license_location').val();
    var new_dba_name = '';//$('#new_dba_name').val();
    var new_license_date = $('#new_license_date').val();
    var new_renewal_date = $('#new_renewal_date').val();
    var new_reminder = $('#new_reminder').val();
    var amount = $('#amount_cost').val();
    if($('#license_add_form').valid()) {
        if(amount > 0.5) {
            $.ajax({
                url: "/license/perches",
                type: 'POST',
                dataType: 'json',
                data : {license_id:new_license_type,location_id:new_license_location,license_no:new_license_number,license_date:new_license_date,renewal_date:new_renewal_date,amount:amount, reminder:new_reminder, name:new_dba_name},
                success: function(result){
                    if(result.success == 'true') {
                        $('#license-table-manager').dataTable().fnDestroy();
                        licenseTableManager();
                        var msg = result.message;
                        var msg_type = 'success';
                        $('#new-license-modal').modal('hide');
                        msgAlert(msg, msg_type);

                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    }
                },
                error: function(result){
                    //console.log(result);
                    var msg = 'License purchase failed';
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            });
        } else {
            var msg = 'Amount must be at least 50 cents';
            var msg_type = 'error';
            msgAlert(msg, msg_type);
        }
    }
});


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

$('#new-license-modal').on('hidden.bs.modal', function (e) {
    $("#cost_amount").text(0);
    $('#amount_cost').val();
    $('#license_add_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#license_add_form" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$('#edit-license-details').on('hidden.bs.modal', function (e) {
    var validator = $( "#license_details_form_edit" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})

$(document).on('click', '.cls-edit-license-form', function(){
    $('#edit-license-details').modal('hide');
    clearLicenseForm();
})

function clearLicenseForm()
{
    var validator = $( "#license_details_form_edit" ).validate();
    validator.resetForm();
}


function changeLicenseStatus(license_id, status)
{
    license_location_id = license_id;
    var company_id = $('#company_id').val();

    console.log(license_count);
    if(license_count > 1) {
        if(status == 0) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this license!",
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
                        swal("Deleted!", "Your license has been deleted.", "success");
                        $.ajax({
                            url: "/change/licenses/location/status",
                            type: 'GET',
                            dataType: 'json',
                            data:{'status':status, 'license_id':license_id},
                            success: function(result){
                                if(result.success == "true") {
                                    $("#license-table-manager").dataTable().fnDestroy();
                                    licenseTableManager();
                                }

                            }
                        });
                    } else {
                        swal("Cancelled", "Your license is safe :)", "error");
                    }
                });
        } else if(status == 2) {
            swal({
                    title: "Do you want to inactivate this license?",
                    text: "",
                    type: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#66CD00",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "/change/licenses/location/status",
                            type: 'GET',
                            dataType: 'json',
                            data:{'status':status, 'license_id':license_id},
                            success: function(result){
                                $("#license-table-manager").dataTable().fnDestroy();
                                licenseTableManager();
                                var msg = result.message;
                                var msg_type = 'success';
                                msgAlert(msg, msg_type);
                            }
                        });
                        swal("Inactivated!", "License Inactivated Successfully", "success");
                    } else {
                        swal("Cancelled", "Your license is safe :)", "success");
                    }
                });

        } else if(status == 1) {

            $.ajax({
                url: "/license/fee/"+license_id,
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.success == 'true') {
                        $("#active_cost_amount").text(result.data);
                        $('#active_amount_cost').val(result.data);

                    } else {

                    }
                }
            });

            $.ajax({
                url: "/get/license/details/"+license_id,
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.success == 'true') {
                        $('#new_license_type_edit').val(result.data.license_details[0].master_license_name);
                        $('#perches_license_number').val(result.data.license_details[0].license_number);
                        $('#perches_license_location').val(result.data.license_details[0].company_loc_name);
                        $('#license_location_id').val(license_location_id);
                        $('#license-perches').modal('show');
                    }else{
                        swal("Not Available!", "This license has no longer available in the system", "error");
                    }
                }
            });
        }
    } else {
        if(status == 2) {
            swal("Cancelled", "You must have at least one active license on your account. Please add another license in order to deactivate the current license.", "error");
        } else if(status == 0) {
            swal("Cancelled", "Your license can not delete :)", "error");
        }
    }

}


$(document).on('click', '#license_active_btn', function(){
    var license_fee = $('#active_amount_cost').val();

    if($('#license_add_form').valid()) {
        $.ajax({
            url: "/activate/license",
            type: 'POST',
            dataType: 'json',
            data : {license_id:license_location_id,amount:license_fee},
            success: function(result){
                if(result.success == 'true') {
                    $('#license-table-manager').dataTable().fnDestroy();
                    licenseTableManager();
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#license-perches').modal('hide');
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

function activeLicensesCount() {
    $.ajax({
        url: "/activate/license/count",
        type: 'GET',
        dataType: 'json',
        success: function(result){
            license_count = result;
        }
    });
}