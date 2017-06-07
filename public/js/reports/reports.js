/**
 * Created by Nishan on 6/6/2016.
 */
$(document).ready(function() {

    var dataTable = {}
    ajax_Datatable_Loader();
    getActionItems(0);
    var type = window.location.hash.substr(1);
    getNavigation($('#appointment_id').val());


    $("#report_finalised").click(function(){
        var appointment_id = $('#appointment_id').val();
        var entityType = $('#report_entity_type').val();

        if(entityType == 4){
            swal({
                    title: "Do you want to share with MJB?",
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
                        finalizeReport(appointment_id, 3, 1, entityType);
                    } else {
                        finalizeReport(appointment_id, 3, 0, entityType);
                    }
                });
        }
        else if(entityType == 3){
            finalizeReport(appointment_id, 3, 1, entityType);
        }
        else{
            finalizeReport(appointment_id, 3, 1, entityType);
        }

    });


    function finalizeReport(appointment_id, status, shareMjb, entityType){
        $.ajax({
            type: "POST",
            url: "/report/edit/finalizeReport",
            data: {id: appointment_id, status: status, shareMjb: shareMjb, entityType: entityType},
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (data) {
                if(data.success == "true"){
                    if(shareMjb == 1){
                        // send notifications to dashbord
                        sendReportNotification(appointment_id);
                    }
                    else{
                        window.location.assign("/reports");
                    }

                }
                else{
                    toastr.error("Error - Can't Finalize the Report.");
                }
                $(".splash").hide();
            },
            error: function () {
                $(".splash").hide();
            }

        });
    }


    function sendReportNotification(appointment_id){
        $.ajax({
            type: "GET",
            url: "/report/"+ appointment_id + "/success",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (data) {
                if(data.success == "true"){
                    window.location.assign("/reports");
                }
                $(".splash").hide();
            },
            error: function () {
                $(".splash").hide();
            }

        });
    }


    // edit question comment jquery validation plugin
    $("#editQuestionCommentForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="question_comment"){
                error.insertAfter(document.getElementById('err-question_comment'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "question_comment" : {
                required: true
            }
        },
        messages: {
            "question_comment": {
                required: "Please add question comment"
            }
        }
    });

    // add question comment jquery validation plugin
    $("#addQuestionCommentForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="qComment"){
                error.insertAfter(document.getElementById('err-qComment'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "qComment" : {
                required: true
            }
        },
        messages: {
            "qComment": {
                required: "Please add question comment"
            }
        }
    });

    // edit action item jquery validation plugin
    $("#editActionItemCommentForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="action_comment"){
                error.insertAfter(document.getElementById('err-action_comment'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "action_comment" : {
                required: true
            }
        },
        messages: {
            "action_comment": {
                required: "Please add action item comment"
            }
        }
    });

    //start date picker initialize
    $('#startDatePicker').datepicker({
        format: 'mm/dd/yyyy'
    }).on('changeDate', function(e) {});

    //start date picker initialize
    $('#endDatePicker').datepicker({
        format: 'mm/dd/yyyy'
    }).on('changeDate', function(e) {});

    // custom fancy
    $('.fancybox-button-cstm').fancybox({
        padding: 0,
        helpers: {
            overlay: {
                locked: false
            }
        }
    });
    /**
     * Tab selection function
     */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
    });

    /**
     * Retrieve parent and child questions
     */
    $('#question_tab_').click(function(){
        var appointment_id = $('#appointment_id').val();
        //post form ajax
        $.ajax({
            type: "POST",
            url: "/report/edit/questions",
            data: { appointment_id: appointment_id, answer_id:null },
            beforeSend: function(){
                $('#questions_log').html('');
                $('#category_listing').html('');
                $('.question_image_list').html('');
                $(".splash").show();
            },
            success: function(data) {
                $(".splash").hide();
                var question_comment = "";
                var question_image = "";
                var question_answers = "";
                var action_items = "";
                var question_action_items = "";

                if(data.category_list){
                    $('#category_listing').append('<a class="list-group-item active" onclick="loadAllQuestions(); activeCategoryItem(this);">All</a>');
                    var x = 0;
                    $.each(data.category_list, function(index, value){
                        $('#category_listing').append('<a class="list-group-item" data-id="'+index+'" onclick="getCategoryQuestions('+index+','+appointment_id+','+data.questions[x]['question_id']+'); activeCategoryItem(this);">'+value+'</a>');
                        x++;
                    });
                }

                $.each(data.questions, function (index, value) {

                    if(value.action_items.action_items!=""){
                        var list = "";

                        question_action_items = '<div id="action_item_block_'+ value.question_id +'"><h5><strong>Action Items</strong></h5><ul id="action_item_list_'+ value.question_id +'"><li>Loading...</li></ul></div>';
                        $.each(value.action_items.action_items, function (index, action_item) {
                            if(action_item!="") {
                                list += '<li>' + action_item.name + '</li>';
                            }
                        });

                        setTimeout(function(){ $('#action_item_list_'+ value.question_id).html(list); }, 500);

                    }else{ question_action_items = ""; }

                    if(value.answers!=""){
                        $.each(value.answers, function (index, value) {
                            question_answers = '<p><strong>' + value.answer_value_name + '</strong></p>';
                        });
                    }else{ question_answers = ""; }

                    if(value.appointment_comment!="")
                    { question_comment ='<p><strong>Note: </strong> <span id="comment_'+ value.question_id +'">'+ value.appointment_comment +'</span> <button class="btn btn-default btn-sm" type="button" onclick="viewQuestionComment('+ value.question_id +','+ appointment_id +');">Edit</button></p> '; }else{ question_comment = '<p><button type="button" class="btn btn-sm btn-default" onclick="addQuestionComment('+ value.question_id +','+ appointment_id +');">Add Comment</button></p>'; }

                    $.each(value.images, function (index, image) {
                        if(image!="") {
                            question_image = '<div class="questionImage"><a class="fancybox-button" rel="fancybox-button-'+value.question_id+'" href="'+image+'" data-title="Image"><img src="' + image + '" alt="question_image" name="image" width="150" height="150" /></a></div>';
                        }else{ question_image = ''; }
                    });

                    $('#questions_log').append('<div class="list-group"><div class="col-md-12 list-group-item m-t">' +
                        '<div class="col-md-1">' +
                        '    <div id="master-question-'+index+'"><h3>Q'+ ++index +'.</h3></div>' +
                        '</div>' +
                        '<div class="col-md-11">' +
                        '    <p>'+value.question+'</p>'+
                        question_comment+
                        question_answers+
                        question_action_items+
                        '    <div id="questionImage_'+value.question_id+'" class="question_image_list"> </div>' +
                        '<div id="childQuestion_'+value.question_id+'"> </div>' +
                        '</div>' +
                        '</div></div>');

                    //get all question child questions
                    childQuestions(value.answers[0].questions, value.question_id, appointment_id);
                    //get all images
                    questionImages(value.images, value.question_id);

                });
            }
        });
    });

    /**
     * find json object key
     * @param obj
     * @param keyToFind
     * @returns {*}
     */
    function getObjectKeyIndex(obj, keyToFind) {
        var i = 0, key;

        for (key in obj) {
            if (key == keyToFind) {
                return true;
            }
            i++;
        }
        return null;
    }


    /**
     * get all action_items of all the questions answered as compliance
     */
    $(document).on('click', '#action_items', function(){
        getActionItems(0);
    });

    /**
     * get all users by location id
     */
    $(document).on('click', '.btn-action-item', function(){
        $('body').css('position', 'relative');

        $('#cleartoasts').trigger('click');

        var appointment_id = $(this).attr('id');
        var action_item_id = $(this).attr('data-actionid');
        var inspection_no = $(this).attr('data-inspectionid');
        var location_based_user = "";

        // get marijuana company users by location
        $.ajax({
            type: "GET",
            url: "/report/getUsersByLocation",
            data: { appointment_id: appointment_id, action_item_id:action_item_id, inspection_no:inspection_no  },
            beforeSend: function () {
                $(".splash").show();
                $('#location_based_users').html('');
            },
            success: function(data) {
                var status = '';
                $.each(data.data, function(index, value){
                    if(value.status==true){
                        status = "checked";
                    }else{
                        status = "";
                    }

                    item = '<input type="checkbox" name="location_user" class="location_user" value="'+ value.id +'" '+ status +'/> <span style="top: -2px; position: relative;">'+ value.name+'</span><br>';
                    $('#location_based_users').append(item);
                });

                $('#action_id').val(action_item_id);
                $('#appointmentId').val(appointment_id);
                $('#inspection_no').val(inspection_no);

            },
            complete: function () {
                $(".splash").hide();
            }
        });
    });

    $('.submitWizard').click(function(){

        var approve = $(".approveCheck").is(':checked');
        if(approve) {
            //Got to step 1
            $('[href=#step1]').tab('show');

            //Serialize data to post method
            var datastring = $("#simpleForm").serialize();

            //Show notification
            swal({
                title: "Thank you!",
                text: "You approved our example form!",
                type: "success"
            });
        } else {
            // Show notification
            swal({
                title: "Error!",
                text: "You have to approve form checkbox.",
                type: "error"
            });
        }
    });

    /**
     * Insert comment to a action item
     */
    $(document).on('click', '.comment_add', function() {

        var appointment_id = $(this).data('appointment_id');
        var action_id = $(this).data('action_id');
        var comment = $('#comment_'+action_id).val();
        var image = $('#imgInp_'+action_id).val();
        var entity_tag = "comment_photo";

        //Submit and validate
        $('#add-comment-form-'+action_id).validate({
            rules: {
                comment: {
                    required: function(element) {
                        return $('#imgInp_'+action_id).val() =='';
                    }
                },
                image: {
                    required: function(element) {
                        return $('#comment_'+action_id).val() =='';
                    }
                }
            },
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    dataType: "json",
                    url: "/reports/comment/insert",
                    type: 'POST',
                    success: function (data) {
                        $(".splash").hide();
                        $('#comment_'+action_id).val('');
                        $('.action_comment_image_close_'+action_id).hide(100);
                        $('.selected_'+action_id).hide(100);

                        if (data.success == 'true') {
                            var msg = data.message;
                            var msg_type = 'success';
                            msgAlert(msg, msg_type);

                            if(data.comment_data.image!=null || data.image!=""){
                                var image = "";
                                $.each(data.image, function (i, v) {
                                    image += '<div class="actionItemImage"><a class="fancybox-button-cstm" rel="fancybox-button-'+action_id+'" href="'+v+'"  data-title="Image"><img src='+v+' style="width:150px; height: auto;"></div>';
                                });

                                $('.pull-down-photo-input').addClass('dontshow');
                                var control = $('#imgInp_'+action_id);
                                control.replaceWith( control.val('').clone( true ) );

                                $('#comment_action_'+action_id).append('<div class="form-group form-inline comment-item"><p><strong> '+ data.comment_data[0].username +'</strong><i> '+ data.comment_data[0].date +'</i></p><div><p><span id="action-item-text-comment_id">'+data.comment_data[0].comment+'</span> </p></div>'+image+'</div>');
                            }else{
                                image = "";
                                $('#comment_action_'+action_id).append('<div class="form-group form-inline comment-item"><p><strong> '+ data.comment_data[0].username +'</strong><i> '+ data.comment_data[0].date +'</i></p><div><p><span id="action-item-text-comment_id">'+data.comment_data[0].comment+'</span> </p></div>'+image+'</div>');
                            }
                        } else {
                            var msg = data.message;
                            var msg_type = 'error';
                            msgAlert(msg, msg_type);
                        }
                        $('#totalCount_'+action_id).html(parseInt($('#totalCount_'+action_id).html(), 10)+1)
                    },
                    error: function () {
                        $(".splash").hide();
                        var msg = data.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    },
                    beforeSend: function () {
                        $(".splash").show();
                    }
                });
            }
        });
    });


    if(type=="/step3") {
        $( "#action_items" ).click();
    }else if(type=="/step2") {
        $('#question_tab').click();
    }

});

