/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var dataTable = {}
    //load datatable onload
    ajax_Datatable_Loader();

    // jquery validation plugin
    $("#add_userGroup").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="visibility"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else if(element.attr('name')=="group_name"){
                error.insertAfter(document.getElementById('err-group_name'));
            } else if(element.attr('name')=="entity_id"){
                error.insertAfter(document.getElementById('err-entity_id'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "visibility" : {
                required: false
            },
            "group_name" : {
                required: true
            },
            "entity_id" : {
                required: true
            }
        },
        messages: {
            "visibility" : {
                required: "Please specify a visibility type"
            },
            "group_name" : {
                required: "Please insert a user group name"
            },
            "entity_id" : {
                required: "Please select entity type"
            }
        }
    });

    // jquery validation plugin
    $("#user-group-edit-form").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="visibility"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else if(element.attr('name')=="group_name"){
                error.insertAfter(document.getElementById('err-group_name'));
            } else if(element.attr('name')=="entity_id"){
                error.insertAfter(document.getElementById('err-entity_id'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "visibility" : {
                required: false
            },
            "group_name" : {
                required: true
            },
            "entity_id" : {
                required: true
            }
        },
        messages: {
            "visibility" : {
                required: "Please specify a visibility type"
            },
            "group_name" : {
                required: "Please insert a user group name"
            },
            "entity_id" : {
                required: "Please select entity type"
            }
        }
    });
});


//load bootstrap datatable
function ajax_Datatable_Loader(){
    dataTable =  $('#user-group-table').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/userGroup/filter/"

    } );
    $("#user-group-table_filter").css("display","none");
    //$("div.toolbar").html('<div class="text-right"><label>Search By Country: <input type="text" name="country_search" id="country_search" value="" /></label>&nbsp;<label>State: <input type="text" name="state_search" id="state_search" value="" /></label>&nbsp;<label>City: <input type="text" name="city_search" id="city_search" value="" /></label></div>');
}

//Send all submitted form data to the backend methods
$(document).on('click', '#save-user-group-btn', function() {
    //if form validation success
    if($("#add_userGroup").valid()){
        var dataset = {};
        //Serialize submitted form data
        var data = $('#add_userGroup').serializeArray();

        //push all values to one array
        $.each(data, function() {
            if (dataset[this.name] !== undefined) {
                if (!dataset[this.name].push) {
                    dataset[this.name] = [dataset[this.name]];
                }
                dataset[this.name].push(this.value || '');
            } else {
                dataset[this.name] = this.value || '';
            }
        });

        $.ajax({
            url: "/userGroup/new/store",
            type: 'POST',
            dataType: 'json',
            data : { dataset:dataset },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#group_name').val('');
                    $('#city_id').val('');
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                    $('#city_id').val('');
                }
            }
        });
    }
});



//Send all submitted form data to the backend methods
$(document).on('click', '#update-user-group-btn', function() {
    //if form validation success
    if($("#user-group-edit-form").valid()){
        var dataset = {};
        //Serialize submitted form data
        var data = $('#user-group-edit-form').serializeArray();

        //push all values to one array
        $.each(data, function() {
            if (dataset[this.name] !== undefined) {
                if (!dataset[this.name].push) {
                    dataset[this.name] = [dataset[this.name]];
                }
                dataset[this.name].push(this.value || '');
            } else {
                dataset[this.name] = this.value || '';
            }
        });

        $.ajax({
            url: "/UserGroup/edit/store",
            type: 'POST',
            dataType: 'json',
            data : { dataset:dataset },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#user-group-edit-model').modal('hide')
                    $('#user-group-table').dataTable().fnDestroy();
                    ajax_Datatable_Loader();
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
});

/**
 * active inactive user groups
 * @param status
 * @param user_group_id
 */
function activeUserGroup(status, user_group_id) {
    if(status==1){
        $.ajax({
            url: '/configuration/UserGroup/remove',
            type: 'GET',
            data: { id : user_group_id, status: 2 },
            success: function(response) {
                $('#user-group-table').dataTable().fnDestroy();
                ajax_Datatable_Loader();

                var msg = response.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
            }
        });
    }
    else if(status==2)
    {
        $.ajax({
            url: '/configuration/UserGroup/remove',
            type: 'GET',
            data: { id : user_group_id, status: 1 },
            success: function(response) {
                $('#user-group-table').dataTable().fnDestroy();
                ajax_Datatable_Loader();

                var msg = response.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
            }
        });
    }
}

/**
 * Individual state edit view popup
 * @param user_group_id
 */
function editUserGroup(user_group_id) {
    $('#entity_id').html('');
    $.ajax({
        url: "/configuration/UserGroup/edit",
        type: 'POST',
        dataType: 'json',
        data : { dataset:user_group_id },
        success: function(result){
            if(result.success == 'true') {
                if(result.user_group.entity_type_id==1){
                    $('.visibility_row').css('display', 'none');
                }else{
                    $('.visibility_row').css('display', 'block');
                }
                var company_list = result.company_list;

                if(result.user_group.id<=8){
                    $('#group_name').val(result.user_group.name);
                    //$('#group_name').attr('readOnly', 'readOnly');
                }else{
                    $('#group_name').val(result.user_group.name);
                    $('#group_name').removeAttr('readOnly');
                }

                if(result.user_group.status==1){
                    $('#visibility_yes').parent().addClass('checked');
                    $('#visibility_yes').attr('checked', 'checked');
                }else if(result.user_group.status==2){
                    $('#visibility_no').parent().addClass('checked');
                    $('#visibility_no').attr('checked', 'checked');
                }

                //Append country list
                $.each(company_list, function(index, value) {
                    $('#entity_id').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });

                $('#entity_id').val(result.user_group.entity_type_id);
                $('#edit_entity_id').val(result.user_group.entity_type_id);

                $('#user_group_id').val(user_group_id);
                $('#user-group-edit-model').modal('show');
            } else {

            }
        }
    });
}

//Close edit user group model
$(document).on('click', '.close-user-group-model', function () {
    $('#group_name').val('');
    $('#visibility_yes').parent().removeClass('checked');
    $('#visibility_no').parent().removeClass('checked');
    $('#user-group-edit-model').modal('hide');
});

/**
 * show notification message
 * @param msg
 * @param msg_type
 */
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