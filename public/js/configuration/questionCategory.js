/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var dataTable = {}
    ajax_Datatable_Loader();

    /**
     * initial view question category add new option iteration function
     * @type {number}
     */
    var id=1;
    $("#add_row").click(function(){
        // <a class='btn btn-success btn-circle' title='Inactive'  onclick='changeStatus("+i+")'><i class='fa fa-thumbs-o-up'></i></a>
        $('#addr'+id).html("<td><input name='option[]' type='text' class='form-control input-md'  /> </td><td><input  name='option_value[]' type='text'  class='form-control input-md'></td><td class='text-center'></td>");

        $('#tab_logic').append('<tr id="addr'+(id+1)+'"></tr>');
        id++;
    });
    $("#delete_row").click(function(){
        if(id>1){
            $("#addr"+(id-1)).html('');
            id--;
        }
    });

    /**
     * qustion category edit update option iteration function
     */
    var count=1;
    $("#add_edit_row").click(function(){
        // <a class='btn btn-success btn-circle' title='Inactive'  onclick='changeStatus("+i+")'><i class='fa fa-thumbs-o-up'></i></a>
        $('#addr_edit'+count).html("<td><input name='option[]' type='text' class='form-control input-md'  /> </td><td><input  name='option_value[]' type='text'  class='form-control input-md'></td><td class='text-center'></td>");

        $('#tab_edit_logic').append('<tr id="addr_edit'+(count+1)+'"></tr>');
        count++;
    });
    $("#delete_edit_row").click(function(){
        if(count>1){
            $("#addr_edit"+(count-1)).html('');
            count--;
        }
    });

    // jquery validation plugin
    $("#question-category-edit-form").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="is_required"){
                error.insertAfter(document.getElementById('err-required'));
            } else if(element.attr('name')=="visible_to"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else if(element.attr('name')=="main_cat"){
                error.insertAfter(document.getElementById('err-main_cat'));
            } else if(element.attr('name')=="category_name"){
                error.insertAfter(document.getElementById('err-cat_name'));
            } else if(element.attr('name')=="option"){
                error.insertAfter(document.getElementById('err-option'));
            } else if(element.attr('name')=="is_multiselect"){
                error.insertAfter(document.getElementById('err-is_multiselect'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "is_required" : {
                required: true
            },
            "visible_to" : {
                required: false
            },
            "main_cat" : {
                required: true
            },
            "category_name" : {
                required: true
            },
            "is_multiselect" : {
                required: true
            }
        },
        messages: {
            "is_required": {
                required: "Please specify required or not"
            },
            "visibility": {
                required: "Please specify visibility"
            },
            "is_main" : {
                required: "Please specify main category"
            },
            "category_name" : {
                required: "Please insert category name"
            },
            "is_multiselect" : {
                required: "Please specify multi-select option"
            }
        }
    });


    // jquery validation plugin
    $("#newSubscriptionForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="is_required"){
                error.insertAfter(document.getElementById('err-required'));
            } else if(element.attr('name')=="visible_to"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else if(element.attr('name')=="category_name"){
                error.insertAfter(document.getElementById('err-cat_name'));
            } else if(element.attr('name')=="option"){
                error.insertAfter(document.getElementById('err-option'));
            } else if(element.attr('name')=="option_value"){
                error.insertAfter(document.getElementById('err-option_value'));
            } else if(element.attr('name')=="is_multiselect"){
                error.insertAfter(document.getElementById('err-is_multiselect'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "is_required" : {
                required: true
            },
            "visible_to" : {
                required: false
            },
            "main_cat" : {
                required: true
            },
            "category_name" : {
                required: true
            },
            "option" : {
                required: true
            },
            "option_value" : {
                required: true
            },
            "is_multiselect" : {
                required: true
            }
        },
        messages: {
            "is_required": {
                required: "Please specify required or not"
            },
            "visibility": {
                required: "Please specify visibility"
            },
            "is_main" : {
                required: "Please specify main category"
            },
            "category_name" : {
                required: "Please insert category name"
            },
            "option" : {
                required: "Please insert option name"
            },
            "option_value" : {
                required: "Please insert option value"
            },
            "is_multiselect" : {
                required: "Please specify multi-select option"
            }
        }
    });

    var scntDiv = $('#option_list');
    var i = $('#option_list p').size() + 1;

    $(document).on('click','#addScnt', function() {
        $('<p><label for="option_list" class="col-md-12">' +
            '<span class="col-md-10"><input type="text" size="20" name="option" id="option_" class="form-control valid " value="" placeholder="Option Name" /></span>' +
            // '<span class="col-md-5"><input type="text" size="20" name="option_value" class="form-control valid " value="" placeholder="Option Value" /></span>' +
            '<span class="col-md-2"><a href="#" id="remScnt" class="btn btn-danger">Remove</a></span>' +
            '</label>' +

            '</p>').appendTo(scntDiv);
        i++;
        return false;
    });

    $(document).on('click', '#remScnt', function() {
        if( i > 2 ) {
            $(this).parents('p').remove();
            i--;
        }
    });

    });