/**
 * Search filter to retrieve filtered data to the
 * reports bootstrap datatable
 */
$(document).on('click', '#searchAppointments', function(){

    $.validator.addMethod("greaterThan", function(value, element, params) {
        var et = new Date(value);
        var st = new Date($('#startDate').val());

        if(value!="") {
            return (et > st);
        }else
        {
            return true;
        }
    }, 'invalid end date');

    var report_filter = $("#reportForm");
    report_filter.validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="endDate"){
                error.insertAfter($('#date_err'));
            }else{
                error.insertAfter(element);
            }
        },
        errorElement: 'span',
        errorClass: 'help-block',
        highlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').addClass("has-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass("has-error");
        },
        // Specify the validation rules
        rules: {
            endDate: {
                greaterThan : "#endDate"
            }
        },
        // Specify the validation error messages
        messages: {

        }
    });

    if (report_filter.valid() == true) {

        //Get all searchable filter values and assign them to variables
        var fromDate    = $("#startDate").val();
        var toDate      = $("#endDate").val();

        var mjBusiness  = $("#mjBusiness").val();
        var companyName = $("#companyName").val();
        var status      = $("#status").val();
        var audit_type  = $("#audit_type").val();

        var date = [fromDate, toDate];
        if(date[0] == '' && date[1] == '') {
            date = '';
        }
        dataTable.destroy();
        ajax_Datatable_Loader();

        dataTable.columns(0).search(date, true).draw();
        dataTable.columns(2).search(mjBusiness, true).draw();
        dataTable.columns(3).search(audit_type, true).draw();
        dataTable.columns(4).search(companyName, true).draw();
        dataTable.columns(5).search(status, true).draw();
    }

});

