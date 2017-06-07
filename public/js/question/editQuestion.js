$(function(){

    var supperQuestionId = $("#supperQuestionId").val();

    $("#accordion_0_0_"+supperQuestionId).accordion({collapsible: true, active : 'none', heightStyle: "content"});

    // show hide select all city option
    if($("#question_cities_edit").val() != "" || $("#question_cities_edit").val() != "0"){
        $(".editQuestionSelectAllCity").show();
    }
    else{
        $(".editQuestionSelectAllCity").hide();
    }

    // City change function
    $("#question_cities_edit").change(function(){
        getCities($("#question_cities_edit").val());
    });


    getCities($("#question_cities_edit").val());

    $("#chkEditQuestionSelectAllCity").click(function(){

        if($("#chkEditQuestionSelectAllCity").is(':checked') ){
            $("#question_cities_edit").empty();
            $('#question_cities_edit').select2('val', '');
            $('.city-wrap').addClass('hidden');
            $('#question_cities_edit').attr('disabled',true);

            if($('#all_cities').hasClass('hidden')){
                $('#all_cities').removeClass('hidden');

            }

        }
        else{
            if($('.city-wrap').hasClass('hidden')){
                $('.city-wrap').removeClass('hidden');
            }
            $('#question_cities_edit').attr('disabled',false);
            $('#all_cities').addClass('hidden');
        }

    });



    $(".create_qu_anser_click").click(function(event){
        event.stopPropagation();
    });

    $(".answerValue").click(function(event){
        event.stopPropagation();
    });

    $("#create_new_version").click(function(){
        $('#questionVersionModel').modal('show');
    });

    $("#create_new_version_model").click(function(){

        var isValid = $("#versionCommentForm").valid();
        var questionId = $("#supperQuestionId").val();
        var comment = $("#versionComment").val();

        if(isValid){
            createNewVersion(questionId, comment);
        }

    })

    //law change function
    $('#question_law_edit').change(function () {
        var law=$(this).val();
        showHideSelectStateCity(law)
    });

    showHideSelectStateCity($('#question_law_edit').val());

    $(document).on('change', "#question_law_edit-disable", function(event) {
        // var answerId = $(this).parents().eq(3).attr('answer-id');
        // var parentQuestionId = $(this).parents().eq(3).attr('answer-question-id');
        // var questionId = $(this).parents().eq(3).attr('question-id');
        // var formId = "form_"+answerId+"_"+parentQuestionId+"_"+questionId;
        // var idx = answerId+"_"+parentQuestionId+"_"+questionId;
        // var element = $(this).parents().eq(3);
        var currentFormId = $(this).parents("form").attr('id');
        console.log(currentFormId);
        var law = $("#question_law_edit-disable", '#' + currentFormId).val();
        $("#question_law_edit", '#' + currentFormId).val(law);// override default value

        if(law==2){

            $('#' + currentFormId).find('.city-section-child').addClass("hidden");
            // $('#' + currentFormId).find('.license-section-child').addClass('hidden');

        }else if(law==3){

            if($('#' + currentFormId).find('.city-section-child').hasClass('hidden')){
                $('#' + currentFormId).find('.city-section-child').removeClass('hidden');
            }
            if($('#' + currentFormId).find('.license-section-child').hasClass('hidden')){
                $('#' + currentFormId).find('.license-section-child').removeClass('hidden');
            }

        }

    });

    $(document).on('click', "#chkEditQuestionSelectAllCity" , function(event) {
        var currentFormId = $(this).parents("form").attr('id');
        var law = $("#question_law_edit-disable", '#' + currentFormId).val();
        if (law == 2) {

        }else if (law == 3) {
            if($("#chkEditQuestionSelectAllCity", "#"+currentFormId).is(':checked') ){
                    // $("#question_cities_edit","#"+currentFormId).empty();
                    // $('#question_cities_edit',"#"+currentFormId).select2('val', '');
                    $('.city-wrap', "#"+currentFormId).addClass('hidden');
                    $('#question_cities_edit', "#"+currentFormId).attr('disabled',true);

                    if($('#all_cities', "#"+currentFormId).hasClass('hidden')){
                        $('#all_cities', "#"+currentFormId).removeClass('hidden');
                    }
            }else {
                if($('.city-wrap', "#"+currentFormId).hasClass('hidden')){
                    $('.city-wrap', "#"+currentFormId).removeClass('hidden');
                }
                $('#question_cities_edit',"#"+currentFormId).attr('disabled',false);
                $('#all_cities',"#"+currentFormId).addClass('hidden');
            }
        }
    });


    // Accordion open click function
    $(document).on('click', ".accordion_open", function(event) {

        var answerId = $(this).attr('answer-id');
        var questionIndexPrefix = $(this).attr('question-index');
        var viewOnlyFlag = $(this).attr('view-only');

        var id = $(this).attr('id');
        var ischecked = $("#"+id+ " :input").is(':checked');

        if(!ischecked){
            swal({
                title: "Error!",
                text: "You must tick the answer first to add questions."
            });

            var accordId = $(this).parent().attr('id');
            $("#"+accordId).accordion({active: false });
        }
        else if(answerId == 0){
            swal({
                title: "Error!",
                text: "You must save the question before you can add sub-questions to the answers."
            });

            var accordId = $(this).parent().attr('id');
            $("#"+accordId).accordion({active: false });

        }
        else{

            var subQuestionLevels = $(this).parents('.child_question_data').length + 1;

            jQuery.ajax({
                type: 'GET',
                url: "/configuration/getSubQuestionLevel",
                async: false,
                dataType: "json",
                beforeSend: function () {
                    $(".splash").show();
                },
                success: function (result) {
                    if(subQuestionLevels > result.value){
                        swal({
                            title: "Error!",
                            text: "Sub Question Count Exceed"
                        });

                        var accordId = $(this).parent().attr('id');
                        //console.log(accordId);
                        $("#"+accordId).accordion({active: false });
                    }
                    else{
                        // accordionOpenClick(answerId, false);
                        accordionOpenClickForAllChildQuestion(answerId, questionIndexPrefix, viewOnlyFlag);
                    }
                    $(".splash").hide();
                },
                error: function (result) {
                    $(".splash").hide();
                }
            });

        }
    });

    //Append new action item row
    $(document).on('click', ".btnAddNewActionItem", function(event){
        var tableId = $(this).attr('table-id');
        AddNewActionItem(tableId);
    });

    //delete Question Answer
    $(document).on('change', ".create_qu_anser_click", function(event){
        var accordioinId = $(this).parents().eq(1).attr('id');
        var id = $(this).attr('id');
        var dom = $(this);
        var answerId = $(this).attr('answer-id');
        var ischecked= $(this).is(':checked');

        if(!ischecked && answerId != 0){
            swal({
                    title: "Are you sure?",
                    text: "You want to delete answer and it's child questions.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: false },
                function (isConfirm) {
                    if (isConfirm) {
                        deleteQuestionAnswer(answerId, accordioinId);
                    }
                    else{
                        $(dom).prop("checked", true);
                        swal("Cancelled", "Answer remains as it is ", "error");

                    }
                });
        }

    });


    //Save child question click function
    $(document).on('click', ".save_child_question", function(event) {

        var answerId = $(this).parents().eq(3).attr('answer-id');
        var parentQuestionId = $(this).parents().eq(3).attr('answer-question-id');
        var questionId = $(this).parents().eq(3).attr('question-id');
        var formId = "form_"+answerId+"_"+parentQuestionId+"_"+questionId;
        var idx = answerId+"_"+parentQuestionId+"_"+questionId;
        var element = $(this).parents().eq(3);

        var audit_types_main = $("#auditTypes_edit").val();
        var country_main = $("#question_country_edit").val();
        var state_main = $("#question_state_edit").val();
        var cities_main = $("#question_cities_edit").val();
        // var law = $("#sub_question_law", "#"+formId).val();
        var law = $("#question_law_edit", "#"+formId).val();


        /*var req_classification_main = $(".req_classification").val();
        var req_classification = $(".req_classification", "#"+formId).val();

        if(req_classification_main != req_classification)
        {
            alert("Requied Classification does not match with parent's required classification.");
            return false;
        }*/

        var errors_exist = false;
        $(".req_classification", "#"+formId).each (function() {
            var multi_select = $(this).hasClass( "multi_select" );

            if(multi_select)
            {
                var val = $(this).val()!= '' && $(this).val()!= null ? $(this).val().toString() : '';
                child_ids = val.split(',');

                var classification_id = $(this).attr('classification-id');
                var req_classification_main = $("#req_classification_" + classification_id).val();
                req_classification_main = req_classification_main != "" && req_classification_main != null ? req_classification_main.toString() : '';
                parent_ids = req_classification_main.split(',');

                for(var x = 0; x < child_ids.length; x++)
                {
                    if(parent_ids.length > 0 && jQuery.inArray(child_ids[x], parent_ids) == -1)
                    {
                        //alert("Required other Classifications do not match with parent's required other classifications.");
                        swal({
                            title: "Error!",
                            text: "Required other Classifications do not match with parent's required other classifications."
                        });
                        errors_exist = true;
                        return false;
                    }
                }

                for(var x = 0; x < parent_ids.length; x++)
                {
                    if(child_ids.length > 0 && jQuery.inArray(parent_ids[x], child_ids) == -1)
                    {
                        //alert("Required other Classifications do not match with parent's required other classifications.");
                        swal({
                            title: "Error!",
                            text: "Required other Classifications do not match with parent's required other classifications."
                        });
                        errors_exist = true;
                        return false;
                    }
                }
            }
            else
            {
                var val = $(this).val();
                var classification_id = $(this).attr('classification-id');
                var req_classification_main = $("#req_classification_" + classification_id).val();

                if(req_classification_main != val)
                {
                    //alert("Required other Classifications do not match with parent's required other classifications.");
                    swal({
                        title: "Error!",
                        text: "Required other Classifications do not match with parent's required other classifications."
                    });
                    errors_exist = true;
                    return false;
                }
            }
        });


        $(".not_req_classification", "#"+formId).each (function() {
            var multi_select = $(this).hasClass( "multi_select" );

            if(multi_select)
            {
                var val = $(this).val()!= '' && $(this).val()!= null ? $(this).val().toString() : '';
                child_ids = val.split(',');

                var classification_id = $(this).attr('classification-id');
                var not_req_classification_main = $(".not_req_classification_" + classification_id).val();
                not_req_classification_main = not_req_classification_main != "" && not_req_classification_main != null ? not_req_classification_main.toString() : '';
                parent_ids = not_req_classification_main.split(',');

                for(var x = 0; x < child_ids.length; x++)
                {
                    if(parent_ids.length > 0 && jQuery.inArray(child_ids[x], parent_ids) == -1)
                    {
                        //alert("Other Classifications do not match with parent's other classifications.");
                        swal({
                            title: "Error!",
                            text: "Other Classifications do not match with parent's other classifications."
                        });
                        errors_exist = true;
                        return false;
                    }
                }

                for(var x = 0; x < parent_ids.length; x++)
                {
                    if(child_ids.length > 0 && jQuery.inArray(parent_ids[x], child_ids) == -1)
                    {
                        //alert("Other Classifications do not match with parent's other classifications.");
                        swal({
                            title: "Error!",
                            text: "Other Classifications do not match with parent's other classifications."
                        });
                        errors_exist = true;
                        return false;
                    }
                }
            }
            else
            {
                var val = $(this).val();
                var classification_id = $(this).attr('classification-id');
                var not_req_classification_main = $(".not_req_classification_" + classification_id).val();

                if(val != '' && not_req_classification_main != val)
                {
                    //alert("Other Classifications do not match with parent's other classifications.");
                    swal({
                        title: "Error!",
                        text: "Other Classifications do not match with parent's other classifications."
                    });
                    errors_exist = true;
                    return false;
                }
            }
        });
        if(errors_exist)
        {
            return false;
        }

        //var license_type_main = $("#license_type_1").val();
        var license_type_main = [];
        $(".license_data_main").each (function() {
            var val = $(this).val();

            $.each( val, function( key, value ) {

                if(value != null && value != ""){
                    license_type_main.push(value);
                }
            });
        });

        var city_idsH = $("#city_idsH").val();
        if (undefined != cities_main || cities_main != null) {
            for(var i = 0; i < cities_main.length; i++)
            {
                if(city_idsH.indexOf(cities_main[i]) == -1)
                {
                    //alert("Please save parent first");
                    swal({
                        title: "Error!",
                        text: "Please save parent first"
                    });
                    return false;
                }
            }
        }

        var licen_idsH = $("#licen_idsH").val();
        for(var i = 0; i < license_type_main.length; i++)
        {
            if(licen_idsH.indexOf(license_type_main[i]) == -1)
            {
                //alert("Please save parent first");
                swal({
                    title: "Error!",
                    text: "Please save parent first"
                });
                return false;
            }
        }

        var audit_types = $("#auditTypes_edit", "#"+formId).val();
        var country = $("#question_country_edit", "#"+formId).val();
        var state = $("#question_state_edit", "#"+formId).val();
        var cities = $("#question_cities_edit", "#"+formId).val();

        //var license_type = $("#license_type_1", "#"+formId).val();
        var license_type = [];
        $(".license_data", "#"+formId).each (function() {
            var val = $(this).val();

            $.each( val, function( key, value ) {

                if(value != null && value != ""){
                    license_type.push(value);
                }
            });
        });

        //console.log(license_type);
        //alert(license_type);

        if(audit_types != null)
        {
            for (var i = 0; i < audit_types.length; ++i) {
                //if(jQuery.inArray(audit_types[i], audit_types_main) == -1)
                if(audit_types_main.indexOf(audit_types[i]) == -1)
                {
                    //alert("Audit Type child list is not compatible with Audit Type parent list");
                    swal({
                        title: "Error!",
                        text: "Audit Type child list is not compatible with Audit Type parent list"
                    });
                    return false;
                }
            }
        }

        // Get the current form element ID
        var currentFormId = $(this).parents("form").attr('id');
        // Get the immediate parent form element ID
        var immediateParentForm = $("#"+currentFormId).parents('form').attr('id');
        // Get the immediate parent cities values
        var immediateParentCity = $("#question_cities_edit", "#" + immediateParentForm).val();
        // var lawnew = $("#question_law_edit", "#" + formId).val();
        // console.log(lawnew);
        console.log(cities_main);
        // return;
        // when law type is `2` (State) needs to check with immediate parent cities
        if (law == 2 && cities_main == null) {
            console.log("low type condition!");
            if (immediateParentForm == 'edit_question_from') {
                // assuming a parent question!
                cities_main = null;
            }else {
                // assign immediate parent question cities to cities_main so we can compare!
                cities_main = immediateParentCity;
            }
        }

        if(cities != null && cities_main != null)
        {
            for (var i = 0; i < cities.length; ++i) {
                if(jQuery.inArray(cities[i], cities_main) == -1)
                {
                    //alert("Cities child list is not compatible with Cities parent list");
                    swal({
                        title: "Error!",
                        text: "Cities child list is not compatible with Cities parent list"
                    });
                    return false;
                }
            }
        }

        if(license_type != null)
        {
            for (var i = 0; i < license_type.length; ++i) {
                if(jQuery.inArray(license_type[i], license_type_main) == -1)
                {
                    //alert("License child list is not compatible with License parent list");
                    swal({
                        title: "Error!",
                        text: "License child list is not compatible with License parent list"
                    });
                    return false;
                }
            }
        }

        /*if(license_type != null)
        {
            for (var i = 0; i < license_type.length; i++) {
                for (var j = 0; j < license_type[i].val.length; j++) {

                    for (var x = 0; x < license_type_main[i].length; x++) {
                        if(license_type_main[i][x] == )
                        {

                        }
                    }

                    if(jQuery.inArray(license_type[i].val[j], license_type_main[i]) == -1)
                    {
                        console.log(license_type_main[i]);
                    }


                }
                console.log('br');
            }
        }*/


        if(country_main != null && country != null && country_main != country)
        {
            //alert("Country in child list is not compatible with Country in parent list");
            swal({
                title: "Error!",
                text: "Country in child list is not compatible with Country in parent list"
            });
            return false;
        }

        if(state_main != null && state != null && state_main != state)
        {
            //alert("State in child list is not compatible with State in parent list");
            swal({
                title: "Error!",
                text: "State in child list is not compatible with State in parent list"
            });
            return false;
        }

        // set form id
        $(this).parents().eq(1).attr('id', formId);

        var isValid = $("#"+formId).valid();
        var isItemValid = validateActionItems("addActionItemTable_"+answerId+"_"+parentQuestionId+"_"+questionId);

        console.log(cities);
        console.log(law);


        if ( law == 3 ) { // when law type is municiple
            var isCityValid = validateChildCities(formId);
            var licenseDataUniqueId = answerId+"_"+parentQuestionId+"_"+questionId;
            var isLicenseValid = validateChildLicences(formId, licenseDataUniqueId);
            if (!isCityValid) return;
            if (!isLicenseValid ) return;
        }else if (law == 2) { // when law type is state
            var isLicenseValid = validateChildLicences(formId, licenseDataUniqueId);
            if (!isLicenseValid) return;
        }

        if(isValid && isItemValid){

            $(".splash").show();
            if(questionId == 0){
                saveUpdateChildQuestion(formId, answerId, parentQuestionId, element, "create", 0, idx);
            }
            else{
                saveUpdateChildQuestion(formId, answerId, parentQuestionId, element, "update", questionId, idx);
            }
        }
    });

    // Add new child question click function
    $(document).on('click', ".addChildQuestionClick", function(event) {
        var answerId = $(this).prev().attr('answer-id');
        accordionOpenClick(answerId, true);
        $(this).hide();
    })

    function findReqClassifictionsChild(formId){
        var classifications = [];
        $("#reqClassifictionTable_edit", "#"+formId).find('select').each (function() {
            //var attr = $(this).attr('id');
            var val = $(this).val();
            var classificationId = $(this).attr('classification-id');

            if(val != null && val != ""){
                classifications.push({classificationId: classificationId, value: val});
            }
        });
        return classifications;
    }

    function findNonReqClassifictionsChild(formId){
        var classifications = [];
        $(".not_req_classification", "#"+formId).each (function() {
            var val = $(this).val();
            var classificationId = $(this).attr('classification-id');
            if(val != null && val != ""){
                classifications.push({classificationId: classificationId, value: val});
            }
        });

        return classifications;
    }

    //find citations
    function findCitationsChild(formId)
    {
        var citations = [];
        //$(".citation_child").each (function() {
        $(".citation_child", "#"+formId).each (function() {
            var citation_name = $(this).val();
            var link = $(this).closest('div').parent('div').parent('div').find('.link_child').val();
            var des = $(this).closest('div').parent('div').parent('div').find('.des_child').val();

            if(citation_name != null){
                var tmp = {"citation":citation_name, "link":link,"description":des};
                citations.push(tmp);
            }
        });
        return citations;
    }

    // Child question save function
    function saveUpdateChildQuestion(formId, answerId, parentQuestionId, element, type, questionId, idx){
        var supperParentQuestionId = $("#supperQuestionId").val();
        var mainCategory = $("#mainCategory_edit").val();
        var classificationId = $("#mainCategory_edit").attr('classification-id');
        //alert(mainCategory + " " + classificationId);

        var visibility = $("input[name='visibility']:checked", "#"+formId).val();
        var mandatory = $("input[name='mandatory']:checked", "#"+formId).val();
        var question = $("#question", "#"+formId).val();
        var explanation = $("#explanation", "#"+formId).val();
        var actionItems = findActionItems("addActionItemTable_" + idx);
        var answerId = answerId;
        var parentQuestionId = parentQuestionId;
        var checkboxId = "create_qu_answer_" + answerId + "_" + parentQuestionId + "_" +questionId;
        var answers = findQuestionAnswers(formId, checkboxId);

        var audit_types = $("#auditTypes_edit", "#"+formId).val();
        //alert(audit_types);
        var country = $("#question_country_edit", "#"+formId).val();
        var state = $("#question_state_edit", "#"+formId).val();
        console.log("selected state " + state);
        var cities = $("#question_cities_edit", "#"+formId).val();
        var license_type = $("#license_type_1", "#"+formId).val();
        console.log(formId);
        var law = $("#sub_question_law", "#"+formId).val();
        if (law != 1) {
            var law = $("#question_law_edit-disable", "#"+formId).val();
        }
        var license_type = [];
        /*$(".license_data", "#"+formId).each (function() {
            var val = $(this).val();
            if(val != null && val != ""){
                license_type.push({val});
                //alert(val + " fid=" + formId);
            }
        });*/
        var fm = formId.split('_');
        fm = fm[1] + "_" + fm[2] + "_" + fm[3];

        $(".licence"+fm, "#"+formId).each (function() {
            var val = $(this).val();
            if(val != null && val != ""){
                license_type.push({val});
                //alert(val + " fid=" + formId);
            }
        });

        //return false;
        //alert(license_type);

        //var not_req_classification = $(".not_req_classification", "#"+formId).val();
        var not_req_classification = findNonReqClassifictionsChild(formId);
        //var req_classification = $(".req_classification", "#"+formId).val();
        var req_classification = findReqClassifictionsChild(formId);
        var citations_child = findCitationsChild(formId);

        var allTag="ALL";
        if (law == 1) {
            license_type = [allTag];
            state = allTag;
            cities = allTag;
        }else if (law == 2) {
            state = $("#question_state_edit", "#"+formId).val();
            cities = allTag;
        }else if (law == 3) {
            state = $("#question_state_edit", "#"+formId).val();
            if($('#chkEditQuestionSelectAllCity', "#"+formId).prop("checked")){
                cities = allTag;
            }else {
                cities = $("#question_cities_edit", "#"+formId).val();
            }
        }

        console.log("state value " + state);
        console.log("law value " + law);

       // alert(not_req_classification);

        if(answers.length >= 2){
            var checkCombinations = checkAnswerCombination(formId, checkboxId);
            if(!checkCombinations){
                //Create Child question
                if(type == "create"){
                    var data = {citations_child:citations_child, supperParentQuestionId: supperParentQuestionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, answers: answers, answerId: answerId, parentQuestionId: parentQuestionId,audit_types:audit_types,country:country,state:state,cities:cities,license_type:license_type, not_req_classification: not_req_classification,req_classification:req_classification,mainCategory:mainCategory, classificationId:classificationId, law:law}
                    $.ajax({
                        url: "/question/createChildQuestion",
                        type: 'POST',
                        dataType: 'json',
                        data : data,
                        beforeSend: function() {
                            $(".splash").show();
                        },
                        success: function(result){
                            $(element).attr('question-number', result.answerCount);
                            $(element).attr('id', 'answer_question_'+answerId+'_'+parentQuestionId+'_'+result.question_id);
                            $(element).attr('question-id', result.question_id);

                            accordionOpenClick(answerId, false);
                            $(".splash").hide();
                        },
                        error: function(xhr){
                            $(".splash").hide();
                        }
                    });
                }
                // update child question
                else{
                    var data = {citations_child:citations_child,supperParentQuestionId: supperParentQuestionId, questionId: questionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, answers: answers, answerId: answerId, parentQuestionId: parentQuestionId,audit_types:audit_types,country:country,state:state,cities:cities,license_type:license_type, not_req_classification:not_req_classification, req_classification:req_classification,mainCategory:mainCategory, classificationId:classificationId, law:law}

                    $.ajax({
                        url: "/question/updateChildQuestion",
                        type: 'POST',
                        dataType: 'json',
                        data : data,
                        beforeSend: function() {
                            $(".splash").show();
                        },
                        success: function(result){

                            if(result.success == "true")
                            {
                                accordionOpenClick(answerId, false);
                                $(".splash").hide();
                            }
                            if(result.success == "false")
                            {
                                swal({
                                    title: "Error!",
                                    text: result.msg
                                });
                                $(".splash").hide();
                            }
                        },
                        error: function(xhr){
                            $(".splash").hide();
                        }
                    });
                }
            }
            else{
                // swal({
                //     title: "Error!",
                //     text: "You can not select the same value for more than one answer."
                // });
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
                            //Create Child question
                            if(type == "create"){
                                var data = {citations_child:citations_child,supperParentQuestionId: supperParentQuestionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, answers: answers, answerId: answerId, parentQuestionId: parentQuestionId,audit_types:audit_types,country:country,state:state,cities:cities,license_type:license_type, not_req_classification: not_req_classification,req_classification:req_classification,mainCategory:mainCategory, classificationId:classificationId}
                                $.ajax({
                                    url: "/question/createChildQuestion",
                                    type: 'POST',
                                    dataType: 'json',
                                    data : data,
                                    beforeSend: function() {
                                        $(".splash").show();
                                    },
                                    success: function(result){
                                        $(element).attr('question-number', result.answerCount);
                                        $(element).attr('id', 'answer_question_'+answerId+'_'+parentQuestionId+'_'+result.question_id);
                                        $(element).attr('question-id', result.question_id);

                                        accordionOpenClick(answerId, false);
                                        $(".splash").hide();
                                    },
                                    error: function(xhr){
                                        $(".splash").hide();
                                    }
                                });
                            }
                            // update child question
                            else{
                                var data = {citations_child:citations_child,supperParentQuestionId: supperParentQuestionId, questionId: questionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, answers: answers, answerId: answerId, parentQuestionId: parentQuestionId,audit_types:audit_types,country:country,state:state,cities:cities,license_type:license_type, not_req_classification:not_req_classification, req_classification:req_classification,mainCategory:mainCategory, classificationId:classificationId}

                                $.ajax({
                                    url: "/question/updateChildQuestion",
                                    type: 'POST',
                                    dataType: 'json',
                                    data : data,
                                    beforeSend: function() {
                                        $(".splash").show();
                                    },
                                    success: function(result){

                                        if(result.success == "true")
                                        {
                                            accordionOpenClick(answerId, false);
                                            $(".splash").hide();
                                        }
                                        if(result.success == "false")
                                        {
                                            swal({
                                                title: "Error!",
                                                text: result.msg
                                            });
                                            $(".splash").hide();
                                        }
                                    },
                                    error: function(xhr){
                                        $(".splash").hide();
                                    }
                                });
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
            $(".splash").hide();
        }
    }

    // find action items
    function findActionItems(id){
        var items = [];
        $("#"+id).find('input').each (function() {
            var itemVal = $(this).val();
            if(itemVal !=  ""){
                items.push(itemVal);
            }
        });
        var jsonString = JSON.stringify(items);
        return jsonString;
    }

    // find answers
    function findQuestionAnswers(formId, checkboxId){

        var answers = [];
        $(".create_question_accordion", "#"+formId).find('input[answer-checkbox]').each (function() {

            if($(this).attr("answer-checkbox") == checkboxId) {
                var val = $(this).val();
                var isChecked = $(this).is(":checked");
                var answerOption = $(this).next().val();

                if (isChecked) {
                    var data = {answerId: val, answerOptionId: answerOption}
                    answers.push(data);
                }
            }
        });
        return answers;
    }


    // check answer combinations
    function checkAnswerCombination(formId, checkboxId){
        var answers = [];
        var isExists = false;
        $(".create_question_accordion", "#"+formId).find('input[answer-checkbox]').each (function() {

            if($(this).attr("answer-checkbox") == checkboxId){
                var answerOption = $(this).next().val();
                var isChecked = $(this).is(":checked");

                if(isChecked){
                    answers.push(answerOption);
                }
            }

        });

        $.each(answers, function(key, value){
            $.each(answers, function(key2, inValue){
                if(key != key2){
                    if(value == inValue){
                        isExists = true;
                    }
                }
            });
        });
        return isExists;
    }

    //Append child questions to HTML
    function getChildQuestion(questionId, appendId, answerId, isLast, parentQuestionId) {

        var supperParentId = $("#supperQuestionId").val();
        var license_data = new Array();
        if($(".license_data").val() != null)
        {
            $('.license_data').each(function() {
                if($(this).val() != null)
                {
                    license_data.push($(this).val().toString());
                }

            });

        }
        license_data = license_data.toString();

        //var license_data = $(".license_data").val() != null ? $(".license_data").val().toString() : '';

        var city_data = $(".cities").val() != null ? $(".cities").val().toString() : '';
        var auditType_data = $(".auditType").val() != null ? $(".auditType").val().toString() : '';
        var country_data = $(".country").val() != null ? $(".country").val().toString() : '';
        var state_data = $(".state").val() != null ? $(".state").val().toString() : '';

        jQuery.ajax({
            type: 'GET',
            url: "/question/getChildQuestion",
            async: false,
            dataType: "json",
            data: {questionId: questionId, answerId: answerId, supperParentId: supperParentId,parentQuestionId:parentQuestionId,license_data:license_data,city_data:city_data,auditType_data:auditType_data,country_data:country_data,state_data:state_data},
            beforeSend: function () {
            },
            success: function (result) {
                $(appendId).empty();
                $(appendId).append(result.data);

                $(".selectDrop_edit").select2();

                $("#accordion_"+answerId+"_"+parentQuestionId+"_"+questionId).accordion({collapsible: true, active : 'none', heightStyle: "content"});

                $('#add_child_question_'+answerId+ '_' +parentQuestionId+'_0').remove();
                if(isLast){
                    $('#answer_question_'+answerId+ '_' +parentQuestionId+'_0').remove();

                    // Append Add child question holding div
                    $(appendId).after('<div id="answer_question_'+answerId+ '_' +parentQuestionId+'_0" answer-id="'+answerId+'" answer-question-id="'+parentQuestionId+'" question-id="0" class="answer_question" > </div>');

                    // Append Add child question button
                    $("#answer_question_"+answerId+"_"+parentQuestionId+"_0").after('<button class="btn w-xs btn-primary2 m-t-n-xs addChildQuestionClick" id="add_child_question_'+answerId+ '_' +parentQuestionId+'_0" type="button"><strong>Add Child Question</strong></button>');
                }


                $.validator.addMethod("actionItemCheck", function (value, element, param) {

                    var isValid = true;
                    var count = $(param+ " tbody").children('tr').length;

                    if(count < 1){
                        isValid = false
                    }
                    return isValid;
                }, 'Need to have at least one Action Item');


                $("#form_"+answerId + "_" + parentQuestionId + "_" + questionId).validate({
                    rules: {
                        question: {
                            required: true
                        },
                        explanation: {
                            required: true
                        },
                        actionItemValidation:{
                            actionItemCheck: "#addActionItemTable_"+answerId + "_" + parentQuestionId + "_" + questionId
                        }
                    }
                });


                $(".create_qu_anser_click").click(function(event){
                    event.stopPropagation();
                });

                $(".answerValue").click(function(event){
                    event.stopPropagation();
                });
            }
        })
    }

    function accordionOpenClickForAllChildQuestion(answerId, indexValue, viewOnly) {
        jQuery.ajax({
            type: 'GET',
            url: "/question/getAllChildQuestions",
            dataType: "json",
            async: true,
            data: {answerId: answerId, questionIndex: indexValue, viewOnly: viewOnly},
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                $(".selectDrop_edit").select2();
                var idx = "#answer_question_"+answerId+"_"+result.parentQuestionId+"_common";
                $(idx).empty();
                $(idx).append(result.view);
                $(".splash").hide();
                $(".create_qu_anser_click").click(function(event){
                    event.stopPropagation();
                });
                $(".answerValue").click(function(event){
                    event.stopPropagation();
                });
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }
    //Accordion open click function
    function accordionOpenClick(answerId, isNewChild){
        /*jQuery.ajax({
            type: 'GET',
            url: "/question/getAllChildQuestions",
            dataType: "json",
            data: {answerId: answerId},
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                console.log(result.parentQuestionId);
                $(".selectDrop_edit").select2();
                var idx = "#answer_question_"+answerId+"_"+result.parentQuestionId+"_common";
                $(idx).empty();
                $(idx).append(result.view);
                $(".splash").hide();
                $(".create_qu_anser_click").click(function(event){
                    event.stopPropagation();
                });
                $(".answerValue").click(function(event){
                    event.stopPropagation();
                });
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });*/


        jQuery.ajax({
            type: 'GET',
            url: "/question/getAnswerQuestion",
            // async: false,
            dataType: "json",
            data: {questionAnswerId: answerId},
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                var parentQuestionId = result.questionAnswer["question_id"];
                if(isNewChild){
                    var idx = "#answer_question_"+answerId+"_"+parentQuestionId+"_0";
                    $("#add_child_question_"+answerId+"_"+parentQuestionId+"_0").hide();
                    getChildQuestion(0, idx, answerId, false, parentQuestionId);
                }
                else{
                    if(result.question.length > 0 ){
                        var length = result.question.length;
                        $.each(result.question, function(index, value){

                            var idx = "#answer_question_"+answerId+"_"+value.parent_question_id+"_"+value.id;
                            if(length - 1 == index){
                                getChildQuestion(value.id, idx, answerId, true, parentQuestionId);
                            }
                            else{
                                getChildQuestion(value.id, idx, answerId, false, parentQuestionId);
                            }
                        });
                    }
                    else{
                        var idx = "#answer_question_"+answerId+"_"+parentQuestionId+"_0";
                        getChildQuestion(0, idx, answerId, false, parentQuestionId);
                    }
                }
                $(".splash").hide();
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }


    // Validate actionItems
    function validateActionItems(tableId){
        var isValid = true;
        var lawType = $('#question_law_edit').val();
            $("#" +tableId).find('input').each (function() {
                var itemVal = $(this).val();

                if(itemVal == ""){
                    $(this).addClass("error");
                    isValid = false;
                }
            });
            return isValid;

    }

    // Validate licences
    function validateLicences() {
        var isValid = true;
        var lawType = $('#question_law_edit').val();
        if (lawType != 1 || (lawType==1 && $('#select-license').prop( "checked")) || (lawType==1 && $('#select-license-edit').prop( "checked"))) {
            $("#editLicenceTable").find('select.license_data').each(function () {
                var licenceAttr = $(this).attr('id');
                var licenceVal = $("#" + licenceAttr).val();

                if (licenceVal == null) {
                    console.log($("#" + licenceAttr).next());
                    // $("#" + licenceAttr).next().html('');
                    $("#" + licenceAttr).next().addClass('error-border');
                    isValid = false;
                }
                else {
                    if($("#" + licenceAttr).next().hasClass('error-border')){

                        $("#" + licenceAttr).next().removeClass('error-border');
                    }
                }

            });
        }
        return isValid;
    }

    function validateChildLicences(formId, licenseIdentifier) {
        var isValid = true;
        var formId = '#' + formId;
        var lawType = $('#question_law_edit', formId).val();
        if (lawType != 1 || (lawType==1 && $('#select-license',formId).prop( "checked"))) {
            $("#editLicenceTable"+licenseIdentifier).find('select.license_data').each(function () {
                var licenceAttr = $(this).attr('id');
                var licenceVal = $("#" + licenceAttr, formId).val();

                if (licenceVal == null) {
                    //console.log($("#" + licenceAttr,formId).next());
                    // $("#" + licenceAttr).next().html('');
                    $("#" + licenceAttr, formId).next().addClass('error-border');
                    isValid = false;
                }
                else {
                    if($("#" + licenceAttr, formId).next().hasClass('error-border')){

                        $("#" + licenceAttr, formId).next().removeClass('error-border');
                    }
                }

            });
        }
        return isValid;
    }

    function validateChildCities(formId) {
        var isValid = true;
        var cities = $("#question_cities_edit", "#"+formId).val();
        //console.log("validating cities " + cities);
        if (cities == null || cities == '') {
            $("#question_cities_edit", "#" + formId).next().addClass('error-border');
            isValid = false;
        }else {
            if($("#question_cities_edit", "#" + formId).next().hasClass('error-border')){
                $("#question_cities_edit", "#" + formId).next().removeClass('error-border');
            }
        }
        return isValid;
    }

    // Validate Required Classifications
    function validateReqClassifications(){

        var isValid = true;
        $("#reqClassifictionTable_edit").find('select').each (function() {
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



    // Add New Item Row
    function AddNewActionItem(tableId){

        var rowId = $("#"+tableId + " tbody tr:last > td:eq(0)").text();

        rowId++;

        if(rowId == 1){
            $("#"+tableId + " tbody").append('<tr>' +
                '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                '<td class="questionTableTdInput"><input class="action_item_data col-sm-12 marginButtom1 form-control" type="text"></td>' +
                '<td class="questionTableTd "><button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left" type="button"><i class="fa fa-times"></i></button></td>' +
                '</tr>');
        }
        else{
            $("#"+tableId + " tbody tr:last").after('<tr>' +
                '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                '<td class="questionTableTdInput"><input class="action_item_data col-sm-12 marginButtom1 form-control" type="text"></td>' +
                '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left" type="button"><i class="fa fa-times"></i></button></td>' +
                '</tr>');

        }

        $("#actionItemValidation-error").remove();
    }


    // Delete Action Item Row
    $(document).on('click', ".btn_delete_item", function() {
        if($(this).attr('item-number') != "1") {
            var tr = $(this).closest('tr');
            //tr.css("background-color", "#FF3700");
            tr.fadeOut(400, function () {
                tr.remove();
            });
        }
        return false;
    });

    // Update question answer
    function deleteQuestionAnswer(answerId, accordioinId){
        jQuery.ajax({
            type: 'GET',
            url: "/question/deleteQuestionAnswer",
            async: false,
            dataType: "json",
            data: {answerId: answerId},
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                swal("Deleted!", "Answer has been deleted.", "success");
                $('#' +accordioinId).accordion({active: false });
                $(".splash").hide();
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }

    $("#edit_keywords").select2({
        placeholder: "Select Keywords",
        tags: true
    });

    $("#auditTypes_edit").select2({
        placeholder: "Select Audit Types"
    });

    $("#question_cities_edit").select2({
        placeholder: "Select Cities"
    });


    $(".classification_option_edit").select2();
    $(".classification_option_not_req_edit").select2();
    $(".license_data").select2();



    // country change function
    $("#question_country_edit").change(function(){
        var countryId = $(this).val();
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

                $("#question_state_edit").empty();
                $("#question_state_edit").append('<option value="">Choose..</option>');

                $("#question_cities_edit").empty();
                $('#question_cities_edit').select2('val', '');
                $(".editQuestionSelectAllCity").hide();

                if(result.data != null){
                    $.each(result.data.master_states, function(incex, value){
                        $("#question_state_edit").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }

                // change licence based on state changes
                $("#editLicenceTable").find("select[custom-attr='licence_type']").each (function() {
                    var id = $(this).attr('id');
                    getLicences(0, "#"+id);
                });

                //$("#edit_question_from").valid();
                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    });

    // state change function
    $("#question_state_edit").change(function(){
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

                $("#question_cities_edit").empty();
                $('#question_cities_edit').select2('val', '');

                if(result.data != null){
                    $(".editQuestionSelectAllCity").show();
                    $("#chkEditQuestionSelectAllCity").attr('checked', false);
                    $.each(result.data.master_city, function(incex, value){
                        $("#question_cities_edit").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                else{
                    $(".editQuestionSelectAllCity").hide();
                }



                $(".splash").hide();

            },
            error: function (result) {
                $(".splash").hide();
            }
        });

        $("#editLicenceTableBody").find("tr:gt(0)").remove();
        // change licence based on state changes
        $("#editLicenceTable").find("select[custom-attr='licence_type']").each (function() {
            var id = $(this).attr('id');
            getLicences(stateId, "#"+id);
        });

    });

    $("#btnEditActionItem").click(function(){
        AddNewActionItem("actionItemTable_edit");
    });

    $(document).on('click', "#btnEditLicense", function(event){
        //$("#btnEditLicense").click(function(){

        var tableId = $(this).attr('table-id');
        var rowId = $('#editLicenceTable tbody tr:last > td:eq(0)').text();
        rowId++;
        rowId++;
        if($('#select-license-edit').prop("checked")){
            if(rowId == 1){
                $('#editLicenceTable tbody').append('<tr>' +
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
                $('#editLicenceTable tbody tr:last').after('<tr>' +
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
                    '   </select>' +
                    '</div>'+
                    '</td>' +
                    '<td class="questionTableTd"><div class="col-xs-12 m-b-xs"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></div>' +
                    '</td>' +
                    '</tr>');
            }
            var countryId=$('#question_country_edit').val();
            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });
            getStatesWithFedaral(countryId,"#states_fedaral_"+rowId)
        }else{
            if(rowId == 1){
                $('#editLicenceTable tbody').append('<tr>' +
                    '<td class="questionTableTd">'+rowId+'</td>' +
                    '<td class="col-sm-9 questionTableTdInput">' +
                    '   <select class="license_data marginButtom1 col-sm-11 form-control padding0" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '   </select>' +
                    '</td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></td>' +
                    '</tr>');
            }
            else{
                $('#editLicenceTable tbody tr:last').after('<tr>' +
                    '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                    '<td class="questionTableTdInput">' +
                    '   <select class="license_data marginButtom1 col-sm-11 form-control padding0" name="license_type_'+rowId+'" id="license_type_'+rowId+'" multiple="multiple" custom-attr="licence_type">' +
                    '   </select>' +
                    '</td>' +
                    '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></td>' +
                    '</tr>');
            }



            $("#license_type_" + rowId).select2({
                placeholder: "Select License Types(s)"
            });

            var stateId = $("#question_state_edit").val();
            getLicences(stateId, "#license_type_"+rowId);
        }

        $("#licenceValidation-error").remove();
    });


    $(document).on('click', "#btnEditLicenseChild", function(event){
        //$("#btnEditLicense").click(function(){
        var tableId = $(this).closest('table').attr('id');
        var rowId = $('#' + tableId + ' tbody tr:last > td:eq(0)').text();
        var licence_class = $(this).attr('licence_class');
        rowId++;

        if(rowId == 1){
            $('#' + tableId + ' tbody').append('<tr>' +
                '<td class="questionTableTd">'+rowId+'</td>' +
                '<td class="col-sm-9 questionTableTdInput">' +
                '   <select class="license_data ' + licence_class + ' marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="license_type_'+rowId+'" id="license_type__'+ tableId + rowId+'" multiple="multiple" custom-attr="licence_type">' +
                '   </select>' +
                '</td>' +
                '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></td>' +
                '</tr>');

        }
        else{
            $('#' + tableId + ' tbody tr:last').after('<tr>' +
                '<td class="questionTableTd" style="display: none">'+rowId+'</td>' +
                '<td class="questionTableTdInput">' +
                '   <select class="license_data ' + licence_class + ' marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="license_type_'+rowId+'" id="license_type__'+ tableId + rowId+'" multiple="multiple" custom-attr="licence_type">' +
                '   </select>' +
                '</td>' +
                '<td class="questionTableTd"><button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button></td>' +
                '</tr>');

        }
        $("#license_type__" + tableId + rowId).select2({
            placeholder: "Select License Types(s)"
        });

        var stateId = $("#question_state_edit").val();

        getLicencesForChild(stateId, "#license_type__"+ tableId + rowId);
        $("#licenceValidation-error").remove();
    });


    // Delete License Types row
    $(document).on('click', ".btn_delete_licence_type", function() {
        if($(this).attr('item-number') != "1") {
            var tr = $(this).closest('tr');
            //tr.css("background-color","#FF3700");
            tr.fadeOut(400, function(){
                tr.remove();
            });
        }

        return false;
    });
    $('body').on('change','.states_fedaral',function(){
        var stateFedaral=$(this).val();
        var stateFedaralId=$(this).attr('id');
        var index=stateFedaralId.split('_');
        getLicences(stateFedaral, "#license_type_"+index[2]);
    });

    // get licences
    function getLicences(stateId, licenceId){
        //alert(licenceId);

        jQuery.ajax({
            type: 'GET',
            url: "/question/getLicences",
            async: false,
            data: { stateId: stateId},
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

    //get fedaral states

    function getFedaralStates() {
        var state=[];
        $('.states_fedaral').each(function(i,obj){
            if(obj.value !=null){
                state.push(obj.value);
            }
        });
        return state;
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
    function getLicencesForChild(stateId, licenceId){
        //alert(licenceId);

        /*var license_data = new Array();
        if($(".license_data").val() != null)
        {
            $('.license_data').each(function() {
                if($(this).val() != null)
                {
                    license_data.push($(this).val().toString());
                }

            });

        }*/

        var license_data = '';
        if($(".license_data").val() != null)
        {
            $('.license_data').each(function() {
                if($(this).val() != null)
                {
                    license_data += $(this).val().toString() + ',';
                }
            });
        }
        license_data = license_data.split(',');

        jQuery.ajax({
            type: 'GET',
            url: "/question/getLicences",
            async: false,
            data: { stateId: stateId},
            dataType: "json",
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {

                $(licenceId).empty();
                $(licenceId).select2('val', '');

                if(result.data != null){
                    $.each(result.data.master_license, function(incex, value){

                        if(jQuery.inArray(value.id.toString(), license_data) !== -1)
                        {
                            $(licenceId).append($('<option>', {value: value.id, text: value.name}));
                        }

                    });
                }

                $(".splash").hide();
            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }

    //find citations
    function findCitations()
    {
        var citations = [];
        $(".citation").each (function() {
            var citation_name = $(this).val();
            var link = $(this).closest('div').parent('div').parent('div').find('.link').val();
            var des = $(this).closest('div').parent('div').parent('div').find('.des').val();
            var id = $(this).closest('div').parent('div').parent('div').find('.citation_id').val();

            if(citation_name != null){
                var tmp = {"id": id, "citation_name":citation_name, "link":link,"des":des};
                citations.push(tmp);
            }
        });
        return citations;
    }
    var citations = findCitations();

    $(".edit_parent_question").click(function(){
        var save_type = $(this).attr('save-type');
        var questionId = $("#supperQuestionId").val();
        var checkboxId = "create_qu_answer_0_0_"+questionId;
        var isFormValid = $("#edit_question_from").valid();
        var isItemValid = validateActionItems("actionItemTable_edit");
        var isLicenceValid = validateLicences();
        var reqClassifiction = validateReqClassifications();
        var answers = findQuestionAnswers("edit_question_from", checkboxId);
        var isValidLicenceCombination = validateLicenceCombination();

        var audit_types_main = $("#auditTypes_edit").val();
        var country_main = $("#question_country_edit").val();
        var state_main = $("#question_state_edit").val();
        var cities_main = $("#question_cities_edit").val();
        //var license_type_main = $("#license_type_1").val();
        var edit_published_date = $('#publishDateEdit').val();
        var license_type_main = [];
        $('.license_data_main').find("option:selected").each(function() {
            license_type_main.push(this.value);
        });

        var is_valid_list = true;
        $('.save_child_question').each(function() {

            var answerId = $(this).parents().eq(3).attr('answer-id');
            var parentQuestionId = $(this).parents().eq(3).attr('answer-question-id');
            var questionId = $(this).parents().eq(3).attr('question-id');
            var formId = "form_"+answerId+"_"+parentQuestionId+"_"+questionId;
            var idx = answerId+"_"+parentQuestionId+"_"+questionId;
            var element = $(this).parents().eq(3);

            var audit_types = $("#auditTypes_edit", "#"+formId).val();
            var country = $("#question_country_edit", "#"+formId).val();
            var state = $("#question_state_edit", "#"+formId).val();
            var cities = $("#question_cities_edit", "#"+formId).val();
            var license_type = $("#license_type_1", "#"+formId).val();
            var law = $("#sub_question_law", "#"+formId).val();

            if(audit_types != null)
            {
                for (var i = 0; i < audit_types.length; ++i) {
                    if(jQuery.inArray(audit_types[i], audit_types_main) == -1)
                    {
                        //alert("Audit Type child list is not compatible with Audit Type parent list");
                        swal({
                            title: "Error!",
                            text: "Audit Type child list is not compatible with Audit Type parent list"
                        });
                        is_valid_list = false;
                        return false;
                    }
                }
            }


            if(cities != null && cities_main!=null)
            {
                for (var i = 0; i < cities.length; ++i) {
                    if(jQuery.inArray(cities[i], cities_main) == -1)
                    {
                        //alert("Cities child list is not compatible with Cities parent list");
                        swal({
                            title: "Error!",
                            text: "Cities child list is not compatible with Cities parent list"
                        });
                        is_valid_list = false;
                        return false;
                    }
                }
            }
            /*alert("kkk");
            alert("LT" + license_type[0] + " main" + license_type_main);
            is_valid_list = false;*/

             if(license_type != null && law != 1)
            {
                for (var i = 0; i < license_type.length; ++i) {
                    if(jQuery.inArray(license_type[i], license_type_main) == -1)
                    {
                        //alert("License child list is not compatible with License parent list");
                        swal({
                            title: "Error!",
                            text: "License child list is not compatible with License parent list"
                        });
                        is_valid_list = false;
                        return false;
                    }
                }
            }


            if(country_main != null && country != null && country_main != country)
            {
                //alert("Country in child list is not compatible with Country in parent list");
                swal({
                    title: "Error!",
                    text: "Country in child list is not compatible with Country in parent list"
                });
                is_valid_list = false;
                return false;
            }

            if(state_main != null && state != null && state_main != state)
            {
                //alert("State in child list is not compatible with State in parent list");
                swal({
                    title: "Error!",
                    text: "State in child list is not compatible with State in parent list"
                });
                is_valid_list = false;
                return false;
            }
        });

        if(!is_valid_list)
        {
            return false;
        }

        if(isValidLicenceCombination){
            swal({
                title: "Error!",
                text: "You can't use the same license combination."
            });
        }
        else{
            console.log(isFormValid);
            console.log(isItemValid);
            console.log(isLicenceValid);
            console.log(reqClassifiction);

            if(isFormValid && isItemValid && isLicenceValid && reqClassifiction){
                if(answers.length >= 2){

                    var checkCombinations = checkAnswerCombination("edit_question_from", checkboxId);
                    var questionType = $(this).attr('save-type');
                    var isDraft = 1;
                    var visibility = $("input[name='visibility']:checked").val();
                    (questionType == "publish") ? isDraft = 0 : isDraft = 1;
                    if(!checkCombinations){
                        console.log("valid check!");
                        if(isDraft == 0) {
                            var published_date = $('#publishDateEdit').val();

                            if(published_date =='') {
                                checkDraftAndUpdateQuestion(isDraft, visibility, save_type);
                                // when inactive question is going to publish
                                // if(isDraft == 0 && visibility == 0){
                                //     swal({
                                //         title: "Error!",
                                //         text: "You can't publish if the question is inactive",
                                //         type: "error"
                                //     });
                                // }
                                // else{
                                //     updateParentQuestion(isDraft);
                                // }
                            } else {
                                swal({
                                            title: "Error!",
                                            text: "Publish date is invalid, please remove entered published date to continue.",
                                            type: "error"
                                        });
                            }
                        } else {
                            checkDraftAndUpdateQuestion(isDraft, visibility, save_type);
                        }
                    }
                    else {
                        // swal({
                        //     title: "Error!",
                        //     text: "You can't select the same value for more than one answer."
                        // });
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
                                    var published_date = $('#publishDateEdit').val();
                                    if(isDraft == 0) {
                                        if(published_date =='') {
                                            checkDraftAndUpdateQuestion(isDraft, visibility, save_type);

                                        } else {
                                            swal({
                                                title: "Error!",
                                                text: "Publish date is invalid, please remove entered published date to continue.",
                                                type: "error"
                                            });
                                        }
                                    } else {
                                        checkDraftAndUpdateQuestion(isDraft, visibility, save_type);
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
                    $(".splash").hide();
                }
            }
        }
    });

    function checkDraftAndUpdateQuestion(isDraft, visibility,save_type) {
        // when inactive question is going to publish
        if(isDraft == 0 && visibility == 0){
            swal({
                title: "Error!",
                text: "You can't publish if the question is inactive",
                type: "error"
            });
        }
        else{
            console.log("update question! draft!");
            updateParentQuestion(isDraft,save_type);
        }
    }


    // validate licence combinations
    function validateLicenceCombination(){
        var isCombinationExists = false;
        var license = [];
        $("#editLicenceTable").find('select.license_data').each (function() {
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

    //find citations
    function findCitations()
    {
        var citations = [];
        $(".citation").each (function() {
            var citation_name = $(this).val();
            var link = $(this).closest('div').parent('div').parent('div').find('.link').val();
            var des = $(this).closest('div').parent('div').parent('div').find('.des').val();
            var id = $(this).closest('div').parent('div').parent('div').find('.citation_id').val();

            if(citation_name != null){
                var tmp = {"id": id, "citation":citation_name, "link":link,"description":des};
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

    $(document).on('click', '.delete_child', function(){
        var formId = $(this).closest('div').parent('div').parent('div').parent('div').parent('div').parent('form').attr('id');
        //alert(formId);
        var count = 0;
        $(".delete_child","#" + formId).each (function() {
            count++;
        });
        if(count > 1)
        {
            $(this).closest('div').parent('div').remove();
        }
    });

    $(document).on('click', '#add_citation_child', function(){
        var formId = $(this).closest('div').parent('div').parent('div').parent('form').attr('id');

        var table_row = $('.table_row_child',"#" + formId).html();
        table_row = '<div class="col-sm-12 table_row_child padder-v" >' + table_row + "</div>";

        var main_div_child = $(this).closest('div').parent('div').parent('div').find('#main_div_child');
        //$(table_row).appendTo("#main_div_child", "#"+formId);
        $(table_row).appendTo(main_div_child);

        var size  = $(".citation_child","#" + formId).size();
        $(".citation_child","#" + formId).each (function(index) {
            //alert(this.val() );
            if( (index + 1) == size)
            {
                $(this).val('');
                $(this).closest('div').parent('div').parent('div').find('.link_child').val('');
                $(this).closest('div').parent('div').parent('div').find('.des_child').val('');
            }
        });

    });

    $(document).on('click', '#add_citation', function(){
        var table_row = $('.table_row').html();
        table_row = '<div class="col-sm-12 table_row padder-v" >' + table_row + "</div>";
        $(table_row).appendTo("#main_div");

        var size  = $(".citation").size();
        $(".citation").each (function(index) {
            if( (index + 1) == size)
            {
                $(this).val('');
                $(this).closest('div').parent('div').parent('div').find('.link').val('');
                $(this).closest('div').parent('div').parent('div').find('.des').val('');
            }
        });

    });

    $('#select-license-edit').change(function () {
        if ( $(this).prop( "checked" )){
            var countryId=$('#question_country_edit').val();
            $('.license_data').attr("disabled",false);
            $('.states_fedaral').attr("disabled",false);
            $('#license_type_1').empty();
            $("#editLicenceTable").find("select[custom-attr='state_type']").each (function() {
                var id = $(this).attr('id');
                getStatesWithFedaral(countryId,"#"+id);
            });

            if($('.license-section-edit').hasClass('hidden')){
                $('.license-section-edit').removeClass('hidden');
            }
        }else {
            $('.license-section-edit').addClass('hidden');
            $('.license_data').attr("disabled",true);
            $('.questionTableStateTdInput').addClass('hidden');
            $('.states_fedaral').attr("disabled",true);
        }


    })

    // Parent question update function
    function updateParentQuestion(isDraft,save_type){
        var questionId = $("#supperQuestionId").val();
        var visibility = $("input[name='visibility']:checked").val();
        var mandatory = $("input[name='mandatory']:checked").val();
        var question = $("#question").val();
        var explanation = $("#explanation").val();
        var actionItems = findActionItems("actionItemTable_edit");
        var keywords = $("#edit_keywords").val();
        var mainCategory = $("#mainCategory_edit").val();
        var classificationId = $("#mainCategory_edit").attr('classification-id');
        var classificationParentID = $("#mainCategory_edit").find(':selected').attr('parent_id');

        var is_child = $("#mainCategory_edit").find(':selected').attr('is_child');
        var auditTypes = $("#auditTypes_edit").val();
        var country = $("#question_country_edit").val();
        var state = $("#question_state_edit").val();
        var cities = $("#question_cities_edit").val();
        var license = findLicences();
        var reqClassifictions = findReqClassifictions();
        var nonReqClassifications = findNonReqClassifictions();
        var checkboxId = "create_qu_answer_0_0_" + questionId;
        var answers = findQuestionAnswers("edit_question_from", checkboxId);
        var citations = findCitations();
        var allTag="ALL";
        var law=$('#question_law_edit').val();
        var published_date=$('#publishDateEdit').val();

        if(law==1 && $('#select-license-edit').prop( "checked") ){
            state = getFedaralStates();
            cities = allTag;

        }else if(law==1 && !$('#select-license-edit').prop( "checked") ){
            state = allTag;
            cities = allTag;

        }else if(law==2){
            state = $("#question_state_edit").val();
            cities = allTag;

        }else if(law==3 && $('#chkEditQuestionSelectAllCity').prop("checked")){
            state = $("#question_state_edit").val();
            cities = allTag;
        }else {
            state = $("#question_state_edit").val();
            cities = $("#question_cities_edit").val();
        }
        // var citations = findCitations();

        if(law==1 && !$('#select-license-edit').prop( "checked") ){
            license = [];
            license.push([allTag]);
        }else{
            license = findLicences();

        }

        var data2 = {questionId: questionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, keywords: keywords, mainCategory: mainCategory, classificationId: classificationId,is_child:is_child, classificationParentID: classificationParentID, auditTypes: auditTypes, country: country, state: state, cities: cities, license: license, reqClassifictions: reqClassifictions, nonReqClassifications: nonReqClassifications, answers: answers, isDraft: isDraft, publishDate: published_date}
        console.log("update question!");
        $.ajax({
            url: "/question/checkParentQuestion",
            type: 'POST',
            dataType: 'json',
            data : data2,
            async :false,
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                // console.log("success!");
                // var msg = result.message;
                // var msg_type = 'success';
                // msgAlert(msg, msg_type);
                // setTimeout(function(){
                //     $(".splash").hide();
                //     return false;
                // }, 10000);

            },
            error: function(xhr){
                console.log("error!");
                $(".splash").hide();
            }
        });

        var data = {questionId: questionId, visibility: visibility, mandatory: mandatory, question: question, explanation: explanation, actionItems: actionItems, keywords: keywords, mainCategory: mainCategory, classificationId: classificationId,is_child:is_child, classificationParentID:classificationParentID, auditTypes: auditTypes, country: country, state: state, cities: cities, license: license, reqClassifictions: reqClassifictions, nonReqClassifications: nonReqClassifications, answers: answers, isDraft: isDraft,  citations:citations,law:law, publishDate: published_date}
        $.ajax({
            url: "/question/updateParentQuestion",
            type: 'POST',
            dataType: 'json',
            async :false,
            data : data,
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                console.log(result)
                if(result.success == 'true')
                {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert('Question updated successfully', msg_type);

                    setTimeout(function(){
                        if(save_type == "publish")
                        {
                            window.location.assign("/question/viewQuestion/" +result.data);
                        }
                        if(save_type == "save")
                        {
                            window.location.assign("/question/editQuestion/" +result.data);
                        }
                        $(".splash").hide();
                        return false;
                    }, 3000);

                }
                if(result.success == 'false')
                {
                    swal({
                        title: "Error!",
                        text: "Invalid Audit Type or Country or State or Cities or Licence exists on child questions.Please update the child questions before you save parent.",
                        type: "error"
                    });

                    $(".splash").hide();

                }

            },
            error: function(xhr){
                console.log("error!");
                $(".splash").hide();
            }
        });

    }

    //find license
    function findLicences(){
        var license = [];
        $("#editLicenceTable").find('select.license_data').each (function() {
            var licenceAttr = $(this).attr('id');
            var licenceVal = $("#" + licenceAttr).val();
            if(licenceVal != null){
                license.push(licenceVal);
            }
        });
        return license;
    }

    //find required classifictions
    function findReqClassifictions(){
        var classifications = [];
        $("#reqClassifictionTable_edit").find('select').each (function() {
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
        $("#nonReqClassifictionTable_edit").find('select').each (function() {
            var attr = $(this).attr('id');
            var val = $("#" + attr).val();
            var classificationId = $(this).attr('classification-id');
            if(val != null && val != ""){
                classifications.push({classificationId: classificationId, value: val});
            }
        });
        return classifications;
    }

    // create new version function
    function createNewVersion(questionId, comment){
        $.ajax({
            url: "/questions/createNewVersion",
            type: 'POST',
            dataType: 'json',
            data : {questionId: questionId, comment: comment},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                if(result.success == "true"){
                    window.location.assign("/question/editQuestion/" +result.data);
                }
                else{
                    swal({
                        title: "Error!",
                        text: "Can't create new version. Please try again"
                    });
                }
                $(".splash").hide();
            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }


    $.validator.setDefaults({
        ignore: []
    });


    $.validator.addMethod("actionItemCheck", function (value, element, param) {
        var isValid = true;
        var count = $(param + " tbody").children('tr').length;

        if(count < 1){
            isValid = false
        }
        return isValid;
    }, 'Need to have at least one Action Item');

    $.validator.addMethod("licenseCheck", function (value, element, param) {
        var isValid = true;
        var count = $("#editLicenceTable tbody").children('tr').length;
        if(count < 1){
            isValid = false
        }
        return isValid;
    }, 'Need to have at least one Licence');

    $.validator.addMethod("publishDareCheck", function (value, element, param) {
        var is_valid = true;
        var publish_at = $('#publishDateEdit').val();
        if(publish_at !='') {
            if(new Date() > new Date(publish_at)) {
                is_valid = false;
            } else {
                is_valid = true;
            }
        }
        return is_valid;
    }, 'Publish date should be greater than current date');

    $("#edit_question_from").validate({
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
            auditType: {
                required: true
            },
            licenceValidation: {
                licenseCheck: true
            },
            actionItemValidation: {
                actionItemCheck: "#actionItemTable_edit"
            },
            publishDateEdit: {
                publishDareCheck: '#publishDateEdit'
            }

        },

        errorPlacement: function(error, element) {
            if(element.attr('name')=="cities"){
                error.insertAfter(document.getElementById('chkCreateQuestionSelectAllCityError'));
            } else if(element.attr('name')=="publishDateEdit") {
                error.insertAfter(document.getElementById('publishDateError'));
            } else{
                error.insertAfter(element);
            }
        },
    });

    $("#versionCommentForm").validate({
        rules: {
            versionComment: {
                required: true
            }
        }
    });

});


// City select all check box checked - unchecked function
function getCities(selectedCities){
    if(selectedCities != null){
        var stateId = $("#question_state_edit").val();

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

                var allCities = 0;
                if(result.data != null && result.data.length > 0)
                {
                    allCities = result.data.master_city.length;
                }

                if(selectedCities.length == allCities){
                   // $("#chkEditQuestionSelectAllCity").prop('checked', true);
                }
                else{
                    //$("#chkEditQuestionSelectAllCity").prop('checked', false);
                }

                $(".splash").hide();
            },
            error: function (result) {
                $(".splash").hide();
            }
        });
    }
}

// update question redirect
function updateQuestion(questionId,is_edit){
    if(is_edit)
    {
        window.location.assign("/question/editQuestion/" +questionId);
    }
    else
    {
        window.location.assign("/question/viewQuestion/" +questionId);
    }
}

function showHideSelectStateCity(law){
    var states=$('#question_state_edit');
    var cities=$('#question_cities_edit');
    var license=$('.license_data');
    var viewOrEdit=$('#viewOrEdit').val()

    if(law==1){
        if($('#select-license-edit').prop('checked')){
            if(viewOrEdit!=1){

                $('#question_state_edit,#question_cities_edit').attr("disabled",true);
            }
            $('.state-section,.city-section-edit').addClass('hidden');
            if($('.license-section-edit').hasClass('hidden')){
                $('.license-section-edit').removeClass('hidden');
            }
        }else {
            $('#question_state_edit,#question_cities_edit,.license_data').attr("disabled",true);
            $('.state-section,.city-section,.license-section').addClass('hidden');
        }
        if($('.apply-all-section-edit').hasClass('hidden')){
            $('.apply-all-section-edit').removeClass('hidden');
        }

    }else if(law==2){
        if(viewOrEdit!=1){

            $('#question_state_edit,.license_data').attr("disabled",false);
        }
        $('#question_cities_edit').attr("disabled",true);
        if($('.state-section-edit').hasClass('hidden')){
            $('.state-section-edit').removeClass('hidden');
        }
        if($('.license-section-edit').hasClass('hidden')){
            $('.license-section-edit').removeClass('hidden');
        }
        $('.city-section-edit,.apply-all-section-edit').addClass('hidden');

    }else if(law==3){
        if(viewOrEdit!=1){

            $('#question_state_edit,.license_data').attr("disabled",false);
        }
        if($('.state-section-edit').hasClass('hidden')){
            $('.state-section-edit').removeClass('hidden');
        }
        if($('.city-section-edit').hasClass('hidden')){
            $('.city-section-edit').removeClass('hidden');
        }
        if($('.license-section-edit').hasClass('hidden')){
            $('.license-section-edit').removeClass('hidden');
        }
        $('.apply-all-section-edit').addClass('hidden');
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

function updateAccordionContent(answerId, questionIndexPrefix) {
    jQuery.ajax({
        type: 'GET',
        url: "/question/getAllChildQuestions",
        dataType: "json",
        async: true,
        data: {answerId: answerId, questionIndex: questionIndexPrefix},
        beforeSend: function () {
            $(".splash").show();
        },
        success: function (result) {
            console.log(result.parentQuestionId);
            $(".selectDrop_edit").select2();
            var idx = "#answer_question_"+answerId+"_"+result.parentQuestionId+"_common";
            $(idx).empty();
            $(idx).append(result.view);
            $(".splash").hide();
            $(".create_qu_anser_click").click(function(event){
                event.stopPropagation();
            });
            $(".answerValue").click(function(event){
                event.stopPropagation();
            });
        },
        error: function(xhr){
            $(".splash").hide();
        }
    });
}

/**
 * Delete sub question in parent nested child view
 * @param questionId
 */
function deleteQuestion(questionId, questionIndexPrefix, answerId){
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this question. \n If you're unsure, cancel and deactivate the question to prevent it from appearing on checklists.",
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

                $.ajax({
                    url: "/questions/deleteSubQuestion",
                    type: 'POST',
                    dataType: 'json',
                    data:{questionId:questionId},
                    success: function(result){
                        if(result.success == 'true') {
                            swal("Deleted!", "Your question has been deleted.", "success");
                            updateAccordionContent(answerId, questionIndexPrefix);
                        }else {
                            var msg = result.message;
                            var msg_type = 'error';
                            swal("Error!", msg, "error");
                        }
                    }
                });
            } else {
                swal("Cancelled", "Your question is safe :)", "error");
            }
        }
    );
}

/**
 * Change question status in parent nested view
 * @param questionId
 * @param status
 * @param isDraft
 * @param answerId

 */
function changeQuestionStatus(questionId, status, isDraft, answerId, parentQuestionId){
    console.log($('#header_' + parentQuestionId+ '_' + answerId ).attr('question-index'));
    var questionIndexPrefix = $('#header_' + parentQuestionId+ '_' + answerId ).attr('question-index');
    if(isDraft == 0){
        swal({
            title: "Error!",
            text: "You can't change status on published questions."
        });
    }
    else{
        $.ajax({
            url: "/questions/updateSubQuestionStatus",
            type: 'POST',
            dataType: 'json',
            data:{questionId:questionId, status:status},
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    updateAccordionContent(answerId, questionIndexPrefix);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
}