function ajax_Datatable_Loader(){
    dataTable =  $('#question-category-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/qcategories/filter/0"

    } );

    dataTable =  $('#main_question-category-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/qcategories/filter/1"

    } );

    $("#question-category-table_filter").css("display","none");
    $("#main_question-category-table_filter").css("display","none");
}

function activeQuestionCategory(state,id) {
    if(state==1){
        $.ajax({
            url: '/configuration/qcategory/remove/',
            type: 'GET',
            data: { id : id, status: 1 },
            success: function(response) {
                $('#question-category-table').dataTable().fnDestroy();
                ajax_Datatable_Loader();
            }
        });
    }
    else if(state==0)
    {
        $.ajax({
            url: '/configuration/qcategory/remove/',
            type: 'GET',
            data: { id : id, status: 0 },
            success: function(response) {
                $('#question-category-table').dataTable().fnDestroy();
                ajax_Datatable_Loader();
            }
        });
    }
}

/**
 * Edit Question categories
 * @param question_cat_id
 */
function editQuestionCategory(question_cat_id) {
    $.ajax({
        url: '/configuration/qcategory/edit/',
        type: 'GET',
        data: { categoryId : question_cat_id },
        beforeSend : function(){
            $('#option_list').html('');
            $('.is_multi_yes').parent().removeClass('checked');
            $('.is_multi_no').parent().removeClass('checked');
            $('.required_options_btns').css('display', 'block');
            $('#option_list').html('<p>'+
            '<label for="option_list" class="col-md-12">'+
                '<span class="col-md-10"></span>'+
                // '<span class="col-md-5"></span>'+
                '<span class="col-md-2"><a href="#" id="addScnt" class="btn btn-info pull-right">Add New Option</a></span>'+
            '</label>'+
            '</p>');
        },
        success: function(response) {
            var is_required = response.data.is_required;
            var is_multiselect = response.data.is_multiselect;
            var main_category = response.data.is_main;
            var category_name = response.data.name;
            var category_id = response.data.id;

            var visibility_arr = response.data.master_classification_allocations;

            var master_classification_options = response.data.master_classification_options;

            //append all option names and values to the input fields
            var count = 1;
            $.each(master_classification_options, function( index, value ) {
                if(count==1){
                    $('#option_list').append('<p>' +
                        '<label for="option_list" class="col-md-12">' +
                        '<span class="col-md-10">' +
                        '<input type="text" size="20" name="option" id="option_'+ value.id +'" class="form-control valid " value="'+ value.name +'" placeholder="Option Name">' +
                        '</span>' +
                        '<span class="col-md-2">' +
                        // '<a href="#" id="remScnts" class="btn btn-danger">Remove</a>' +
                        '</span>' +
                        '</label>' +
                        '</p>'+
                        '<span id="err-option"></span>');
                }else{
                    $('#option_list').append('<p>' +
                        '<label for="option_list" class="col-md-12">' +
                        '<span class="col-md-10">' +
                        '<input type="text" size="20" name="option" id="option_'+ value.id +'" class="form-control valid " value="'+ value.name +'" placeholder="Option Name">' +
                        '</span>' +
                        '<span class="col-md-2">' +
                        '<a href="#" id="remScnts" class="btn btn-danger">Remove</a>' +
                        '</span>' +
                        '</label>' +
                        '</p>'+
                        '<span id="err-option"></span>');
                }
                count++;
            });

            //check if any visible entities
            $.each(visibility_arr, function( index, value ) {
                console.log(value.entity_type_id);
                if(value.entity_type_id==2){ $('#mjb').prop('checked', true); }
                if(value.entity_type_id==3){ $('#cc').prop('checked', true); }
                if(value.entity_type_id==4){  $('#ge').prop('checked', true); }

            });

            //select is required option
            if(is_multiselect==1)
            {
                $('.is_multi_yes').parent().addClass('checked');
                $('.is_multi_yes').attr('checked', 'checked');
                $('.is_multi_yes').prop('checked', true);
            }else{
                $('.is_multi_no').parent().addClass('checked');
                $('.is_multi_no').attr('checked', 'checked');
                $('.is_multi_no').prop('checked', true);
            }

            //select is required option
            if(is_required==1)
            {
                $('.is_req_yes').parent().addClass('checked');
                $('.is_req_yes').attr('checked', 'checked');
                $('.is_req_yes').prop('checked', true);
            }else{
                $('.is_req_no').parent().addClass('checked');
                $('.is_req_no').attr('checked', 'checked');
                $('.is_req_no').prop('checked', true);
            }

            //select is main option
            if(main_category==1)
            {
                $('.required_options_btns').css('display', 'none');
                $('#is_main_cat').val(1);
                $('.is_main_yes').parent().addClass('checked');
            }else{
                $('#is_main_cat').val(0);
                $('.is_main_no').parent().addClass('checked');
            }
            $('#category_name').val(category_name);
            $('#id').val(category_id);

            $('#question-category-edit-model').modal('show');
        }
    });
}

