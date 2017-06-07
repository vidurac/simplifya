/**
 * Created by Harsha on 5/28/2016.
 */

var userTable = {};
var company_id;
var invite_users = {};
var edit_invite_users = {};

$(function() {



    $(document).on('change', '#permission_level', function(){
        if($(this).val() == "2"){
            $('#permissionDescription').text("Can add/remove staff, add business locations, add licenses, request 3rd Party audits, initiate self-audits, conduct self-audits, view all audit reports, and assign Action Items.")
        }
        else if($(this).val() == "3"){
            $('#permissionDescription').text("Can add/remove staff, view audit reports for their location, conduct self-audits, respond to Action Items, and assign Action Items.");
        }
        else if($(this).val() == "4"){
            $('#permissionDescription').text("Can respond to Action Items.");
        }
        else{
            $('#permissionDescription').text("");
        }
    });


    $(document).on('change', '#edit_permission_level', function(){
        if($(this).val() == "2"){
            $('#editPermissionDescription').text("Can add/remove staff, add business locations, add licenses, request 3rd Party audits, initiate self-audits, conduct self-audits, view all audit reports, and assign Action Items.")
        }
        else if($(this).val() == "3"){
            $('#editPermissionDescription').text("Can add/remove staff, view audit reports for their location, conduct self-audits, respond to Action Items, and assign Action Items.");
        }
        else if($(this).val() == "4"){
            $('#editPermissionDescription').text("Can respond to Action Items.");
        }
        else{
            $('#editPermissionDescription').text("");
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
    userPermissionLevelByCompany();
    usersTable();

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

     invite_users = $('#invite_employ_form').validate({
        rules: {
            name: {
                required: true
            },
            email_address:{
                required: true,
                email: true
            },
            permission_level:{
                required: true
            },
            location:{
                allow_location: '#location'
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

    $('#registerForm').validate({
        rules: {
            password: {
                required: true
            },
            conf_password: {
                equalTo: "#password"
            }
        },
        messages: {
            password:{
                required: "The password is required"
            },
            conf_password:{
                required: "The confirm password is required"
            }
        }
    });
});




$(document).on('click', '#new-user-model', function(){
    var company_id = $('#company_id').val();
    getCompanyLocationsById(company_id);
    getPermissionLevelById(company_id);
    $('#locations-enable').css('display', 'block');
    $('#add-new-employees').modal('show');
});

function usersTable()
{
    var entity_type  = $('#entity_type').val();

    userTable =  $('#users-detail-table').dataTable( {
        "ajax": {
            "url": "/get/all/users",
            "type": "GET"
        },
        "searching": false,
        "paging": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "30%", "mData": 4},
            {"sWidth": "10%", "mData": 2},
            {"sWidth": "20%", "mData": 3}
        ],
        "columnDefs": [
            {
                "targets": [ 2 ],
                "visible": entity_type == 2 ? true : false,
                "searchable": false
            }
        ]
    } );
}

$(document).on('click', '#user-search', function()
{
    var permission_levels = $('#permission_levels').val();
    var user_name    = $('#user_name').val();
    var status  = $('#status').val();
    //if(permission_levels =='' && user_name =='' && status == '') {
        //$("#users-detail-table").dataTable().fnDestroy();
        //usersTable();
    //} else {
        $("#users-detail-table").dataTable().fnDestroy();
        userTable =  $('#users-detail-table').dataTable( {
            "ajax": {
                "url": "/users/filtering",
                "type": "POST",
                data : {permission_id:permission_levels,name:user_name,status:status}
            },
            "searching": false,
            "paging": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "aoColumns": [
//              {"sWidth": "10%", "mData": 0},
                {"sWidth": "20%", "mData": 0},
                {"sWidth": "20%", "mData": 1},
                {"sWidth": "10%", "mData": 2},
                {"sWidth": "10%", "mData": 3}
            ]
        } );
    //}
});

function userPermissionLevelByCompany()
{
    $.ajax({
        url: "/user/permission/levels",
        type: 'GET',
        dataType: 'json',
        success: function(result){
            $.each(result.data, function(index, value){
                $("#permission_levels").append($('<option>', {value: value.permission_id, text: value.permission_name}));
            });
        }
    });
}

function changeUserDetails(user_id)
{
    var user_role = $('#master_user_group_id').val();
    var entity_type = $('#entity_type').val();
    $.ajax({
        url: "/get/all/user/details/"+user_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            getCompanyLocationsById(result.data[0].company_id, 'edit');
            getPermissionLevelById(result.data[0].company_id, 'edit', result.data[0].master_user_group_id, user_role);
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

function getCompanyLocationsById(company_id, type)
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

function getPermissionLevelById(company_id, type, select_id, user_role)
{
    var edit_user_role = $( ".btn-circle" ).data( "user_role");
    $.ajax({
        url: "/get/company/permission/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result != null){
                $("#edit_permission_level").empty().append('<option value="">Select Permission Level</option>');
                if(type == 'edit') {
                    $.each(result.edit_permission, function(index, value){
                        if(user_role == 3) {
                            if(edit_user_role == 2) {
                                $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                            } else {
                                if(value.name != 'Master Admin') {
                                    $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                                }
                            }

                        } else {
                            if(select_id == 2) {
                                $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                            } else {
                                if(value.name != 'Master Admin') {
                                    $("#edit_permission_level").append($('<option>', {value: value.id, text: value.name}));
                                }
                            }

                        }
                    });
                } else {
                    $("#permission_level").empty().append('<option value="">Select Permission Level</option>');
                    $.each(result.permissions, function(index, value){
                        if(edit_user_role == 1) {
                            $("#permission_level").append($('<option>', {value: value.id, text: value.name}));
                        } else {
                            if(value.name != 'Master Admin') {
                                $("#permission_level").append($('<option>', {value: value.id, text: value.name}));
                            }
                        }

                    });

                    $("#permissionDescription").html('');
                }
                $("#edit_permission_level").val(select_id);
            }
        }
    });
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
                    $('#users-detail-table').dataTable().fnDestroy();
                    usersTable();
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

$(document).on('click', '.cls-edit-user-form', function(){
    var validator = $('#user_details_form_edit').validate();
    validator.resetForm();
});

function  reCreateUser(user_id, status){

    var company_id = $('#company_id').val();
    var log_user_id = $('#user_id').val();
    var log_user_role_id = $('#master_user_group_id').val();
    var user_role = $(this).data("user_role");
    if(status == 1) {
        $.ajax({
            url: "/restore/users",
            type: 'GET',
            dataType: 'json',
            data:{user_id:user_id, status:status},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                if(result.success == 'true') {
                    $("#status").val("1").change();
                    $(".splash").hide();
                    $('#users-detail-table').dataTable().fnDestroy();
                    usersTable();
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);

                } else {
                    $(".splash").hide();
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            },
            error: function(result) {
                $(".splash").hide();
            }
        });
    }
}
/**
 * Delete Invited Employee
 * @param user_id
 */
function changeUserStatus(user_id, status)
{
    var company_id = $('#company_id').val();
    var log_user_id = $('#user_id').val();
    var log_user_role_id = $('#master_user_group_id').val();
    var user_role = $(this).data("user_role");

    if(status == 0) {
        if(log_user_role_id == '2') {
            if(log_user_id == user_id) {
                var msg = 'User can not be deleted. you are currently logged in';
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            } else {
                swal({
                        title: "Are you sure?",
                        //text: "Your will not be able to recover this user details!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
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
                                    $('#users-detail-table').dataTable().fnDestroy();
                                    usersTable();
                                }
                            });
                        } else {
                            swal("Cancelled", "User details are safe :)", "error");
                        }
                    });
            }
        } else if((log_user_role_id == '3')) { alert(user_role);
                if(log_user_id == user_id) {
                        var msg = 'User can not be deleted. you are currently logged in';
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                } else if(user_role == '2') {
                    var msg = 'You can not delete a Master Admin';
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
                else if(user_role == 3) {
                    var msg = 'You can not delete a Manager';
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                } else {
                    swal({
                            title: "Are you sure?",
                            //text: "Your will not be able to recover this user details!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Delete",
                            cancelButtonText: "Cancel",
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
                                        $('#users-detail-table').dataTable().fnDestroy();
                                        usersTable();
                                    }
                                });
                            } else {
                                swal("Cancelled", "User details are safe :)", "error");
                            }
                        });
                }
        } else if((log_user_role_id == 5) || (log_user_role_id == 7)) {
            if(user_role == log_user_role_id) {
                var msg = 'User can not be deleted. you are currently logged in';
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            } else {
                swal({
                        title: "Are you sure?",
                        //text: "Your will not be able to recover this user details!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
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
                                    $('#users-detail-table').dataTable().fnDestroy();
                                    usersTable();
                                }
                            });
                        } else {
                            swal("Cancelled", "User details are safe :)", "error");
                        }
                    });
            }
        } else {
            swal({
                    title: "Are you sure?",
                    //text: "Your will not be able to recover this user details!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel",
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
                                $('#users-detail-table').dataTable().fnDestroy();
                                usersTable();
                            }
                        });
                    } else {
                        swal("Cancelled", "User details are safe :)", "error");
                    }
                });
        }
    }else {
        if(log_user_role_id == 2) {
            $.ajax({
                url: "/change/users/status",
                type: 'GET',
                dataType: 'json',
                data:{user_id:user_id, status:status},
                success: function(result){
                    if(result.success == 'true') {
                        $('#users-detail-table').dataTable().fnDestroy();
                        usersTable();
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
        } else if((log_user_role_id == 3)) {
            if(user_role == 2) {
                var msg = 'You can not inactivate a Master Admin';
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            } else if(user_role == 3) {
                var msg = 'You can not inactivate a Manager';
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            } else {
                $.ajax({
                    url: "/change/users/status",
                    type: 'GET',
                    dataType: 'json',
                    data:{user_id:user_id, status:status},
                    success: function(result){
                        if(result.success == 'true') {
                            $('#users-detail-table').dataTable().fnDestroy();
                            usersTable();
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
        }
        else {
            $.ajax({
                url: "/change/users/status",
                type: 'GET',
                dataType: 'json',
                data:{user_id:user_id, status:status},
                success: function(result){
                    if(result.success == 'true') {
                        $('#users-detail-table').dataTable().fnDestroy();
                        usersTable();
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
    }

}

/**
 * cls-emp-form
 */
$(document).on('click', '#cls-emp-form', function(){
    $('#add-new-employees').modal('hide');
    $("#location").select2("val", "");
    clearEmployeeForm();
})

function clearEmployeeForm()
{
    $('#title').val('');
    $('#permission_level').val('');
    $('#invite_employ_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
    var validator = $( "#invite_employ_form" ).validate();
    validator.resetForm();
}

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
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                if(result.success == 'true') {
                    clearEmployeeForm();
                    $("#users-detail-table").dataTable().fnDestroy();
                    $(".splash").hide();
                    usersTable();
                    $('#add-new-employees').modal('hide');
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                    $(".splash").hide();
                }
            },
            error: function(result) {
                $(".splash").hide();
            }
        });
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

$(document).on('click', '#save-user-changes', function(){
    var title = $('#edit_title').val();
    var name = $('#edit_name').val();
    var email_address = $('#edit_email_address').val();
    var locations = $('#edit_location').val();
    var permission = $('#edit_permission_level').val();
    var company_id = $('#company_id').val();

    if($('#user_details_form_edit').valid()) {
        $.ajax({
            url: "/edit/invite/employees",
            type: 'POST',
            dataType: 'json',
            data : {title:title,name:name,email_address:email_address,locations:locations,permission:permission,company_id:company_id},
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    $('#edit-user-details').modal('hide');
                    $('#users-detail-table').dataTable().fnDestroy();
                    usersTable();
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

// $('#add-new-employees').on('hidden.bs.modal', function (e) {
//     clearEmployeeForm();
// });
$(function() {
    $('#add-new-employees').on('hidden.bs.modal', function (e) {
        $('#invite_employ_form').closest('form').find("input[type=text], textarea, input[type=email]").val("");
        var validator = $( "#invite_employ_form" ).validate();
        validator.resetForm();
        $('.form-control').removeClass('error');
    });

    $('#edit-user-details').on('hidden.bs.modal', function (e) {
        var validator = $('#user_details_form_edit').validate();
        validator.resetForm();
        $('.form-control').removeClass('error');
    });
});


$(document).on('change', '#permission_level', function(){
    var role_id = $(this).val();
    if((role_id== 1) || (role_id== 2) || (role_id== 5) || (role_id== 7)) {
        $('#locations-enable').css("display", "none");
    } else {
        $('#locations-enable').css("display", "block");
    }
});

$(document).on('change', '#edit_permission_level', function() {
    var role_id = $(this).val();
    if((role_id== 1) || (role_id== 2) || (role_id== 5) || (role_id== 7)) {
        $('.edit_location_enable').css("display", "none");
    } else {
        $('.edit_location_enable').css("display", "block");
    }
});

$(document).on('change', '#location', function(){
    invite_users.element( "#location" );
});

$(document).on('change', '#edit_locations', function(){
    edit_invite_users.element( "#edit_locations" );
});

$('#edit-user-details').on('hidden.bs.modal', function (e) {
    var validator = $( "#user_details_form_edit" ).validate();
    validator.resetForm();
    $('.form-control').removeClass('error');
})