/**
 * Get all questions to
 * @param category_id
 */
function getCategoryQuestions(category_id, appointment_id, question_id) {
    //post form ajax
    $.ajax({
        type: "GET",
        url: "/report/category/questions",
        data: { category_id:category_id, appointment_id:appointment_id, question_id:question_id },
        beforeSend: function () {
            $('#questions_log').html('');
            $(".splash").show();
        },
        success: function(data) {
            $(".splash").hide();
            var question_comment = "";
            var question_image = "";
            var question_answers = "";
            var action_items = "";
            var question_action_items = "";

            $.each(data.questions, function (index, value) {

                if(value.action_items!=""){
                    var list = "";
                    question_action_items = '<div id="action_item_block_'+ value.question_id +'"><h5><strong>Action Items</strong></h5><ul id="action_item_list_'+ value.question_id +'"><li>Loading...</li></ul></div>';
                    $.each(value.action_items.action_items, function (index, action_item) {
                        if(action_item!="") {
                            list += '<li>' + action_item.name + '</li>';
                        }
                    });
                    setTimeout(function(){ $('#action_item_list_'+ value.question_id).html(list); }, 500);
                }else{ question_action_items = ""; }

                if(value.answers!=""){
                    $.each(value.answers, function (index, value) {
                        question_answers = '<p><strong>' + value.answer_value_name + '</strong></p>';
                    });
                }else{ question_answers = ""; }

                if(value.appointment_comment!="")
                { question_comment ='<p><strong>Note: </strong> '+ value.appointment_comment +' <button class="btn btn-default btn-sm" type="button" data-id="'+ value.question_id +'">Edit</button> </p> '; }else{ question_comment = ""; }

                $.each(value.images, function (index, value) {
                    if(value!="") {
                        question_image = '<img src="' + value + '" alt="question_image" name="image" width="150" height="150" />';
                    }else{ question_image = ''; }
                });

                $('#questions_log').append('<div class="list-group">' +
                    '<div class="col-md-12 list-group-item m-t">' +
                    '    <div class="col-md-1">' +
                    '        <div id="master-question-'+index+'">' +
                    '            <h3>Q'+ ++index +'.</h3>' +
                    '        </div>' +
                    '    </div>' +
                    '<div class="col-md-11">' +
                    '    <p>'+value.question+'</p>'+
                    question_comment+
                    question_answers+
                    question_action_items+
                    '<div id="questionImage_'+value.question_id+'"> </div>' +
                    '<div id="childQuestion_'+value.question_id+'"> </div>' +
                    '</div>' +
                    '</div></div>');

                //get all question child questions
                childQuestions(value.answers[0].questions, value.question_id, appointment_id);
                //get all images
                questionImages(value.images, value.question_id);
            });
        }
    });
}

/**
 * Active category item
 * @param elem
 */
function activeCategoryItem(elem) {
    var a = document.getElementsByTagName('a')
    for (i = 0; i < a.length; i++) {
        a[i].classList.remove('active')
    }
    elem.classList.add('active');
}

/**
 * trigger quetion tab on click action to load all the questions
 */
function loadAllQuestions() {
    $( "#question_tab" ).trigger( "click" );
}
/*
 * retrieve all active reports to datatable
 */
function ajax_Datatable_Loader() {
    dataTable =  $('#report-detail-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : true,
        "sAjaxSource":"/report/all",
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 7},
            // {"mData": 3},
            {"mData": 4},
            {"mData": 8},
            {"mData": 9},
            {"mData": 5, "bSortable" : false},
            {"mData": 6, "bSortable" : false},
            // {"mData": 10,"bSortable" : false},
        ]
    } );

    $("#report-detail-table_filter").css("display", "none");
}

$(document).on('click', '#pdf_download', function() {
    var appointment_id = $('#appointment_id').val();
    var pdf_password = $('#pdf_password').val();

    if(pdf_password.length < 8)
    {
        swal('Password should contain more than 8 charactors');
        return false;
    }
    if(!/[a-z]/.test(pdf_password))
    {
        swal("Password should contain at least one lower case charactor");
        return false;
    }
    if(!/[A-Z]/.test(pdf_password))
    {
        swal("Password should contain at least one upper case charactor");
        return false;
    }
    if(!/[0-9]/.test(pdf_password))
    {
        swal("Password should contain at least one digit");
        return false;
    }

    if(appointment_id != "")
    {
        $(".splash").show();
        window.location.assign("/report/export/" + appointment_id + "/" + pdf_password);
        setTimeout(
            function()
            {
                $(".splash").hide();
            }, 25000);

    }
});