$(document).on('click', '#remScnts', function() {
    $(this).parents('p').remove();
});

/**
 * clear all data after edit model close
 */
$(document).on('click', '#close-qcategory-edit-model', function(){
    $('#question-category-edit-model').modal('hide');
    $('.is_req_yes').parent().removeClass('checked');
    $('.is_req_no').parent().removeClass('checked');
    $('.is_req_no').removeAttr('checked');
    $('.is_req_yes').removeAttr('checked');
    $('.is_req_yes').prop('checked', true);
    $('.is_req_no').prop('checked', true);

    $('.is_multi_yes').parent().removeClass('checked');
    $('.is_multi_no').parent().removeClass('checked');
    $('.is_multi_no').removeAttr('checked');
    $('.is_multi_yes').removeAttr('checked');
    $('.is_multi_yes').prop('checked', true);
    $('.is_multi_no').prop('checked', true);

    $('.is_main_yes').parent().removeClass('checked');
    $('.is_main_no').parent().removeClass('checked');


    $('#mjb').prop('checked', false);
    $('#cc').prop('checked', false);
    $('#ge').prop('checked', false);

    $('#question-category-table').dataTable().fnDestroy();
    ajax_Datatable_Loader();

    $("#tab_edit_logic tr").remove();
});

/**
 * store all updated data
 */
