/**
 * Created by Harsha on 5/25/2016.
 */
$(function(){
    licensesTable();

    $("#add_license_form").validate({
        rules: {
            license_name: {
                required: true
            },
            country:{
                required: true
            },
            state:{
                required: true
            },
            checklist_Fee:{
                required: true
            }
        },
        messages: {
            license_name:{
                required: "The license name field is required"
            },
            country:{
                required: "The country line 1 is required"
            },
            state:{
                required: "The state field is required"
            },
            checklist_Fee:{
                required: "The check list fee is required"
            }
        }
    });

    $("#edit_license_form").validate({
        rules: {
            edit_license_name: {
                required: true
            },
            edit_country:{
                required: true
            },
            edit_state:{
                required: true
            },
            edit_checklist_Fee:{
                required: true
            }
        },
        messages: {
            edit_license_name:{
                required: "The license name field is required"
            },
            edit_country:{
                required: "The country line 1 is required"
            },
            edit_state:{
                required: "The state field is required"
            },
            edit_checklist_Fee:{
                required: "The check list fee is required"
            }
        }
    });
});

$(document).on('click', '.cls-license-form', function(){
    var validator = $( "#edit_license_form" ).validate();
    validator.resetForm();
    $('#edit_license_type').modal('hide');
});

$(document).on('click', '.cls-add-license-form', function(){
    var validator = $( "#add_license_form" ).validate();
    validator.resetForm();
    clearLicenseForm();
    $('#add_new_license_modal').modal('hide');
});

$(document).on('click', '.edit-cls-license-form', function(){
    var validator = $( "#edit_license_form" ).validate();
    validator.resetForm();
    $('#edit_license_type').modal('hide');
});

$(document).on('click', '.save-license', function(){

    var license_name = $('#license_name').val();
    var country = $('#country').val();
    var type = $('#type').val();
    var state = $('#state').val();
    var checklist_fee = $('#checklist_Fee').val();
    if($("#add_license_form").valid()){
        $.ajax({
            url: "/add/license/type",
            type: 'POST',
            dataType: 'json',
            data:{license_name:license_name, country:country, state:state, checklist_fee:checklist_fee, type: type},
            success: function(result){
                if(result.success == 'true') {
                    clearLicenseForm();
                    $('#license-manager-table').dataTable().fnDestroy();
                    $('#add_new_license_modal').modal('hide');
                    licensesTable();
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

function clearLicenseForm()
{
    $('#country').val(0);
    $('#state').find('option').remove().end().append('<option value="">Select a State</option>').val('');
    $('#add_license_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#add_license_form" ).validate();
    validator.resetForm();
}

function licensesTable()
{
    companyTable =  $('#license-manager-table').dataTable( {
        "ajax": {
            "url": "/get/all/license",
            "type": "GET",
        },
        "searching": false,
        "paging": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "19%", "mData": 0},
            {"sWidth": "10%", "mData": 1},
            {"sWidth": "5%", "mData": 2},
            {"sWidth": "10%", "mData": 3, "bSort": "false"}
        ]
    } );
}

$(document).on('click', '#add-new-license', function(){
    $('#add_new_license_modal').modal('show');
});

function changeLocationLicenseStatus (id, status)
{
    var company_id = $('#company_id').val();
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
                        data:{'status':status, 'license_id':id},
                        success: function(result){
                            if(result.success == "true") {
                                $("#licenses_table").dataTable().fnDestroy();
                                licenses_tbl(company_id);
                            }

                        }
                    });
                } else {
                    swal("Cancelled", "Your license is safe :)", "error");
                }
            });
    } else {
        $.ajax({
            url: "/change/licenses/location/status",
            type: 'GET',
            dataType: 'json',
            data:{'status':status, 'license_id':id},
            success: function(result){
                $("#licenses_table").dataTable().fnDestroy();
                licenses_tbl(company_id);
                var msg = result.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
            }
        });
    }
}