/**
 * assign user to action item in the action item list
 */
$('#assign_user_btn').click(function (event) {

    var dataset = {};
    var action_id=$('#action_id').val();
    var appointmentId=$('#appointmentId').val();
    console.log(action_id+','+appointmentId);
    //serialize form data
    var data = $('#AssignUserform').serializeArray();

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

    //post form ajax
    $.ajax({
        type: "POST",
        url: "/reports/assignUsers",
        data: { dataset: dataset },
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(data) {
            console.log(data)
            if(data.success == 'true') {
                var msg = data.message;
                var msg_type = 'success';
                $(".splash").hide();
                msgAlert(msg, msg_type);
                getActionItemsAssignee(action_id,appointmentId);
                $('#myModal0').modal('hide');

            } else {
                var msg = data.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#myModal0').modal('hide');
            }

        },
        complete: function () {

            $(".splash").hide();
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
});

/**
 * Action item comment upload photo preview before upload
 */
$(document).on('change', '.imgInp', function(event){
    var action_id = $(this).attr( "data-actionId" );
    $('.result_'+action_id).html('');

    if (this.files && this.files[0]) {
        var inp = document.getElementById('imgInp_'+action_id);
        for (var i = 0; i < inp.files.length; ++i) {
            var tmppath = URL.createObjectURL(event.target.files[i]);
            $('.result_'+action_id).append('<img src="'+tmppath+'" style="padding:4px;" width="150" height="150" alt="images" />');
        }
    }
    $('.hide-item-'+action_id).removeClass('dontshow');
});

$(document).on('click', '#reset_button', function(event){
    var action_id = $(this).attr( "data-actionId" );

    $('.pull-down-photo-input').addClass('dontshow');
    $('.result_'+action_id).html('');
    var control = $('#imgInp_'+action_id);
    control.replaceWith( control.val('').clone( true ) );
});

$(document).on('click', '.read_comments', function (event) {
    var action_id = $(this).attr( "data-actionId" );
    var appointment_id = $(this).attr( "data-appointmentId" );

    $.ajax({
        type: "GET",
        url: "/reports/readAllActionItemComments",
        data: { action_id: action_id, appointment_id:appointment_id },
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(data) {
            if(data.success == 'true') {
                var msg = data.message;
                var msg_type = 'success';
                $(".splash").hide();
                $('#unreadCount_'+action_id).html(0);

                $('#myModal0').modal('hide');

            } else {
                var msg = data.message;
                var msg_type = 'error';

                $('#myModal0').modal('hide');
            }
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
});

/**
 * Comment image close button action
 */
$(document).on('click', '.action_comment_image_close', function(){
    var actionId = $(this).attr('data-actionId');

    var control = $("#imgInp_"+actionId);
    $('.selected_'+actionId).hide(100);
    $('.action_comment_image_close_'+actionId).hide(100);

    control.replaceWith( control = control.clone( true ) );
});

/**
 * Retrieve parent and child questions
 */
$('#unknownCompliance_tab_').click(function(){
    var appointment_id = $('#appointment_id').val();
    //post form ajax
    $.ajax({
        type: "POST",
        url: "/report/unknownCompliance",
        data: { appointment_id: appointment_id, answer_id:3 },
        beforeSend: function(){
            $('#questions_log').html('');
            $('#category_listing').html('');

            $('#unknownCompliance_log').html('');
            $('.action_item_block').html('');
            $(".splash").show();
        },
        success: function(data) {
            $(".splash").hide();
            var question_comment = "";
            var question_image = "";
            var question_answers = "";
            var action_items = "";
            var question_action_items = "";

            $.each(data.questions, function (index, value) {
                if(value.answers!=""){
                    $.each(value.answers, function (index, value) {
                        question_answers = '<p><strong>' + value.answer_value_name + '</strong></p>';
                    });
                }else{ question_answers = ""; }

                if(value.action_items.action_items!=""){
                    var list = "";
                    question_action_items = '<div id="action_item_block_'+ value.question_id +'"><h5><strong>Action Items</strong></h5><ul id="action_item_list_'+ value.question_id +'"><li>Loading...</li></ul></div>';

                    $.each(value.action_items.action_items, function (pointer, action_item) {
                        if(action_item!="") {
                            if(action_item.name!=undefined) {
                                list += '<li>' + action_item.name + '</li>';
                            }
                        }
                    });
                    setTimeout(function(){ $('#action_item_list_'+ value.question_id).html(list); }, 500);
                }else{ question_action_items = ""; }

                if(value.appointment_comment!="")
                { question_comment ='<p><strong>Note: </strong> '+ value.appointment_comment +' </p>'; }else{ question_comment = ""; }

                $.each(value.images, function (index, value) {
                    if(value!="") {
                        question_image = '<img src="' + value + '" alt="question_image" name="image" width="150" height="150" />';
                    }else{ question_image = ''; }
                });

                $('#unknownCompliance_log').append('<div class="list-group">' +
                    '<div class="col-md-12 list-group-item m-t">' +
                    '   <div class="col-md-1">' +
                    '       <h3>Q'+ ++index +'.</h3>' +
                    '   </div>' +
                    '   <div class="col-md-11">' +
                    '       <p>'+value.question+'</p>'+
                    question_comment+
                    question_answers+
                    question_action_items+
                    '<div id="questionImage_'+value.question_id+'"> </div>' +
                    '<div id="childQuestion_'+value.question_id+'"> </div>' +
                    '   </div>' +
                    '</div></div>');

                //get all question child questions
                childQuestions(value.answers[0].questions, value.question_id, appointment_id);
                //get all images
                questionImages(value.images, value.question_id);
            });
        }
    });
});

//get all question child questions
function childQuestions(childQuestion, parentQuestionId, appointment_id) {

    var question_comment = "";
    var question_image = "";
    var question_answers = "";
    var action_items = "";
    var question_action_items = "";
    var action = "";

    $.each(childQuestion, function (index, value) {
        if(value.action_items.action_items!=""){
            var list = "";
            question_action_items = '<div id="action_item_block_'+ value.question_id +'"><h5><strong>Action Items</strong></h5><ul id="action_item_list_'+ value.question_id +'"><li>Loading...</li></ul></div>';

            $.each(value.action_items.action_items, function (pointer, action_item) {
                if(action_item!="") {
                    if(action_item.name!=undefined) {
                        list += '<li>' + action_item.name + '</li>';
                    }

                }
            });
            setTimeout(function(){ $('#action_item_list_'+ value.question_id).html(list); }, 500);
        }else{ question_action_items = ""; }

        if(value.answers!=""){
            $.each(value.answers, function (index, value) {
                question_answers = '<p><strong>' + value.answer_value_name + '</strong></p>';
            });
        }else{ question_answers = ""; }

        if(value.appointment_comment!="")
        { question_comment ='<p><strong>Note: </strong> <span id="comment_'+ value.question_id +'"> '+ value.appointment_comment +'</span> <button class="btn btn-default btn-sm" type="button" onclick="viewQuestionComment('+ value.question_id +','+ appointment_id +');">Edit</button></p> '; }
        else
        { question_comment = '<p><button type="button" class="btn btn-default btn-sm" onclick="addQuestionComment('+ value.question_id +','+ appointment_id +');">Add Comment</button></p>'; }

        $.each(value.images, function (index, image) {
            if(image!="") {
                question_image = '<img src="' + image + '" alt="question_image" name="image" width="150" height="150" />';
                $('#questionImage_'+value.question_id).html('<img src="' + image + '" alt="question_image" name="image" width="150" height="150" />');
            }else{ question_image = ''; }
        });

        //draw child questions
        $("#childQuestion_"+parentQuestionId).append('<div class="list-group">' +
            '<div class="col-md-12 list-group-item m-t">' +
            '   <div class="col-md-1">' +
            '       <div id="master-question-'+index+'"><h3>Q'+ ++index +'.</h3></div>' +
            '   </div>' +
            '   <div class="col-md-11"><p>'+value.question+'</p>' +

            '     <div id="childQuestion_'+value.question_id+'">' +
            question_answers+
            question_comment+

            '     <div id="questionImage_'+value.question_id+'"  class="question_image_list"></div>' +
            question_action_items+

            '      </div>' +
            '   </div>' +
            '</div></div>');

        //get all images
        questionImages(value.images, value.question_id);
        //get all question child questions
        childQuestions(value.answers[0].questions, value.question_id, appointment_id);
    });
}

/**
 * get all question images
 * @param imagesArray
 * @param question_id
 */
function questionImages(imagesArray, question_id){
    $.each(imagesArray, function(index, image){
        $('#questionImage_'+question_id).append('<img src="' + image + '" style="padding-right:10px" alt="question_image" name="image" width="150" height="150" />');
    });
}

/**
 * Question comment viw function
 * @param question_id
 */
function viewQuestionComment(question_id, appointment_id) {

    //assign values to elements
    $('#question_id').val(question_id);
    $('#appointment_id').val(appointment_id);

    $.ajax({
        type: "GET",
        url: "/report/edit/questions/comment",
        data: {question_id: question_id, appointment_id:appointment_id},
        beforeSend: function () {
            $('#question_comment').html('');
            $(".splash").show();
        },
        success: function (data) {
            $(".splash").hide();
            $("#question_comment").text(data.data);
        }
    });
    $('#editQuestionCommentModel').modal('show');
}

/**
 * Update question comment
 */
$('#update_qcomment_btn').click(function(){
    var question_comment = $('#question_comment').val();
    var question_id = $('#question_id').val();
    var appointment_id = $('#appointment_id').val();

    if($("#editQuestionCommentForm").valid()) {
        $.ajax({
            type: "GET",
            url: "/report/edit/questions/comment/store",
            data: {comment: question_comment, question_id: question_id, appointment_id: appointment_id},
            beforeSend: function () {
                $('#question_comment').html('');
                $(".splash").show();
            },
            success: function (data) {
                $(".splash").hide();
                $("#question_comment").text(data.data);
                if (data.success == 'true') {
                    var msg = data.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#editQuestionCommentModel').modal('hide');
                    loadAllQuestions();
                } else {
                    var msg = data.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            },
            error: function () {
                $(".splash").hide();
                var msg = data.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        });
    }
});

/**
 * add new question comment
 */
function addQuestionComment(question_id, appointment_id){
    $('#addQuestionCommentModel').modal('show');
    //assign values to elements
    $('#question_id').val(question_id);
    $('#appointment_id').val(appointment_id);
    var qcomment = $('#question_comment').val();

    if($('#qComment').val().trim().length == 0){
        console.log('null');
        setTimeout($('#remove_qcomment_btn').css('display', 'none'), 10);
        $('#remove_qcomment_btn').remove();
    }else{
        // console.log('not null');
        setTimeout($('#remove_qcomment_btn').css('display', 'inline'), 10);
    }
}

/**
 * Update question comment
 */
$(document).on('click','#remove_qcomment_btn', function(){

    var question_comment = "";
    var question_id = $('#question_id').val();
    var appointment_id = $('#appointment_id').val();

    $.ajax({
        type: "GET",
        url: "/report/edit/questions/comment/store",
        data: {comment: question_comment, question_id: question_id, appointment_id: appointment_id},
        beforeSend: function () {
            $('#qComment').html('');
            $(".splash").show();
        },
        success: function (data) {
            $(".splash").hide();
            $("#qComment").text(data.data);
            if (data.success == 'true') {
                var msg = data.message;
                var msg_type = 'success';
                $('.answer-'+question_id).text(question_comment);
                $('.answer-'+question_id).parent().find('button i').removeClass('fa-comment').addClass('fa-pencil-square-o');
                $('#qComment').val('');
                msgAlert(msg, msg_type);
                $('#addQuestionCommentModel').modal('hide');
                //loadAllQuestions();
                location.reload();
            } else {
                var msg = "Question Comment Removed Successfully!";
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        },
        error: function () {
            $(".splash").hide();
            var msg = "Question Comment Remove Failed!";
            var msg_type = 'error';
            msgAlert(msg, msg_type);
        }
    });
});

/**
 * Update question comment
 */
$(document).on('click','#add_qcomment_btn', function(){
    var question_comment = $('#qComment').val();
    var question_id = $('#question_id').val();
    var appointment_id = $('#appointment_id').val();


    //submit if validated
    if($("#addQuestionCommentForm").valid()) {
        $.ajax({
            type: "GET",
            url: "/report/edit/questions/comment/store",
            data: {comment: question_comment, question_id: question_id, appointment_id: appointment_id},
            beforeSend: function () {
                $('#qComment').html('');
                $(".splash").show();
            },
            success: function (data) {
                $(".splash").hide();
                $("#qComment").text(data.data);
                if (data.success == 'true') {
                    var msg = data.message;
                    var msg_type = 'success';
                    $('.answer-'+question_id).text(question_comment);
                    $('.answer-'+question_id).parent().find('button i').removeClass('fa-comment').addClass('fa-pencil-square-o');
                    $('#qComment').val('');
                    msgAlert(msg, msg_type);
                    $('#addQuestionCommentModel').modal('hide');
                    //loadAllQuestions();
                    location.reload();
                } else {
                    var msg = data.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            },
            error: function () {
                $(".splash").hide();
                var msg = data.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        });
    }
});

/**
 * edit view model of action item comment
 * @param dataset
 */
function viewActionItemComment(action_item_id, comment_id, appointment_id) {
    $('#editActionItemCommentModel').modal('show');
    $('#action_item_id').val(action_item_id);
    $('#comment_id').val(comment_id);
    $('#appointment_id').val(appointment_id);

    $.ajax({
        type: "GET",
        url: "/report/edit/actionItems/comment",
        data: {comment: comment_id, action_item_id: action_item_id, appointment_id: appointment_id},
        beforeSend: function () {
            $('#action_comment').html('');
            $(".splash").show();
        },
        success: function (data) {
            $(".splash").hide();
            $("#action_comment").text(data.data);
            if (data.success == 'true') {
                $('#editActionItemCommentModel').modal('show');

            } else {
                var msg = data.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        }
    });
}

/**
 * Update action item comment
 */
$(document).on('click','#edit_action_comment_btn', function(){

    var question_comment = $('#action_comment').val();
    var comment_id = $('#comment_id').val();
    var action_item_id = $('#action_item_id').val();
    var appointment_id = $('#appointment_id').val();
    if($("#editActionItemCommentForm").valid()) {
        $.ajax({
            type: "POST",
            url: "/report/edit/actionItems/update",
            data: {
                comment: question_comment,
                comment_id: comment_id,
                action_item_id: action_item_id,
                appointment_id: appointment_id
            },
            beforeSend: function () {
                $('#action_comment').html('');
                $(".splash").show();
            },
            success: function (data) {
                $(".splash").hide();
                $("#action_comment").text(data.data);
                if (data.success == 'true') {
                    var msg = data.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#editActionItemCommentModel').modal('hide');
                    loadAllActionItems();
                } else {
                    var msg = data.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            },
            error: function () {
                $(".splash").hide();
                var msg = data.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
            }
        });
    }
});

$(document).on('click', '.action-item-comment-exp', function (){
    if($(this).attr("aria-expanded") == 'false'){
        $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }else{
        $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
});

$(document).on('click', '.action-item-comment-exp i', function (){
    if($(this).attr("aria-expanded") == 'false'){
        $(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }else{
        $(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
});

// Report status message
$(document).on('click','.pendingValidation', function(){
    swal({
        title: "Error!",
        //text: "This Appointment Not Yet Completed."
        text: "In order to view this audit report, the audit must first be completed and synced."
    });
});

/**
 * action item reload
 */
function loadAllActionItems() {
    $( "#action_items" ).trigger( "click" );
}

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

$('#cleartoasts').click(function () {
    toastr.clear();
});

//Navigation for Categories
function getNavigation(appointment_id) {
    var item;
    // alert(appointment_id)
    $.ajax({
        type: "GET",
        url: "/report/getNavigationCategories/",
        data: { appointment_id: appointment_id,answer_id:[2, 3]},
        beforeSend: function(){
            $('#category_listings').html('');
            $(".splash").show();
        },
        success: function(data) {
            var user_group_btn = "";
            var appointment_status = data.status;

            item='<a class="list-group-item active" id="cat_0" onclick="getActionItems(0)">All</a>'
            $('#category_listings').append(item);
            $.each(data.categories, function(index, value){
                item='<a class="list-group-item" id="cat_'+value.id+'" onclick="getActionItems('+value.id+')">'+value.name+'</a>'
                $('#category_listings').append(item);
            });
            // Notification
        },
        complete: function () {
            $(".splash").hide();
        }
    });
}
var appointment_id = "";
var action_id = "";
var obj = "";
$(document).on('click','#reopen', function(){
    appointment_id = $('#appointment_id').val();
    //action_id = $(this).parent('div').find('.action_id').val();
    action_id = $(this).parent('div').parent('div').parent('div').parent('div').find('.action_id').val();
    //alert(action_id);
    obj = this;

    $('#commentModel').modal('show');
});

$(document).on('click','#comment_model', function(){

    var comment = $('#comment').val();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/report/reopen_action_item",
        data: { action_id: action_id, appointment_id:appointment_id,comment:comment},
        beforeSend: function(){

            $(".splash").show();
        },
        success: function(data) {
            if(data.success == true)
            {
                $(".splash").hide();
                $(obj).attr('value', 'Close Action Item');
                $(obj).attr('id', 'deactivate');
                $(obj).attr('class', 'btn btn-success');

                $(obj).parent('div').parent('div').parent('div').parent('div').find('.comment_add').removeAttr('disabled');
                $(obj).parent('div').parent('div').parent('div').parent('div').find('.comment').removeAttr('disabled');
                $(obj).parent('div').parent('div').parent('div').parent('div').find('.imgInp').removeAttr('disabled');
                //$(obj).parent('div').parent('div').parent('div').parent('div').find('.comment_add').attr('data-target','#myModal0');

                var msg = "Comment saved successfully.";

                var msg_type = 'success';
                msgAlert(msg, msg_type);
            }
            $(".splash").hide();
            $('.close_window').click();

            $("#action_item_panel_" + action_id)[0].scrollIntoView(true);
        }
    });

});

$(document).on('click','#deactivate', function(){
    var appointment_id = $('#appointment_id').val();
    //var action_id = $(this).parent('div').find('.action_id').val();
    var action_id = $(this).parent('div').parent('div').parent('div').parent('div').find('.action_id').val();
    var obj = this;

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/report/deactivate_action_item",
        data: { action_id: action_id, appointment_id:appointment_id},
        beforeSend: function(){

            $(".splash").show();
        },
        success: function(data) {
            if(data.success == true)
            {
                if(data.user_group_id == 4)
                {
                    $(obj).parent('div').html("<span class='badge badge-warning'>Action item is Closed</span>");
                    //$(obj).attr('id', '');
                    //$(obj).attr('class', 'btn btn-warning');
                }
                else
                {
                    $(obj).attr('value', 'Reopen Action Item');
                    $(obj).attr('id', 'reopen');
                    $(obj).attr('class', 'btn btn-warning');
                }

                $(obj).parent('div').parent('div').parent('div').parent('div').find('.comment_add').attr('disabled','disabled');
                $(obj).parent('div').parent('div').parent('div').parent('div').find('.comment').attr('disabled','disabled');
                $(obj).parent('div').parent('div').parent('div').parent('div').find('.imgInp').attr('disabled','disabled');
                $(".splash").hide();
            }
        }
    });
});


$(function() {
    var url = window.location.href;
    var aid = url.split('=');

    if(typeof(aid[1]) != "undefined")
    {
        var action_item_id = aid[1].split('#');
        if(typeof(action_item_id[0]) != "undefined" && action_item_id[0] != "")
        {
            $("#action_item_panel_" + action_item_id[0])[0].scrollIntoView();
        }
    }

});


//Load Action Items

function getActionItems(category){
    var appointment_id = $('#appointment_id').val();
    $('#category_listings').find('.active').removeClass('active');
    $('#cat_'+category).addClass('active');

    //post form ajax
    // var item;
    var action_item_question;
    var action_item_name;
    var action_item_comment;
    var action_item_assignee;
    var action_item_id;
    var question_action_item_id;
    var inspection_no;
    var not_assigned="Not assigned";
    var appointment_action_item_closed_count=0;
    $.ajax({
        type: "POST",
        url: "/report/edit/actionItems",
        data: { appointment_id: appointment_id, answer_id:[2, 3],category:category},
        async:false,
        beforeSend: function(){
            $('#action-item-content').html('');
            $(".splash").show();
        },
        success: function(data) {
            var user_group_btn = "";
            var appointment_status = data.status;
            if (data.data.length == 0) {
                $('#action-item-content').html('<div><div class="p-lg">No action items</div></div>');
            }
            $.each(data.data, function(index, value){
                action_item_question = value.question;
                action_item_name = value.action_item_name;
                action_item_comment = value.comment;
                action_item_assignee=(value.assigned_users!=null)?value.assigned_users:"Not Assigned"

                action_item_id = value.action_item_id;
                appointment_action_item_closed_count = value.appointment_action_item_closed_count;
                inspection_no = data.inspection_number;
                var unread_count = value.unread_count;
                var total_count = value.total_count;
                var level = value.level;


                //data.user.master_user_group_id = 4;
                var deactivate = "<input type='button' value='Close Action Item' id='deactivate' class='btn btn-success' >";
                var is_deactivate = "";
                if(appointment_action_item_closed_count > 0)
                {
                    deactivate = "<input type='button' value='Reopen Action Item' id='reopen' class='btn btn-warning'  >";
                    if(data.user.master_user_group_id ==4)
                    {
                        deactivate = "<span class='badge badge-warning'>Action item is Closed</span>";
                    }
                    is_deactivate = "disabled";
                }

                if(data.user.master_user_group_id==2 || data.user.master_user_group_id==3){
                    user_group_btn = '<a class="btn btn-default pull-right btn-action-item"  data-toggle="modal" data-target="#myModal0" data-inspectionId="'+inspection_no+'" data-actionId="'+ action_item_id +'" id="'+ appointment_id +'"><i class="fa fa-user-plus" ></i></a>';
                    if(is_deactivate == "disabled")
                    {
                        //user_group_btn = '<a class="btn btn-default pull-right btn-action-item"  data-toggle="modal"  data-target="" readonly data-inspectionId="'+inspection_no+'" data-actionId="'+ action_item_id +'" id="'+ appointment_id +'"><i class="fa fa-user-plus" ></i></a>';

                    }
                }else{
                    user_group_btn = "";
                }

                var item = '<div class="panel panel-default m-t"  id="action_item_panel_'+action_item_id+'">'+
                    '       <div class="panel-body">'+
                    '           <div class="row">'+
                    '               <div class="col-md-12"><span class="question question-no-p-l">' + level +'. '+ action_item_question +'</span></div>'+
                    '               <div class="col-md-12"><span class="answer m-b">'+ action_item_name +'</span>' +
                    '                   <div style="float: right;">' + deactivate + '</div>' + '</div>';
                if(action_item_comment!=''){
                    item+='<div class="col-md-12"><div class="col-md-12 no-padding note m-b"><div class="col-md-12 no-padding"><span class="title">Auditor\'s Note:</span></div><div class="col-md-12 no-padding"> '+ action_item_comment +'</div></div></div>';
                }
                item+='<div class="assignee_div col-md-12" xmlns="http://www.w3.org/1999/html">';
                if(value.assigned_users!=null){
                    item+='<i class="fa fa-user-circle-o"></i> <span class="users">'+ action_item_assignee +'</span>';
                }
                item += ' </div><div class="col-md-12 text-right arrow-hide">'+
                    '                   <a class=" collapsed action-item-comment-exp text-right " data-toggle="collapse" data-target="#item'+index+'" aria-expanded="true" aria-controls="item'+index+'" title="Hide/View Comments"><i style="color:#888" data-actionId="'+ action_item_id +'" data-appointmentId="'+appointment_id+'" class="fa fa-chevron-down read_comments"></i></a>'+
                    '        </div>'+
                    '           </div>'+
                    '           <div class="collapse in comment-wrapper" id="item'+index+'">'+
                    '               <div id="comment_action_'+action_item_id+'"><p><b>Comments</b></p></div>'+
                    '           </div>'+
                    '           <form action="/reports/comment/insert" method="post" class="add-comment-form" id="add-comment-form-'+ action_item_id +'">'+
                    '               <input type="hidden" name="appointment_id" value="'+appointment_id+'" />'+
                    '               <input type="hidden" name="action_id" class="action_id" value="'+action_item_id+'" />'+
                    '               <input type="hidden" name="entity_tag" value="comment_photo" />'+
                    '               <div class="row m-b">'+
                    '                   <div class="col-md-12 col-xs-12 comment-box"><input type="text" ' + is_deactivate + ' name="comment" id="comment_'+action_item_id+'" class="comment form-control" placeholder="Add Comment"  value=""/></div>' +
                    '                   <div class="row form-group text-right pull-down-photo-input dontshow hide-item-'+action_item_id+'">'+
                    '                       <output id="result" class="add-image result_'+action_item_id+'"></output>'+
                    '                       <a id="reset_button" class="btn btn-sm btn-default" data-actionId="'+action_item_id+'"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>'+
                    '                       <div class="form-group"><div class="action_comment_image_close action_comment_image_close_'+action_item_id+'" data-actionId="'+action_item_id+'"></div><img class="selected_'+action_item_id+' action_comment_image" src="#" width="150" height="150" alt="Comment image preview" /></div>'+
                    '                   </div>'+
                    '               </div>'+
                    '                <div class="row">'+
                    '                  <div class="col-md-12 col-xs-12"><button type="submit" title="Submit Comment" ' + is_deactivate + ' class="btn btn-primary comment_add" data-action_id="'+action_item_id+'" data-appointment_id="'+ appointment_id +'">Submit</button>'+
                    '                       <label class="btn btn-default btn-file pull-right">'+
                    '                           <i class="fa fa-camera"></i><input type="file" style="display: none;" ' + is_deactivate + ' class="imgInp" multiple name="imgInp[]" id="imgInp_'+action_item_id+'" data-actionId="'+action_item_id+'" accept="image/jpeg, image/png" title="Attach Images" value="" multiple="multiple" />'+
                    '                       </label>'+user_group_btn+
                    '               </div>'+
                    '               </div>'+
                    '               <div class="row"> <br/>'+
                    '                  <div class="col-md-5 col-xs-12"> <p><span id="totalCount_'+action_item_id+'" class="msg-count">'  + total_count + '</span>  Total message(s) </p> </div>'+
                    '                  <div class="col-md-5 col-xs-12"> <p><span id="unreadCount_'+action_item_id+'" class="msg-count">'  + unread_count + '</span>  Unread message(s) </p> </div>'+
                    '               </div>'+
                    '           </form>'+
                    '       </div>'+
                    '   </div>';

                $('#action-item-content').append(item);
                $.each(data.comments, function (index, value) {
                    question_action_item_id = value.question_action_item_id;
                    if(action_item_id==question_action_item_id){
                        var image = '';
                        var edit_button = '';
                        $.each(value.image, function (i, v){
                            if(v.name==""){
                                image += "";
                            }else{
                                image += '<div class="actionItemImage"><a class="fancybox-button-cstm" rel="fancybox-button-'+question_action_item_id+'" href="'+value.image_path+v.name+'"  data-title="Image"><img src="'+value.image_path+v.name+'" style="width:150px; height: auto;" name="iamge_'+v.id+'" /></a></div>';
                            }
                        });

                        if(appointment_status=="false"){
                            edit_button = '<button class="btn btn-sm btn-default" onclick="viewActionItemComment('+ value.question_action_item_id+', '+ value.comment_id +', '+ appointment_id +');">Edit</button>';
                        }else{ edit_button = ''; }
                        var location = (value.location !=null) ? ' at '+value.location:'';
                        $('#comment_action_'+question_action_item_id).append('<div class="form-group form-inline comment-item"><p><strong>'+ value.username +'</strong><i> '+value.date_time+location+'</i></p><div><p><span id="action-item-text-'+value.comment_id+'">'+value.content+'</span> '+edit_button+' </p></div><section><div>'+ image +'</section></div>');
                    }
                });
            });
            // Notification
        },
        complete: function () {
            $(".splash").hide();
        }
    });
}


//Load Assigness for Action items

function getActionItemsAssignee(action_id,appointmentId){

    var users=$('#action_item_panel_'+action_id);

    $.ajax({
        type: "POST",
        url: "/report/edit/actionItemsAssignee",
        data: { action_id: action_id, appointmentId:appointmentId},
        beforeSend: function(){
            users.find('.assignee_div').html('');
            // $(".splash").show();
        },
        success: function(data) {

            var user_group_btn = "";
            var appointment_status = data.status;

            $.each(data.data, function(index, value){
                var assigned_users='<i class="fa fa-user-circle-o"></i><span class="users">' + value.assigned_users +'</span>';

                if(value.assigned_users!=null){

                    users.find('.assignee_div').html('');
                    users.find('.assignee_div').html(assigned_users);
                }

            });
            $('html,body').animate({
                    scrollTop: $('#action_item_panel_'+action_id).offset().top},
                'slow');


        }
    });
}