$(function(){

    //publish date picker initialize
    $('#publishDatePicker').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '+1d'
    }).on('changeDate', function(e) {});


    $.validator.setDefaults({
        ignore: []
    });

    getStatesBasedOnCountry($("#question_country").val(), true);
    $('#select-license').attr('checked',false);
    $(".createQuestionSelectAllCity").hide();

    // City change function
    $("#create_question_cities").change(function(){
        getCities($("#create_question_cities").val());
    });


    $.validator.addMethod("actionItemCheck", function (value, element) {
        var isValid = true;
        var count = $("#addActionItemTable tbody", "#create_question_from").children('tr').length;

        if(count < 1){
            isValid = false
        }
        return isValid;
    }, 'Need to have at least one Action Item');

    $.validator.addMethod("licenseCheck", function (value, element) {
        var isValid = true;
        var count = $("#addLicenceTable tbody").children('tr').length;
        if(count < 1){
            isValid = false
        }
        return isValid;
    }, 'Need to have at least one Licence');

    $("#create_question_from").validate({
        rules: {
            question: {
                required: true
            },
            explanation: {
                required: true,
                maxlength: 26
            },
            mainCategory: {
                required: true
            },
            country: {
                required: true
            },
            state: {
                required: true
            },
            cities: {
                required: true
            },
            auditType:{
               required: true
            }
            ,
            licenceValidation:{
                licenseCheck: true
            }
            ,
            actionItemValidation:{
                actionItemCheck: true
            }
        },
        errorPlacement: function(error, element) {
            if(element.attr('name')=="cities"){
                error.insertAfter(document.getElementById('chkCreateQuestionSelectAllCityError'));
            } else{
                error.insertAfter(element);
            }
        },

    });

    $("#add_citation").click(function(){
        var table_row = $('.table_row').html();
        table_row = '<div class="col-sm-12 table_row padder-v" >' + table_row + "</div>";
        $(table_row).appendTo("#main_div");
    });


    // Accordion open click function
    $(document).on('click', ".master_question_answer", function(event) {
        var questionAnswerId = $(this).attr('question-answer-id');
        if(questionAnswerId == 0){
            swal({
                title: "Error!",
                text: "You must save the question before you can add sub-questions to the answers."
            });

            var accordId = $(this).parent().attr('id');
            $("#"+accordId).accordion({active: false });
        }
    });


    $("#btnAddNewActionItem").click(function(){
        AddNewActionItem("addActionItemTable" , "0_0");
    });

    $("#chkCreateQuestionSelectAllCity").click(function(){

       if($("#chkCreateQuestionSelectAllCity").is(':checked') ){
           $("#create_question_cities").empty();
           $('#create_question_cities').select2('val', '');
           $('#chkCreateQuestionSelectAllCity').prop('checked', true);
           $('.city-wrap').addClass('hidden');
           $('#create_question_cities').attr('disabled',true);

           if($('#all_cities').hasClass('hidden')){
                $('#all_cities').removeClass('hidden');

           }

       }
       else{
           if($('.city-wrap').hasClass('hidden')){
                $('.city-wrap').removeClass('hidden');
               $('#create_question_cities').attr('disabled',false);
           }
           $('#all_cities').addClass('hidden');
        }

    });


    // Add New Item Row
    function AddNewActionItem(tableId, inputId){

            var rowId = $("#"+tableId + " tbody tr:last > td:eq(0)").text();
            rowId++;

            if(rowId == 1){
                $("#"+tableId + " tbody").append('<tr>' +
                    '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput"><input class="action_item_data col-sm-12 marginButtom1 form-control" type="text" id="action_name_'+rowId+'_'+inputId+'" ></td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left" type="button"><i class="fa fa-times"></i></button></td>' +
                    '</tr>');
            }
            else{
                $("#"+tableId + " tbody tr:last").after('<tr>' +
                    '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput"><input class="action_item_data col-sm-12 marginButtom1 form-control" type="text" id="action_name_'+rowId+'_'+inputId+'" ></td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left" type="button"><i class="fa fa-times"></i></button></td>' +
                    '</tr>');

            }



        // Delete Action Item Row
        $(document).on('click', ".btn_delete_item", function() {
            if($(this).attr('item-number') != "1"){
                var tr = $(this).closest('tr');
                //tr.css("background-color","#FF3700");
                tr.fadeOut(400, function(){
                    tr.remove();
                });
            }

            return false;
        });

        $("#actionItemValidation-error").remove();
    }
    $('body').on('change','.states_fedaral',function(){
        var stateFedaral=$(this).val();
        var stateFedaralId=$(this).attr('id');
        var index=stateFedaralId.split('_');
        getLicences(stateFedaral, "#license_type_"+index[2]);
    });

    $('body').on('click','#btnAddLicense',function () {
        var rowId = $('#addLicenceTable tbody tr:last > td:eq(0)').text();

        rowId++;
        if($('#select-license').prop( "checked")){
            if(rowId == 1){
                $('#addLicenceTable tbody').append('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableStateTdInput state-list-position-top hidden">'+
                    '<div class="row">'+
                    '<div class="col-xs-12">'+
                    '<label>State:</label>'+
                    '<select class="form-control states_fedaral"  id="states_fedaral_'+rowId+'">'+
                    '</select>'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td class="questionTableTdInput">' +
                    '<div class="col-xs-12">'+
                    '<label>License Type:</label>'+
                    '<select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '</select>' +
                    '</div>'+
                    '</td>' +
                    '<td class="questionTableTd"><div class="col-xs-12 m-b-xs"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></div>' +
                    '</td>' +

                    '</tr>');
            }
            else{
                $('#addLicenceTable tbody tr:last').after('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableStateTdInput state-list-position-top hidden">'+
                    '<div class="row">'+
                    '<div class="col-xs-12">'+
                    '<label>State:</label>'+
                    '<select class="form-control states_fedaral"  id="states_fedaral_'+rowId+'">'+
                    '</select>'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td class="questionTableTdInput">' +
                    '<div class="col-xs-12">'+
                    '<label>License Type:</label>'+
                    '<select class="license_data marginButtom1 col-sm-11 form-control" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '</select>' +
                    '</div>'+
                    '</td>' +
                    '<td class="questionTableTd"><div class="col-xs-12 m-b-xs"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></div>' +
                    '</td>' +
                    '</tr>');
            }
            var countryId=$('#question_country').val();
            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });
            getStatesWithFedaral(countryId,"#states_fedaral_"+rowId)
        }else {
            if(rowId == 1){
                $('#addLicenceTable tbody').append('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput">' +
                    '   <select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '   </select>' +
                    '</td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
                    '</td>' +

                    '</tr>');
            }
            else{
                $('#addLicenceTable tbody tr:last').after('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput">' +
                    '<select class="license_data marginButtom1 col-sm-11 form-control" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '   </select>' +


                    '</td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
                    '</td>' +
                    '</tr>');
            }
            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });

            var stateId = $("#create_question_state").val();
            getLicences(stateId, "#license_type_"+rowId);
        }






        $("#licenceValidation-error").remove();
    })

    // // Add new License
    // $("#btnAddLicense").click(function(){
    //     var rowId = $('#addLicenceTable tbody tr:last > td:eq(0)').text();
    //
    //     rowId++;
    //     if($('#select-license').prop( "checked")){
    //         if(rowId == 1){
    //             $('#addLicenceTable tbody').append('<tr>' +
    //                 '<td style="display: none">'+rowId+'</td>' +
    //                 '<td class="questionTableStateTdInput hidden">'+
    //                 '<select class="form-control states_fedaral"  id="states_fedaral_'+rowId+'">'+
    //                 '</select>'+
    //                 '</td>'+
    //                 '<td class="questionTableTdInput">' +
    //                 '   <select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
    //                 '   </select>' +
    //                 '</td>' +
    //                 '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
    //                 '</td>' +
    //
    //                 '</tr>');
    //         }
    //         else{
    //             $('#addLicenceTable tbody tr:last').after('<tr>' +
    //                 '<td style="display: none">'+rowId+'</td>' +
    //                 '<td class="questionTableStateTdInput hidden">'+
    //                 '<select class="form-control states_fedaral"  id="states_fedaral_'+rowId+'">'+
    //                 '</select>'+
    //                 '</td>'+
    //                 '<td class="questionTableTdInput">' +
    //                 '<select class="license_data marginButtom1 col-sm-11 form-control" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
    //                 '   </select>' +
    //
    //
    //                 '</td>' +
    //                 '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
    //                 '</td>' +
    //                 '</tr>');
    //         }
    //         var countryId=$('#question_country').val();
    //         $("#license_type_" + rowId).select2({
    //             placeholder: "Select License Types(s)"
    //         });
    //         getStatesWithFedaral(countryId,"#states_fedaral_"+rowId)
    //     }else {
    //         if(rowId == 1){
    //             $('#addLicenceTable tbody').append('<tr>' +
    //                 '<td style="display: none">'+rowId+'</td>' +
    //                 '<td class="questionTableTdInput">' +
    //                 '   <select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
    //                 '   </select>' +
    //                 '</td>' +
    //                 '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
    //                 '</td>' +
    //
    //                 '</tr>');
    //         }
    //         else{
    //             $('#addLicenceTable tbody tr:last').after('<tr>' +
    //                 '<td style="display: none">'+rowId+'</td>' +
    //                 '<td class="questionTableTdInput">' +
    //                 '<select class="license_data marginButtom1 col-sm-11 form-control" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
    //                 '   </select>' +
    //
    //
    //                 '</td>' +
    //                 '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>' +
    //                 '</td>' +
    //                 '</tr>');
    //         }
    //         $("#license_type_" + rowId).select2({
    //             placeholder: "Select License Types(s)"
    //         });
    //
    //         var stateId = $("#create_question_state").val();
    //         getLicences(stateId, "#license_type_"+rowId);
    //     }
    //
    //
    //
    //
    //
    //
    //     $("#licenceValidation-error").remove();
    //
    // });

    // Delete License Types row
    $(document).on('click', ".btn_delete_licence_type", function() {
        if($(this).attr('item-number') != "1"){
            var tr = $(this).closest('tr');
            //tr.css("background-color","#FF3700");
            tr.fadeOut(400, function(){
                tr.remove();
            });
        }
        return false;


    });
    //law change function
    $('#question_law').change(function () {
        var law=$(this).val();
        $('.questionTableStateTdInput').addClass('hidden');
        $('.states_fedaral').attr("disabled",true);
        $('#select-license').attr('checked',false);
        showHideSelectStateCity(law);
        addNewLicenseRow(law);
    });


    //select licenses on state change for fedaral

    // $('.states_fedaral').change(function () {
    //     var stateFedaral=$(this).val();
    //     var stateFedaralId=$(this).attr('id');
    //     console.log('dddd')
    //     $("#addLicenceTable").find("select[custom-attr='licence_type']").each (function() {
    //         var id = $(this).attr('id');
    //         getLicences(stateFedaral, "#"+id);
    //     });
    // });
    //select license change function
    $('#select-license').change(function () {
        if ( $(this).prop( "checked" )){
            var countryId=$('#question_country').val();
            $('.license_data').attr("disabled",false);
            $('.states_fedaral').attr("disabled",false);
            $('#license_type_1').empty();
            $("#addLicenceTable").find("select[custom-attr='state_type']").each (function() {
                var id = $(this).attr('id');
                getStatesWithFedaral(countryId,"#"+id);
            });

            if($('.license-section').hasClass('hidden')){
                $('.license-section').removeClass('hidden');
            }
        }else {
            $('.license-section').addClass('hidden');
            $('.license_data').attr("disabled",true);
            $('.questionTableStateTdInput').addClass('hidden');
            $('.states_fedaral').attr("disabled",true);
        }


    })

    // country change function
    $("#question_country").change(function(){
        var countryId = $(this).val();
        getStatesBasedOnCountry(countryId, false);

    });

    // state change function
    $("#create_question_state").change(function(){
        var stateId = $(this).val();

        jQuery.ajax({
            type: 'GET',
            url: "/question/getCities",
            async: false,
            data: { stateId: stateId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {

                $("#create_question_cities").empty();
                $('#create_question_cities').select2('val', '');


                if(result.data != null){
                    $(".createQuestionSelectAllCity").show();
                    $("#chkCreateQuestionSelectAllCity").attr('checked', false);
                    $.each(result.data.master_city, function(incex, value){
                        $("#create_question_cities").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                else{
                    $(".createQuestionSelectAllCity").hide();
                }
                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });

        // change licence based on state changes
        $("#addLicenceTable").find("select[custom-attr='licence_type']").each (function() {
            var id = $(this).attr('id');
            getLicences(stateId, "#"+id);
        });

    });


    // get licences
    function getLicences(stateId, licenceId){
        counry_id = $('#question_country').val();

        jQuery.ajax({
            type: 'GET',
            url: "/question/getLicences",
            async: false,
            data: { stateId: stateId, counry_id : counry_id},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                $(licenceId).empty();
                $(licenceId).select2('val', '');


                if(result.data != null){
                    $.each(result.data.master_license, function(incex, value){
                        $(licenceId).append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                $(".splash").hide();
            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }
    //get states based on country
    function getStatesBasedOnCountry(countryId, onload){
        jQuery.ajax({
            type: 'GET',
            url: "/question/getStates",
            async: false,
            data: { countryId: countryId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {


                $("#create_question_state").empty();
                $("#create_question_state").append('<option value="">Select State</option>');

                $("#addLicenceTableBody").find("tr:gt(0)").remove();
                $(".states_fedaral").empty();
                $(".states_fedaral").append('<option value="">Select State</option>');
                $(".createQuestionSelectAllCity").hide();

                if(result.data != null){
                    $.each(result.data.master_states, function(incex, value){
                        $("#create_question_state").append('<option value="'+value.id+'"> '+value.name+'</option>');
                        $(".states_fedaral").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }

                if(!onload){
                    $("#create_question_cities").empty();
                    $('#create_question_cities').select2('val', '');

                    // change licence based on state changes
                    $("#addLicenceTable").find("select[custom-attr='licence_type']").each (function() {
                        var id = $(this).attr('id');
                        getLicences(0, "#"+id);
                    });
                }

                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }

    function getLicensesFromCountry(countryId,licenceId) {
        jQuery.ajax({
            type: 'GET',
            url: "/question/getLicenseFromCountry",
            async: false,
            data: { countryId: countryId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {

                $(licenceId).empty();
                $(licenceId).select2('val', '');


                if(result.data != null){
                    $.each(result.data.master_license, function(incex, value){
                        $(licenceId).append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }

    function addNewLicenseRow(law) {
        $('#addLicenceTable tbody').html('');
        var rowId=1;
        if(law==1){

            if(rowId == 1){
                $('#addLicenceTable tbody').append('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableStateTdInput state-list-position-top hidden">'+
                    '<div class="row">'+
                    '<div class="col-xs-12">'+
                    '<label>State:</label>'+
                    '<select class="form-control states_fedaral"  id="states_fedaral_'+rowId+'">'+
                    '</select>'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td class="questionTableTdInput">' +
                    '<div class="col-xs-12">'+
                    '<label>License Type:</label>'+
                    '<select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '</select>' +
                    '</div>'+
                    '</td>' +
                    '<td class="questionTableTd" valign="bottom"><div class="col-xs-12 m-b-xs"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button><button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnAddLicense" type="button"><i class="fa fa-plus"></i></button></div></td>' +
                    '</td>' +
                    '</tr>');
            }
            var countryId=$('#question_country').val();
            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });
            getStatesWithFedaral(countryId,"#states_fedaral_"+rowId);
        }else {
            if(rowId == 1){
                $('#addLicenceTable tbody').append('<tr>' +
                    '<td style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput">' +
                    '   <select class="license_data marginButtom1 col-sm-11 form-control" style="width: auto !important" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '   </select>' +
                    '</td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button><button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnAddLicense" type="button"><i class="fa fa-plus"></i></button></td>' +
                    '</td>' +

                    '</tr>');
            }
            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });

            var stateId = $("#create_question_state").val();
            getLicences(stateId, "#license_type_"+rowId);
        }
    }

    //get states based on Fedaral
    function getStatesWithFedaral(countryId,licenseId){
        jQuery.ajax({
            type: 'GET',
            url: "/question/getStates",
            async: false,
            data: { countryId: countryId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                // $("#license_type_1").empty();
                $(licenseId).empty();
                $(licenseId).append('<option value="">Select State</option>');



                if(result.data != null){
                    $.each(result.data.master_states, function(incex, value){
                        $(licenseId).append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }
                if($('.questionTableStateTdInput').hasClass('hidden')){
                    $('.questionTableStateTdInput').removeClass('hidden');
                }


                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }

});


    $(".create_qu_anser_click").click(function(event){
        event.stopPropagation();
    });

    $(".answerValue").click(function(event){
        event.stopPropagation();
    });

    // Define Selectors
    $("#auditTypes").select2({
        placeholder: "Select Audit Type(s)"
    });
    $("#create_question_cities").select2({
        placeholder: "Select Citi(es)"
    });

    $("#license_type_1").select2({
        placeholder: "Select License Types(s)"
    });

    $("#create_keywords").select2({
        placeholder: "Select Keywords",
        tags: true
    });



    $(".classification_option").select2();
    $(".classification_option_not_req").select2();

    $("#question_answer_id_0").accordion({collapsible : true, active : 'none', heightStyle: "content"});


    // create parent questrion click function
    $(".create_parent_question").click(function(){
        var isFormValid = $("#create_question_from").valid();
        var isItemValid = validateActionItems("addActionItemTableBody");
        var isLicenceValid = validateLicences();
        var reqClassifiction = validateReqClassifications();
        var answers = findQuestionAnswers("0");
        var isValidLicenceCombination = validateLicenceCombination();

        if(isValidLicenceCombination){
            swal({
                title: "Error!",
                text: "You can't use the same license combination."
            });
        }
        else{
            if(isFormValid && isItemValid && isLicenceValid && reqClassifiction){
                if(answers.length >= 2){
                    var checkCombinations = checkAnswerCombination("0");
                    var questionType = $(this).attr('save-type');
                    var isDraft = 1;
                    var visibility = $("input[name='visibility']:checked").val();
                    (questionType == "publish") ? isDraft = 0 : isDraft = 1;

                    if(!checkCombinations){

                        if(isDraft == 0) {
                            var published_date = $('#publishDate').val();
                            if(published_date =='' ) {
                                checkDraftAndSaveQuestion(isDraft, visibility);

                            } else {
                                swal({
                                    title: "Error!",
                                    text: "Publish date is invalid, please remove entered published date to continue.",
                                    type: "error"
                                });
                            }
                        } else {
                            checkDraftAndSaveQuestion(isDraft, visibility);
                        }
                    }
                    else {
                        swal({
                                title: "Please confirm",
                                text: "You have selected the same value for more than one answer.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#F8BB86",
                                confirmButtonText: "Confirm!",
                                cancelButtonText: "Cancel!",
                                closeOnConfirm: true,
                                closeOnCancel: true },
                            function (isConfirm) {
                                if (isConfirm) {
                                    if(isDraft == 0) {
                                        var published_date = $('#publishDate').val();

                                        if(published_date =='') {
                                            checkDraftAndSaveQuestion(isDraft, visibility);

                                        } else {
                                            swal({
                                                title: "Error!",
                                                text: "Publish date is invalid, please remove entered published date to continue.",
                                                type: "error"
                                            });
                                        }
                                    } else {
                                        checkDraftAndSaveQuestion(isDraft, visibility);
                                    }
                                }
                            });
                    }

                }
                else{
                    swal({
                        title: "Error!",
                        text: "You should select at least two answers."
                    });
                }
            }
        }

    });


    function checkDraftAndSaveQuestion(isDraft, visibility) {
        // when inactive question is going to publish
        if(isDraft == 0 && visibility == 0){
            swal({
                    title: "Are you sure?",
                    text: "You want to publish inactive question.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#F8BB86",
                    confirmButtonText: "Yes, publish it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: false },
                function (isConfirm) {
                    if (isConfirm) {
                        SaveCreateMasterQuestion(isDraft);
                    }
                    else{
                        swal("Cancelled", "Question didn't published ", "error");

                    }
                });
        }
        else{
            SaveCreateMasterQuestion(isDraft);
        }
    }

    // validate licence combinations
    function validateLicenceCombination(){
        var isCombinationExists = false;
        var license = [];
        $("#addLicenceTable").find('select.license_data').each (function() {
            var licenceAttr = $(this).attr('id');
            var licenceVal = $("#" + licenceAttr).val();
            if(licenceVal != null){
                license.push(licenceVal);
            }
        });

        $.each( license, function( key, value ) {
            $.each( license, function( key2, value2 ) {
                if(key != key2){
                    value = value.sort();
                    value2 = value2.sort();

                    var is_same = value.length == value2.length && value.every(function(element, index) {
                            return element === value2[index];
                        });

                    if(is_same){
                        isCombinationExists = true;
                        return false;
                    }
                }
            });
        });

        return isCombinationExists;
    }

    // check answer combinations
    function checkAnswerCombination(id){
        var answers = [];
        var isExists = false;
        $("#question_answer_id_"+ id).find('.create_qu_anser_click').each (function() {

            var val = $(this).val();
            var isChecked = $(this).is(":checked");

            if(isChecked){
                answers.push($("#answerValue_"+val+"_"+id).val());
            }
        });

        $.each(answers, function($key, value){
            $.each(answers, function($key2, inValue){
                if($key != $key2){
                    if(value == inValue){
                        isExists = true;
                    }
                }
            });
        });
        return isExists;
    }

    // Validate actionItems
    function validateActionItems(tableId){
        var isValid = true;
        $("#" +tableId).find('input').each (function() {
            var attr = $(this).attr('id');
            var itemVal = $("#" + attr).val();

            if(itemVal == ""){
                $("#" + attr).addClass("error");
                isValid = false;
            }
        });
        return isValid;
    }

    // Validate licences
    function validateLicences(){
        var lawType=$('#question_law').val();
        var isValid = true;
        if(lawType!=1 || (lawType==1 && $('#select-license').prop( "checked"))){
            $("#addLicenceTable").find('select.license_data').each (function() {
                var licenceAttr = $(this).attr('id');
                var licenceVal = $("#" + licenceAttr).val();

                if(licenceVal == null){
                    $( "#" + licenceAttr).next().css( "border", "solid 1px #ff0000" );
                    isValid = false;
                }
                else {
                    $( "#" + licenceAttr).next().css( "border", "none" );
                    //$($("#" + licenceAttr).select2("container")).removeClass("error");
                }

            });
            return isValid;
        }else {
            return isValid;

        }

    }

    // Validate Required Classifications
    function validateReqClassifications(){
        var isValid = true;
        $("#reqClassifictionTable").find('select').each (function() {
            var licenceAttr = $(this).attr('id');
            var licenceVal = $("#" + licenceAttr).val();

            if(licenceVal == null){
                $(this).next().addClass('question-error');
                isValid = false;
            }
            else {
                $(this).next().removeClass('question-error');
                $(this).removeClass('question-error');
            }


            if(licenceVal == ""){
                $(this).addClass('question-error');
                isValid = false;
            }

        });
        return isValid;
    }

    // find action items
    function findActionItems(formId){
        var items = [];
        $("#addActionItemTable", "#"+formId).find('input').each (function() {
            var attr = $(this).attr('id');
            var itemVal = $("#" + attr).val();
            if(itemVal !=  ""){
                items.push(itemVal);
            }

        });
        var jsonString = JSON.stringify(items);
        return jsonString;
    }


    //find license
    function findLicences(){
        var license = [];
        $("#addLicenceTable").find('select.license_data').each (function() {
            var licenceAttr = $(this).attr('id');
            var licenceVal = $("#" + licenceAttr).val();
            if(licenceVal != null){
                license.push(licenceVal);
            }
        });
        return license;
    }

    //get fedaral states

    function getFedaralStates() {
        var state=[];
        $('.states_fedaral').each(function(i,obj){
            if(obj.value !=null){
                state.push(obj.value)
            }
        });
        return state;
    }

    //find citations
    function findCitations()
    {
        var citations = [];
        $(".citation").each (function() {
            var citation_name = $(this).val();
            var link = $(this).closest('div').parent('div').parent('div').find('.link').val();
            var des = $(this).closest('div').parent('div').parent('div').find('.des').val();

            if(citation_name != null){
                var tmp = {"citation":citation_name, "link":link,"description":des};
                citations.push(tmp);
            }
        });
        return citations;
    }

    $(document).on('click', '.delete', function(){
        var count = 0;
        $(".delete").each (function() {
             count++;
        });
        if(count > 1)
        {
            $(this).closest('div').parent('div').remove();
        }
    });

    //find required classifictions
    function findReqClassifictions(){
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

    //find non required classifictions
    function findNonReqClassifictions(){
        var classifications = [];
        $("#nonReqClassifictionTable").find('select').each (function() {
            var attr = $(this).attr('id');
            var val = $("#" + attr).val();
            var classificationId = $(this).attr('classification-id');
            if(val != null && val != ""){
                classifications.push({classificationId: classificationId, value: val});
            }
        });
        return classifications;
    }

    // find answers
    function findQuestionAnswers(id){

        var answers = [];
        $("#question_answer_id_"+ id).find('.create_qu_anser_click').each (function() {

            var val = $(this).val();
            var isChecked = $(this).is(":checked");

            if(isChecked){
                var data = {answerId: val, answerOptionId: $("#answerValue_"+val+"_"+id).val()}
                answers.push(data);
            }
        });
        return answers;
    }

    //Save create master question
    function SaveCreateMasterQuestion(isDraft){
        var visibility = $("input[name='visibility']:checked").val();
        var mandatory = $("input[name='mandatory']:checked").val();
        var law=$('#question_law').val();
        var question = $("#question").val();
        var explanation = $("#explanation").val();
        var keywords = $("#create_keywords").val();
        var mainCategory = $("#mainCategory").val();
        var classificationId = $("#mainCategory").attr('classification-id');
        var classificationParentID = $("#mainCategory").find(':selected').attr('parent_id');
        var is_child = $("#mainCategory").find(':selected').attr('is_child');

        var auditTypes = $("#auditTypes").val();
        var country = $("#question_country").val();
        var publish_date = $("#publishDate").val();

        var allTag="ALL";
        var state,cities,license;
        if(law==1 && $('#select-license').prop( "checked") ){
            state = getFedaralStates();
            cities = allTag;

        }else if(law==1 && !$('#select-license').prop( "checked") ){
            state = allTag;
            cities = allTag;

        }else if(law==2){
            state = $("#create_question_state").val();
            cities = allTag;

        }else if(law==3 && $('#chkCreateQuestionSelectAllCity').prop("checked")){
            state = $("#create_question_state").val();
            cities = allTag;
        }else {
            state = $("#create_question_state").val();
            cities = $("#create_question_cities").val();
        }

        var actionItems = findActionItems("create_question_from");

        if(law==1 && !$('#select-license').prop( "checked") ){
            license = [];
            license.push(allTag);
        }else{
            license = findLicences();

        }
        var reqClassifictions = findReqClassifictions();
        var nonReqClassifications = findNonReqClassifictions();
        var answers = findQuestionAnswers("0");
        var citations = findCitations();

        var data = {visibility: visibility, mandatory: mandatory, law:law,question: question, explanation: explanation, keywords: keywords, mainCategory: mainCategory, classificationId: classificationId,classificationParentID:classificationParentID,is_child:is_child, auditTypes: auditTypes, country: country, state: state, cities: cities,  actionItems: actionItems, license: license, reqClassifictions: reqClassifictions, nonReqClassifications: nonReqClassifications, answers: answers, isDraft: isDraft, citations:citations, publishDate:publish_date};
        var is_create_new_checked = $("#create_new").is(':checked');
        $.ajax({
            url: "/question/createQuestion",
            type: 'POST',
            dataType: 'json',
            data : data,
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                var msg = result.message;
                var msg_type = 'success';
                setTimeout(function(){
                    msgAlert(msg, msg_type);
                    $(".splash").hide();
                    if(is_create_new_checked)
                    {
                        window.location.assign("/question/create");
                    }
                    else
                    {
                        window.location.assign("/question/editQuestion/" +result.data);
                    }
                    return false;
                }, 4000);
                //setTimeout( window.location.assign("/question/editQuestion/" +result.data), 3000);
            },
            error: function(xhr){
                $(".splash").hide();
            }

        });
    }




// City select all check box checked - unchecked function
function getCities(selectedCities){
    if(selectedCities != null){
        var stateId = $("#create_question_state").val();

        jQuery.ajax({
            type: 'GET',
            url: "/question/getCities",
            async: false,
            data: { stateId: stateId},
            data: { stateId: stateId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {

                if(result.data != null && result.data.length > 0)
                {
                    var allCities = result.data.master_city.length;
                }

                if(selectedCities.length == allCities){
                    //$("#chkCreateQuestionSelectAllCity").prop('checked', true);
                }
                else{
                    //$("#chkCreateQuestionSelectAllCity").prop('checked', false);
                }

                $(".splash").hide();
            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }
}



function showHideSelectStateCity(law){
    var states=$('#create_question_state');
    var cities=$('#create_question_cities');
    var license=$('.license_data');
    if(law==1){
        $('#create_question_state,#create_question_cities,.license_data').attr("disabled",true);
        if($('.apply-all-section').hasClass('hidden')){
            $('.apply-all-section').removeClass('hidden');
        }
        $('.state-section,.city-section,.license-section').addClass('hidden');
        $('#select-license').prop( "checked",false);

    }else if(law==2){

        $('#create_question_state,.license_data').attr("disabled",false);
        $('#create_question_cities').attr("disabled",true);
        if($('.state-section').hasClass('hidden')){
            $('.state-section').removeClass('hidden');
        }
        if($('.license-section').hasClass('hidden')){
            $('.license-section').removeClass('hidden');
        }
        $('.city-section,.apply-all-section').addClass('hidden');
        if($('.createQuestionSelectAllCity').hasClass('hidden')){
            $('.createQuestionSelectAllCity').removeClass('hidden');
        }
    }else if(law==3){
        $('#create_question_state,#create_question_cities,.license_data').attr("disabled",false);
        if($('.state-section').hasClass('hidden')){
            $('.state-section').removeClass('hidden');
        }
        if($('.city-section').hasClass('hidden')){
            $('.city-section').removeClass('hidden');
        }
        // hide all city checkbox
        $('.createQuestionSelectAllCity').addClass('hidden');

        if($('.license-section').hasClass('hidden')){
            $('.license-section').removeClass('hidden');
        }
        $('.apply-all-section').addClass('hidden');
    }
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