$('#update-qcategory-btn').on('click', function () {
    //var qcategory_data = {};
    //var all_data = $('#question-category-edit-form').serializeArray();

    var is_required = $('input[name="is_required"]:checked').val();
    var is_multiselect = $('#is_multiselect').val();
    var category_name  =   $('#category_name').val();
    var id = $('#id').val();
    var is_main_cat = $('#is_main_cat').val();
    var values = [];
    var visible_to = [];

    //get all visible entities
    $("input[name='visible_to[]']:checked").each( function () {
    // $('input[name="visible_to"]:checked').each(function() {
        var data = $(this).attr('id');
        var dataset = {
            'id' : $(this).attr('id'),
            'value' : this.value
        };
        visible_to.push(dataset);
    });

    //get all option list
    $("input[name='option']").each(function() {
        var data = $(this).attr('id');
        var arr = data.split('option_');
        var dataset = {
            'id' : arr[1],
            'value' : $(this).val()
        };
        values.push(dataset);
    });

    //option list validation
    var status = checkOptionListEmpty();

    if(status){

        $.ajax({
            url: "/configuration/qcategory/insert",
            type: 'POST',
            dataType: 'json',
            data : { is_required:is_required, is_multiselect:is_multiselect, visible_to:visible_to, category_name:category_name, classification_id:id, is_main_cat:is_main_cat, options:values },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';

                    $('.is_req_yes').parent().removeClass('checked');
                    $('.is_req_no').parent().removeClass('checked');
                    $('.is_req_no').removeAttr('checked');
                    $('.is_req_yes').removeAttr('checked');

                    $('.is_multi_yes').parent().removeClass('checked');
                    $('.is_multi_no').parent().removeClass('checked');
                    $('.is_multi_no').removeAttr('checked');
                    $('.is_multi_yes').removeAttr('checked');

                    $('#mjb').prop('checked', false);
                    $('#cc').prop('checked', false);
                    $('#ge').prop('checked', false);

                    msgAlert(msg, msg_type);
                    $('#question-category-edit-model').modal('hide');
                    $("#question-category-table").dataTable().fnDestroy();
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

function removeQuestionCategory(id) {

    swal({
            title: "Are you sure?",
            text: "Your will not be able to recover this classification!",
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
                    url: "/configuration/qcategory/main/check",
                    type: 'GET',
                    dataType: 'json',
                    data:{classification_id:id},
                    success: function(result){
                        if(result.status=='true'){
                            swal({
                                    title: "Are you sure?",
                                    text: "This classification is currently occupied! Do you really want to remove?",
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
                                            url: "/configuration/qcategory/main/delete",
                                            type: 'GET',
                                            dataType: 'json',
                                            data: {classification_id: id},
                                            success: function (result) {
                                                swal("Deleted!", "Classification has been deleted.", "success");
                                                $('#question-category-table').dataTable().fnDestroy();
                                                ajax_Datatable_Loader();
                                            }
                                        });
                                    } else {
                                        swal("Cancelled", "Classification is safe :)", "error");
                                    }
                                });
                        }else if(result.status=='false'){
                            $.ajax({
                                url: "/configuration/qcategory/main/delete",
                                type: 'GET',
                                dataType: 'json',
                                data:{classification_id:id},
                                success: function(result){
                                    swal("Deleted!", "Classification has been deleted.", "success");
                                    $('#question-category-table').dataTable().fnDestroy();
                                    ajax_Datatable_Loader();
                                }
                            });
                        }
                    }
                });
            } else {
                swal("Cancelled", "Classification is safe :)", "error");
            }
        });
}

/**
 * store all updated data
 */
$(document).on('click', '#save-qcategory-btn', function () {
    var qcategory_data = {};
    var all_data = $('#newSubscriptionForm').serializeArray();

    /**
     * serialize array and push array data to one array
     */
    $.each(all_data, function () {
        if (qcategory_data[this.name] !== undefined) {
            if (!qcategory_data[this.name].push) {
                qcategory_data[this.name] = [qcategory_data[this.name]];
            }
            qcategory_data[this.name].push(this.value || '');
        } else {
            qcategory_data[this.name] = this.value || '';
        }
    });

    /**
     * Validate and send to QuestionCategoryController
     * Store all serialized data after form submit
     */
    if ($("#newSubscriptionForm").valid()) {
        location.reload();
        $.ajax({
            url: "/configuration/qcategories/store",
            type: 'POST',
            dataType: 'json',
            data: {data: qcategory_data},
            success: function (result) {
                if (result.success == 'true') {
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

//quesiton category edit mode option list validation
function checkOptionListEmpty() {
    var isValid = true;
    $("#option_list").find('input').each (function(index, value) {
        var value = $(this).val();

        if(value == ""){
            $(this).css( "border", "solid 1px #ff0000" );

            if($("#err-"+index).length == 0) {
                $(this).parent().append('<span id="err-'+ index +'" style="color: #ff0000;">This field cannot be empty</span>');
            }

            isValid = false;
        }
        else {
            $(this).css( "border", "solid 1px #e4e5e7" );
        }

    });
    return isValid;
}