// country change function
$(document).on('change', '#country',function(){
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
            $("#state").empty();
            $("#state").append('<option value="">Select a State</option>');
            if(result.data != null){
                $.each(result.data.master_states, function(incex, value){
                    $("#state").append('<option value="'+value.id+'"> '+value.name+'</option>');
                });
            }
        },
        error: function (result) {

        }
    });
});

$(document).on('change', '#edit_country',function(){
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
            $("#edit_state").append('<option value="0">---</option>');
            if(result.data != null){
                $.each(result.data.master_states, function(incex, value){
                    $("#edit_state").append('<option value="'+value.id+'"> '+value.name+'</option>');
                });
            }
        },
        error: function (result) {

        }
    });
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

function changeLicenseStatus(license_id, status)
{
    if(status==1){
        $.ajax({
            url: "/change/license/status",
            type: 'GET',
            dataType: 'json',
            data:{'status':status, 'license_id':license_id},
            success: function(result){
                if(result.success == 'true') {
                    if(status==1){
                        swal("activated!", "License has been activated successfully.", "success");
                    }

                    $("#license-manager-table").dataTable().fnDestroy();
                    licensesTable();
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }else{
        swal({
                title: "Are you sure?",
                text: "By inactivating this Master license – it will be inactivated in all companies which has purchased this license and this license will not be available for purchasing until activated again",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "/change/license/status",
                        type: 'GET',
                        dataType: 'json',
                        data:{'status':status, 'license_id':license_id},
                        success: function(result){
                            if(result.success == 'true') {
                                if(status==2) {
                                    swal("Inactivated!", "License has been inactivated successfully.", "success");
                                }

                                $("#license-manager-table").dataTable().fnDestroy();
                                licensesTable();
                                var msg = result.message;
                                var msg_type = 'success';
                                msgAlert(msg, msg_type);
                            }
                        }
                    });
                }else{
                    swal("Cancelled", "Action aborted! License is safe :)", "success");
                }
            });
    }

}

function changeLicense(license_id)
{
    $.ajax({
        url: "/get/license/"+license_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result != null){
                $('#license_id').val(license_id);
                $('#edit_license_name').val(result.data.license_name);
                $('#edit_type').val(result.data.type);
                $('#edit_country').val(result.data.country_id);
                $('#edit_checklist_Fee').val(result.data.checklist_fee);
                $.each(result.data.states, function(index, value){
                    $("#edit_state").append($('<option>', {value: value.id, text: value.name}));
                });
                $('#edit_state').val(result.data.state_id);
            }

            $('#edit_license_type').modal('show');
        }
    });
}

$(document).on('click', '.save-license-changes', function(){
    var license_name = $('#edit_license_name').val();
    var country = $('#edit_country').val();
    var state = $('#edit_state').val();
    var type = $('#edit_type').val();
    var checklist_fee = $('#edit_checklist_Fee').val();
    var license_id = $('#license_id').val();

    if($("#edit_license_form").valid()){
        $.ajax({
            url: "/change/license/type",
            type: 'POST',
            dataType: 'json',
            data:{license_name:license_name, country:country, state:state, checklist_fee:checklist_fee, license_id:license_id, type:type},
            success: function(result){
                if(result.success == 'true') {
                    $('#license-manager-table').dataTable().fnDestroy();
                    $('#edit_license_type').modal('hide');
                    licensesTable();
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

function removeLicenceStatusCheck(id) {
    swal({
            title: "Are you sure?",
            text: "This license no longer will be available in the system!",
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
                $.ajax({
                    url: "/remove/license/check",
                    type: 'GET',
                    dataType: 'json',
                    data: {license_id: id},
                    success: function (result) {
                        if (result.success == 'true') {
                            if (result.status == 'false') {
                                swal("Access Denied!", result.message, "error");
                            }else if(result.status == 'true'){
                                swal("Action Completed!", result.message, "success");
                                $("#license-manager-table").dataTable().fnDestroy();
                                licensesTable();
                            }
                        } else {
                            var msg = result.message;
                            var msg_type = 'error';
                            msgAlert(msg, msg_type);
                        }
                    }
                });
            } else {
                swal("Cancelled", "License is safe", "success");
            }
        });
}

