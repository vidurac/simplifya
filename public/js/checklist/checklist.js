$(function() {
    $("#checklistLicences").select2({
        placeholder: "Licences"
    });
    getStates(0);
    getCities(0);
    getLicences(0);

    $("#checklistCountry").change(function(){
        getStates($(this).val());
        if($(this).val() == 0){
            getLicences(0);
        }
    });

    $("#checklistState").change(function(){
        getCities($(this).val());
        getLicences($(this).val());
    });

    $("#generateChecklist").click(function(){
        generateChecklist();
    })

    $("#checklistCities").change(function(){
        if($(this).val() == 0)
        {
            $("#city_only").attr('checked', false);
            $("#city_only").prop('disabled', true);
        }
        else
        {
            $("#city_only").prop('disabled', false);
        }
    })
});

// get states
function getStates(countryId){
    jQuery.ajax({
        type: 'GET',
        url: "/checklist/getStates",
        async: false,
        data: { countryId: countryId},
        dataType: "json",
        beforeSend: function () {
            $(".splash").show();
        },
        success: function (result) {

            $("#checklistState").empty();
            $("#checklistState").append('<option value="0">All</option>');

            $("#checklistCities").empty();
            $("#checklistCities").append('<option value="0">All</option>');

            $("#checklistLicences").empty();
            $("#checklistLicences").select2('val', '');

            if(countryId != 0){
                if(result.data != null){
                    $.each(result.data.master_states, function(incex, value){
                        $("#checklistState").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }
            }
            else{
                if(result.data != null){
                    $.each(result.data, function(incex, value){
                        $("#checklistState").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }
            }

            $(".splash").hide();

        },
        error: function (result) {
            $(".splash").hide();
        }
    });
}

// get cities
function getCities(stateId){
    jQuery.ajax({
        type: 'GET',
        url: "/checklist/getCities",
        async: false,
        data: { stateId: stateId},
        dataType: "json",
        beforeSend: function () {
            $(".splash").show();
        },
        success: function (result) {

            $("#checklistCities").empty();
            $("#checklistCities").append('<option value="0">All</option>');


            if(stateId != 0) {
                if (result.data != null) {
                    $.each(result.data.master_city, function (incex, value) {
                        $("#checklistCities").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
            }
            else{
                if(result.data != null){
                    $.each(result.data, function(incex, value){
                        $("#checklistCities").append('<option value="'+value.id+'"> '+value.name+'</option>');
                    });
                }
            }
            $(".splash").hide();

        },
        error: function (result) {
            $(".splash").hide();
        }
    });
}

// get licences
function getLicences(stateId){

    jQuery.ajax({
        type: 'GET',
        url: "/checklist/getLicences",
        async: false,
        data: { stateId: stateId},
        dataType: "json",
        beforeSend: function () {
            $(".splash").show();
        },
        success: function (result) {
            $("#checklistLicences").empty();
            $("#checklistLicences").select2('val', '');

            if(stateId != 0) {
                if (result.data != null) {
                    $.each(result.data.master_license, function (incex, value) {
                        $("#checklistLicences").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
            }
            else{
                $.each(result.data, function(incex, value){
                    $("#checklistLicences").append($('<option>', {value: value.id, text: value.name}));
                });
            }
            $(".splash").hide();
        },
        error: function (result) {
            $(".splash").hide();
        }
    });
}

// Generate Checklist
function generateChecklist(){

    var data = Array();
    var auditType = $("#checklistAuditTypes").val();
    var country = $("#checklistCountry").val();
    var state = $("#checklistState").val();
    var city = $("#checklistCities").val();
    //alert($("#checklistCities").val());
    var city_only = $("#city_only").is(':checked');
    var mainCategory = $("#checklistMainCategory").val();
    var mainCatId = $("#checklistMainCategory").attr("classification-id");
    var classifications = findClassifictions();

    var license = $("#checklistLicences").val();

    if(classifications.length == 0){
        classifications = 0;
    }

    var data = {auditType: auditType, country: country, state: state, city: city, city_only: city_only,mainCategory: mainCategory, classifications: classifications, license_type : license,  mainCatId: mainCatId};

    var table = $('#checklist-detail-table').DataTable();
    table.destroy();

     $('#checklist-detail-table').dataTable({
       "ajax": {
            "url": "/checklist/searchQuestions",
            "type": "GET",
            data : data,
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false,
        "aoColumns": [
            // {"sWidth": "3%", "mData": 0},
            {"sWidth": "30%", "mData": 0},
            {"sWidth": "15%", "mData": 1},
            {"sWidth": "14%", "mData": 2},
            {"sWidth": "2%", "mData": 3},
        ]
    });
}


//find classifictions
function findClassifictions(){
    var classifications = [];
    $("#checklistClassifictionTable").find('select').each (function() {
        var attr = $(this).attr('id');
        var val = $("#" + attr).val();
        var classificationId = $(this).attr('classification-id');

        if(val != null && val != ""){
            classifications.push({classificationId: classificationId, value: val});
        }
    });

    return classifications;
}

//view question
function viewCheckListQuestion(id){
    window.location.assign("/question/editQuestion/" +id);
}